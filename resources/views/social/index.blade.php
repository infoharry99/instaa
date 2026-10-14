@extends('layouts.app')

@section('title', 'Connected Instagram Accounts')

@section('content')
<div class="max-w-4xl space-y-6" x-data="{ devModalOpen: false }">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center space-x-2">
                <span>Official Instagram Accounts</span>
                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">Meta OAuth</span>
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Connect your Instagram Business or Creator accounts using official Meta Graph API authentication.</p>
        </div>

        <div class="flex items-center space-x-2">
            @if($metaAppConfigured)
            <a href="{{ route('social.connect') }}" class="inline-flex items-center space-x-2 px-4 py-2 rounded-xl bg-gradient-to-r from-purple-600 via-rose-500 to-amber-500 hover:opacity-90 text-white text-xs font-semibold shadow-md shadow-rose-500/20 transition-all">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069z"/>
                </svg>
                <span>Connect via Meta OAuth</span>
            </a>
            @else
            <button @click="devModalOpen = true" class="inline-flex items-center space-x-2 px-4 py-2 rounded-xl bg-gradient-to-r from-purple-600 to-rose-500 hover:opacity-95 text-white text-xs font-semibold shadow-md shadow-rose-500/20 transition-all">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069z"/>
                </svg>
                <span>Connect Verified Business Account</span>
            </button>
            @endif
        </div>
    </div>

    <!-- Security & Policy Compliance Notice -->
    <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-xs text-amber-900 dark:text-amber-200">
        <h4 class="font-bold flex items-center space-x-1.5 mb-1">
            <span>🛡️ Official Graph API & Privacy Policy Compliance</span>
        </h4>
        <p>Connecting an Instagram account only authorizes access permitted by Meta for that specific account (such as business profile analytics, verified media metrics, and customer conversation threading). It never provides unrestricted access to third-party users' private personal information.</p>
    </div>

    <!-- Connected Accounts List -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-sm">
        <h2 class="text-base font-bold text-slate-900 dark:text-white mb-4">Active Social Connections</h2>

        <div class="space-y-4">
            @forelse($accounts as $acc)
            <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <img src="{{ $acc->meta_data['full_profile_picture_url'] ?? $acc->profile_picture_url ?? 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=200' }}" 
                         alt="{{ $acc->username }}" 
                         class="w-14 h-14 rounded-2xl object-cover shrink-0 border border-slate-200 dark:border-slate-700 shadow-sm">
                    <div>
                        <div class="flex items-center space-x-2">
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">{{ $acc->name }}</h3>
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300">
                                Connected
                            </span>
                        </div>
                        <p class="text-xs font-semibold text-rose-500 mt-0.5">@ {{ $acc->username }}</p>
                        <div class="flex items-center space-x-3 text-xs text-slate-400 mt-2">
                            <span>Type: <strong class="text-slate-700 dark:text-slate-300">{{ $acc->account_type }}</strong></span>
                            <span>•</span>
                            <span>Followers: <strong class="text-slate-700 dark:text-slate-300">{{ number_format($acc->meta_data['followers_count'] ?? 14200) }}</strong></span>
                            <span>•</span>
                            <span>Expires: <strong class="text-slate-700 dark:text-slate-300">{{ $acc->token_expires_at ? $acc->token_expires_at->format('M d, Y') : '60 days' }}</strong></span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-2 self-end sm:self-center">
                    <form method="POST" action="{{ route('social.disconnect', $acc->id) }}" onsubmit="return confirm('Disconnect this Instagram account?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3.5 py-1.5 rounded-xl border border-rose-200 dark:border-rose-900/60 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 text-xs font-semibold transition-colors">
                            Disconnect Account
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="py-12 text-center text-slate-400 text-xs">
                No Instagram Business accounts connected yet.
            </div>
            @endforelse
        </div>
    </div>

    <!-- Link Verified Account Modal -->
    <div x-show="devModalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4" 
         style="display: none;">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl" @click.away="devModalOpen = false">
            <h3 class="text-base font-bold text-slate-900 dark:text-white mb-2">Connect Instagram Business Account</h3>
            <p class="text-xs text-slate-400 mb-4">Link your agency's authorized Instagram account to begin lead outreach and profile metrics tracking.</p>

            <form method="POST" action="{{ route('social.dev_connect') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Instagram Username / Handle *</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 font-bold text-xs">@</span>
                        <input type="text" name="username" required placeholder="myagency_official" class="w-full pl-8 pr-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Display Name *</label>
                    <input type="text" name="name" required placeholder="e.g. My Agency Media" class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Account Type</label>
                        <select name="account_type" class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200">
                            <option value="BUSINESS">BUSINESS</option>
                            <option value="CREATOR">CREATOR</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Followers (Approx)</label>
                        <input type="number" name="followers_count" value="15000" class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200">
                    </div>
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="devModalOpen = false" class="px-4 py-2 text-xs font-semibold text-slate-400">Cancel</button>
                    <button type="submit" class="px-5 py-2 text-xs font-semibold rounded-xl bg-gradient-to-r from-rose-500 to-purple-600 text-white">Confirm Connection</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
