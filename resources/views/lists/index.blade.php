@extends('layouts.app')

@section('title', 'Custom Lead Lists')

@section('content')
<div class="space-y-6" x-data="{ createListModal: false }">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center space-x-2">
                <span>Custom Lead Lists</span>
                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">Campaigns</span>
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Organize discovered business leads into focused campaigns and target lists.</p>
        </div>

        <button @click="createListModal = true" class="inline-flex items-center space-x-1.5 px-4 py-2 text-xs font-semibold rounded-xl bg-gradient-to-r from-rose-500 via-purple-600 to-indigo-600 text-white shadow-md shadow-rose-500/20 hover:opacity-95 transition-opacity">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Create New List</span>
        </button>
    </div>

    <!-- Lists Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        @forelse($lists as $list)
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-5 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-white text-sm" style="background-color: {{ $list->color }}">
                        📂
                    </div>
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                        {{ $list->leads_count }} Leads
                    </span>
                </div>

                <a href="{{ route('lists.show', $list->id) }}" class="text-base font-bold text-slate-900 dark:text-white hover:text-rose-500 block truncate">
                    {{ $list->name }}
                </a>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-2">
                    {{ $list->description ?? 'Custom target campaign list.' }}
                </p>
            </div>

            <div class="mt-5 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                <a href="{{ route('lists.show', $list->id) }}" class="font-semibold text-rose-500 hover:text-rose-600">
                    Open Leads →
                </a>
                <form method="POST" action="{{ route('lists.destroy', $list->id) }}" onsubmit="return confirm('Delete this list?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-slate-400 hover:text-rose-500">Delete</button>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-full py-12 text-center text-slate-400 text-xs bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800">
            No custom lists created yet. Create your first list to segment your business leads!
        </div>
        @endforelse
    </div>

    <!-- Create List Modal -->
    <div x-show="createListModal" 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4" 
         style="display: none;">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl" @click.away="createListModal = false">
            <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4">Create New Lead List</h3>
            <form method="POST" action="{{ route('lists.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">List Name *</label>
                    <input type="text" name="name" required placeholder="e.g. South Mumbai Cafes Q4" class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Description (Optional)</label>
                    <textarea name="description" rows="2" placeholder="List objectives, criteria..." class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Color Theme</label>
                    <select name="color" class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200">
                        <option value="#EF4444">Rose Red</option>
                        <option value="#8B5CF6">Purple / Violet</option>
                        <option value="#3B82F6">Blue</option>
                        <option value="#10B981">Emerald Green</option>
                        <option value="#F59E0B">Amber Gold</option>
                    </select>
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="createListModal = false" class="px-4 py-2 text-xs font-semibold text-slate-400">Cancel</button>
                    <button type="submit" class="px-5 py-2 text-xs font-semibold rounded-xl bg-gradient-to-r from-rose-500 to-purple-600 text-white">Create List</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
