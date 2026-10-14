@extends('layouts.app')

@section('title', 'My Leads CRM')

@section('content')
<div class="space-y-6" x-data="{ 
    selectedLeads: [], 
    selectAll: false,
    toggleAll() {
        if (this.selectAll) {
            this.selectedLeads = {{ json_encode($leads->pluck('id')) }};
        } else {
            this.selectedLeads = [];
        }
    }
}">

    <!-- Top Header & Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center space-x-2">
                <span>My Leads CRM</span>
                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                    {{ $leads->total() }} Total
                </span>
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Manage, categorize, assign, and communicate with your company's business prospects.</p>
        </div>
        <div class="flex items-center space-x-2.5">
            <a href="{{ route('import-export.export', request()->all()) }}" class="inline-flex items-center space-x-1.5 px-3.5 py-2 text-xs font-semibold rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition-all shadow-sm">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                <span>Export CSV</span>
            </a>
            <a href="{{ route('import-export.index') }}" class="inline-flex items-center space-x-1.5 px-3.5 py-2 text-xs font-semibold rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition-all shadow-sm">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                <span>Import Leads</span>
            </a>
            <a href="{{ route('leads.create') }}" class="inline-flex items-center space-x-1.5 px-4 py-2 text-xs font-semibold rounded-xl bg-gradient-to-r from-rose-500 via-purple-600 to-indigo-600 text-white shadow-md shadow-rose-500/20 hover:opacity-95 transition-opacity">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Add New Lead</span>
            </a>
        </div>
    </div>

    <!-- Filter Bar Card -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <form method="GET" action="{{ route('leads.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
            <!-- Search Keyword -->
            <div class="lg:col-span-2">
                <input type="text" 
                       name="query" 
                       value="{{ $filters['query'] ?? '' }}" 
                       placeholder="Search name, instagram, email..." 
                       class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-rose-500 text-slate-800 dark:text-slate-200">
            </div>

            <!-- Category -->
            <div>
                <select name="category" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-rose-500 text-slate-800 dark:text-slate-200">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ ($filters['category'] ?? '') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status -->
            <div>
                <select name="status" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-rose-500 text-slate-800 dark:text-slate-200">
                    <option value="">All Statuses</option>
                    <option value="new" {{ ($filters['status'] ?? '') === 'new' ? 'selected' : '' }}>New</option>
                    <option value="contacted" {{ ($filters['status'] ?? '') === 'contacted' ? 'selected' : '' }}>Contacted</option>
                    <option value="interested" {{ ($filters['status'] ?? '') === 'interested' ? 'selected' : '' }}>Interested</option>
                    <option value="follow_up" {{ ($filters['status'] ?? '') === 'follow_up' ? 'selected' : '' }}>Follow-up</option>
                    <option value="converted" {{ ($filters['status'] ?? '') === 'converted' ? 'selected' : '' }}>Converted</option>
                    <option value="not_interested" {{ ($filters['status'] ?? '') === 'not_interested' ? 'selected' : '' }}>Not Interested</option>
                </select>
            </div>

            <!-- Priority -->
            <div>
                <select name="priority" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-rose-500 text-slate-800 dark:text-slate-200">
                    <option value="">All Priorities</option>
                    <option value="high" {{ ($filters['priority'] ?? '') === 'high' ? 'selected' : '' }}>High</option>
                    <option value="medium" {{ ($filters['priority'] ?? '') === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="low" {{ ($filters['priority'] ?? '') === 'low' ? 'selected' : '' }}>Low</option>
                </select>
            </div>

            <!-- Submit Filter Button -->
            <div class="flex items-center space-x-2">
                <button type="submit" class="w-full py-2 px-3 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-slate-800 dark:hover:bg-slate-700 text-white text-xs font-semibold transition-colors">
                    Filter Leads
                </button>
                @if(count(array_filter($filters)) > 0)
                <a href="{{ route('leads.index') }}" class="p-2 text-slate-400 hover:text-rose-500" title="Reset">
                    ✕
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Bulk Action Sticky Bar (appears when 1 or more leads are checked) -->
    <div x-show="selectedLeads.length > 0" 
         x-transition
         class="p-3 bg-slate-900 text-white rounded-2xl shadow-xl flex flex-wrap items-center justify-between gap-3 border border-slate-700" 
         style="display: none;">
        <div class="flex items-center space-x-2 text-xs font-semibold">
            <span class="w-6 h-6 rounded-full bg-rose-500 text-white flex items-center justify-center font-bold" x-text="selectedLeads.length"></span>
            <span>Leads Selected</span>
        </div>

        <form method="POST" action="{{ route('leads.bulk') }}" class="flex items-center space-x-2">
            @csrf
            <template x-for="id in selectedLeads" :key="id">
                <input type="hidden" name="lead_ids[]" :value="id">
            </template>

            <select name="bulk_action" class="text-xs bg-slate-800 border-slate-700 text-white rounded-xl px-3 py-1.5 focus:ring-rose-500">
                <option value="">Choose Bulk Action...</option>
                <optgroup label="Change Status">
                    <option value="status_new">Mark New</option>
                    <option value="status_contacted">Mark Contacted</option>
                    <option value="status_interested">Mark Interested</option>
                    <option value="status_follow_up">Mark Follow-up</option>
                    <option value="status_converted">Mark Converted</option>
                    <option value="status_not_interested">Mark Not Interested</option>
                </optgroup>
                <optgroup label="Manage">
                    <option value="delete">Move to Trash</option>
                </optgroup>
            </select>

            <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-500 hover:bg-rose-600 text-white text-xs font-semibold transition-colors">
                Apply Action
            </button>
            <button type="button" @click="selectedLeads = []; selectAll = false" class="text-xs text-slate-400 hover:text-white px-2">
                Cancel
            </button>
        </form>
    </div>

    <!-- Leads Table Card -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
                        <th class="py-3 px-4 w-10">
                            <input type="checkbox" x-model="selectAll" @change="toggleAll()" class="rounded border-slate-300 dark:border-slate-700 text-rose-500 focus:ring-rose-500">
                        </th>
                        <th class="py-3 px-4">Business & Instagram</th>
                        <th class="py-3 px-4">Category & Location</th>
                        <th class="py-3 px-4">Contact Points</th>
                        <th class="py-3 px-4">Priority & Status</th>
                        <th class="py-3 px-4">Assigned To</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 text-xs">
                    @forelse($leads as $lead)
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors group">
                        <!-- Checkbox -->
                        <td class="py-3.5 px-4">
                            <input type="checkbox" :value="{{ $lead->id }}" x-model="selectedLeads" class="rounded border-slate-300 dark:border-slate-700 text-rose-500 focus:ring-rose-500">
                        </td>

                        <!-- Business Name & Instagram Handle -->
                        <td class="py-3.5 px-4">
                            <div class="flex items-center space-x-3">
                                <img src="{{ $lead->profile_picture_url ?? 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=200' }}" 
                                     alt="{{ $lead->business_name }}" 
                                     class="w-10 h-10 rounded-xl object-cover shrink-0 border border-slate-200 dark:border-slate-800">
                                <div class="min-w-0">
                                    <a href="{{ route('leads.show', $lead->id) }}" class="font-bold text-slate-900 dark:text-white hover:text-rose-500 transition-colors block truncate max-w-[200px]">
                                        {{ $lead->business_name }}
                                    </a>
                                    @if($lead->instagram_username)
                                    <a href="{{ $lead->instagram_profile_url ?? 'https://instagram.com/' . $lead->instagram_username }}" target="_blank" rel="noopener noreferrer" class="text-rose-500 hover:text-rose-600 font-medium inline-flex items-center space-x-0.5">
                                        <span>@ {{ $lead->instagram_username }}</span>
                                    </a>
                                    @else
                                    <span class="text-slate-400">No IG handle</span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <!-- Category & City -->
                        <td class="py-3.5 px-4">
                            <p class="font-medium text-slate-800 dark:text-slate-200 truncate max-w-[180px]">{{ $lead->category }}</p>
                            <p class="text-slate-400 text-[11px] mt-0.5">📍 {{ $lead->city ?? 'Not specified' }}</p>
                            <!-- Tags pill list -->
                            @if($lead->tags->count() > 0)
                            <div class="flex flex-wrap gap-1 mt-1">
                                @foreach($lead->tags->take(2) as $tag)
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold" style="background-color: {{ $tag->color }}15; color: {{ $tag->color }};">
                                    {{ $tag->name }}
                                </span>
                                @endforeach
                            </div>
                            @endif
                        </td>

                        <!-- Contact Points -->
                        <td class="py-3.5 px-4">
                            <div class="space-y-0.5">
                                @if($lead->public_email)
                                <div class="flex items-center space-x-1.5 text-slate-600 dark:text-slate-300">
                                    <span class="text-slate-400">✉️</span>
                                    <span class="truncate max-w-[160px]">{{ $lead->public_email }}</span>
                                </div>
                                @endif
                                @if($lead->public_phone)
                                <div class="flex items-center space-x-1.5 text-slate-600 dark:text-slate-300">
                                    <span class="text-slate-400">📞</span>
                                    <span>{{ $lead->public_phone }}</span>
                                </div>
                                @endif
                                @if(!$lead->public_email && !$lead->public_phone)
                                <span class="text-slate-400 italic">No direct public contact</span>
                                @endif
                            </div>
                        </td>

                        <!-- Priority & Status -->
                        <td class="py-3.5 px-4">
                            <div class="space-y-1">
                                <!-- Status badge -->
                                @php
                                    $statusClasses = [
                                        'new' => 'bg-blue-100 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300',
                                        'contacted' => 'bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300',
                                        'interested' => 'bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300',
                                        'follow_up' => 'bg-purple-100 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300',
                                        'converted' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300',
                                        'not_interested' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-400',
                                    ];
                                @endphp
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $statusClasses[$lead->status] ?? '' }}">
                                    {{ str_replace('_', ' ', $lead->status) }}
                                </span>
                                <!-- Priority indicator -->
                                <div class="flex items-center space-x-1 text-[11px] text-slate-400">
                                    <span class="w-2 h-2 rounded-full 
                                          {{ $lead->priority === 'high' ? 'bg-red-500' : ($lead->priority === 'medium' ? 'bg-amber-500' : 'bg-slate-400') }}"></span>
                                    <span class="capitalize">{{ $lead->priority }} Priority</span>
                                </div>
                            </div>
                        </td>

                        <!-- Assigned To -->
                        <td class="py-3.5 px-4">
                            @if($lead->currentAssignment)
                            <div class="flex items-center space-x-1.5">
                                <div class="w-6 h-6 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center font-bold text-[10px]">
                                    {{ substr($lead->currentAssignment->assignedTo->name, 0, 1) }}
                                </div>
                                <span class="font-medium text-slate-700 dark:text-slate-300 truncate max-w-[100px]">
                                    {{ $lead->currentAssignment->assignedTo->name }}
                                </span>
                            </div>
                            @else
                            <span class="text-slate-400 italic">Unassigned</span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="py-3.5 px-4 text-right">
                            <div class="flex items-center justify-end space-x-1">
                                <a href="{{ route('leads.show', $lead->id) }}" class="p-1.5 text-slate-400 hover:text-slate-800 dark:hover:text-white rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800" title="View Profile">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                @if($lead->instagram_profile_url)
                                <a href="{{ $lead->instagram_profile_url }}" target="_blank" rel="noopener noreferrer" class="p-1.5 text-slate-400 hover:text-rose-500 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800" title="Open Instagram">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                    </svg>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400">
                            No business leads found matching the filters.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($leads->hasPages())
        <div class="p-4 border-t border-slate-200 dark:border-slate-800">
            {{ $leads->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
