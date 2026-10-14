@extends('layouts.app')

@section('title', $list->name)

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-400 mb-1">
                <a href="{{ route('lists.index') }}" class="hover:text-rose-500 font-medium">Custom Lists</a>
                <span>/</span>
                <span class="text-slate-700 dark:text-slate-200 font-semibold">{{ $list->name }}</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center space-x-2">
                <span>{{ $list->name }}</span>
                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                    {{ $leads->total() }} Leads
                </span>
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ $list->description ?? 'Segmented campaign list' }}</p>
        </div>

        <div class="flex items-center space-x-2">
            <a href="{{ route('discovery.index') }}" class="px-4 py-2 text-xs font-semibold rounded-xl bg-gradient-to-r from-rose-500 to-purple-600 text-white shadow-md shadow-rose-500/20">
                + Add More from Discovery
            </a>
        </div>
    </div>

    <!-- Leads in List Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
                        <th class="py-3 px-4">Business & Instagram</th>
                        <th class="py-3 px-4">Category & Location</th>
                        <th class="py-3 px-4">Contact Person / Phone</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 text-xs">
                    @forelse($leads as $lead)
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40">
                        <td class="py-3.5 px-4">
                            <a href="{{ route('leads.show', $lead->id) }}" class="font-bold text-slate-900 dark:text-white hover:text-rose-500">
                                {{ $lead->business_name }}
                            </a>
                            <p class="text-rose-500 text-[11px]">@ {{ $lead->instagram_username ?? 'No handle' }}</p>
                        </td>
                        <td class="py-3.5 px-4">
                            <p class="text-slate-800 dark:text-slate-200">{{ $lead->category }}</p>
                            <p class="text-slate-400 text-[11px]">{{ $lead->city ?? 'Not available' }}</p>
                        </td>
                        <td class="py-3.5 px-4">
                            <p class="font-medium text-slate-800 dark:text-slate-200">{{ $lead->contact_person ?? 'Not available' }}</p>
                            <p class="text-slate-400 text-[11px]">{{ $lead->public_phone ?? $lead->public_email ?? 'No contact point' }}</p>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                {{ str_replace('_', ' ', $lead->status) }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            <form method="POST" action="{{ route('lists.remove-lead', [$list->id, $lead->id]) }}" onsubmit="return confirm('Remove lead from this list?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-slate-400 hover:text-rose-500">Remove</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-slate-400">
                            No leads have been added to this list yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($leads->hasPages())
        <div class="p-4 border-t border-slate-200 dark:border-slate-800">
            {{ $leads->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
