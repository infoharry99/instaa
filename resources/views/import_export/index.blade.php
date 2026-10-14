@extends('layouts.app')

@section('title', 'Import & Export Leads')

@section('content')
<div class="max-w-4xl space-y-6">

    <!-- Top Header -->
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center space-x-2">
            <span>Import & Export Leads</span>
            <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">CSV & Sheets</span>
        </h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Bulk ingest authorized contact records or export your segmented leads for external campaigns.</p>
    </div>

    <!-- 2 Column: Upload Card & Export Card -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Import Card -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 flex items-center justify-center font-bold">
                        📥
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-white">Import Leads (CSV)</h2>
                        <p class="text-xs text-slate-400">Automatic duplicate detection & column mapping</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('import-export.import') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-2">Select CSV File (Max 10MB)</label>
                        <input type="file" 
                               name="csv_file" 
                               accept=".csv,text/csv" 
                               required 
                               class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 dark:file:bg-slate-800 dark:file:text-slate-300">
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800 text-[11px] text-slate-500 space-y-1">
                        <p class="font-semibold text-slate-700 dark:text-slate-300">Supported Headers:</p>
                        <p><code>business_name</code>, <code>instagram_username</code>, <code>category</code>, <code>city</code>, <code>official_website</code>, <code>public_email</code>, <code>public_phone</code>, <code>contact_person</code></p>
                    </div>

                    <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-rose-500 to-purple-600 hover:from-rose-600 hover:to-purple-700 text-white font-semibold text-xs shadow-md shadow-rose-500/20 transition-all">
                        Upload & Ingest Leads
                    </button>
                </form>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 text-center">
                <a href="{{ route('import-export.sample') }}" class="text-xs font-semibold text-rose-500 hover:text-rose-600 inline-flex items-center space-x-1">
                    <span>📄 Download Sample CSV Template</span>
                </a>
            </div>
        </div>

        <!-- Export Card -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold">
                        📤
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-white">Export Filtered Leads</h2>
                        <p class="text-xs text-slate-400">Download formatted CRM records</p>
                    </div>
                </div>

                <form method="GET" action="{{ route('import-export.export') }}" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Filter by Status</label>
                        <select name="status" class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200">
                            <option value="">All Statuses (Full Export)</option>
                            <option value="new">New</option>
                            <option value="contacted">Contacted</option>
                            <option value="interested">Interested</option>
                            <option value="follow_up">Follow-up</option>
                            <option value="converted">Converted</option>
                        </select>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800 text-[11px] text-slate-500">
                        Exports complete lead profile, Instagram handle, verified public emails, phones, and assignment dates.
                    </div>

                    <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-slate-800 dark:hover:bg-slate-700 text-white font-semibold text-xs transition-colors">
                        Download CSV Export
                    </button>
                </form>
            </div>
            
            <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 text-center text-xs text-slate-400">
                Exports reflect real-time permissions & suppression lists.
            </div>
        </div>
    </div>

    <!-- Import History Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-sm">
        <h2 class="text-base font-bold text-slate-900 dark:text-white mb-4">Import Jobs & Ingestion History</h2>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-800 text-slate-400 font-semibold uppercase text-[10px]">
                        <th class="py-3 px-3">File Name</th>
                        <th class="py-3 px-3">Status</th>
                        <th class="py-3 px-3">Total Rows</th>
                        <th class="py-3 px-3">Successful</th>
                        <th class="py-3 px-3">Skipped / Duplicates</th>
                        <th class="py-3 px-3">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($jobs as $job)
                    <tr>
                        <td class="py-3 px-3 font-semibold text-slate-800 dark:text-slate-200">{{ $job->file_name }}</td>
                        <td class="py-3 px-3">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase
                                  {{ $job->status === 'completed' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : '' }}
                                  {{ $job->status === 'failed' ? 'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300' : '' }}
                                  {{ $job->status === 'processing' ? 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300 animate-pulse' : '' }}">
                                {{ $job->status }}
                            </span>
                        </td>
                        <td class="py-3 px-3">{{ $job->total_rows }}</td>
                        <td class="py-3 px-3 text-emerald-500 font-semibold">{{ $job->successful_rows }}</td>
                        <td class="py-3 px-3 text-slate-400">{{ $job->failed_rows }}</td>
                        <td class="py-3 px-3 text-slate-400">{{ $job->created_at->format('M d, Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-400">
                            No lead import jobs recorded yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($jobs->hasPages())
        <div class="pt-4 border-t border-slate-200 dark:border-slate-800">
            {{ $jobs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
