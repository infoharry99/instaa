<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BusinessLead;
use App\Models\LeadAssignment;
use App\Models\LeadContact;
use App\Models\LeadList;
use App\Models\LeadNote;
use App\Models\LeadSource;
use App\Models\LeadTag;
use App\Models\OutreachTemplate;
use App\Models\User;
use App\Services\AuditLogService;
use App\Services\LeadDiscoveryService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadController extends Controller
{
    protected LeadDiscoveryService $discoveryService;

    public function __construct(LeadDiscoveryService $discoveryService)
    {
        $this->discoveryService = $discoveryService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;

        $filters = $request->only([
            'query', 'category', 'city', 'state', 'status', 'priority',
            'has_email', 'has_phone', 'has_website', 'tag_id', 'list_id',
            'assigned_to', 'sort_by', 'sort_order'
        ]);

        $leads = $this->discoveryService->searchCompanyLeads($company, $filters, 12);

        $categories = LeadDiscoveryService::getCategories();
        $tags = LeadTag::where('company_id', $company->id)->get();
        $lists = LeadList::where('company_id', $company->id)->get();
        $teamMembers = User::where('company_id', $company->id)->get();

        return view('leads.index', compact(
            'leads',
            'filters',
            'categories',
            'tags',
            'lists',
            'teamMembers',
            'company'
        ));
    }

    public function create()
    {
        $user = Auth::user();
        $company = $user->company;

        $categories = LeadDiscoveryService::getCategories();
        $sources = LeadSource::where('is_active', true)->get();
        $tags = LeadTag::where('company_id', $company->id)->get();
        $lists = LeadList::where('company_id', $company->id)->get();
        $teamMembers = User::where('company_id', $company->id)->get();

        return view('leads.create', compact('categories', 'sources', 'tags', 'lists', 'teamMembers', 'company'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;

        // Check quota
        if ($company->leads_used_count >= $company->max_leads_quota) {
            return back()->with('error', "Lead quota limit ({$company->max_leads_quota}) reached. Please upgrade your plan.")->withInput();
        }

        $validated = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'instagram_username' => ['nullable', 'string', 'max:255'],
            'category' => ['required', 'string'],
            'city' => ['nullable', 'string'],
            'state' => ['nullable', 'string'],
            'country' => ['nullable', 'string'],
            'location_address' => ['nullable', 'string'],
            'official_website' => ['nullable', 'url'],
            'public_email' => ['nullable', 'email'],
            'public_phone' => ['nullable', 'string'],
            'contact_person' => ['nullable', 'string'],
            'business_description' => ['nullable', 'string'],
            'priority' => ['required', 'in:low,medium,high'],
            'status' => ['required', 'in:new,contacted,interested,follow_up,converted,not_interested'],
            'lead_source_id' => ['nullable', 'exists:lead_sources,id'],
            'assigned_to_user_id' => ['nullable', 'exists:users,id'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['exists:lead_tags,id'],
            'list_id' => ['nullable', 'exists:lead_lists,id'],
        ]);

        $igUser = !empty($validated['instagram_username']) ? ltrim($validated['instagram_username'], '@') : null;

        // Duplicate check
        $duplicate = BusinessLead::where('company_id', $company->id)
            ->where(function ($q) use ($igUser, $validated) {
                if ($igUser) $q->orWhere('instagram_username', $igUser);
                if (!empty($validated['public_email'])) $q->orWhere('public_email', $validated['public_email']);
                if (!empty($validated['public_phone'])) $q->orWhere('public_phone', $validated['public_phone']);
            })
            ->first();

        if ($duplicate) {
            return back()->with('error', "Duplicate record detected. Matching existing lead: '{$duplicate->business_name}'.")->withInput();
        }

        $lead = BusinessLead::create([
            'company_id' => $company->id,
            'lead_source_id' => $validated['lead_source_id'] ?? null,
            'business_name' => $validated['business_name'],
            'instagram_username' => $igUser,
            'instagram_profile_url' => $igUser ? "https://instagram.com/{$igUser}" : null,
            'category' => $validated['category'],
            'city' => $validated['city'] ?? null,
            'state' => $validated['state'] ?? null,
            'country' => $validated['country'] ?? 'India',
            'location_address' => $validated['location_address'] ?? null,
            'official_website' => $validated['official_website'] ?? null,
            'public_email' => $validated['public_email'] ?? null,
            'public_phone' => $validated['public_phone'] ?? null,
            'contact_person' => $validated['contact_person'] ?? null,
            'business_description' => $validated['business_description'] ?? null,
            'priority' => $validated['priority'],
            'status' => $validated['status'],
            'source_type' => 'manual_entry',
            'collected_at' => Carbon::now(),
            'last_verified_at' => Carbon::now(),
        ]);

        if (!empty($validated['contact_person'])) {
            LeadContact::create([
                'business_lead_id' => $lead->id,
                'name' => $validated['contact_person'],
                'email' => $validated['public_email'] ?? null,
                'phone' => $validated['public_phone'] ?? null,
                'is_primary' => true,
            ]);
        }

        if (!empty($validated['tag_ids'])) {
            $lead->tags()->sync($validated['tag_ids']);
        }

        if (!empty($validated['list_id'])) {
            $lead->lists()->syncWithoutDetaching([$validated['list_id'] => ['added_by_user_id' => $user->id]]);
        }

        if (!empty($validated['assigned_to_user_id'])) {
            LeadAssignment::create([
                'business_lead_id' => $lead->id,
                'company_id' => $company->id,
                'assigned_to_user_id' => $validated['assigned_to_user_id'],
                'assigned_by_user_id' => $user->id,
                'notes' => 'Assigned upon creation.',
            ]);
        }

        $company->increment('leads_used_count');

        AuditLogService::log(
            action: 'lead.created',
            entityType: 'BusinessLead',
            entityId: $lead->id,
            description: "Created lead '{$lead->business_name}'."
        );

        return redirect()->route('leads.show', $lead->id)
            ->with('success', "Lead '{$lead->business_name}' created successfully!");
    }

    public function show(BusinessLead $lead)
    {
        $user = Auth::user();
        if ($lead->company_id !== $user->company_id) {
            abort(403);
        }

        $lead->load([
            'tags',
            'contacts',
            'lists',
            'notes.user',
            'communicationLogs.user',
            'followUps.user',
            'leadSource',
            'assignments.assignedTo',
            'currentAssignment.assignedTo',
        ]);

        $company = $user->company;
        $teamMembers = User::where('company_id', $company->id)->get();
        $availableTags = LeadTag::where('company_id', $company->id)->get();
        $availableLists = LeadList::where('company_id', $company->id)->get();
        $outreachTemplates = OutreachTemplate::where('company_id', $company->id)->get();

        return view('leads.show', compact(
            'lead',
            'teamMembers',
            'availableTags',
            'availableLists',
            'outreachTemplates'
        ));
    }

    public function update(Request $request, BusinessLead $lead)
    {
        $user = Auth::user();
        if ($lead->company_id !== $user->company_id) {
            abort(403);
        }

        $validated = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'instagram_username' => ['nullable', 'string', 'max:255'],
            'category' => ['required', 'string'],
            'city' => ['nullable', 'string'],
            'state' => ['nullable', 'string'],
            'country' => ['nullable', 'string'],
            'location_address' => ['nullable', 'string'],
            'official_website' => ['nullable', 'url'],
            'public_email' => ['nullable', 'email'],
            'public_phone' => ['nullable', 'string'],
            'contact_person' => ['nullable', 'string'],
            'business_description' => ['nullable', 'string'],
            'priority' => ['required', 'in:low,medium,high'],
            'status' => ['required', 'in:new,contacted,interested,follow_up,converted,not_interested'],
        ]);

        $igUser = !empty($validated['instagram_username']) ? ltrim($validated['instagram_username'], '@') : null;

        $lead->update([
            'business_name' => $validated['business_name'],
            'instagram_username' => $igUser,
            'instagram_profile_url' => $igUser ? "https://instagram.com/{$igUser}" : null,
            'category' => $validated['category'],
            'city' => $validated['city'] ?? null,
            'state' => $validated['state'] ?? null,
            'country' => $validated['country'] ?? 'India',
            'location_address' => $validated['location_address'] ?? null,
            'official_website' => $validated['official_website'] ?? null,
            'public_email' => $validated['public_email'] ?? null,
            'public_phone' => $validated['public_phone'] ?? null,
            'contact_person' => $validated['contact_person'] ?? null,
            'business_description' => $validated['business_description'] ?? null,
            'priority' => $validated['priority'],
            'status' => $validated['status'],
            'last_verified_at' => Carbon::now(),
        ]);

        AuditLogService::log(
            action: 'lead.updated',
            entityType: 'BusinessLead',
            entityId: $lead->id,
            description: "Updated lead '{$lead->business_name}'."
        );

        return back()->with('success', 'Lead details updated successfully.');
    }

    public function destroy(BusinessLead $lead)
    {
        $user = Auth::user();
        if ($lead->company_id !== $user->company_id) {
            abort(403);
        }

        $leadName = $lead->business_name;
        $lead->delete();

        AuditLogService::log(
            action: 'lead.deleted',
            entityType: 'BusinessLead',
            entityId: $lead->id,
            description: "Deleted lead '{$leadName}'."
        );

        return redirect()->route('leads.index')
            ->with('success', "Lead '{$leadName}' moved to trash.");
    }

    public function addNote(Request $request, BusinessLead $lead)
    {
        $user = Auth::user();
        if ($lead->company_id !== $user->company_id) {
            abort(403);
        }

        $validated = $request->validate([
            'content' => ['required', 'string'],
            'is_pinned' => ['nullable', 'boolean'],
        ]);

        LeadNote::create([
            'business_lead_id' => $lead->id,
            'user_id' => $user->id,
            'content' => $validated['content'],
            'is_pinned' => $request->boolean('is_pinned'),
        ]);

        return back()->with('success', 'Note added.');
    }

    public function assign(Request $request, BusinessLead $lead)
    {
        $user = Auth::user();
        if ($lead->company_id !== $user->company_id) {
            abort(403);
        }

        $validated = $request->validate([
            'assigned_to_user_id' => ['required', 'exists:users,id'],
            'notes' => ['nullable', 'string'],
        ]);

        $assignee = User::findOrFail($validated['assigned_to_user_id']);
        if ($assignee->company_id !== $user->company_id) {
            abort(403);
        }

        LeadAssignment::create([
            'business_lead_id' => $lead->id,
            'company_id' => $user->company_id,
            'assigned_to_user_id' => $assignee->id,
            'assigned_by_user_id' => $user->id,
            'notes' => $validated['notes'] ?? null,
        ]);

        AuditLogService::log(
            action: 'lead.assigned',
            entityType: 'BusinessLead',
            entityId: $lead->id,
            description: "Assigned '{$lead->business_name}' to {$assignee->name}."
        );

        return back()->with('success', "Lead assigned to {$assignee->name}.");
    }

    public function updateTags(Request $request, BusinessLead $lead)
    {
        $user = Auth::user();
        if ($lead->company_id !== $user->company_id) {
            abort(403);
        }

        $lead->tags()->sync($request->input('tag_ids', []));

        return back()->with('success', 'Tags updated.');
    }

    public function updateLists(Request $request, BusinessLead $lead)
    {
        $user = Auth::user();
        if ($lead->company_id !== $user->company_id) {
            abort(403);
        }

        $listIds = $request->input('list_ids', []);
        $syncData = [];
        foreach ($listIds as $lid) {
            $syncData[$lid] = ['added_by_user_id' => $user->id];
        }

        $lead->lists()->sync($syncData);

        return back()->with('success', 'Lists updated.');
    }

    public function bulkAction(Request $request)
    {
        $user = Auth::user();
        $action = $request->input('bulk_action');
        $leadIds = $request->input('lead_ids', []);

        if (empty($leadIds)) {
            return back()->with('error', 'No leads selected.');
        }

        $leads = BusinessLead::where('company_id', $user->company_id)
            ->whereIn('id', $leadIds)
            ->get();

        if ($action === 'delete') {
            BusinessLead::where('company_id', $user->company_id)
                ->whereIn('id', $leadIds)
                ->delete();

            AuditLogService::log(
                action: 'lead.bulk_deleted',
                description: "Bulk deleted " . count($leads) . " leads."
            );

            return back()->with('success', count($leads) . ' leads deleted.');
        }

        if (str_starts_with($action, 'status_')) {
            $newStatus = substr($action, 7);
            if (in_array($newStatus, ['new', 'contacted', 'interested', 'follow_up', 'converted', 'not_interested'])) {
                BusinessLead::where('company_id', $user->company_id)
                    ->whereIn('id', $leadIds)
                    ->update(['status' => $newStatus]);

                return back()->with('success', count($leads) . " leads updated to status: {$newStatus}.");
            }
        }

        if ($action === 'assign' && $request->filled('assigned_to_user_id')) {
            $targetUserId = $request->input('assigned_to_user_id');
            foreach ($leads as $lead) {
                LeadAssignment::create([
                    'business_lead_id' => $lead->id,
                    'company_id' => $user->company_id,
                    'assigned_to_user_id' => $targetUserId,
                    'assigned_by_user_id' => $user->id,
                    'notes' => 'Bulk assignment.',
                ]);
            }
            return back()->with('success', count($leads) . ' leads reassigned.');
        }

        if ($action === 'add_to_list' && $request->filled('target_list_id')) {
            $listId = $request->input('target_list_id');
            foreach ($leads as $lead) {
                $lead->lists()->syncWithoutDetaching([$listId => ['added_by_user_id' => $user->id]]);
            }
            return back()->with('success', count($leads) . ' leads added to custom list.');
        }

        return back()->with('info', 'Action completed.');
    }
}
