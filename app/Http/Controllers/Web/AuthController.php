<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Subscription;
use App\Models\User;
use App\Services\AuditLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            AuditLogService::log(
                action: 'user.login',
                description: 'User logged in successfully.'
            );

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Welcome back, ' . Auth::user()->name . '!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        // 1. Create Company
        $company = Company::create([
            'name' => $validated['company_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'plan' => 'free',
            'max_leads_quota' => 100,
            'leads_used_count' => 0,
        ]);

        // 2. Create Trial Subscription
        Subscription::create([
            'company_id' => $company->id,
            'plan_name' => 'Free Trial',
            'status' => 'trialing',
            'leads_limit' => 100,
            'monthly_price' => 0.00,
            'starts_at' => now(),
            'expires_at' => now()->addDays(14),
        ]);

        // 3. Create Admin User
        $user = User::create([
            'company_id' => $company->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        Auth::login($user);

        AuditLogService::log(
            action: 'company.registered',
            entityType: 'Company',
            entityId: $company->id,
            description: "Registered company '{$company->name}' with admin '{$user->name}'."
        );

        return redirect()->route('dashboard')
            ->with('success', 'Account created successfully! Welcome to InstaLead CRM.');
    }

    public function logout(Request $request)
    {
        AuditLogService::log(
            action: 'user.logout',
            description: 'User logged out.'
        );

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'You have been logged out.');
    }

    public function showProfile()
    {
        $user = Auth::user();
        $company = $user->company;

        return view('profile.show', compact('user', 'company'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $user->name = $validated['name'];
        $user->phone = $validated['phone'] ?? $user->phone;

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        if ($user->isAdmin() && $request->has('company_name')) {
            $request->validate([
                'company_name' => ['required', 'string', 'max:255'],
                'domain' => ['nullable', 'string', 'max:255'],
                'city' => ['nullable', 'string', 'max:100'],
                'address' => ['nullable', 'string', 'max:255'],
            ]);

            $user->company->update([
                'name' => $request->company_name,
                'domain' => $request->domain,
                'city' => $request->city,
                'address' => $request->address,
            ]);
        }

        AuditLogService::log(
            action: 'user.profile_updated',
            description: 'Updated profile information.'
        );

        return back()->with('success', 'Profile updated successfully.');
    }
}
