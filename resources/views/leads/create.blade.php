@extends('layouts.app')

@section('title', 'Add New Lead')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Add New Business Lead</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Record a verified commercial lead into your CRM system.</p>
        </div>
        <a href="{{ route('leads.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-700 dark:hover:text-slate-300">
            ← Back to Leads
        </a>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-sm">
        <form method="POST" action="{{ route('leads.store') }}" class="space-y-5">
            @csrf

            <!-- Business Information -->
            <div class="space-y-4">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Business Identity</h3>
                
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Business Name *</label>
                    <input type="text" 
                           name="business_name" 
                           value="{{ old('business_name') }}" 
                           required 
                           placeholder="e.g. Copper Chimney Restaurant"
                           class="w-full px-3.5 py-2.5 text-sm rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-rose-500 text-slate-900 dark:text-white">
                    @error('business_name')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Instagram Username / Handle</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 font-bold text-xs">@</span>
                            <input type="text" 
                                   name="instagram_username" 
                                   value="{{ old('instagram_username') }}" 
                                   placeholder="copperchimney_in"
                                   class="w-full pl-8 pr-3 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-rose-500 text-slate-900 dark:text-white">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Business Category *</label>
                        <select name="category" required class="w-full px-3 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-rose-500 text-slate-900 dark:text-white">
                            @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Business Description</label>
                    <textarea name="business_description" rows="2" placeholder="Brief summary of offerings, specialty, audience..." class="w-full px-3.5 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-rose-500 text-slate-900 dark:text-white">{{ old('business_description') }}</textarea>
                </div>
            </div>

            <!-- Contact & Location -->
            <div class="space-y-4 pt-3 border-t border-slate-100 dark:border-slate-800">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Public Contact & Location</h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Public Business Email</label>
                        <input type="email" name="public_email" value="{{ old('public_email') }}" placeholder="info@brand.com" class="w-full px-3 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-rose-500 text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Public Business Phone</label>
                        <input type="text" name="public_phone" value="{{ old('public_phone') }}" placeholder="+91 98200 12345" class="w-full px-3 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-rose-500 text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Contact Person's Name</label>
                        <input type="text" name="contact_person" value="{{ old('contact_person') }}" placeholder="Rajesh Kumar" class="w-full px-3 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-rose-500 text-slate-900 dark:text-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Official Website</label>
                        <input type="url" name="official_website" value="{{ old('official_website') }}" placeholder="https://brand.com" class="w-full px-3 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-rose-500 text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">City</label>
                        <input type="text" name="city" value="{{ old('city') }}" placeholder="Mumbai" class="w-full px-3 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-rose-500 text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">State / Province</label>
                        <input type="text" name="state" value="{{ old('state') }}" placeholder="Maharashtra" class="w-full px-3 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-rose-500 text-slate-900 dark:text-white">
                    </div>
                </div>
            </div>

            <!-- Status, Priority & Assignment -->
            <div class="space-y-4 pt-3 border-t border-slate-100 dark:border-slate-800">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">CRM Pipeline Configuration</h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Status</label>
                        <select name="status" class="w-full px-3 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-rose-500 text-slate-900 dark:text-white">
                            <option value="new">New</option>
                            <option value="contacted">Contacted</option>
                            <option value="interested">Interested</option>
                            <option value="follow_up">Follow-up</option>
                            <option value="converted">Converted</option>
                            <option value="not_interested">Not Interested</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Priority</label>
                        <select name="priority" class="w-full px-3 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-rose-500 text-slate-900 dark:text-white">
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="low">Low</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Assign to Agent</label>
                        <select name="assigned_to_user_id" class="w-full px-3 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-rose-500 text-slate-900 dark:text-white">
                            <option value="">Leave Unassigned</option>
                            @foreach($teamMembers as $tm)
                            <option value="{{ $tm->id }}">{{ $tm->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Add to Custom List</label>
                        <select name="list_id" class="w-full px-3 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-rose-500 text-slate-900 dark:text-white">
                            <option value="">None (General Leads)</option>
                            @foreach($lists as $lst)
                            <option value="{{ $lst->id }}">{{ $lst->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Data Source Attributed</label>
                        <select name="lead_source_id" class="w-full px-3 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-rose-500 text-slate-900 dark:text-white">
                            @foreach($sources as $src)
                            <option value="{{ $src->id }}">{{ $src->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                <a href="{{ route('leads.index') }}" class="px-4 py-2 text-xs font-semibold text-slate-500 hover:text-slate-700 dark:hover:text-slate-300">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-rose-500 to-purple-600 hover:from-rose-600 hover:to-purple-700 text-white text-xs font-semibold shadow-md shadow-rose-500/20 transition-all">
                    Create & Save Lead
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
