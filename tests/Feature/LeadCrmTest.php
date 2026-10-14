<?php

namespace Tests\Feature;

use App\Models\BusinessLead;
use App\Models\Company;
use App\Models\User;
use Tests\TestCase;

class LeadCrmTest extends TestCase
{
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');
    }

    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('InstaLead');
    }

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $user = User::where('email', 'admin@instalead.com')->first();
        $this->actingAs($user);

        $response = $this->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard & Analytics');
        $response->assertSee('Total Leads');
    }

    public function test_authenticated_user_can_access_lead_finder(): void
    {
        $user = User::where('email', 'admin@instalead.com')->first();
        $this->actingAs($user);

        $response = $this->get('/discovery');
        $response->assertStatus(200);
        $response->assertSee('Business Lead Finder');
        $response->assertSee('Restaurants and cafes');
    }

    public function test_authenticated_user_can_access_leads_crm(): void
    {
        $user = User::where('email', 'admin@instalead.com')->first();
        $this->actingAs($user);

        $response = $this->get('/leads');
        $response->assertStatus(200);
        $response->assertSee('My Leads CRM');
    }

    public function test_lead_details_page_renders(): void
    {
        $user = User::where('email', 'admin@instalead.com')->first();
        $this->actingAs($user);

        $lead = BusinessLead::first();
        $response = $this->get("/leads/{$lead->id}");
        $response->assertStatus(200);
        $response->assertSee($lead->business_name);
        $response->assertSee('Official Information');
    }

    public function test_csv_export_returns_csv_file(): void
    {
        $user = User::where('email', 'admin@instalead.com')->first();
        $this->actingAs($user);

        $response = $this->get('/import-export/export');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_rest_api_login_returns_sanctum_token(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'admin@instalead.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['token', 'user']);
    }

    public function test_rest_api_returns_leads_with_sanctum_auth(): void
    {
        $user = User::where('email', 'admin@instalead.com')->first();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/leads');

        $response->assertStatus(200);
        $response->assertJsonStructure(['data', 'current_page']);
    }
}
