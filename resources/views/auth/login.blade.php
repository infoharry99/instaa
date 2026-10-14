@extends('layouts.auth')

@section('title', 'Sign In')

@section('content')
<div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-xl shadow-black/40">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-white">Welcome back</h2>
        <p class="text-sm text-slate-400">Sign in to your team dashboard</p>
    </div>

    <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Email Address</label>
            <input type="email" 
                   name="email" 
                   id="email_input"
                   value="{{ old('email', config('app.debug') ? 'admin@instalead.com' : '') }}" 
                   required 
                   class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 focus:border-rose-500 focus:ring-1 focus:ring-rose-500 text-white text-sm">
            @error('email')
                <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider">Password</label>
            </div>
            <input type="password" 
                   name="password" 
                   id="password_input"
                   value="{{ config('app.debug') ? 'password' : '' }}" 
                   required 
                   class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 focus:border-rose-500 focus:ring-1 focus:ring-rose-500 text-white text-sm">
            @error('password')
                <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between pt-1">
            <label class="flex items-center space-x-2 text-xs text-slate-400 cursor-pointer">
                <input type="checkbox" name="remember" class="rounded bg-slate-950 border-slate-700 text-rose-500 focus:ring-rose-500">
                <span>Remember me</span>
            </label>
        </div>

        <button type="submit" class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-rose-500 via-purple-600 to-indigo-600 hover:from-rose-600 hover:to-indigo-700 text-white font-semibold text-sm shadow-md shadow-rose-500/20 transition-all">
            Sign In to Dashboard
        </button>
    </form>

    @if(config('app.debug'))
    <!-- Quick Demo Logins (Debug Only) -->
    <div class="mt-6 pt-6 border-t border-slate-800">
        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2.5 text-center">Quick Demo Switcher (Debug Mode)</p>
        <div class="grid grid-cols-3 gap-2">
            <button type="button" 
                    onclick="document.getElementById('email_input').value='admin@instalead.com'; document.getElementById('password_input').value='password';"
                    class="py-2 px-2 text-xs font-medium rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 transition-colors">
                Admin
            </button>
            <button type="button" 
                    onclick="document.getElementById('email_input').value='manager@instalead.com'; document.getElementById('password_input').value='password';"
                    class="py-2 px-2 text-xs font-medium rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 transition-colors">
                Manager
            </button>
            <button type="button" 
                    onclick="document.getElementById('email_input').value='agent@instalead.com'; document.getElementById('password_input').value='password';"
                    class="py-2 px-2 text-xs font-medium rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 transition-colors">
                Sales Agent
            </button>
        </div>
    </div>
    @endif

    <div class="mt-6 text-center">
        <p class="text-xs text-slate-400">
            Need a workspace? 
            <a href="{{ route('register') }}" class="font-semibold text-rose-400 hover:text-rose-300">Create new account</a>
        </p>
    </div>
</div>
@endsection
