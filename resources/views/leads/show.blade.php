@extends('layouts.app')

@section('title', $lead->business_name)

@section('content')
<div class="space-y-6" x-data="{ 
    editModalOpen: false, 
    assignModalOpen: false, 
    outreachModalOpen: false, 
    selectedTemplateText: '',
    insertTemplate(body) {
        this.selectedTemplateText = body;
    }
}">

    <!-- Top Navigation Breadcrumbs & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2">
        <div class="flex items-center space-x-2 text-xs text-slate-400">
            <a href="{{ route('leads.index') }}" class="hover:text-rose-500 font-medium">My Leads</a>
            <span>/</span>
            <span class="text-slate-700 dark:text-slate-200 font-semibold truncate">{{ $lead->business_name }}</span>
        </div>

        <div class="flex items-center space-x-2">
            <!-- Edit Details Button -->
            <button @click="editModalOpen = true" class="px-3.5 py-1.5 text-xs font-semibold rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 shadow-sm transition-all flex items-center space-x-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <span>Edit Lead</span>
            </button>

            <!-- Assign Lead Button -->
            <button @click="assignModalOpen = true" class="px-3.5 py-1.5 text-xs font-semibold rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 shadow-sm transition-all flex items-center space-x-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span>Reassign</span>
            </button>

            <!-- Delete Form -->
            <form method="POST" action="{{ route('leads.destroy', $lead->id) }}" onsubmit="return confirm('Move this lead to trash?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-500 rounded-xl hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors" title="Delete Lead">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>

    <!-- Main Lead Profile Header Card -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-start justify-between gap-6">
            <div class="flex items-start space-x-4">
                <img src="{{ $lead->profile_picture_url ?? 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=200' }}" 
                     alt="{{ $lead->business_name }}" 
                     class="w-20 h-20 rounded-2xl object-cover shrink-0 border border-slate-200 dark:border-slate-800 shadow-md">
                
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $lead->business_name }}</h1>
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
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider {{ $statusClasses[$lead->status] ?? '' }}">
                            {{ str_replace('_', ' ', $lead->status) }}
                        </span>
                        <span class="text-xs px-2.5 py-0.5 rounded-full font-semibold {{ $lead->priority === 'high' ? 'bg-red-50 text-red-600 border border-red-200 dark:bg-red-950/40 dark:border-red-800' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' }}">
                            {{ ucfirst($lead->priority) }} Priority
                        </span>
                    </div>

                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 flex flex-wrap items-center gap-3">
                        <span class="font-medium text-slate-700 dark:text-slate-300">📂 {{ $lead->category }}</span>
                        <span>•</span>
                        <span>📍 {{ $lead->city ?? 'Not available' }}{{ $lead->state ? ', ' . $lead->state : '' }}, {{ $lead->country }}</span>
                    </p>

                    <p class="text-xs text-slate-600 dark:text-slate-400 mt-3 max-w-2xl leading-relaxed">
                        {{ $lead->business_description ?? 'No business description provided by source.' }}
                    </p>
                </div>
            </div>

            <!-- Quick Communication Trigger Pills -->
            <div class="flex flex-wrap md:flex-col gap-2 shrink-0">
                @if($lead->instagram_username)
                <a href="{{ $lead->instagram_profile_url ?? 'https://instagram.com/' . $lead->instagram_username }}" 
                   target="_blank" 
                   rel="noopener noreferrer" 
                   class="inline-flex items-center space-x-2 px-3 py-2 rounded-xl bg-gradient-to-r from-purple-600 to-rose-500 text-white text-xs font-semibold shadow-sm hover:opacity-90 transition-opacity">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                    </svg>
                    <span>Open @ {{ $lead->instagram_username }}</span>
                </a>
                @endif

                @if($lead->public_phone)
                <!-- WhatsApp Click to Chat -->
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $lead->public_phone) }}?text=Hello%20{{ urlencode($lead->contact_person ?? $lead->business_name) }}%2C%20I%20saw%20your%20business%20on%20Instagram." 
                   target="_blank" 
                   rel="noopener noreferrer" 
                   class="inline-flex items-center space-x-2 px-3 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm transition-colors">
                    <span>💬 WhatsApp Chat</span>
                </a>

                <!-- Click to Call -->
                <a href="tel:{{ $lead->public_phone }}" 
                   class="inline-flex items-center space-x-2 px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-semibold transition-colors">
                    <span>📞 Call {{ $lead->public_phone }}</span>
                </a>
                @endif

                @if($lead->public_email)
                <a href="mailto:{{ $lead->public_email }}?subject=Inquiry%20for%20{{ urlencode($lead->business_name) }}" 
                   class="inline-flex items-center space-x-2 px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-semibold transition-colors">
                    <span>✉️ Send Email</span>
                </a>
                @endif

                @if($lead->official_website)
                <a href="{{ $lead->official_website }}" 
                   target="_blank" 
                   rel="noopener noreferrer" 
                   class="inline-flex items-center space-x-2 px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-semibold transition-colors">
                    <span>🌐 Official Website</span>
                </a>
                @endif
            </div>
        </div>

        <!-- Compliance & Provenance Footer Row -->
        <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs text-slate-500 dark:text-slate-400">
            <div>
                <span class="block text-slate-400 uppercase tracking-wider text-[10px]">Data Source</span>
                <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $lead->leadSource?->name ?? 'Authorized Business Directory' }}</span>
            </div>
            <div>
                <span class="block text-slate-400 uppercase tracking-wider text-[10px]">Collected On</span>
                <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $lead->collected_at ? $lead->collected_at->format('M d, Y') : 'Not available' }}</span>
            </div>
            <div>
                <span class="block text-slate-400 uppercase tracking-wider text-[10px]">Last Verified</span>
                <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $lead->last_verified_at ? $lead->last_verified_at->format('M d, Y') : 'Not available' }}</span>
            </div>
            <div>
                <span class="block text-slate-400 uppercase tracking-wider text-[10px]">Assigned Agent</span>
                <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $lead->currentAssignment?->assignedTo?->name ?? 'Unassigned' }}</span>
            </div>
        </div>
    </div>

    <!-- 2 Column Layout: Details & CRM Communication Suite -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left Column: Lawfully Sourced Information & Contacts & Custom Lists -->
        <div class="space-y-6">

            <!-- Contact & Verified Profile Card -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-5 shadow-sm">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-4">Official Information</h3>

                <dl class="space-y-3 text-xs">
                    <div>
                        <dt class="text-slate-400 uppercase tracking-wider text-[10px]">Contact Person</dt>
                        <dd class="font-medium text-slate-800 dark:text-slate-200 text-sm mt-0.5">{{ $lead->contact_person ?? 'Not available' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400 uppercase tracking-wider text-[10px]">Public Business Email</dt>
                        <dd class="font-medium text-slate-800 dark:text-slate-200 mt-0.5 select-all">{{ $lead->public_email ?? 'Not available' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400 uppercase tracking-wider text-[10px]">Public Business Phone</dt>
                        <dd class="font-medium text-slate-800 dark:text-slate-200 mt-0.5 select-all">{{ $lead->public_phone ?? 'Not available' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400 uppercase tracking-wider text-[10px]">Official Website</dt>
                        <dd class="font-medium text-slate-800 dark:text-slate-200 mt-0.5">
                            @if($lead->official_website)
                            <a href="{{ $lead->official_website }}" target="_blank" rel="noopener noreferrer" class="text-indigo-500 hover:underline">
                                {{ $lead->official_website }}
                            </a>
                            @else
                            Not available
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-slate-400 uppercase tracking-wider text-[10px]">Physical / Commercial Address</dt>
                        <dd class="font-medium text-slate-800 dark:text-slate-200 mt-0.5">{{ $lead->location_address ?? 'Not available' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400 uppercase tracking-wider text-[10px]">Instagram Public URL</dt>
                        <dd class="font-medium text-slate-800 dark:text-slate-200 mt-0.5">
                            @if($lead->instagram_profile_url)
                            <a href="{{ $lead->instagram_profile_url }}" target="_blank" rel="noopener noreferrer" class="text-rose-500 hover:underline">
                                {{ $lead->instagram_profile_url }}
                            </a>
                            @else
                            Not available
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Tags Manager -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-5 shadow-sm">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-3">Lead Tags</h3>

                <form method="POST" action="{{ route('leads.tags.update', $lead->id) }}">
                    @csrf
                    <div class="flex flex-wrap gap-2 mb-3">
                        @foreach($availableTags as $tag)
                        <label class="flex items-center space-x-1 px-2.5 py-1 rounded-lg text-xs cursor-pointer border transition-all"
                               :class="document.getElementById('tag_{{ $tag->id }}').checked ? 'border-rose-500 bg-rose-50 dark:bg-rose-950/40' : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800'">
                            <input type="checkbox" 
                                   name="tag_ids[]" 
                                   id="tag_{{ $tag->id }}"
                                   value="{{ $tag->id }}" 
                                   {{ $lead->tags->contains($tag->id) ? 'checked' : '' }} 
                                   class="rounded text-rose-500 border-slate-300 focus:ring-rose-500">
                            <span class="font-medium" style="color: {{ $tag->color }}">{{ $tag->name }}</span>
                        </label>
                        @endforeach
                    </div>
                    <button type="submit" class="w-full py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold transition-colors">
                        Save Tags
                    </button>
                </form>
            </div>

            <!-- Custom Lists Assignment -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-5 shadow-sm">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-3">Lists & Campaigns</h3>

                <form method="POST" action="{{ route('leads.lists.update', $lead->id) }}">
                    @csrf
                    <div class="space-y-1.5 mb-3">
                        @foreach($availableLists as $list)
                        <label class="flex items-center space-x-2 text-xs text-slate-700 dark:text-slate-300 cursor-pointer p-1.5 rounded hover:bg-slate-50 dark:hover:bg-slate-800">
                            <input type="checkbox" 
                                   name="list_ids[]" 
                                   value="{{ $list->id }}" 
                                   {{ $lead->lists->contains($list->id) ? 'checked' : '' }} 
                                   class="rounded text-indigo-500 border-slate-300 focus:ring-indigo-500">
                            <span>{{ $list->name }}</span>
                        </label>
                        @endforeach
                    </div>
                    <button type="submit" class="w-full py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold transition-colors">
                        Update Lists
                    </button>
                </form>
            </div>
        </div>

        <!-- Right 2 Columns: Communication Logger & History & Notes & Follow-ups -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Log Communication Outreach Box -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">Log Outreach & Communication</h3>
                    
                    <!-- Template Inserter Dropdown -->
                    @if($outreachTemplates->count() > 0)
                    <div class="relative" x-data="{ open: false }">
                        <button type="button" @click="open = !open" class="text-xs font-semibold text-rose-500 hover:text-rose-600 flex items-center space-x-1">
                            <span>Insert Template ▾</span>
                        </button>
                        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-64 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl z-20 p-2 space-y-1" style="display: none;">
                            @foreach($outreachTemplates as $tmpl)
                            @php
                                $replacedBody = str_replace(
                                    ['{{business_name}}', '{{contact_person}}', '{{city}}', '{{instagram_username}}', '{{category}}'],
                                    [$lead->business_name, $lead->contact_person ?? $lead->business_name, $lead->city ?? 'your area', $lead->instagram_username ?? '', $lead->category],
                                    $tmpl->body
                                );
                            @endphp
                            <button type="button" 
                                    @click="insertTemplate({{ json_encode($replacedBody) }}); open = false"
                                    class="w-full text-left px-3 py-1.5 text-xs rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200">
                                <span class="font-semibold block truncate">{{ $tmpl->name }}</span>
                                <span class="text-[10px] text-slate-400 capitalize">{{ $tmpl->channel }}</span>
                            </button>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <form method="POST" action="{{ route('communication.log', $lead->id) }}" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 mb-1">Channel</label>
                            <select name="channel" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-rose-500 text-slate-800 dark:text-slate-200">
                                <option value="instagram_manual">Instagram DM / Manual</option>
                                <option value="whatsapp">WhatsApp Outreach</option>
                                <option value="email">Email Sent</option>
                                <option value="call">Phone Call</option>
                                <option value="instagram_api">Official Instagram API</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 mb-1">Direction</label>
                            <select name="direction" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-rose-500 text-slate-800 dark:text-slate-200">
                                <option value="outbound">Outbound (We reached out)</option>
                                <option value="inbound">Inbound (Prospect replied/called)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 mb-1">Advance Status To</label>
                            <select name="update_lead_status" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-rose-500 text-slate-800 dark:text-slate-200">
                                <option value="">Auto (Contacted)</option>
                                <option value="contacted">Contacted</option>
                                <option value="interested">Interested</option>
                                <option value="follow_up">Follow-up</option>
                                <option value="converted">Converted</option>
                                <option value="not_interested">Not Interested</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1">Outreach Summary / Notes</label>
                        <textarea name="notes" 
                                  x-model="selectedTemplateText"
                                  rows="3" 
                                  required 
                                  placeholder="e.g. Sent personalized invitation for marketing collaboration via Instagram DM..." 
                                  class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-rose-500 text-slate-800 dark:text-slate-200"></textarea>
                    </div>

                    <!-- Follow Up Scheduler -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 mb-1">Next Follow-up Date (Optional)</label>
                            <input type="datetime-local" 
                                   name="schedule_follow_up" 
                                   class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-rose-500 text-slate-800 dark:text-slate-200">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 mb-1">Follow-up Reminder Title</label>
                            <input type="text" 
                                   name="follow_up_title" 
                                   placeholder="e.g. Check response to Instagram pitch" 
                                   class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-rose-500 text-slate-800 dark:text-slate-200">
                        </div>
                    </div>

                    <div class="flex justify-end pt-1">
                        <button type="submit" class="px-5 py-2 rounded-xl bg-gradient-to-r from-rose-500 to-purple-600 hover:from-rose-600 hover:to-purple-700 text-white text-xs font-semibold shadow-md shadow-rose-500/20 transition-all">
                            Save Communication Log
                        </button>
                    </div>
                </form>
            </div>

            <!-- Notes Section with Pinning -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-5 shadow-sm">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-4">Internal Team Notes</h3>

                <!-- Add Note Form -->
                <form method="POST" action="{{ route('leads.notes.store', $lead->id) }}" class="space-y-3 mb-6">
                    @csrf
                    <textarea name="content" 
                              rows="2" 
                              required 
                              placeholder="Add an internal note or observation..." 
                              class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-rose-500 text-slate-800 dark:text-slate-200"></textarea>
                    <div class="flex items-center justify-between">
                        <label class="flex items-center space-x-1.5 text-xs text-slate-500 dark:text-slate-400 cursor-pointer">
                            <input type="checkbox" name="is_pinned" value="1" class="rounded border-slate-300 text-rose-500 focus:ring-rose-500">
                            <span>Pin this note to top</span>
                        </label>
                        <button type="submit" class="px-4 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-slate-800 dark:hover:bg-slate-700 text-white text-xs font-semibold transition-colors">
                            Add Note
                        </button>
                    </div>
                </form>

                <!-- Notes Timeline -->
                <div class="space-y-3">
                    @forelse($lead->notes as $note)
                    <div class="p-3.5 rounded-xl {{ $note->is_pinned ? 'bg-amber-50/80 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/60' : 'bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800' }}">
                        <div class="flex items-center justify-between mb-1.5">
                            <div class="flex items-center space-x-2">
                                <span class="font-bold text-xs text-slate-800 dark:text-slate-200">{{ $note->user->name }}</span>
                                <span class="text-[10px] text-slate-400">{{ $note->created_at->diffForHumans() }}</span>
                            </div>
                            @if($note->is_pinned)
                            <span class="text-[10px] font-semibold text-amber-600 dark:text-amber-400 flex items-center space-x-0.5">
                                <span>📌 Pinned</span>
                            </span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-700 dark:text-slate-300 whitespace-pre-line">{{ $note->content }}</p>
                    </div>
                    @empty
                    <p class="text-xs text-slate-400 text-center py-4">No internal notes added yet.</p>
                    @endforelse
                </div>
            </div>

            <!-- Communication History Timeline -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-5 shadow-sm">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-4">Outreach & Activity Timeline</h3>

                <div class="space-y-4">
                    @forelse($lead->communicationLogs as $log)
                    <div class="flex items-start space-x-3 text-xs">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 font-bold
                             {{ $log->channel === 'email' ? 'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300' : '' }}
                             {{ $log->channel === 'whatsapp' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : '' }}
                             {{ $log->channel === 'call' ? 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300' : '' }}
                             {{ str_starts_with($log->channel, 'instagram') ? 'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300' : '' }}">
                            @if($log->channel === 'email') ✉️
                            @elseif($log->channel === 'whatsapp') 💬
                            @elseif($log->channel === 'call') 📞
                            @else 📷
                            @endif
                        </div>
                        <div class="flex-1 bg-slate-50 dark:bg-slate-800/40 p-3.5 rounded-xl border border-slate-100 dark:border-slate-800">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-900 dark:text-white capitalize">
                                    {{ $log->direction }} {{ str_replace('_', ' ', $log->channel) }}
                                </span>
                                <span class="text-[10px] text-slate-400">{{ $log->logged_at->format('M d, Y H:i') }}</span>
                            </div>
                            <p class="text-slate-600 dark:text-slate-300 mt-1 whitespace-pre-line">{{ $log->notes }}</p>
                            <div class="mt-2 text-[10px] text-slate-400">
                                Logged by: <strong class="text-slate-700 dark:text-slate-300">{{ $log->user->name }}</strong>
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-slate-400 text-center py-6">No communication recorded with this lead yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Lead Details Modal -->
    <div x-show="editModalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4" 
         style="display: none;">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-xl w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl" @click.away="editModalOpen = false">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Edit Lead Information</h3>
            <form method="POST" action="{{ route('leads.update', $lead->id) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Business Name</label>
                    <input type="text" name="business_name" value="{{ $lead->business_name }}" required class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Instagram Handle</label>
                        <input type="text" name="instagram_username" value="{{ $lead->instagram_username }}" class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Category</label>
                        <input type="text" name="category" value="{{ $lead->category }}" required class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Public Email</label>
                        <input type="email" name="public_email" value="{{ $lead->public_email }}" class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Public Phone</label>
                        <input type="text" name="public_phone" value="{{ $lead->public_phone }}" class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Status</label>
                        <select name="status" class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                            @foreach(['new', 'contacted', 'interested', 'follow_up', 'converted', 'not_interested'] as $st)
                            <option value="{{ $st }}" {{ $lead->status === $st ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $st)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Priority</label>
                        <select name="priority" class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                            @foreach(['low', 'medium', 'high'] as $pr)
                            <option value="{{ $pr }}" {{ $lead->priority === $pr ? 'selected' : '' }}>{{ ucfirst($pr) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Business Description</label>
                    <textarea name="business_description" rows="3" class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">{{ $lead->business_description }}</textarea>
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="editModalOpen = false" class="px-4 py-2 text-xs font-semibold text-slate-400 hover:text-slate-200">Cancel</button>
                    <button type="submit" class="px-5 py-2 text-xs font-semibold rounded-xl bg-rose-500 hover:bg-rose-600 text-white">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Reassign Modal -->
    <div x-show="assignModalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4" 
         style="display: none;">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-sm w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl" @click.away="assignModalOpen = false">
            <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4">Assign Lead to Team Member</h3>
            <form method="POST" action="{{ route('leads.assign', $lead->id) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Team Member</label>
                    <select name="assigned_to_user_id" required class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200">
                        @foreach($teamMembers as $tm)
                        <option value="{{ $tm->id }}" {{ $lead->currentAssignment?->assigned_to_user_id === $tm->id ? 'selected' : '' }}>
                            {{ $tm->name }} ({{ ucfirst(str_replace('_', ' ', $tm->role)) }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Assignment Note</label>
                    <textarea name="notes" rows="2" placeholder="Instructions for agent..." class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700"></textarea>
                </div>
                <div class="flex justify-end space-x-2 pt-1">
                    <button type="button" @click="assignModalOpen = false" class="px-4 py-2 text-xs font-semibold text-slate-400">Cancel</button>
                    <button type="submit" class="px-5 py-2 text-xs font-semibold rounded-xl bg-rose-500 hover:bg-rose-600 text-white">Assign</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
