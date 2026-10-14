<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\LeadList;
use App\Models\LeadSource;
use App\Services\LeadDiscoveryService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadDiscoveryController extends Controller
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

        $categories = LeadDiscoveryService::getCategories();
        $sources = LeadSource::where('is_active', true)->get();
        $lists = LeadList::where('company_id', $company->id)->get();

        $params = [
            'query' => $request->get('query'),
            'category' => $request->get('category'),
            'city' => $request->get('city'),
            'has_email' => $request->boolean('has_email'),
            'has_phone' => $request->boolean('has_phone'),
            'has_website' => $request->boolean('has_website'),
        ];

        $discoveredLeads = $this->discoveryService->discoverLeads($params);

        return view('discovery.index', compact(
            'company',
            'categories',
            'sources',
            'lists',
            'discoveredLeads',
            'params'
        ));
    }

    public function saveToCrm(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;

        $validated = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'instagram_username' => ['nullable', 'string', 'max:255'],
            'instagram_profile_url' => ['nullable', 'url'],
            'profile_picture_url' => ['nullable', 'url'],
            'business_description' => ['nullable', 'string'],
            'category' => ['required', 'string'],
            'city' => ['nullable', 'string'],
            'state' => ['nullable', 'string'],
            'country' => ['nullable', 'string'],
            'official_website' => ['nullable', 'url'],
            'public_email' => ['nullable', 'email'],
            'public_phone' => ['nullable', 'string'],
            'contact_person' => ['nullable', 'string'],
            'priority' => ['nullable', 'in:low,medium,high'],
            'list_id' => ['nullable', 'exists:lead_lists,id'],
        ]);

        try {
            $lead = $this->discoveryService->saveLeadToCrm($company, $user->id, $validated);

            if (!empty($validated['list_id'])) {
                $lead->lists()->syncWithoutDetaching([$validated['list_id'] => ['added_by_user_id' => $user->id]]);
            }

            return redirect()->route('leads.show', $lead->id)
                ->with('success', "Lead '{$lead->business_name}' saved to My Leads successfully!");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
