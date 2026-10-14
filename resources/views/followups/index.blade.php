@extends('layouts.app')

@section('title', 'Follow-up Reminders')

@section('content')
<div class="space-y-6" x-data="{ createFollowUpModal: false }">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center space-x-2">
                <span>Follow-up Reminders</span>
                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">Tasks</span>
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Track scheduled callbacks, proposal follow-ups, and prospective client check-ins.</p>
        </div>

        <button @click="createFollowUpModal = true" class="inline-flex items-center space-x-1.5 px-4 py-2 text-xs font-semibold rounded-xl bg-gradient-to-r from-rose-500 via-purple-600 to-indigo-600 text-white shadow-md shadow-rose-500/20 hover:opacity-95 transition-opacity">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Schedule Follow-up</span>
        </button>
    </div>

    <!-- Filter Tabs -->
    <div class="flex items-center space-x-2 text-xs font-semibold">
        <a href="{{ route('followups.index', ['status' => 'pending']) }}" class="px-3.5 py-2 rounded-xl {{ $status === 'pending' ? 'bg-slate-900 text-white dark:bg-slate-800' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            Pending Tasks
        </a>
        <a href="{{ route('followups.index', ['status' => 'completed']) }}" class="px-3.5 py-2 rounded-xl {{ $status === 'completed' ? 'bg-slate-900 text-white dark:bg-slate-800' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            Completed
        </a>
        <a href="{{ route('followups.index', ['status' => 'all']) }}" class="px-3.5 py-2 rounded-xl {{ $status === 'all' ? 'bg-slate-900 text-white dark:bg-slate-800' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            All
        </a>
    </div>

    <!-- Follow Ups List -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-sm">
        <div class="divide-y divide-slate-100 dark:divide-slate-800/80">
            @forelse($followUps as $fu)
            @php
                $isOverdue = $fu->status === 'pending' && $fu->due_at->isPast();
            @endphp
            <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-start space-x-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 font-bold text-sm
                         {{ $fu->status === 'completed' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : ($isOverdue ? 'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-300' : 'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300') }}">
                        @if($fu->status === 'completed') ✓
                        @elseif($isOverdue) ⚠️
                        @else ⏰
                        @endif
                    </div>
                    <div>
                        <div class="flex items-center space-x-2">
                            <h3 class="font-bold text-sm text-slate-900 dark:text-white">{{ $fu->title }}</h3>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full uppercase {{ $fu->priority === 'high' ? 'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-300' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300' }}">
                                {{ $fu->priority }}
                            </span>
                            @if($isOverdue)
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-red-500 text-white">Overdue</span>
                            @endif
                        </div>

                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Lead: <a href="{{ route('leads.show', $fu->lead->id) }}" class="font-semibold text-rose-500 hover:underline">{{ $fu->lead->business_name }}</a>
                            • Assigned: {{ $fu->user->name }}
                        </p>

                        @if($fu->notes)
                        <p class="text-xs text-slate-600 dark:text-slate-300 mt-2 bg-slate-50 dark:bg-slate-800/40 p-2.5 rounded-xl border border-slate-100 dark:border-slate-800">
                            {{ $fu->notes }}
                        </p>
                        @endif
                    </div>
                </div>

                <div class="flex items-center space-x-3 self-end sm:self-center">
                    <div class="text-right text-xs">
                        <span class="block text-slate-400 text-[10px] uppercase">Due Date</span>
                        <span class="font-semibold text-slate-700 dark:text-slate-300 {{ $isOverdue ? 'text-red-500' : '' }}">
                            {{ $fu->due_at->format('M d, Y h:i A') }}
                        </span>
                    </div>

                    @if($fu->status === 'pending')
                    <form method="POST" action="{{ route('followups.complete', $fu->id) }}">
                        @csrf
                        <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 font-semibold text-xs border border-emerald-200 dark:border-emerald-800 transition-colors">
                            Complete
                        </button>
                    </form>
                    @endif

                    <form method="POST" action="{{ route('followups.destroy', $fu->id) }}" onsubmit="return confirm('Delete follow-up?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs text-slate-400 hover:text-rose-500 p-1.5">
                            ✕
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="py-12 text-center text-slate-400 text-xs">
                No follow-up reminders in this category.
            </div>
            @endforelse
        </div>

        @if($followUps->hasPages())
        <div class="pt-4 border-t border-slate-200 dark:border-slate-800">
            {{ $followUps->links() }}
        </div>
        @endif
    </div>

    <!-- Create Follow Up Modal -->
    <div x-show="createFollowUpModal" 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4" 
         style="display: none;">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl" @click.away="createFollowUpModal = false">
            <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4">Schedule Follow-up Task</h3>
            <form method="POST" action="{{ route('followups.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Select Lead *</label>
                    <select name="business_lead_id" required class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200">
                        @foreach($leads as $ld)
                        <option value="{{ $ld->id }}">{{ $ld->business_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Task Title *</label>
                    <input type="text" name="title" required placeholder="e.g. Call to finalize Instagram campaign" class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Due Date & Time *</label>
                        <input type="datetime-local" name="due_at" required class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Priority</label>
                        <select name="priority" class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200">
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="low">Low</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Task Notes</label>
                    <textarea name="notes" rows="2" placeholder="Specific talking points or proposal notes..." class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200"></textarea>
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="createFollowUpModal = false" class="px-4 py-2 text-xs font-semibold text-slate-400">Cancel</button>
                    <button type="submit" class="px-5 py-2 text-xs font-semibold rounded-xl bg-gradient-to-r from-rose-500 to-purple-600 text-white">Save Follow-up</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
