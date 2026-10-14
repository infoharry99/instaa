@extends('layouts.app')

@section('title', 'Admin Panel')

@section('content')
<div class="space-y-6" x-data="{ addMemberModal: false, upgradePlanModal: false }">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center space-x-2">
                <span>Enterprise Administration</span>
                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300">Admin Only</span>
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Configure company subscriptions, team role permissions, authorized API lead sources, and compliance audit logs.</p>
        </div>

        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.audit-logs') }}" class="px-3.5 py-2 text-xs font-semibold rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 shadow-sm">
                View Audit Trail →
            </a>
            <button @click="addMemberModal = true" class="px-4 py-2 text-xs font-semibold rounded-xl bg-gradient-to-r from-rose-500 to-purple-600 text-white shadow-md shadow-rose-500/20">
                + Add Team Member
            </button>
        </div>
    </div>

    <!-- Company Quota & Subscription Overview Banner -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Current Plan</p>
                <p class="text-xl font-bold text-slate-900 dark:text-white capitalize mt-1">{{ $company->plan }} Plan</p>
                <button @click="upgradePlanModal = true" class="text-xs font-semibold text-rose-500 hover:text-rose-600 mt-2 block">
                    Change Plan / Upgrade Quota ↗
                </button>
            </div>
            <div class="p-3 rounded-2xl bg-gradient-to-tr from-amber-500 to-rose-500 text-white font-bold text-lg">
                💎
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Lead Quota Usage</p>
                <p class="text-xl font-bold text-slate-900 dark:text-white mt-1">{{ $stats['leads_used'] }} / {{ $stats['leads_quota'] }}</p>
                <p class="text-xs text-slate-400 mt-2">{{ $stats['leads_quota'] - $stats['leads_used'] }} leads remaining</p>
            </div>
            <div class="p-3 rounded-2xl bg-blue-50 dark:bg-blue-950/40 text-blue-500 font-bold text-lg">
                📊
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Active Team Seats</p>
                <p class="text-xl font-bold text-slate-900 dark:text-white mt-1">{{ $stats['total_users'] }} Users</p>
                <p class="text-xs text-emerald-500 font-semibold mt-2">Roles: Admin, Manager, Agent</p>
            </div>
            <div class="p-3 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-500 font-bold text-lg">
                👥
            </div>
        </div>
    </div>

    <!-- Team Members Management -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-bold text-slate-900 dark:text-white">Team Members & Role-Based Access</h2>
            <span class="text-xs text-slate-400">{{ $teamMembers->count() }} Active Seats</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-800 text-slate-400 font-semibold uppercase text-[10px]">
                        <th class="py-3 px-3">User</th>
                        <th class="py-3 px-3">Email Address</th>
                        <th class="py-3 px-3">Role</th>
                        <th class="py-3 px-3">Status</th>
                        <th class="py-3 px-3">Joined Date</th>
                        <th class="py-3 px-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach($teamMembers as $member)
                    <tr>
                        <td class="py-3.5 px-3">
                            <div class="flex items-center space-x-2.5">
                                <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center font-bold text-slate-700 dark:text-slate-300">
                                    {{ substr($member->name, 0, 1) }}
                                </div>
                                <span class="font-bold text-slate-900 dark:text-white">{{ $member->name }}</span>
                            </div>
                        </td>
                        <td class="py-3.5 px-3 text-slate-500">{{ $member->email }}</td>
                        <td class="py-3.5 px-3">
                            <form method="POST" action="{{ route('admin.team.update', $member->id) }}" class="inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="{{ $member->status }}">
                                <select name="role" onchange="this.form.submit()" class="text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-2 py-1 font-semibold text-slate-700 dark:text-slate-200">
                                    <option value="admin" {{ $member->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="manager" {{ $member->role === 'manager' ? 'selected' : '' }}>Manager</option>
                                    <option value="sales_agent" {{ $member->role === 'sales_agent' ? 'selected' : '' }}>Sales Agent</option>
                                </select>
                            </form>
                        </td>
                        <td class="py-3.5 px-3">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase
                                  {{ $member->status === 'active' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-100 text-slate-600' }}">
                                {{ $member->status }}
                            </span>
                        </td>
                        <td class="py-3.5 px-3 text-slate-400">{{ $member->created_at->format('M d, Y') }}</td>
                        <td class="py-3.5 px-3 text-right">
                            <span class="text-slate-400 text-xs">Active</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Lead Sources Management -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-sm">
        <h2 class="text-base font-bold text-slate-900 dark:text-white mb-2">Permitted Data Sources & Lead Providers</h2>
        <p class="text-xs text-slate-400 mb-4">Control which authorized providers feed into your discovery engine.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($sources as $source)
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-sm text-slate-900 dark:text-white">{{ $source->name }}</h3>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $source->description }}</p>
                    <span class="inline-block mt-2 text-[10px] text-slate-400">Rate Limit: {{ $source->rate_limit_per_minute }} req/min</span>
                </div>
                <form method="POST" action="{{ route('admin.sources.toggle', $source->id) }}">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 rounded-xl text-xs font-semibold border transition-all
                            {{ $source->is_active ? 'bg-emerald-50 border-emerald-300 text-emerald-700 dark:bg-emerald-950/40 dark:border-emerald-800 dark:text-emerald-300' : 'bg-slate-100 border-slate-300 text-slate-500' }}">
                        {{ $source->is_active ? 'Enabled' : 'Disabled' }}
                    </button>
                </form>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Add Member Modal -->
    <div x-show="addMemberModal" 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4" 
         style="display: none;">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl" @click.away="addMemberModal = false">
            <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4">Add Team Member</h3>
            <form method="POST" action="{{ route('admin.team.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Full Name *</label>
                    <input type="text" name="name" required class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Email Address *</label>
                    <input type="email" name="email" required class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Role</label>
                        <select name="role" required class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200">
                            <option value="sales_agent">Sales Agent</option>
                            <option value="manager">Manager</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Temporary Password</label>
                        <input type="password" name="password" required value="password" class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200">
                    </div>
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="addMemberModal = false" class="px-4 py-2 text-xs font-semibold text-slate-400">Cancel</button>
                    <button type="submit" class="px-5 py-2 text-xs font-semibold rounded-xl bg-gradient-to-r from-rose-500 to-purple-600 text-white">Create Member</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Upgrade Plan Modal -->
    <div x-show="upgradePlanModal" 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4" 
         style="display: none;">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-lg w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl" @click.away="upgradePlanModal = false">
            <h3 class="text-base font-bold text-slate-900 dark:text-white mb-2">Subscription & Quota Management</h3>
            <p class="text-xs text-slate-400 mb-4">Select the scale tier suited for your agency's monthly lead discovery volume.</p>

            <form method="POST" action="{{ route('admin.subscription.update') }}" class="space-y-3">
                @csrf
                <label class="flex items-center justify-between p-3.5 rounded-xl border border-slate-200 dark:border-slate-700 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800">
                    <div class="flex items-center space-x-3">
                        <input type="radio" name="plan" value="free" {{ $company->plan === 'free' ? 'checked' : '' }} class="text-rose-500">
                        <div>
                            <span class="font-bold text-xs text-slate-800 dark:text-white">Free Starter</span>
                            <span class="text-[11px] text-slate-400 block">100 Leads / month • 1 User</span>
                        </div>
                    </div>
                    <span class="font-bold text-xs text-slate-700 dark:text-slate-300">₹0 / mo</span>
                </label>

                <label class="flex items-center justify-between p-3.5 rounded-xl border border-slate-200 dark:border-slate-700 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800">
                    <div class="flex items-center space-x-3">
                        <input type="radio" name="plan" value="starter" {{ $company->plan === 'starter' ? 'checked' : '' }} class="text-rose-500">
                        <div>
                            <span class="font-bold text-xs text-slate-800 dark:text-white">Growth Agency</span>
                            <span class="text-[11px] text-slate-400 block">500 Leads / month • 3 Users</span>
                        </div>
                    </div>
                    <span class="font-bold text-xs text-slate-700 dark:text-slate-300">₹1,999 / mo</span>
                </label>

                <label class="flex items-center justify-between p-3.5 rounded-xl border border-rose-500/60 bg-rose-50/30 dark:bg-rose-950/20 cursor-pointer">
                    <div class="flex items-center space-x-3">
                        <input type="radio" name="plan" value="pro" {{ $company->plan === 'pro' ? 'checked' : '' }} class="text-rose-500">
                        <div>
                            <span class="font-bold text-xs text-rose-600 dark:text-rose-400">Pro Powerhouse (Popular)</span>
                            <span class="text-[11px] text-slate-400 block">2,500 Leads / month • Unlimited Users • Meta API</span>
                        </div>
                    </div>
                    <span class="font-bold text-xs text-rose-600 dark:text-rose-400">₹4,999 / mo</span>
                </label>

                <label class="flex items-center justify-between p-3.5 rounded-xl border border-slate-200 dark:border-slate-700 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800">
                    <div class="flex items-center space-x-3">
                        <input type="radio" name="plan" value="enterprise" {{ $company->plan === 'enterprise' ? 'checked' : '' }} class="text-rose-500">
                        <div>
                            <span class="font-bold text-xs text-slate-800 dark:text-white">Enterprise Scaler</span>
                            <span class="text-[11px] text-slate-400 block">10,000 Leads / month • Dedicated Account Manager</span>
                        </div>
                    </div>
                    <span class="font-bold text-xs text-slate-700 dark:text-slate-300">₹14,999 / mo</span>
                </label>

                <div class="flex justify-end space-x-2 pt-3">
                    <button type="button" @click="upgradePlanModal = false" class="px-4 py-2 text-xs font-semibold text-slate-400">Cancel</button>
                    <button type="submit" class="px-5 py-2 text-xs font-semibold rounded-xl bg-gradient-to-r from-rose-500 to-purple-600 text-white">Save Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
