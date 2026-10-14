@extends('layouts.app')

@section('title', 'Dashboard & Analytics')

@section('content')
<div class="space-y-6">

    <!-- Top Hero Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center space-x-2">
                <span>Welcome back, {{ Auth::user()->name }}</span>
                <span class="text-xl">👋</span>
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Here is the latest lead discovery performance for <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $company->name }}</span>.</p>
        </div>
        <div class="flex items-center space-x-2.5">
            <a href="{{ route('discovery.index') }}" class="inline-flex items-center space-x-2 px-4 py-2 rounded-xl bg-gradient-to-r from-rose-500 via-purple-600 to-indigo-600 hover:from-rose-600 hover:to-indigo-700 text-white text-sm font-semibold shadow-md shadow-rose-500/20 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <span>Find Business Leads</span>
            </a>
            <a href="{{ route('leads.create') }}" class="inline-flex items-center space-x-2 px-4 py-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-sm font-semibold transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Manual Lead</span>
            </a>
        </div>
    </div>

    <!-- Analytics Cards Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
        <!-- Total Leads -->
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm relative overflow-hidden group hover:border-slate-300 dark:hover:border-slate-700 transition-all">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Leads</span>
                <div class="p-2 rounded-xl bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ number_format($totalLeads) }}</p>
            <p class="text-[11px] text-emerald-500 font-medium mt-1 flex items-center">
                <span>All active records</span>
            </p>
        </div>

        <!-- New Leads This Week -->
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm relative overflow-hidden group hover:border-slate-300 dark:hover:border-slate-700 transition-all">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">New This Week</span>
                <div class="p-2 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ number_format($newLeadsThisWeek) }}</p>
            <p class="text-[11px] text-slate-400 font-medium mt-1">Since Monday</p>
        </div>

        <!-- Contacted Leads -->
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm relative overflow-hidden group hover:border-slate-300 dark:hover:border-slate-700 transition-all">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Contacted</span>
                <div class="p-2 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ number_format($contactedLeads) }}</p>
            <p class="text-[11px] text-slate-400 font-medium mt-1">In outreach pipeline</p>
        </div>

        <!-- Interested Prospects -->
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm relative overflow-hidden group hover:border-slate-300 dark:hover:border-slate-700 transition-all">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Interested</span>
                <div class="p-2 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ number_format($interestedLeads) }}</p>
            <p class="text-[11px] text-rose-500 font-medium mt-1">Hot discovery leads</p>
        </div>

        <!-- Follow-ups Due -->
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm relative overflow-hidden group hover:border-slate-300 dark:hover:border-slate-700 transition-all">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Follow-ups Due</span>
                <div class="p-2 rounded-xl bg-red-50 dark:bg-red-950/40 text-red-600 dark:text-red-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold {{ $followUpsDue > 0 ? 'text-red-500' : 'text-slate-900 dark:text-white' }}">{{ number_format($followUpsDue) }}</p>
            <p class="text-[11px] text-slate-400 font-medium mt-1">Pending tasks</p>
        </div>

        <!-- Converted / Win Rate -->
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm relative overflow-hidden group hover:border-slate-300 dark:hover:border-slate-700 transition-all">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Converted</span>
                <div class="p-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ number_format($convertedLeads) }}</p>
            <p class="text-[11px] text-emerald-500 font-medium mt-1">{{ $conversionRate }}% conversion rate</p>
        </div>
    </div>

    <!-- Analytics Charts & Categorization -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Leads by Category Bar Breakdown -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Leads by Business Category</h2>
                    <p class="text-xs text-slate-400">Distribution across verified business sectors</p>
                </div>
                <a href="{{ route('leads.index') }}" class="text-xs font-semibold text-rose-500 hover:text-rose-600">View All</a>
            </div>

            <div class="space-y-4">
                @foreach($leadsByCategory as $cat)
                @php
                    $pct = $totalLeads > 0 ? round(($cat->count / $totalLeads) * 100) : 0;
                @endphp
                <div>
                    <div class="flex items-center justify-between text-xs font-medium mb-1.5">
                        <span class="text-slate-700 dark:text-slate-300 font-semibold">{{ $cat->category }}</span>
                        <span class="text-slate-500 dark:text-slate-400">{{ $cat->count }} leads ({{ $pct }}%)</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-800 h-2.5 rounded-full overflow-hidden">
                        <div class="h-full rounded-full bg-gradient-to-r from-purple-500 via-rose-500 to-amber-500 transition-all duration-700" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Top Locations (Cities) -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Top Geographic Markets</h2>
                    <p class="text-xs text-slate-400">Target cities by lead volume</p>
                </div>
            </div>

            <div class="space-y-3">
                @foreach($leadsByLocation as $loc)
                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-lg bg-rose-50 dark:bg-rose-950/50 text-rose-500 flex items-center justify-center font-bold text-xs">
                            📍
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $loc->city }}</p>
                            <p class="text-xs text-slate-400">Verified contacts</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 text-xs font-bold rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300">
                        {{ $loc->count }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Bottom Split: Team Workload, Upcoming Follow-ups, and Recent Communications -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Team Workload -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-bold text-slate-900 dark:text-white">Team Lead Assignment</h2>
                <a href="{{ route('admin.index') }}" class="text-xs text-rose-500 hover:text-rose-600 font-semibold">Manage</a>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @foreach($teamWorkload as $member)
                <div class="py-3 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-700 dark:text-slate-300 font-bold text-xs">
                            {{ substr($member->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $member->name }}</p>
                            <span class="text-xs text-slate-400 capitalize">{{ str_replace('_', ' ', $member->role) }}</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                            {{ $member->assigned_leads_count }} Leads
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Upcoming Follow-ups -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-bold text-slate-900 dark:text-white">Upcoming Follow-ups</h2>
                <a href="{{ route('followups.index') }}" class="text-xs text-rose-500 hover:text-rose-600 font-semibold">View All</a>
            </div>
            <div class="space-y-3">
                @forelse($upcomingFollowUps as $fu)
                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                    <div class="flex items-center justify-between">
                        <a href="{{ route('leads.show', $fu->lead->id) }}" class="text-sm font-semibold text-slate-900 dark:text-white hover:text-rose-500 line-clamp-1">
                            {{ $fu->lead->business_name }}
                        </a>
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $fu->priority === 'high' ? 'bg-red-100 text-red-700 dark:bg-red-950/60 dark:text-red-300' : 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-300' }}">
                            {{ ucfirst($fu->priority) }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-1">{{ $fu->title }}</p>
                    <div class="flex items-center justify-between mt-2 pt-2 border-t border-slate-200/60 dark:border-slate-700/60 text-[11px] text-slate-400">
                        <span>Due: {{ $fu->due_at->format('M d, H:i') }}</span>
                        <form method="POST" action="{{ route('followups.complete', $fu->id) }}">
                            @csrf
                            <button type="submit" class="text-emerald-500 hover:text-emerald-600 font-medium">Mark Done</button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="text-center py-6 text-slate-400 text-xs">
                    No pending follow-ups. All caught up!
                </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Outreach Logs -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-bold text-slate-900 dark:text-white">Recent Communication</h2>
                <a href="{{ route('communication.index') }}" class="text-xs text-rose-500 hover:text-rose-600 font-semibold">Timeline</a>
            </div>
            <div class="space-y-3">
                @forelse($recentCommunications as $com)
                <div class="flex items-start space-x-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 text-xs font-bold
                         {{ $com->channel === 'email' ? 'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300' : '' }}
                         {{ $com->channel === 'whatsapp' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : '' }}
                         {{ $com->channel === 'call' ? 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300' : '' }}
                         {{ str_starts_with($com->channel, 'instagram') ? 'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300' : '' }}">
                        @if($com->channel === 'email') ✉️
                        @elseif($com->channel === 'whatsapp') 💬
                        @elseif($com->channel === 'call') 📞
                        @else 📷
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <a href="{{ route('leads.show', $com->lead->id) }}" class="text-xs font-semibold text-slate-900 dark:text-white truncate hover:text-rose-500">
                                {{ $com->lead->business_name }}
                            </a>
                            <span class="text-[10px] text-slate-400 shrink-0">{{ $com->logged_at->diffForHumans(null, true) }}</span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-1">{{ $com->notes }}</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-6 text-slate-400 text-xs">
                    No communications logged yet.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
