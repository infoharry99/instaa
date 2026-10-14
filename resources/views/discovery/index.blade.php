@extends('layouts.app')

@section('title', 'Business Lead Finder')

@section('content')
<div class="space-y-6">

    <!-- Header & Compliance Info Banner -->
    <div>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center space-x-2">
                    <span>Business Lead Finder</span>
                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 dark:border-rose-800">Verified Sources</span>
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Discover prospective clients with publicly available business contacts from authorized registries and official Instagram profiles.</p>
            </div>
            <div>
                <span class="text-xs text-slate-400 bg-white dark:bg-slate-900 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-800">
                    Remaining Quota: <strong class="text-slate-800 dark:text-white">{{ $company->max_leads_quota - $company->leads_used_count }}</strong> leads
                </span>
            </div>
        </div>

        <div class="mt-4 p-3.5 rounded-xl bg-gradient-to-r from-blue-500/10 via-indigo-500/10 to-purple-500/10 border border-indigo-200 dark:border-indigo-800/60 flex items-start space-x-3 text-xs text-indigo-900 dark:text-indigo-200">
            <svg class="w-5 h-5 text-indigo-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            <div>
                <strong class="font-semibold">Compliance Assurance:</strong> 
                This discovery engine only returns verified commercial entities with publicly disclosed contact points adhering to Meta Platform Terms and global privacy standards (GDPR, DPDP). Private personal accounts and restricted endpoints are strictly protected.
            </div>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <form method="GET" action="{{ route('discovery.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <!-- Keyword / Business Name -->
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Business Name or Keyword</label>
                    <div class="relative">
                        <input type="text" 
                               name="query" 
                               value="{{ $params['query'] ?? '' }}" 
                               placeholder="e.g. Coffee, Dental, Fashion..." 
                               class="w-full pl-9 pr-3 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-rose-500 text-slate-800 dark:text-slate-200">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Business Category</label>
                    <select name="category" class="w-full px-3 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-rose-500 text-slate-800 dark:text-slate-200">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ ($params['category'] ?? '') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- City / Location -->
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">City or Target Area</label>
                    <div class="relative">
                        <input type="text" 
                               name="city" 
                               value="{{ $params['city'] ?? '' }}" 
                               placeholder="e.g. Mumbai, Delhi, Bengaluru..." 
                               class="w-full pl-9 pr-3 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-rose-500 text-slate-800 dark:text-slate-200">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            📍
                        </div>
                    </div>
                </div>
            </div>

            <!-- Checkbox Filters & Search Button -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2 border-t border-slate-100 dark:border-slate-800">
                <div class="flex flex-wrap items-center gap-4 text-xs font-medium text-slate-600 dark:text-slate-300">
                    <label class="flex items-center space-x-1.5 cursor-pointer">
                        <input type="checkbox" name="has_email" value="1" {{ !empty($params['has_email']) ? 'checked' : '' }} class="rounded border-slate-300 text-rose-500 focus:ring-rose-500">
                        <span>Public Email Available</span>
                    </label>
                    <label class="flex items-center space-x-1.5 cursor-pointer">
                        <input type="checkbox" name="has_phone" value="1" {{ !empty($params['has_phone']) ? 'checked' : '' }} class="rounded border-slate-300 text-rose-500 focus:ring-rose-500">
                        <span>Public Phone Available</span>
                    </label>
                    <label class="flex items-center space-x-1.5 cursor-pointer">
                        <input type="checkbox" name="has_website" value="1" {{ !empty($params['has_website']) ? 'checked' : '' }} class="rounded border-slate-300 text-rose-500 focus:ring-rose-500">
                        <span>Official Website Available</span>
                    </label>
                </div>

                <div class="flex items-center space-x-2">
                    <a href="{{ route('discovery.index') }}" class="px-3.5 py-2 text-xs font-semibold text-slate-500 hover:text-slate-700 dark:hover:text-slate-300">
                        Reset Filters
                    </a>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-gradient-to-r from-rose-500 to-purple-600 hover:from-rose-600 hover:to-purple-700 text-white text-xs font-semibold shadow-md shadow-rose-500/20 transition-all flex items-center space-x-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span>Search Leads ({{ count($discoveredLeads) }})</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Discovered Results Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($discoveredLeads as $lead)
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-5 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
            <div>
                <!-- Card Header -->
                <div class="flex items-start space-x-3 mb-3">
                    <img src="{{ $lead['profile_picture_url'] ?? 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=200' }}" 
                         alt="{{ $lead['business_name'] }}" 
                         class="w-12 h-12 rounded-xl object-cover shrink-0 border border-slate-200 dark:border-slate-800">
                    <div class="flex-1 min-w-0">
                        <h2 class="text-base font-bold text-slate-900 dark:text-white truncate">
                            {{ $lead['business_name'] }}
                        </h2>
                        <a href="{{ $lead['instagram_profile_url'] ?? '#' }}" target="_blank" rel="noopener noreferrer" class="text-xs font-semibold text-rose-500 hover:text-rose-600 inline-flex items-center space-x-1">
                            <span>@ {{ $lead['instagram_username'] }}</span>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </a>
                        <p class="text-[11px] text-slate-400 mt-0.5">{{ $lead['category'] }} • {{ $lead['city'] ?? 'India' }}</p>
                    </div>
                </div>

                <!-- Description -->
                <p class="text-xs text-slate-600 dark:text-slate-400 line-clamp-2 mb-4">
                    {{ $lead['business_description'] ?? 'Verified commercial enterprise with active digital presence.' }}
                </p>

                <!-- Contact Badges -->
                <div class="space-y-1.5 text-xs text-slate-600 dark:text-slate-300 bg-slate-50 dark:bg-slate-800/40 p-3 rounded-xl mb-4 border border-slate-100 dark:border-slate-800">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Email:</span>
                        <span class="font-medium truncate max-w-[200px]">{{ $lead['public_email'] ?? 'Not available' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Phone:</span>
                        <span class="font-medium">{{ $lead['public_phone'] ?? 'Not available' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Website:</span>
                        @if(!empty($lead['official_website']))
                            <a href="{{ $lead['official_website'] }}" target="_blank" rel="noopener noreferrer" class="text-indigo-500 hover:underline truncate max-w-[180px]">
                                {{ parse_url($lead['official_website'], PHP_URL_HOST) ?? 'Visit Site' }}
                            </a>
                        @else
                            <span class="text-slate-400">Not available</span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Key Contact:</span>
                        <span class="font-medium truncate max-w-[200px]">{{ $lead['contact_person'] ?? 'Not available' }}</span>
                    </div>
                </div>
            </div>

            <!-- Save Action Form -->
            <form method="POST" action="{{ route('discovery.save') }}" class="pt-2 border-t border-slate-100 dark:border-slate-800">
                @csrf
                <input type="hidden" name="business_name" value="{{ $lead['business_name'] }}">
                <input type="hidden" name="instagram_username" value="{{ $lead['instagram_username'] }}">
                <input type="hidden" name="instagram_profile_url" value="{{ $lead['instagram_profile_url'] ?? '' }}">
                <input type="hidden" name="profile_picture_url" value="{{ $lead['profile_picture_url'] ?? '' }}">
                <input type="hidden" name="business_description" value="{{ $lead['business_description'] ?? '' }}">
                <input type="hidden" name="category" value="{{ $lead['category'] }}">
                <input type="hidden" name="city" value="{{ $lead['city'] ?? '' }}">
                <input type="hidden" name="state" value="{{ $lead['state'] ?? '' }}">
                <input type="hidden" name="country" value="{{ $lead['country'] ?? 'India' }}">
                <input type="hidden" name="official_website" value="{{ $lead['official_website'] ?? '' }}">
                <input type="hidden" name="public_email" value="{{ $lead['public_email'] ?? '' }}">
                <input type="hidden" name="public_phone" value="{{ $lead['public_phone'] ?? '' }}">
                <input type="hidden" name="contact_person" value="{{ $lead['contact_person'] ?? '' }}">

                <div class="flex items-center space-x-2">
                    <select name="list_id" class="text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-2 py-2 text-slate-700 dark:text-slate-300 flex-1">
                        <option value="">No list (My Leads)</option>
                        @foreach($lists as $lst)
                        <option value="{{ $lst->id }}">{{ $lst->name }}</option>
                        @endforeach
                    </select>

                    <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-rose-600 dark:hover:bg-rose-700 text-white text-xs font-semibold shrink-0 transition-all flex items-center space-x-1.5 shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Save to CRM</span>
                    </button>
                </div>
            </form>
        </div>
        @empty
        <div class="col-span-full bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-12 text-center">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-2xl mb-4">
                🔍
            </div>
            <h3 class="text-base font-bold text-slate-800 dark:text-slate-200">No leads match your exact filter criteria</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-md mx-auto">Try broadening your search keyword, selecting "All Categories", or clearing city constraints to view more verified business contacts.</p>
            <a href="{{ route('discovery.index') }}" class="inline-block mt-4 px-4 py-2 text-xs font-semibold rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300">
                Clear Filters
            </a>
        </div>
        @endforelse
    </div>
</div>
@endsection
