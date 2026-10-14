<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BusinessLead;
use App\Models\CommunicationLog;
use App\Models\FollowUp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;

        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date')) : Carbon::now()->subDays(30);
        $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date'))->endOfDay() : Carbon::now()->endOfDay();

        // Base query scoped to company
        $leadsQuery = BusinessLead::where('company_id', $company->id);

        // Core metric cards
        $totalLeads = (clone $leadsQuery)->count();
        $newLeadsThisWeek = (clone $leadsQuery)->where('created_at', '>=', Carbon::now()->startOfWeek())->count();
        $contactedLeads = (clone $leadsQuery)->where('status', 'contacted')->count();
        $interestedLeads = (clone $leadsQuery)->where('status', 'interested')->count();
        $convertedLeads = (clone $leadsQuery)->where('status', 'converted')->count();
        $conversionRate = $totalLeads > 0 ? round(($convertedLeads / $totalLeads) * 100, 1) : 0;

        // Follow ups due
        $followUpsDue = FollowUp::where('company_id', $company->id)
            ->where('status', 'pending')
            ->where('due_at', '<=', Carbon::now()->endOfDay())
            ->count();

        // Leads by Category
        $leadsByCategory = BusinessLead::where('company_id', $company->id)
            ->select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->orderByDesc('count')
            ->get();

        // Leads by Location (City)
        $leadsByLocation = BusinessLead::where('company_id', $company->id)
            ->whereNotNull('city')
            ->select('city', DB::raw('count(*) as count'))
            ->groupBy('city')
            ->orderByDesc('count')
            ->take(8)
            ->get();

        // Leads Assigned to Team Members
        $teamWorkload = User::where('company_id', $company->id)
            ->withCount(['leadNotes'])
            ->get()
            ->map(function ($member) use ($company) {
                $assignedCount = BusinessLead::where('company_id', $company->id)
                    ->whereHas('currentAssignment', function ($q) use ($member) {
                        $q->where('assigned_to_user_id', $member->id);
                    })->count();

                $member->assigned_leads_count = $assignedCount;
                return $member;
            });

        // Upcoming Follow-ups (Next 5)
        $upcomingFollowUps = FollowUp::with(['lead', 'user'])
            ->where('company_id', $company->id)
            ->where('status', 'pending')
            ->orderBy('due_at', 'asc')
            ->take(5)
            ->get();

        // Recent Communications (Last 6)
        $recentCommunications = CommunicationLog::with(['lead', 'user'])
            ->where('company_id', $company->id)
            ->latest('logged_at')
            ->take(6)
            ->get();

        // Quota usage
        $quotaUsed = $company->leads_used_count;
        $quotaLimit = $company->max_leads_quota;
        $quotaPercent = $quotaLimit > 0 ? min(100, round(($quotaUsed / $quotaLimit) * 100)) : 100;

        return view('dashboard.index', compact(
            'company',
            'user',
            'totalLeads',
            'newLeadsThisWeek',
            'contactedLeads',
            'interestedLeads',
            'convertedLeads',
            'conversionRate',
            'followUpsDue',
            'leadsByCategory',
            'leadsByLocation',
            'teamWorkload',
            'upcomingFollowUps',
            'recentCommunications',
            'quotaUsed',
            'quotaLimit',
            'quotaPercent'
        ));
    }
}
