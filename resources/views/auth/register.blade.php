@extends('layouts.auth')

@section('title', 'Create Workspace')

@section('content')
<div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-xl shadow-black/40">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-white">Start your Free Trial</h2>
        <p class="text-sm text-slate-400">14 days full access, 100 free verified leads</p>
    </div>

    <form method="POST" action="{{ route('register.post') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Agency or Business Name</label>
            <input type="text" 
                   name="company_name" 
                   value="{{ old('company_name') }}" 
                   required 
                   placeholder="e.g. Acme Marketing Agency"
                   class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 focus:border-rose-500 focus:ring-1 focus:ring-rose-500 text-white text-sm">
            @error('company_name')
                <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Full Name</label>
            <input type="text" 
                   name="name" 
                   value="{{ old('name') }}" 
                   required 
                   placeholder="e.g. Alex Morgan"
                   class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 focus:border-rose-500 focus:ring-1 focus:ring-rose-500 text-white text-sm">
            @error('name')
                <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Business Email</label>
            <input type="email" 
                   name="email" 
                   value="{{ old('email') }}" 
                   required 
                   placeholder="alex@acmemarketing.com"
                   class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 focus:border-rose-500 focus:ring-1 focus:ring-rose-500 text-white text-sm">
            @error('email')
                <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Phone Number (Optional)</label>
            <input type="text" 
                   name="phone" 
                   value="{{ old('phone') }}" 
                   placeholder="+91 98000 00000"
                   class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 focus:border-rose-500 focus:ring-1 focus:ring-rose-500 text-white text-sm">
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Password</label>
                <input type="password" 
                       name="password" 
                       required 
                       class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 focus:border-rose-500 focus:ring-1 focus:ring-rose-500 text-white text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Confirm</label>
                <input type="password" 
                       name="password_confirmation" 
                       required 
                       class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 focus:border-rose-500 focus:ring-1 focus:ring-rose-500 text-white text-sm">
            </div>
        </div>
        @error('password')
            <p class="text-xs text-rose-400">{{ $message }}</p>
        @enderror

        <button type="submit" class="w-full mt-2 py-3 px-4 rounded-xl bg-gradient-to-r from-rose-500 via-purple-600 to-indigo-600 hover:from-rose-600 hover:to-indigo-700 text-white font-semibold text-sm shadow-md shadow-rose-500/20 transition-all">
            Create Workspace & Start Trial
        </button>
    </form>

    <div class="mt-6 text-center">
        <p class="text-xs text-slate-400">
            Already have an account? 
            <a href="{{ route('login') }}" class="font-semibold text-rose-400 hover:text-rose-300">Sign in</a>
        </p>
    </div>
</div>
@endsection
