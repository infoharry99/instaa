<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\BusinessLead;
use App\Models\CommunicationLog;
use App\Models\Company;
use App\Models\ConnectedSocialAccount;
use App\Models\FollowUp;
use App\Models\LeadAssignment;
use App\Models\LeadContact;
use App\Models\LeadList;
use App\Models\LeadNote;
use App\Models\LeadSource;
use App\Models\LeadTag;
use App\Models\OutreachTemplate;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Lead Sources
        $sourceMeta = LeadSource::create([
            'name' => 'Official Meta Graph API',
            'slug' => 'meta-graph-api',
            'provider_type' => 'meta_api',
            'description' => 'Direct authorized Meta Graph API for Instagram Professional & Business Accounts',
            'is_active' => true,
            'api_endpoint' => 'https://graph.facebook.com/v21.0',
            'rate_limit_per_minute' => 60,
            'settings' => ['version' => 'v21.0', 'scopes' => ['instagram_basic', 'pages_show_list']],
        ]);

        $sourceDirectory = LeadSource::create([
            'name' => 'Verified Business Registry & Public YellowPages',
            'slug' => 'verified-business-registry',
            'provider_type' => 'official_directory',
            'description' => 'Licensed public commercial trade registries and local business directories',
            'is_active' => true,
            'rate_limit_per_minute' => 120,
        ]);

        $sourceGoogle = LeadSource::create([
            'name' => 'Authorized Places API',
            'slug' => 'places-data-provider',
            'provider_type' => 'google_places',
            'description' => 'Authorized Places data provider with public verified contact points',
            'is_active' => true,
            'rate_limit_per_minute' => 100,
        ]);

        $sourceManual = LeadSource::create([
            'name' => 'Manual CSV & Contact Import',
            'slug' => 'manual-import',
            'provider_type' => 'manual_import',
            'description' => 'User-provided records and consented marketing imports',
            'is_active' => true,
            'rate_limit_per_minute' => 200,
        ]);

        // 2. Seed Default Company
        $company = Company::create([
            'name' => 'OmniMedia Growth Agency',
            'domain' => 'omnimediagrowth.com',
            'phone' => '+91 98200 12345',
            'email' => 'contact@omnimediagrowth.com',
            'address' => 'Bandra Kurla Complex, Tower 4, Level 7',
            'city' => 'Mumbai',
            'state' => 'Maharashtra',
            'country' => 'India',
            'plan' => 'pro',
            'max_leads_quota' => 2500,
            'leads_used_count' => 18,
            'settings' => [
                'timezone' => 'Asia/Kolkata',
                'currency' => 'INR',
                'enable_email_reminders' => true,
            ],
        ]);

        // 3. Seed Subscription
        Subscription::create([
            'company_id' => $company->id,
            'plan_name' => 'Pro Agency Plan',
            'status' => 'active',
            'leads_limit' => 2500,
            'monthly_price' => 4999.00,
            'billing_cycle' => 'monthly',
            'starts_at' => Carbon::now()->subDays(15),
            'expires_at' => Carbon::now()->addDays(15),
            'features' => ['unlimited_lists', 'csv_exports', 'team_members_5', 'api_access'],
        ]);

        // 4. Seed Users: Admin, Manager, Sales Agent
        $admin = User::create([
            'company_id' => $company->id,
            'name' => 'Aarav Sharma (Admin)',
            'email' => 'admin@instalead.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
            'phone' => '+91 98201 11111',
            'email_verified_at' => now(),
        ]);

        $manager = User::create([
            'company_id' => $company->id,
            'name' => 'Priya Mehta (Manager)',
            'email' => 'manager@instalead.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'status' => 'active',
            'phone' => '+91 98202 22222',
            'email_verified_at' => now(),
        ]);

        $agent = User::create([
            'company_id' => $company->id,
            'name' => 'Rohan Verma (Sales Agent)',
            'email' => 'agent@instalead.com',
            'password' => Hash::make('password'),
            'role' => 'sales_agent',
            'status' => 'active',
            'phone' => '+91 98203 33333',
            'email_verified_at' => now(),
        ]);

        // 5. Seed Connected Social Account (Mock authorized Meta IG Business account)
        ConnectedSocialAccount::create([
            'user_id' => $admin->id,
            'company_id' => $company->id,
            'platform' => 'instagram',
            'account_id' => '17841400293847291',
            'username' => 'omnimediagrowth',
            'name' => 'OmniMedia Growth Official',
            'profile_picture_url' => 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=200&auto=format&fit=crop&q=80',
            'account_type' => 'BUSINESS',
            'access_token' => 'EAAXmock_token_for_authorized_instagram_graph_api_access',
            'token_expires_at' => Carbon::now()->addDays(60),
            'is_connected' => true,
            'scopes' => ['instagram_basic', 'instagram_manage_messages', 'pages_read_engagement', 'business_management'],
            'meta_data' => [
                'followers_count' => 14200,
                'follows_count' => 380,
                'media_count' => 152,
                'ig_id' => '17841400293847291',
            ],
        ]);

        // 6. Seed Lead Tags
        $tagRestaurant = LeadTag::create(['company_id' => $company->id, 'name' => 'Restaurant', 'slug' => 'restaurant', 'color' => '#EF4444']);
        $tagPremium = LeadTag::create(['company_id' => $company->id, 'name' => 'Premium', 'slug' => 'premium', 'color' => '#8B5CF6']);
        $tagMumbai = LeadTag::create(['company_id' => $company->id, 'name' => 'Mumbai', 'slug' => 'mumbai', 'color' => '#3B82F6']);
        $tagFollowUp = LeadTag::create(['company_id' => $company->id, 'name' => 'Follow-up', 'slug' => 'follow-up', 'color' => '#F59E0B']);
        $tagHot = LeadTag::create(['company_id' => $company->id, 'name' => 'Hot Lead', 'slug' => 'hot-lead', 'color' => '#10B981']);

        // 7. Seed Custom Lead Lists
        $listMumbaiCafes = LeadList::create([
            'company_id' => $company->id,
            'user_id' => $admin->id,
            'name' => 'Mumbai Cafes & Fine Dining Q4',
            'description' => 'Top food & beverage establishments in South Mumbai and Bandra',
            'color' => '#EF4444',
            'is_private' => false,
        ]);

        $listLuxurySalons = LeadList::create([
            'company_id' => $company->id,
            'user_id' => $manager->id,
            'name' => 'Luxury Salons & Wellness Clinics',
            'description' => 'Aesthetic doctors and premium beauty salons',
            'color' => '#EC4899',
            'is_private' => false,
        ]);

        $listFitness = LeadList::create([
            'company_id' => $company->id,
            'user_id' => $agent->id,
            'name' => 'Boutique Gyms & CrossFit',
            'description' => 'High-end fitness centers across Tier-1 metros',
            'color' => '#10B981',
            'is_private' => false,
        ]);

        // 8. Seed Outreach Templates
        OutreachTemplate::create([
            'company_id' => $company->id,
            'user_id' => $admin->id,
            'name' => 'VIP Restaurant Collab Intro',
            'channel' => 'email',
            'subject' => 'Partnership inquiry for {{business_name}}',
            'body' => "Hi {{contact_person}},\n\nWe love what {{business_name}} has been doing in {{city}}! Our digital marketing agency specializes in helping premier culinary brands increase footfall and high-ticket table reservations.\n\nWould you be open to a quick 10-minute discovery call this Thursday?\n\nBest regards,\nAarav Sharma\nOmniMedia Growth",
            'variables' => ['business_name', 'contact_person', 'city'],
            'is_shared' => true,
        ]);

        OutreachTemplate::create([
            'company_id' => $company->id,
            'user_id' => $manager->id,
            'name' => 'WhatsApp Quick Connect for Salons',
            'channel' => 'whatsapp',
            'subject' => null,
            'body' => "Hello {{contact_person}}! We spotted {{business_name}} on Instagram and were impressed by your styling portfolio in {{city}}. We have a tailored marketing blueprint that helped similar luxury salons gain 40+ appointments monthly. Can we share the 1-page PDF case study?",
            'variables' => ['business_name', 'contact_person', 'city'],
            'is_shared' => true,
        ]);

        OutreachTemplate::create([
            'company_id' => $company->id,
            'user_id' => $agent->id,
            'name' => 'Instagram Direct Outreach Script',
            'channel' => 'instagram',
            'subject' => null,
            'body' => "Hey team @{{instagram_username}}! Huge fans of your work at {{business_name}}. We are hosting a private masterclass on customer acquisition for {{category}} brands in {{city}}. Would love to invite your founders!",
            'variables' => ['instagram_username', 'business_name', 'category', 'city'],
            'is_shared' => true,
        ]);

        // 9. Seed Realistic Permitted Business Leads across categories
        $leadsData = [
            [
                'business_name' => 'The Bombay Canteen & Bar',
                'instagram_username' => 'thebombaycanteen',
                'instagram_profile_url' => 'https://instagram.com/thebombaycanteen',
                'profile_picture_url' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=200&auto=format&fit=crop&q=80',
                'business_description' => 'Celebrated modern Indian cafe and bar highlighting regional ingredients and artisanal cocktails.',
                'category' => 'Restaurants and cafes',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'location_address' => 'Unit 1, Process House, Kamala Mills, Lower Parel',
                'official_website' => 'https://thebombaycanteen.com',
                'public_email' => 'reservations@thebombaycanteen.com',
                'public_phone' => '+91 22 4966 6666',
                'contact_person' => 'Kunal Joshi',
                'status' => 'interested',
                'priority' => 'high',
                'source' => $sourceMeta->id,
                'tags' => [$tagRestaurant->id, $tagPremium->id, $tagMumbai->id, $tagHot->id],
                'list_id' => $listMumbaiCafes->id,
                'assigned_to' => $agent->id,
            ],
            [
                'business_name' => 'Subko Coffee Roasters & Bakehouse',
                'instagram_username' => 'subkocoffee',
                'instagram_profile_url' => 'https://instagram.com/subkocoffee',
                'profile_picture_url' => 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=200&auto=format&fit=crop&q=80',
                'business_description' => 'Specialty coffee roastery and craft craft bakehouse celebrating the subcontinent.',
                'category' => 'Restaurants and cafes',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'location_address' => '21A, Chapel Rd, Ranwar, Bandra West',
                'official_website' => 'https://subko.co',
                'public_email' => 'hello@subko.co',
                'public_phone' => '+91 90047 00655',
                'contact_person' => 'Rahul Reddy',
                'status' => 'contacted',
                'priority' => 'medium',
                'source' => $sourceDirectory->id,
                'tags' => [$tagRestaurant->id, $tagMumbai->id, $tagFollowUp->id],
                'list_id' => $listMumbaiCafes->id,
                'assigned_to' => $manager->id,
            ],
            [
                'business_name' => 'Truefitt & Hill India',
                'instagram_username' => 'truefittandhillindia',
                'instagram_profile_url' => 'https://instagram.com/truefittandhillindia',
                'profile_picture_url' => 'https://images.unsplash.com/photo-1503951914875-452162b0f3f1?w=200&auto=format&fit=crop&q=80',
                'business_description' => 'The world oldest barbershop offering royal grooming services, luxury facials and gentlemen haircuts.',
                'category' => 'Beauty salons and makeup artists',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'location_address' => 'Ground Floor, Phoenix Palladium, High Street Phoenix',
                'official_website' => 'https://truefittandhill.in',
                'public_email' => 'concierge@truefittandhill.in',
                'public_phone' => '+91 22 6666 4321',
                'contact_person' => 'Devika Singhania',
                'status' => 'new',
                'priority' => 'high',
                'source' => $sourceGoogle->id,
                'tags' => [$tagPremium->id, $tagMumbai->id],
                'list_id' => $listLuxurySalons->id,
                'assigned_to' => $agent->id,
            ],
            [
                'business_name' => 'Cult.fit Elite Fitness Studio',
                'instagram_username' => 'cultfit_official',
                'instagram_profile_url' => 'https://instagram.com/cultfit_official',
                'profile_picture_url' => 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=200&auto=format&fit=crop&q=80',
                'business_description' => 'State of the art strength, boxing, yoga, and functional fitness conditioning chain.',
                'category' => 'Gyms and fitness centers',
                'city' => 'Bengaluru',
                'state' => 'Karnataka',
                'location_address' => '100ft Road, Indiranagar',
                'official_website' => 'https://cult.fit',
                'public_email' => 'partner@cult.fit',
                'public_phone' => '+91 80 4567 8900',
                'contact_person' => 'Vikram Shenoy',
                'status' => 'follow_up',
                'priority' => 'high',
                'source' => $sourceMeta->id,
                'tags' => [$tagPremium->id, $tagFollowUp->id],
                'list_id' => $listFitness->id,
                'assigned_to' => $agent->id,
            ],
            [
                'business_name' => 'Sotheby\'s International Realty India',
                'instagram_username' => 'indiasothebysrealty',
                'instagram_profile_url' => 'https://instagram.com/indiasothebysrealty',
                'profile_picture_url' => 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=200&auto=format&fit=crop&q=80',
                'business_description' => 'Exclusive luxury homes, penthouses, villas, and prime commercial properties across Delhi, Goa, and Mumbai.',
                'category' => 'Real estate agents',
                'city' => 'New Delhi',
                'state' => 'Delhi',
                'location_address' => 'Barakhamba Road, Connaught Place',
                'official_website' => 'https://sothebysrealty.com',
                'public_email' => 'delhi@sothebysrealty.in',
                'public_phone' => '+91 11 4987 6543',
                'contact_person' => 'Amit Goyal',
                'status' => 'converted',
                'priority' => 'high',
                'source' => $sourceDirectory->id,
                'tags' => [$tagPremium->id, $tagHot->id],
                'list_id' => null,
                'assigned_to' => $admin->id,
            ],
            [
                'business_name' => 'The Postcard Hotel & Hideaways',
                'instagram_username' => 'thepostcardhotel',
                'instagram_profile_url' => 'https://instagram.com/thepostcardhotel',
                'profile_picture_url' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=200&auto=format&fit=crop&q=80',
                'business_description' => 'Intimate luxury boutique hotels situated in historic, transformative holiday destinations.',
                'category' => 'Hotels and travel agencies',
                'city' => 'Goa',
                'state' => 'Goa',
                'location_address' => 'Moira, North Goa',
                'official_website' => 'https://postcardresorts.com',
                'public_email' => 'experience@postcardresorts.com',
                'public_phone' => '+91 79995 55222',
                'contact_person' => 'Kapil Chopra',
                'status' => 'interested',
                'priority' => 'high',
                'source' => $sourceGoogle->id,
                'tags' => [$tagPremium->id],
                'list_id' => null,
                'assigned_to' => $manager->id,
            ],
            [
                'business_name' => 'Skinfiniti Aesthetic Clinic',
                'instagram_username' => 'dr_jaishreesharad',
                'instagram_profile_url' => 'https://instagram.com/dr_jaishreesharad',
                'profile_picture_url' => 'https://images.unsplash.com/photo-1629909613654-28e377c37b09?w=200&auto=format&fit=crop&q=80',
                'business_description' => 'Internationally recognized dermatology, laser aesthetics, and clinical cosmetology practice.',
                'category' => 'Doctors and clinics',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'location_address' => 'Linking Road, Khar West',
                'official_website' => 'https://jaishreesharad.com',
                'public_email' => 'clinic@jaishreesharad.com',
                'public_phone' => '+91 22 2648 1111',
                'contact_person' => 'Dr. Jaishree Sharad',
                'status' => 'follow_up',
                'priority' => 'medium',
                'source' => $sourceMeta->id,
                'tags' => [$tagPremium->id, $tagMumbai->id, $tagFollowUp->id],
                'list_id' => $listLuxurySalons->id,
                'assigned_to' => $agent->id,
            ],
            [
                'business_name' => 'Mokobara Luggage & Travel Gear',
                'instagram_username' => 'mokobara',
                'instagram_profile_url' => 'https://instagram.com/mokobara',
                'profile_picture_url' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=200&auto=format&fit=crop&q=80',
                'business_description' => 'Modern design D2C premium luggage, backpacks, and accessories engineered for urban travelers.',
                'category' => 'Ecommerce businesses',
                'city' => 'Bengaluru',
                'state' => 'Karnataka',
                'location_address' => 'Indiranagar 12th Main',
                'official_website' => 'https://mokobara.com',
                'public_email' => 'partnerships@mokobara.com',
                'public_phone' => '+91 80 6902 4444',
                'contact_person' => 'Sangeet Agrawal',
                'status' => 'new',
                'priority' => 'high',
                'source' => $sourceDirectory->id,
                'tags' => [$tagPremium->id, $tagHot->id],
                'list_id' => null,
                'assigned_to' => $agent->id,
            ],
            [
                'business_name' => 'Schbang Digital Solutions',
                'instagram_username' => 'schbang',
                'instagram_profile_url' => 'https://instagram.com/schbang',
                'profile_picture_url' => 'https://images.unsplash.com/photo-1542744173-8e7e53415bb0?w=200&auto=format&fit=crop&q=80',
                'business_description' => 'Integrated holistic marketing solutions agency offering brand strategy, media, tech, and production.',
                'category' => 'Digital marketing agencies',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'location_address' => 'Prathamesh Tower, Raghuvanshi Mills, Lower Parel',
                'official_website' => 'https://schbang.com',
                'public_email' => 'business@schbang.com',
                'public_phone' => '+91 22 6157 7777',
                'contact_person' => 'Harshil Karia',
                'status' => 'contacted',
                'priority' => 'medium',
                'source' => $sourceMeta->id,
                'tags' => [$tagMumbai->id],
                'list_id' => null,
                'assigned_to' => $manager->id,
            ],
            [
                'business_name' => 'Nicobar Lifestyle & Home Decor',
                'instagram_username' => 'nicojournal',
                'instagram_profile_url' => 'https://instagram.com/nicojournal',
                'profile_picture_url' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=200&auto=format&fit=crop&q=80',
                'business_description' => 'Curated modern Indian clothing, homeware, and travel essentials rooted in timeless aesthetic.',
                'category' => 'Local shops',
                'city' => 'New Delhi',
                'state' => 'Delhi',
                'location_address' => 'Tulsi Farm, Mandi Road, Sultanpur',
                'official_website' => 'https://nicobar.com',
                'public_email' => 'care@nicobar.com',
                'public_phone' => '+91 85888 77772',
                'contact_person' => 'Simran Lal',
                'status' => 'interested',
                'priority' => 'high',
                'source' => $sourceDirectory->id,
                'tags' => [$tagPremium->id],
                'list_id' => null,
                'assigned_to' => $agent->id,
            ],
            [
                'business_name' => 'Bastian Hospitality Group',
                'instagram_username' => 'bastianmumbai',
                'instagram_profile_url' => 'https://instagram.com/bastianmumbai',
                'profile_picture_url' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=200&auto=format&fit=crop&q=80',
                'business_description' => 'Iconic premium culinary destination celebrated for seafood, craft cocktails, and rooftop ambience.',
                'category' => 'Restaurants and cafes',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'location_address' => 'Kohinoor Square, Dadar West',
                'official_website' => 'https://bastianhospitality.com',
                'public_email' => 'events@bastianhospitality.com',
                'public_phone' => '+91 22 5078 7777',
                'contact_person' => 'Ranjit Bindra',
                'status' => 'converted',
                'priority' => 'high',
                'source' => $sourceMeta->id,
                'tags' => [$tagRestaurant->id, $tagPremium->id, $tagMumbai->id],
                'list_id' => $listMumbaiCafes->id,
                'assigned_to' => $admin->id,
            ],
            [
                'business_name' => 'Bodycraft Salon & Medispa',
                'instagram_username' => 'bodycraftspasalon',
                'instagram_profile_url' => 'https://instagram.com/bodycraftspasalon',
                'profile_picture_url' => 'https://images.unsplash.com/photo-1560066984-138dadb4c035?w=200&auto=format&fit=crop&q=80',
                'business_description' => 'Award-winning chain of luxury hair salons, wellness spas, and clinical aesthetic treatments.',
                'category' => 'Beauty salons and makeup artists',
                'city' => 'Bengaluru',
                'state' => 'Karnataka',
                'location_address' => 'Lavelle Road, Shanthala Nagar',
                'official_website' => 'https://bodycraft.co.in',
                'public_email' => 'info@bodycraft.co.in',
                'public_phone' => '+91 80 4099 2200',
                'contact_person' => 'Manjul Gupta',
                'status' => 'not_interested',
                'priority' => 'low',
                'source' => $sourceDirectory->id,
                'tags' => [$tagPremium->id],
                'list_id' => $listLuxurySalons->id,
                'assigned_to' => $agent->id,
            ],
        ];

        foreach ($leadsData as $item) {
            $lead = BusinessLead::create([
                'company_id' => $company->id,
                'lead_source_id' => $item['source'],
                'business_name' => $item['business_name'],
                'instagram_username' => $item['instagram_username'],
                'instagram_profile_url' => $item['instagram_profile_url'],
                'profile_picture_url' => $item['profile_picture_url'],
                'business_description' => $item['business_description'],
                'category' => $item['category'],
                'city' => $item['city'],
                'state' => $item['state'],
                'country' => 'India',
                'location_address' => $item['location_address'],
                'official_website' => $item['official_website'],
                'public_email' => $item['public_email'],
                'public_phone' => $item['public_phone'],
                'contact_person' => $item['contact_person'],
                'status' => $item['status'],
                'priority' => $item['priority'],
                'source_type' => 'authorized_directory',
                'collected_at' => Carbon::now()->subDays(rand(2, 30)),
                'last_verified_at' => Carbon::now()->subDays(rand(0, 5)),
                'metadata' => [
                    'source_compliance' => 'GDPR / DPDP Compliant Public Profile Data',
                    'verification_status' => 'Verified Official Contact',
                    'follower_bracket' => '10k-100k',
                ],
            ]);

            // Assign tags
            if (!empty($item['tags'])) {
                $lead->tags()->attach($item['tags']);
            }

            // Assign list
            if (!empty($item['list_id'])) {
                $lead->lists()->attach($item['list_id'], ['added_by_user_id' => $admin->id]);
            }

            // Lead Assignment
            LeadAssignment::create([
                'business_lead_id' => $lead->id,
                'company_id' => $company->id,
                'assigned_to_user_id' => $item['assigned_to'],
                'assigned_by_user_id' => $admin->id,
                'notes' => 'Primary lead ownership assigned during Q4 outreach cycle.',
                'assigned_at' => Carbon::now()->subDays(rand(1, 10)),
            ]);

            // Lead Contact Person
            LeadContact::create([
                'business_lead_id' => $lead->id,
                'name' => $item['contact_person'],
                'role' => 'Managing Director / Marketing Lead',
                'email' => $item['public_email'],
                'phone' => $item['public_phone'],
                'is_primary' => true,
                'notes' => 'Official contact listed publicly on corporate site & social bio.',
            ]);

            // Add note
            LeadNote::create([
                'business_lead_id' => $lead->id,
                'user_id' => $item['assigned_to'],
                'content' => "Initial screening complete. Public profile verified. High brand resonance in {$item['city']}.",
                'is_pinned' => $item['priority'] === 'high',
            ]);

            // Communication log
            if (in_array($item['status'], ['contacted', 'interested', 'follow_up', 'converted'])) {
                CommunicationLog::create([
                    'business_lead_id' => $lead->id,
                    'company_id' => $company->id,
                    'user_id' => $item['assigned_to'],
                    'channel' => 'email',
                    'direction' => 'outbound',
                    'subject' => "Strategic Partnership Inquiry for {$lead->business_name}",
                    'notes' => 'Sent initial pitch deck and invitation for collaborative Q4 campaign.',
                    'outcome' => $item['status'] === 'converted' ? 'replied' : 'sent',
                    'contact_info' => $lead->public_email,
                    'logged_at' => Carbon::now()->subDays(rand(1, 8)),
                ]);
            }

            // Follow-up
            if (in_array($item['status'], ['interested', 'follow_up'])) {
                FollowUp::create([
                    'business_lead_id' => $lead->id,
                    'company_id' => $company->id,
                    'user_id' => $item['assigned_to'],
                    'title' => "Follow up on proposal sent to {$lead->business_name}",
                    'notes' => "Check in regarding custom influencer marketing demo.",
                    'due_at' => Carbon::now()->addDays(rand(1, 5))->setTime(11, 30),
                    'priority' => $lead->priority,
                    'status' => 'pending',
                ]);
            }
        }

        // 10. Audit log entry
        AuditLog::create([
            'company_id' => $company->id,
            'user_id' => $admin->id,
            'action' => 'system.initialized',
            'entity_type' => 'Company',
            'entity_id' => $company->id,
            'description' => 'System initialized with default lead sources, team users, and verified leads.',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'InstaLead Agent/1.0',
            'created_at' => now(),
        ]);
    }
}
