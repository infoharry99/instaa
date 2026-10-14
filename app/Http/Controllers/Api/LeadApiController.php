<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BusinessLead;
use App\Services\LeadDiscoveryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeadApiController extends Controller
{
    protected LeadDiscoveryService $discoveryService;

    public function __construct(LeadDiscoveryService $discoveryService)
    {
        $this->discoveryService = $discoveryService;
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $filters = $request->only(['query', 'category', 'city', 'status', 'priority', 'has_email', 'has_phone']);

        $leads = $this->discoveryService->searchCompanyLeads($user->company, $filters, $request->get('per_page', 20));

        return response()->json($leads);
    }

    public function show(Request $request, BusinessLead $lead): JsonResponse
    {
        if ($lead->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $lead->load(['tags', 'contacts', 'notes.user', 'communicationLogs.user', 'followUps.user', 'currentAssignment.assignedTo']);

        return response()->json(['lead' => $lead]);
    }

    public function discover(Request $request): JsonResponse
    {
        $params = $request->only(['query', 'category', 'city', 'has_email', 'has_phone', 'has_website']);
        $results = $this->discoveryService->discoverLeads($params);

        return response()->json(['data' => $results]);
    }
}
