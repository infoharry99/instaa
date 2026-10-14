<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BusinessLead;
use App\Models\LeadList;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadListController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $company = $user->company;

        $lists = LeadList::where('company_id', $company->id)
            ->withCount('leads')
            ->latest()
            ->get();

        return view('lists.index', compact('lists', 'company'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'color' => ['nullable', 'string', 'max:20'],
            'is_private' => ['nullable', 'boolean'],
        ]);

        $list = LeadList::create([
            'company_id' => $user->company_id,
            'user_id' => $user->id,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'color' => $validated['color'] ?? '#6366F1',
            'is_private' => $request->boolean('is_private'),
        ]);

        AuditLogService::log(
            action: 'lead_list.created',
            entityType: 'LeadList',
            entityId: $list->id,
            description: "Created custom lead list '{$list->name}'."
        );

        return back()->with('success', "Lead list '{$list->name}' created successfully!");
    }

    public function show(LeadList $list)
    {
        $user = Auth::user();
        if ($list->company_id !== $user->company_id) {
            abort(403);
        }

        $leads = $list->leads()->with(['tags', 'contacts', 'currentAssignment.assignedTo'])->paginate(15);

        return view('lists.show', compact('list', 'leads'));
    }

    public function removeLead(LeadList $list, BusinessLead $lead)
    {
        $user = Auth::user();
        if ($list->company_id !== $user->company_id || $lead->company_id !== $user->company_id) {
            abort(403);
        }

        $list->leads()->detach($lead->id);

        return back()->with('info', "Lead removed from list '{$list->name}'.");
    }

    public function destroy(LeadList $list)
    {
        $user = Auth::user();
        if ($list->company_id !== $user->company_id) {
            abort(403);
        }

        $listName = $list->name;
        $list->delete();

        return redirect()->route('lists.index')->with('success', "List '{$listName}' deleted.");
    }
}
