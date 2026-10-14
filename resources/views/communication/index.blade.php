@extends('layouts.app')

@section('title', 'Communication Center')

@section('content')
<div class="space-y-6" x-data="{ templateModalOpen: false }">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center space-x-2">
                <span>Communication Center</span>
                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">Multi-Channel</span>
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Manage outreach templates, view cross-channel history, and track interaction velocity.</p>
        </div>

        <button @click="templateModalOpen = true" class="inline-flex items-center space-x-1.5 px-4 py-2 text-xs font-semibold rounded-xl bg-gradient-to-r from-rose-500 via-purple-600 to-indigo-600 text-white shadow-md shadow-rose-500/20 hover:opacity-95 transition-opacity">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>New Outreach Template</span>
        </button>
    </div>

    <!-- Outreach Templates Carousel / Grid -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-5 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">Reusable Outreach Templates</h2>
            <span class="text-xs text-slate-400">{{ $templates->count() }} Saved</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @forelse($templates as $tmpl)
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/70 dark:border-slate-800 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-bold text-xs text-slate-900 dark:text-white truncate">{{ $tmpl->name }}</span>
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full uppercase
                              {{ $tmpl->channel === 'email' ? 'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300' : '' }}
                              {{ $tmpl->channel === 'whatsapp' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : '' }}
                              {{ $tmpl->channel === 'instagram' ? 'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300' : '' }}">
                            {{ $tmpl->channel }}
                        </span>
                    </div>
                    @if($tmpl->subject)
                    <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 mb-1 truncate">Sub: {{ $tmpl->subject }}</p>
                    @endif
                    <p class="text-xs text-slate-600 dark:text-slate-400 whitespace-pre-line line-clamp-4">{{ $tmpl->body }}</p>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-200/60 dark:border-slate-700/60 flex items-center justify-between">
                    <span class="text-[10px] text-slate-400">
                        Variables: <code>{{ implode(', ', $tmpl->variables ?? ['business_name']) }}</code>
                    </span>
                    <form method="POST" action="{{ route('communication.templates.destroy', $tmpl->id) }}" onsubmit="return confirm('Delete template?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs text-rose-500 hover:text-rose-600">Delete</button>
                    </form>
                </div>
            </div>
            @empty
            <div class="col-span-full py-8 text-center text-slate-400 text-xs">
                No templates created yet. Create a template to standardize your team's outreach!
            </div>
            @endforelse
        </div>
    </div>

    <!-- Communication History Feed with Channel Filter -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-5 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
            <div>
                <h2 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">Outreach Activity Log</h2>
                <p class="text-xs text-slate-400">Unified timeline across calls, emails, WhatsApp and Instagram</p>
            </div>

            <!-- Channel Filter Pills -->
            <div class="flex items-center space-x-1.5 text-xs">
                <a href="{{ route('communication.index') }}" 
                   class="px-3 py-1.5 rounded-lg font-semibold {{ empty($channel) ? 'bg-slate-900 text-white dark:bg-slate-800' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    All
                </a>
                <a href="{{ route('communication.index', ['channel' => 'instagram_manual']) }}" 
                   class="px-3 py-1.5 rounded-lg font-semibold {{ $channel === 'instagram_manual' ? 'bg-rose-500 text-white' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    Instagram
                </a>
                <a href="{{ route('communication.index', ['channel' => 'whatsapp']) }}" 
                   class="px-3 py-1.5 rounded-lg font-semibold {{ $channel === 'whatsapp' ? 'bg-emerald-500 text-white' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    WhatsApp
                </a>
                <a href="{{ route('communication.index', ['channel' => 'email']) }}" 
                   class="px-3 py-1.5 rounded-lg font-semibold {{ $channel === 'email' ? 'bg-blue-500 text-white' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    Email
                </a>
                <a href="{{ route('communication.index', ['channel' => 'call']) }}" 
                   class="px-3 py-1.5 rounded-lg font-semibold {{ $channel === 'call' ? 'bg-amber-500 text-white' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    Phone Call
                </a>
            </div>
        </div>

        <div class="divide-y divide-slate-100 dark:divide-slate-800/80">
            @forelse($logs as $log)
            <div class="py-4 flex items-start space-x-4">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 text-sm font-bold shadow-sm
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

                <div class="flex-1 min-w-0">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                        <div>
                            <span class="font-bold text-sm text-slate-900 dark:text-white">{{ $log->lead->business_name }}</span>
                            <span class="text-xs text-slate-400 ml-2">({{ $log->direction }} {{ str_replace('_', ' ', $log->channel) }})</span>
                        </div>
                        <span class="text-xs text-slate-400">{{ $log->logged_at->format('M d, Y h:i A') }}</span>
                    </div>

                    <p class="text-xs text-slate-600 dark:text-slate-300 mt-1 whitespace-pre-line bg-slate-50 dark:bg-slate-800/40 p-3 rounded-xl border border-slate-100 dark:border-slate-800">
                        {{ $log->notes }}
                    </p>

                    <div class="flex items-center justify-between mt-2 text-[11px] text-slate-400">
                        <span>Agent: <strong class="text-slate-700 dark:text-slate-300">{{ $log->user->name }}</strong></span>
                        <a href="{{ route('leads.show', $log->lead->id) }}" class="text-rose-500 hover:text-rose-600 font-semibold">Open Lead Profile →</a>
                    </div>
                </div>
            </div>
            @empty
            <div class="py-12 text-center text-slate-400 text-xs">
                No communication history records matching this filter.
            </div>
            @endforelse
        </div>

        @if($logs->hasPages())
        <div class="pt-4 border-t border-slate-200 dark:border-slate-800">
            {{ $logs->links() }}
        </div>
        @endif
    </div>

    <!-- Create Template Modal -->
    <div x-show="templateModalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4" 
         style="display: none;">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-lg w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl" @click.away="templateModalOpen = false">
            <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4">Create Outreach Template</h3>
            <form method="POST" action="{{ route('communication.templates.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Template Name</label>
                    <input type="text" name="name" required placeholder="e.g. Salon Collab Outreach" class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Target Channel</label>
                        <select name="channel" required class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200">
                            <option value="instagram">Instagram Direct</option>
                            <option value="whatsapp">WhatsApp</option>
                            <option value="email">Email</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Subject (For Email)</label>
                        <input type="text" name="subject" placeholder="Partnership for @business" class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200">
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-semibold text-slate-500">Template Message Body</label>
                        <span class="text-[10px] text-slate-400">Use @{{business_name}}, @{{contact_person}}, @{{city}}</span>
                    </div>
                    <textarea name="body" rows="4" required placeholder="Hi @{{contact_person}}, we loved @{{business_name}}'s recent work in @{{city}}..." class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200"></textarea>
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="templateModalOpen = false" class="px-4 py-2 text-xs font-semibold text-slate-400">Cancel</button>
                    <button type="submit" class="px-5 py-2 text-xs font-semibold rounded-xl bg-gradient-to-r from-rose-500 to-purple-600 text-white">Save Template</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
