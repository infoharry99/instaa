@extends('layouts.app')

@section('title', 'Profile & Settings')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Profile & Workspace Settings</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Manage your credentials, team role, and company registration information.</p>
    </div>

    <!-- Personal Profile Card -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-sm">
        <h2 class="text-base font-bold text-slate-900 dark:text-white mb-4">Personal Details</h2>

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Full Name *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Email Address</label>
                    <input type="email" value="{{ $user->email }}" disabled class="w-full px-3 py-2 text-xs rounded-xl bg-slate-100 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-500 cursor-not-allowed">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Assigned Role</label>
                    <input type="text" value="{{ ucfirst(str_replace('_', ' ', $user->role)) }}" disabled class="w-full px-3 py-2 text-xs rounded-xl bg-slate-100 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-500 capitalize cursor-not-allowed">
                </div>
            </div>

            <!-- Password Change -->
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 space-y-3">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Change Password</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">New Password</label>
                        <input type="password" name="password" placeholder="Leave blank to keep current" class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Confirm New Password</label>
                        <input type="password" name="password_confirmation" placeholder="Confirm password" class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200">
                    </div>
                </div>
            </div>

            <!-- Company Profile (For Admin) -->
            @if($user->isAdmin())
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 space-y-3">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Company & Business Information</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Company Name</label>
                        <input type="text" name="company_name" value="{{ old('company_name', $company->name) }}" required class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Official Domain</label>
                        <input type="text" name="domain" value="{{ old('domain', $company->domain) }}" placeholder="e.g. agency.com" class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">City / Hub</label>
                        <input type="text" name="city" value="{{ old('city', $company->city) }}" class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Registered Address</label>
                        <input type="text" name="address" value="{{ old('address', $company->address) }}" class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200">
                    </div>
                </div>
            </div>
            @endif

            <div class="flex justify-end pt-3">
                <button type="submit" class="px-5 py-2 rounded-xl bg-gradient-to-r from-rose-500 to-purple-600 hover:from-rose-600 hover:to-purple-700 text-white font-semibold text-xs shadow-md shadow-rose-500/20 transition-all">
                    Update Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
