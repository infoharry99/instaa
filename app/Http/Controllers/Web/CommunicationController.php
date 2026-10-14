<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BusinessLead;
use App\Models\CommunicationLog;
use App\Models\FollowUp;
use App\Models\OutreachTemplate;
use App\Services\AuditLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommunicationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;

        $channel = $request->get('channel');
        $query = CommunicationLog::with(['lead', 'user'])
            ->where('company_id', $company->id);

        if ($channel) {
            $query->where('channel', $channel);
        }

        $logs = $query->latest('logged_at')->paginate(15);
        $templates = OutreachTemplate::where('company_id', $company->id)->get();

        return view('communication.index', compact('logs', 'templates', 'channel'));
    }

    public function logCommunication(Request $request, BusinessLead $lead)
    {
        $user = Auth::user();
        if ($lead->company_id !== $user->company_id) {
            abort(403);
        }

        $validated = $request->validate([
            'channel' => ['required', 'in:email,call,whatsapp,instagram_manual,instagram_api'],
            'direction' => ['required', 'in:outbound,inbound'],
            'subject' => ['nullable', 'string', 'max:255'],
            'notes' => ['required', 'string'],
            'outcome' => ['nullable', 'string', 'max:50'],
            'contact_info' => ['nullable', 'string', 'max:255'],
            'update_lead_status' => ['nullable', 'in:contacted,interested,follow_up,converted,not_interested'],
            'schedule_follow_up' => ['nullable', 'date'],
            'follow_up_title' => ['nullable', 'string'],
        ]);

        $log = CommunicationLog::create([
            'business_lead_id' => $lead->id,
            'company_id' => $user->company_id,
            'user_id' => $user->id,
            'channel' => $validated['channel'],
            'direction' => $validated['direction'],
            'subject' => $validated['subject'] ?? null,
            'notes' => $validated['notes'],
            'outcome' => $validated['outcome'] ?? null,
            'contact_info' => $validated['contact_info'] ?? ($validated['channel'] === 'email' ? $lead->public_email : $lead->public_phone),
            'logged_at' => Carbon::now(),
        ]);

        // Automatically advance lead status if requested
        if (!empty($validated['update_lead_status'])) {
            $lead->update(['status' => $validated['update_lead_status']]);
        } elseif ($lead->status === 'new') {
            $lead->update(['status' => 'contacted']);
        }

        // Schedule follow up if set
        if (!empty($validated['schedule_follow_up'])) {
            FollowUp::create([
                'business_lead_id' => $lead->id,
                'company_id' => $user->company_id,
                'user_id' => $user->id,
                'title' => $validated['follow_up_title'] ?? "Follow up regarding {$validated['channel']} outreach",
                'due_at' => Carbon::parse($validated['schedule_follow_up']),
                'priority' => $lead->priority,
                'status' => 'pending',
            ]);
        }

        AuditLogService::log(
            action: 'communication.logged',
            entityType: 'CommunicationLog',
            entityId: $log->id,
            description: "Logged {$validated['direction']} {$validated['channel']} with '{$lead->business_name}'."
        );

        return back()->with('success', 'Communication outreach logged successfully!');
    }

    public function storeTemplate(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'channel' => ['required', 'in:email,whatsapp,instagram'],
            'subject' => ['nullable', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'is_shared' => ['nullable', 'boolean'],
        ]);

        // Auto extract variables
        preg_match_all('/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/', $validated['body'], $matches);
        $variables = array_values(array_unique($matches[1] ?? []));

        OutreachTemplate::create([
            'company_id' => $company->id,
            'user_id' => $user->id,
            'name' => $validated['name'],
            'channel' => $validated['channel'],
            'subject' => $validated['subject'] ?? null,
            'body' => $validated['body'],
            'variables' => $variables,
            'is_shared' => $request->boolean('is_shared', true),
        ]);

        return back()->with('success', 'Outreach template created successfully!');
    }

    public function deleteTemplate(OutreachTemplate $template)
    {
        $user = Auth::user();
        if ($template->company_id !== $user->company_id) {
            abort(403);
        }

        $template->delete();
        return back()->with('success', 'Template removed.');
    }
}
