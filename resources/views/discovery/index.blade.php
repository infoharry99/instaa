@extends('layouts.app')

@section('title', 'Business Lead Finder & Direct Outreach')

@section('content')
<div class="space-y-6" x-data="{
    outreachModal: false,
    selectedLead: {
        business_name: '',
        instagram_username: '',
        public_phone: '',
        public_email: '',
        city: '',
        category: '',
        contact_person: ''
    },
    selectedTemplate: 'pitch',
    messageText: '',
    copied: false,
    templates: {
        pitch: 'Hi {name}! I came across your Instagram page @{username}. We help businesses in {city} grow their customer base and scale online. Would you be open to a quick 2-minute chat about increasing your inquiries this month?',
        inquiry: 'Hello {name} team! I love the work you share on @{username}. I am reaching out to inquire about your {category} services and current availability for new clients.',
        marketing: 'Hey {name}! Fantastic brand on @{username}. We noticed a couple of simple improvements that could help double your customer engagement and bookings. Can I share a quick 60-second idea with you?',
        question: 'Hi {name}! Quick question regarding your {category} offerings posted on @{username}. Who is the best person on your team to speak with?'
    },
    openOutreach(lead) {
        this.selectedLead = lead;
        this.updateMessage();
        this.outreachModal = true;
        this.copied = false;
    },
    updateMessage() {
        let tpl = this.templates[this.selectedTemplate] || this.templates.pitch;
        let name = this.selectedLead.contact_person || this.selectedLead.business_name || 'there';
        let username = this.selectedLead.instagram_username || '';
        let city = this.selectedLead.city || 'your area';
        let category = this.selectedLead.category || 'business';
        this.messageText = tpl.replaceAll('{name}', name)
                             .replaceAll('{username}', username)
                             .replaceAll('{city}', city)
                             .replaceAll('{category}', category);
    },
    copyMessage() {
        navigator.clipboard.writeText(this.messageText);
        this.copied = true;
        setTimeout(() => this.copied = false, 2500);
    },
    copyAndOpenInstagram() {
        navigator.clipboard.writeText(this.messageText);
        this.copied = true;
        let url = 'https://ig.me/m/' + this.selectedLead.instagram_username;
        window.open(url, '_blank');
    },
    openWhatsApp() {
        let cleanPhone = (this.selectedLead.public_phone || '').replace(/[^0-9]/g, '');
        let url = 'https://wa.me/' + cleanPhone + '?text=' + encodeURIComponent(this.messageText);
        window.open(url, '_blank');
    }
}">

    <!-- Header & Info Banner -->
    <div>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center space-x-2">
                    <span>Instagram Lead Finder & Outreach</span>
                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 dark:border-rose-800">Direct Chat Enabled</span>
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Search any public Instagram profile or local business, view verified contact points, and start outreach conversations instantly.</p>
            </div>
            <div>
                <span class="text-xs text-slate-400 bg-white dark:bg-slate-900 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                    Monthly Quota: <strong class="text-slate-800 dark:text-white">{{ $company->max_leads_quota - $company->leads_used_count }}</strong> remaining
                </span>
            </div>
        </div>

        <!-- Compliance & Safe Sourcing Notice -->
        <div class="mt-4 p-3.5 rounded-xl bg-gradient-to-r from-blue-500/10 via-indigo-500/10 to-purple-500/10 border border-indigo-200 dark:border-indigo-800/60 flex items-start space-x-3 text-xs text-indigo-900 dark:text-indigo-200">
            <svg class="w-5 h-5 text-indigo-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            <div>
                <strong class="font-semibold">Compliant Public Outreach:</strong> 
                Direct chat links utilize Meta's official universal direct messaging protocol (<code class="bg-indigo-100 dark:bg-indigo-950 px-1 py-0.5 rounded font-mono">ig.me/m/username</code>) and WhatsApp Click-to-Chat. Respect platform messaging limits and local commercial communications policies.
            </div>
        </div>
    </div>

    <!-- Quick Public Account & Keyword Search Bar -->
    <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <form method="GET" action="{{ route('discovery.index') }}" class="space-y-4">
            <!-- Dedicated Handle / Keyword Input Hero -->
            <div class="relative">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                    <span>Look Up Public Account or Business</span>
                    <span class="text-[11px] text-rose-500 font-normal">Supports @handles, Instagram links, and business keywords</span>
                </label>
                <div class="relative flex items-center">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-rose-500 font-bold">
                        @
                    </div>
                    <input type="text" 
                           name="query" 
                           value="{{ $params['query'] ?? '' }}" 
                           placeholder="Enter Instagram username (e.g. @zomato, @nike, @starbucksindia) or keyword..." 
                           class="w-full pl-9 pr-28 py-3 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-rose-500 text-slate-800 dark:text-slate-200 font-medium">
                    <button type="submit" class="absolute right-2 px-4 py-2 rounded-lg bg-gradient-to-r from-rose-500 to-purple-600 hover:from-rose-600 hover:to-purple-700 text-white text-xs font-semibold shadow-md shadow-rose-500/20 transition-all flex items-center space-x-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span>Search & Chat</span>
                    </button>
                </div>
            </div>

            <!-- Advanced Category & Location Filters -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-2">
                <!-- Category -->
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Business Category</label>
                    <select name="category" class="w-full px-3 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-rose-500 text-slate-800 dark:text-slate-200">
                        <option value="">All Business Categories</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ ($params['category'] ?? '') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- City / Location -->
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Target City or Region</label>
                    <div class="relative">
                        <input type="text" 
                               name="city" 
                               value="{{ $params['city'] ?? '' }}" 
                               placeholder="e.g. Mumbai, Delhi, Bengaluru, Dubai..." 
                               class="w-full pl-9 pr-3 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-rose-500 text-slate-800 dark:text-slate-200">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            📍
                        </div>
                    </div>
                </div>
            </div>

            <!-- Checkbox Filters & Reset -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-3 border-t border-slate-100 dark:border-slate-800">
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
                    <a href="{{ route('discovery.index') }}" class="px-3 py-1.5 text-xs font-semibold text-slate-500 hover:text-slate-700 dark:hover:text-slate-300">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Discovered Results Section Header -->
    <div class="flex items-center justify-between">
        <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center space-x-2">
            <span>Discovered Leads & Public Profiles</span>
            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                {{ count($discoveredLeads) }} Results
            </span>
        </h2>
        @if(!empty($params['query']))
        <span class="text-xs text-slate-400">
            Showing results for <strong class="text-rose-500">"{{ $params['query'] }}"</strong>
        </span>
        @endif
    </div>

    <!-- Discovered Results Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($discoveredLeads as $lead)
        <div class="bg-white dark:bg-slate-900 rounded-2xl border {{ !empty($lead['is_direct_search']) ? 'border-rose-400 dark:border-rose-600 ring-2 ring-rose-500/20' : 'border-slate-200/80 dark:border-slate-800' }} p-5 shadow-sm hover:shadow-md transition-all flex flex-col justify-between relative">
            
            @if(!empty($lead['is_direct_search']))
            <div class="absolute -top-3 left-4 px-2.5 py-0.5 rounded-full bg-gradient-to-r from-rose-500 to-purple-600 text-[10px] font-bold text-white shadow-sm uppercase tracking-wider">
                Direct Account Lookup
            </div>
            @endif

            <div>
                <!-- Card Header -->
                <div class="flex items-start space-x-3 mb-3">
                    <img src="{{ $lead['profile_picture_url'] ?? 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=200' }}" 
                         alt="{{ $lead['business_name'] }}" 
                         class="w-12 h-12 rounded-xl object-cover shrink-0 border border-slate-200 dark:border-slate-800 shadow-sm">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white truncate">
                            {{ $lead['business_name'] }}
                        </h3>
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
                <p class="text-xs text-slate-600 dark:text-slate-400 line-clamp-2 mb-3 leading-relaxed">
                    {{ $lead['business_description'] ?? 'Public profile with active digital presence.' }}
                </p>

                <!-- Direct Outreach Action Strip -->
                <div class="grid grid-cols-2 gap-2 mb-3">
                    <!-- Direct Instagram Chat Link -->
                    <!-- In-Platform Chat trigger button -->
                    @if(!empty($lead['instagram_username']))
                    <button type="button" 
                            @click="$el.closest('.p-5').querySelector('form').submit()" 
                            class="inline-flex items-center justify-center space-x-1.5 py-2 px-3 rounded-xl bg-gradient-to-r from-purple-600 via-rose-500 to-amber-500 hover:opacity-95 text-white text-xs font-bold shadow-sm shadow-rose-500/20 transition-all">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                        <span>Chat in Platform</span>
                    </button>
                    @endif

                    <!-- Quick Outreach Template Button -->
                    <button type="button" 
                            @click="openOutreach({{ json_encode($lead) }})" 
                            class="inline-flex items-center justify-center space-x-1 py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-semibold transition-colors">
                        <span>💬 Quick Message</span>
                    </button>
                </div>

                <!-- Contact Points Summary -->
                <div class="space-y-1 text-xs text-slate-600 dark:text-slate-300 bg-slate-50 dark:bg-slate-800/40 p-2.5 rounded-xl mb-3 border border-slate-100 dark:border-slate-800">
                    @if(!empty($lead['public_phone']))
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Phone:</span>
                        <div class="flex items-center space-x-2">
                            <span class="font-medium">{{ $lead['public_phone'] }}</span>
                            @if(!empty($lead['whatsapp_url']))
                            <a href="{{ $lead['whatsapp_url'] }}" target="_blank" rel="noopener noreferrer" class="text-emerald-500 font-bold hover:underline" title="Chat on WhatsApp">WA</a>
                            @endif
                        </div>
                    </div>
                    @endif
                    @if(!empty($lead['public_email']))
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Email:</span>
                        <a href="mailto:{{ $lead['public_email'] }}" class="font-medium text-indigo-500 hover:underline truncate max-w-[180px]">{{ $lead['public_email'] }}</a>
                    </div>
                    @endif
                    @if(!empty($lead['official_website']))
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Website:</span>
                        <a href="{{ $lead['official_website'] }}" target="_blank" rel="noopener noreferrer" class="text-indigo-500 hover:underline truncate max-w-[180px]">
                            {{ parse_url($lead['official_website'], PHP_URL_HOST) ?? 'Visit Site' }}
                        </a>
                    </div>
                    @endif
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
                        <option value="">Save to: My Leads</option>
                        @foreach($lists as $lst)
                        <option value="{{ $lst->id }}">{{ $lst->name }}</option>
                        @endforeach
                    </select>

                    <button type="submit" class="px-3.5 py-2 rounded-xl bg-gradient-to-r from-rose-500 to-purple-600 hover:opacity-95 text-white text-xs font-semibold shrink-0 transition-all flex items-center space-x-1.5 shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <span>Save & Chat In-Platform →</span>
                    </button>
                </div>
            </form>
        </div>
        @empty
        <div class="col-span-full bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-12 text-center">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-rose-50 dark:bg-rose-950/40 text-rose-500 flex items-center justify-center text-3xl mb-4">
                @
            </div>
            <h3 class="text-base font-bold text-slate-800 dark:text-slate-200">Search Any Public Instagram Handle</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-md mx-auto">
                Type an Instagram username (e.g. <strong class="text-rose-500">@zomato</strong>, <strong class="text-rose-500">@nike</strong>, or any business handle) above to inspect their profile and start outreach conversations directly!
            </p>
        </div>
        @endforelse
    </div>

    <!-- Quick Outreach & Chat Composer Modal -->
    <div x-show="outreachModal" 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4" 
         style="display: none;">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-lg w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4" @click.away="outreachModal = false">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center space-x-2">
                        <span>Direct Outreach Composer</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Targeting: <strong class="text-rose-500" x-text="'@' + selectedLead.instagram_username"></strong> (<span x-text="selectedLead.business_name"></span>)
                    </p>
                </div>
                <button type="button" @click="outreachModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg">✕</button>
            </div>

            <!-- Outreach Template Selector -->
            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Choose Message Template</label>
                <select x-model="selectedTemplate" @change="updateMessage()" class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200">
                    <option value="pitch">🎯 Instagram Collaboration Pitch (High Conversion)</option>
                    <option value="inquiry">💼 Business & Service Inquiry</option>
                    <option value="marketing">🚀 Marketing & Growth Audit Offer</option>
                    <option value="question">❓ Quick Question / Introduction</option>
                </select>
            </div>

            <!-- Message Textarea -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Customized Message</label>
                    <span x-show="copied" class="text-xs text-emerald-500 font-bold transition-opacity">✓ Copied to clipboard!</span>
                </div>
                <textarea x-model="messageText" rows="4" class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-rose-500 font-sans leading-relaxed"></textarea>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-2 pt-2">
                <button type="button" 
                        @click="copyMessage()" 
                        class="w-full sm:w-auto px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold transition-colors flex items-center justify-center space-x-1">
                    <span>📋 Copy Text</span>
                </button>

                <div class="flex items-center space-x-2 w-full sm:w-auto justify-end">
                    <template x-if="selectedLead.public_phone">
                        <button type="button" 
                                @click="openWhatsApp()" 
                                class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm transition-colors flex items-center space-x-1">
                            <span>💬 Send WhatsApp</span>
                        </button>
                    </template>

                    <button type="button" 
                            @click="copyAndOpenInstagram()" 
                            class="px-4 py-2 rounded-xl bg-gradient-to-r from-purple-600 via-rose-500 to-amber-500 hover:opacity-95 text-white text-xs font-bold shadow-md shadow-rose-500/20 transition-all flex items-center space-x-1.5">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                        <span>Copy & Chat on Instagram</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
