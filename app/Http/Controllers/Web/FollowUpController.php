<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BusinessLead;
use App\Models\FollowUp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowUpController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;

        $status = $request->get('status', 'pending');
        $priority = $request->get('priority');

        $query = FollowUp::with(['lead', 'user'])
            ->where('company_id', $company->id);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($priority) {
            $query->where('priority', $priority);
        }

        $followUps = $query->orderBy('due_at', 'asc')->paginate(15);
        $teamMembers = User::where('company_id', $company->id)->get();
        $leads = BusinessLead::where('company_id', $company->id)->select('id', 'business_name')->get();

        return view('followups.index', compact('followUps', 'status', 'priority', 'teamMembers', 'leads'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'business_lead_id' => ['required', 'exists:business_leads,id'],
            'title' => ['required', 'string', 'max:255'],
            'due_at' => ['required', 'date'],
            'priority' => ['required', 'in:low,medium,high'],
            'notes' => ['nullable', 'string'],
        ]);

        $lead = BusinessLead::findOrFail($validated['business_lead_id']);
        if ($lead->company_id !== $user->company_id) {
            abort(403);
        }

        FollowUp::create([
            'business_lead_id' => $lead->id,
            'company_id' => $user->company_id,
            'user_id' => $user->id,
            'title' => $validated['title'],
            'due_at' => Carbon::parse($validated['due_at']),
            'priority' => $validated['priority'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Follow-up task scheduled successfully!');
    }

    public function complete(FollowUp $followUp)
    {
        $user = Auth::user();
        if ($followUp->company_id !== $user->company_id) {
            abort(403);
        }

        $followUp->update([
            'status' => 'completed',
            'completed_at' => Carbon::now(),
        ]);

        return back()->with('success', 'Follow-up marked as completed.');
    }

    public function destroy(FollowUp $followUp)
    {
        $user = Auth::user();
        if ($followUp->company_id !== $user->company_id) {
            abort(403);
        }

        $followUp->delete();
        return back()->with('info', 'Follow-up deleted.');
    }
}
