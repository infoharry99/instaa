@extends('layouts.app')

@section('title', 'Compliance & Security Audit Trail')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-400 mb-1">
                <a href="{{ route('admin.index') }}" class="hover:text-rose-500 font-medium">Administration</a>
                <span>/</span>
                <span class="text-slate-700 dark:text-slate-200 font-semibold">Audit Logs</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center space-x-2">
                <span>Compliance & Audit Trail</span>
                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                    {{ $logs->total() }} Events
                </span>
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Immutable ledger of lead exports, team assignments, logins, and API sync events.</p>
        </div>
    </div>

    <!-- Audit Logs Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/80 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">
                        <th class="py-3 px-4">Event Action</th>
                        <th class="py-3 px-4">User</th>
                        <th class="py-3 px-4">Description</th>
                        <th class="py-3 px-4">Target Entity</th>
                        <th class="py-3 px-4">IP Address</th>
                        <th class="py-3 px-4 text-right">Timestamp</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    @forelse($logs as $log)
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40">
                        <td class="py-3.5 px-4 font-mono font-semibold text-rose-500 text-[11px]">
                            {{ $log->action }}
                        </td>
                        <td class="py-3.5 px-4 font-medium text-slate-800 dark:text-slate-200">
                            {{ $log->user?->name ?? 'System' }}
                        </td>
                        <td class="py-3.5 px-4 text-slate-600 dark:text-slate-300">
                            {{ $log->description }}
                        </td>
                        <td class="py-3.5 px-4 text-slate-400">
                            {{ $log->entity_type ? "{$log->entity_type} #{$log->entity_id}" : '—' }}
                        </td>
                        <td class="py-3.5 px-4 font-mono text-slate-400 text-[11px]">
                            {{ $log->ip_address ?? '127.0.0.1' }}
                        </td>
                        <td class="py-3.5 px-4 text-right text-slate-400">
                            {{ $log->created_at->format('Y-m-d H:i:s') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-400">
                            No audit trail entries recorded yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="p-4 border-t border-slate-200 dark:border-slate-800">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
