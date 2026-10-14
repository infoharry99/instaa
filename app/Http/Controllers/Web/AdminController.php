<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\BusinessLead;
use App\Models\Company;
use App\Models\ImportJob;
use App\Models\LeadSource;
use App\Models\Subscription;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized access to Admin Panel.');
        }

        $company = $user->company;

        $teamMembers = User::where('company_id', $company->id)->get();
        $sources = LeadSource::all();
        $subscription = $company->activeSubscription;
        $recentAuditLogs = AuditLog::where('company_id', $company->id)->latest('created_at')->take(10)->get();
        $importJobs = ImportJob::where('company_id', $company->id)->latest()->take(5)->get();

        $stats = [
            'total_users' => $teamMembers->count(),
            'total_leads' => BusinessLead::where('company_id', $company->id)->count(),
            'leads_used' => $company->leads_used_count,
            'leads_quota' => $company->max_leads_quota,
            'plan_name' => $company->plan,
        ];

        return view('admin.index', compact(
            'company',
            'teamMembers',
            'sources',
            'subscription',
            'recentAuditLogs',
            'importJobs',
            'stats'
        ));
    }

    public function storeTeamMember(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'role' => ['required', 'in:admin,manager,sales_agent'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', Password::defaults()],
        ]);

        $newMember = User::create([
            'company_id' => $user->company_id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        AuditLogService::log(
            action: 'team.member_created',
            entityType: 'User',
            entityId: $newMember->id,
            description: "Added team member '{$newMember->name}' with role '{$newMember->role}'."
        );

        return back()->with('success', "Team member '{$newMember->name}' created successfully!");
    }

    public function updateTeamMember(Request $request, User $member)
    {
        $user = Auth::user();
        if (!$user->isAdmin() || $member->company_id !== $user->company_id) {
            abort(403);
        }

        $validated = $request->validate([
            'role' => ['required', 'in:admin,manager,sales_agent'],
            'status' => ['required', 'in:active,inactive,suspended'],
        ]);

        $member->update([
            'role' => $validated['role'],
            'status' => $validated['status'],
        ]);

        AuditLogService::log(
            action: 'team.member_updated',
            entityType: 'User',
            entityId: $member->id,
            description: "Updated role/status for '{$member->name}'."
        );

        return back()->with('success', "Member '{$member->name}' updated.");
    }

    public function toggleSource(LeadSource $source)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        $source->update(['is_active' => !$source->is_active]);

        AuditLogService::log(
            action: 'lead_source.toggled',
            entityType: 'LeadSource',
            entityId: $source->id,
            description: "Toggled lead source '{$source->name}' to " . ($source->is_active ? 'Active' : 'Inactive') . "."
        );

        return back()->with('success', "Lead source '{$source->name}' status updated.");
    }

    public function updateSubscription(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'plan' => ['required', 'in:free,starter,pro,enterprise'],
        ]);

        $planLimits = [
            'free' => 100,
            'starter' => 500,
            'pro' => 2500,
            'enterprise' => 10000,
        ];

        $company = $user->company;
        $company->update([
            'plan' => $validated['plan'],
            'max_leads_quota' => $planLimits[$validated['plan']],
        ]);

        Subscription::create([
            'company_id' => $company->id,
            'plan_name' => ucfirst($validated['plan']) . ' Plan',
            'status' => 'active',
            'leads_limit' => $planLimits[$validated['plan']],
            'monthly_price' => match($validated['plan']) {
                'free' => 0.00,
                'starter' => 1999.00,
                'pro' => 4999.00,
                'enterprise' => 14999.00,
            },
            'starts_at' => now(),
            'expires_at' => now()->addMonth(),
        ]);

        AuditLogService::log(
            action: 'subscription.updated',
            description: "Upgraded company plan to " . ucfirst($validated['plan']) . "."
        );

        return back()->with('success', "Plan upgraded to " . ucfirst($validated['plan']) . " successfully!");
    }

    public function auditLogs()
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        $logs = AuditLog::with('user')
            ->where('company_id', $user->company_id)
            ->latest('created_at')
            ->paginate(25);

        return view('admin.audit_logs', compact('logs'));
    }
}
