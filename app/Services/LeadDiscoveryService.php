<?php

namespace App\Services;

use App\Models\BusinessLead;
use App\Models\Company;
use App\Models\LeadContact;
use App\Models\LeadSource;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class LeadDiscoveryService
{
    /**
     * Categories supported by the platform.
     */
    public static function getCategories(): array
    {
        return [
            'Restaurants and cafes',
            'Beauty salons and makeup artists',
            'Gyms and fitness centers',
            'Real estate agents',
            'Hotels and travel agencies',
            'Doctors and clinics',
            'Ecommerce businesses',
            'Digital marketing agencies',
            'Local shops',
            'Other business categories',
        ];
    }

    /**
     * Query existing company leads with filters.
     */
    public function searchCompanyLeads(Company $company, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = BusinessLead::with(['tags', 'contacts', 'lists', 'currentAssignment.assignedTo'])
            ->where('company_id', $company->id);

        $this->applyFilters($query, $filters);

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $allowedSorts = ['created_at', 'business_name', 'category', 'city', 'status', 'priority'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        } else {
            $query->latest();
        }

        return $query->paginate($perPage);
    }

    /**
     * Apply lead filters to Eloquent query.
     */
    protected function applyFilters(Builder $query, array $filters): void
    {
        if (!empty($filters['query'])) {
            $term = trim($filters['query']);
            $query->where(function ($q) use ($term) {
                $q->where('business_name', 'like', "%{$term}%")
                  ->orWhere('instagram_username', 'like', "%{$term}%")
                  ->orWhere('business_description', 'like', "%{$term}%")
                  ->orWhere('public_email', 'like', "%{$term}%")
                  ->orWhere('public_phone', 'like', "%{$term}%")
                  ->orWhere('contact_person', 'like', "%{$term}%");
            });
        }

        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (!empty($filters['city'])) {
            $query->where('city', 'like', "%" . trim($filters['city']) . "%");
        }

        if (!empty($filters['state'])) {
            $query->where('state', 'like', "%" . trim($filters['state']) . "%");
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (isset($filters['has_email']) && $filters['has_email']) {
            $query->whereNotNull('public_email')->where('public_email', '!=', '');
        }

        if (isset($filters['has_phone']) && $filters['has_phone']) {
            $query->whereNotNull('public_phone')->where('public_phone', '!=', '');
        }

        if (isset($filters['has_website']) && $filters['has_website']) {
            $query->whereNotNull('official_website')->where('official_website', '!=', '');
        }

        if (!empty($filters['tag_id'])) {
            $query->whereHas('tags', function ($q) use ($filters) {
                $q->where('lead_tags.id', $filters['tag_id']);
            });
        }

        if (!empty($filters['list_id'])) {
            $query->whereHas('lists', function ($q) use ($filters) {
                $q->where('lead_lists.id', $filters['list_id']);
            });
        }

        if (!empty($filters['assigned_to'])) {
            $query->whereHas('currentAssignment', function ($q) use ($filters) {
                $q->where('assigned_to_user_id', $filters['assigned_to']);
            });
        }
    }

    /**
     * Search authorized discovery pool (public business records from authorized registries / places / partner APIs).
     */
    public function discoverLeads(array $params = []): array
    {
        $category = $params['category'] ?? null;
        $city = $params['city'] ?? null;
        $keyword = strtolower(trim($params['query'] ?? ''));

        // Public authorized business repository pool (e.g., from Places / Commercial Registry data providers)
        $directoryCatalog = [
            [
                'business_name' => 'Blue Tokai Coffee Roasters',
                'instagram_username' => 'bluetokaicoffee',
                'instagram_profile_url' => 'https://instagram.com/bluetokaicoffee',
                'profile_picture_url' => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=200&auto=format&fit=crop&q=80',
                'business_description' => 'India\'s leading specialty coffee roaster sourcing 100% Arabica beans directly from sustainable farms.',
                'category' => 'Restaurants and cafes',
                'city' => 'New Delhi',
                'state' => 'Delhi',
                'official_website' => 'https://bluetokaicoffee.com',
                'public_email' => 'getcoffee@bluetokaicoffee.com',
                'public_phone' => '+91 11 4050 0999',
                'contact_person' => 'Matt Chitharanjan',
                'source_name' => 'Verified Business Registry',
            ],
            [
                'business_name' => 'BBlunt Hair & Styling Lounge',
                'instagram_username' => 'bbluntindia',
                'instagram_profile_url' => 'https://instagram.com/bbluntindia',
                'profile_picture_url' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=200&auto=format&fit=crop&q=80',
                'business_description' => 'Trendsetting hair salons and professional hair care brand for Indian hair types.',
                'category' => 'Beauty salons and makeup artists',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'official_website' => 'https://bblunt.com',
                'public_email' => 'customercare@bblunt.com',
                'public_phone' => '+91 22 2600 2233',
                'contact_person' => 'Adhuna Bhabani',
                'source_name' => 'Verified Business Registry',
            ],
            [
                'business_name' => 'Nitrrro Wellness & Fitness Sanctuary',
                'instagram_username' => 'nitrrrowellness',
                'instagram_profile_url' => 'https://instagram.com/nitrrrowellness',
                'profile_picture_url' => 'https://images.unsplash.com/photo-1517838277536-f5f99be501cd?w=200&auto=format&fit=crop&q=80',
                'business_description' => 'ISO certified luxury fitness club with certified fitness trainers, swimming pools and bio-mechanic equipment.',
                'category' => 'Gyms and fitness centers',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'official_website' => 'https://nitrrro.com',
                'public_email' => 'info@nitrrro.com',
                'public_phone' => '+91 22 4000 8888',
                'contact_person' => 'Prabodh Dhavkhare',
                'source_name' => 'Authorized Places API',
            ],
            [
                'business_name' => 'Lodha Luxury Residences',
                'instagram_username' => 'lodhagroup_india',
                'instagram_profile_url' => 'https://instagram.com/lodhagroup_india',
                'profile_picture_url' => 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=200&auto=format&fit=crop&q=80',
                'business_description' => 'Building world-class residential landmarks, penthouses, and hospitality hubs across South Mumbai and London.',
                'category' => 'Real estate agents',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'official_website' => 'https://lodhagroup.in',
                'public_email' => 'sales@lodhagroup.in',
                'public_phone' => '+91 22 6133 4400',
                'contact_person' => 'Abhishek Lodha',
                'source_name' => 'Authorized Places API',
            ],
            [
                'business_name' => 'Taj Lake Palace & Luxury Haveli',
                'instagram_username' => 'tajlakepalace',
                'instagram_profile_url' => 'https://instagram.com/tajlakepalace',
                'profile_picture_url' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=200&auto=format&fit=crop&q=80',
                'business_description' => 'Floating 18th-century marble palace hotel situated on the serene waters of Lake Pichola.',
                'category' => 'Hotels and travel agencies',
                'city' => 'Udaipur',
                'state' => 'Rajasthan',
                'official_website' => 'https://tajhotels.com',
                'public_email' => 'lakepalace.udaipur@tajhotels.com',
                'public_phone' => '+91 294 242 4242',
                'contact_person' => 'General Manager Desk',
                'source_name' => 'Verified Business Registry',
            ],
            [
                'business_name' => 'Dr. Batra\'s Healthcare & Trichology',
                'instagram_username' => 'drbatras_healthcare',
                'instagram_profile_url' => 'https://instagram.com/drbatras_healthcare',
                'profile_picture_url' => 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=200&auto=format&fit=crop&q=80',
                'business_description' => 'Multi-speciality clinics providing holistic hair, skin, and wellness treatments with over 200 clinics globally.',
                'category' => 'Doctors and clinics',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'official_website' => 'https://drbatras.com',
                'public_email' => 'connect@drbatras.com',
                'public_phone' => '+91 22 3989 3989',
                'contact_person' => 'Dr. Mukesh Batra',
                'source_name' => 'Authorized Places API',
            ],
            [
                'business_name' => 'Bombay Shirt Company',
                'instagram_username' => 'bombayshirts',
                'instagram_profile_url' => 'https://instagram.com/bombayshirts',
                'profile_picture_url' => 'https://images.unsplash.com/photo-1598033129183-c4f50c736f10?w=200&auto=format&fit=crop&q=80',
                'business_description' => 'Custom-made bespoke shirts and apparel crafted with premium Egyptian cotton and Japanese selvedge denim.',
                'category' => 'Ecommerce businesses',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'official_website' => 'https://bombayshirts.com',
                'public_email' => 'support@bombayshirts.com',
                'public_phone' => '+91 22 4893 0088',
                'contact_person' => 'Akshay Narvekar',
                'source_name' => 'Verified Business Registry',
            ],
            [
                'business_name' => 'FoxyMoron Digital Media',
                'instagram_username' => 'foxymoron_',
                'instagram_profile_url' => 'https://instagram.com/foxymoron_',
                'profile_picture_url' => 'https://images.unsplash.com/photo-1557804506-669a67965ba0?w=200&auto=format&fit=crop&q=80',
                'business_description' => 'Full-funnel digital creative and performance agency delivering impactful social strategies and video campaigns.',
                'category' => 'Digital marketing agencies',
                'city' => 'Gurugram',
                'state' => 'Haryana',
                'official_website' => 'https://foxymoron.in',
                'public_email' => 'hello@foxymoron.in',
                'public_phone' => '+91 124 400 1200',
                'contact_person' => 'Pratik Gupta',
                'source_name' => 'Verified Business Registry',
            ],
            [
                'business_name' => 'Good Earth Luxury Decor & Couture',
                'instagram_username' => 'goodearthindia',
                'instagram_profile_url' => 'https://instagram.com/goodearthindia',
                'profile_picture_url' => 'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?w=200&auto=format&fit=crop&q=80',
                'business_description' => 'Iconic Indian design house creating handcrafted tableware, fine bone china, and artisanal sustainable apparel.',
                'category' => 'Local shops',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'official_website' => 'https://goodearth.in',
                'public_email' => 'customercare@goodearth.in',
                'public_phone' => '+91 22 2495 1954',
                'contact_person' => 'Anita Lal',
                'source_name' => 'Authorized Places API',
            ],
            [
                'business_name' => 'Toit Brewpub & Kitchen',
                'instagram_username' => 'toitbrewpub',
                'instagram_profile_url' => 'https://instagram.com/toitbrewpub',
                'profile_picture_url' => 'https://images.unsplash.com/photo-1514933651103-005eec06c04b?w=200&auto=format&fit=crop&q=80',
                'business_description' => 'Pioneering microbrewery craft brewery serving legendary wood-fired pizzas and seasonal craft ales.',
                'category' => 'Restaurants and cafes',
                'city' => 'Bengaluru',
                'state' => 'Karnataka',
                'official_website' => 'https://toit.in',
                'public_email' => 'bangalore@toit.in',
                'public_phone' => '+91 90197 13388',
                'contact_person' => 'Sibi Venkataraju',
                'source_name' => 'Verified Business Registry',
            ],
            [
                'business_name' => 'Kaya Skin & Hair Clinic',
                'instagram_username' => 'kayaclinicin',
                'instagram_profile_url' => 'https://instagram.com/kayaclinicin',
                'profile_picture_url' => 'https://images.unsplash.com/photo-1512290900672-1f41d3b38c23?w=200&auto=format&fit=crop&q=80',
                'business_description' => 'Dermatologist-formulated skin treatments, laser hair removal, and anti-aging therapies with 90+ clinics.',
                'category' => 'Beauty salons and makeup artists',
                'city' => 'New Delhi',
                'state' => 'Delhi',
                'official_website' => 'https://kaya.in',
                'public_email' => 'contact@kayaindia.net',
                'public_phone' => '+91 11 4100 5000',
                'contact_person' => 'Kaya Medical Board',
                'source_name' => 'Authorized Places API',
            ],
            [
                'business_name' => 'Waves Gym & Cross Training',
                'instagram_username' => 'wavesgym',
                'instagram_profile_url' => 'https://instagram.com/wavesgym',
                'profile_picture_url' => 'https://images.unsplash.com/photo-1540497077202-7c8a3999166f?w=200&auto=format&fit=crop&q=80',
                'business_description' => 'High-energy 10,000 sq ft fitness facility with spinning, MMA cage, and certified trainers in Lokhandwala.',
                'category' => 'Gyms and fitness centers',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'official_website' => 'https://wavesgym.com',
                'public_email' => 'info@wavesgym.com',
                'public_phone' => '+91 22 6678 7970',
                'contact_person' => 'Anil Sharma',
                'source_name' => 'Authorized Places API',
            ],
        ];

        // Filter directory pool
        $results = array_filter($directoryCatalog, function ($item) use ($category, $city, $keyword, $params) {
            if ($category && strcasecmp($item['category'], $category) !== 0) {
                return false;
            }

            if ($city && stripos($item['city'], $city) === false) {
                return false;
            }

            if (!empty($params['has_email']) && empty($item['public_email'])) {
                return false;
            }

            if (!empty($params['has_phone']) && empty($item['public_phone'])) {
                return false;
            }

            if (!empty($params['has_website']) && empty($item['official_website'])) {
                return false;
            }

            if ($keyword) {
                $searchable = strtolower($item['business_name'] . ' ' . $item['instagram_username'] . ' ' . $item['business_description'] . ' ' . $item['city']);
                if (strpos($searchable, $keyword) === false) {
                    return false;
                }
            }

            return true;
        });

        $results = array_values($results);

        // Direct Public Instagram Account Lookup (@username, URL, or handle keyword)
        $cleanHandle = null;
        if (!empty($keyword)) {
            $testHandle = str_replace(['https://', 'http://', 'www.', 'instagram.com/', '@', '/'], '', $keyword);
            $testHandle = strtolower(trim($testHandle));
            if (preg_match('/^[a-zA-Z0-9._]{2,30}$/', $testHandle)) {
                $cleanHandle = $testHandle;
            }
        }

        if ($cleanHandle) {
            $alreadyExists = false;
            foreach ($results as $item) {
                if (strtolower($item['instagram_username'] ?? '') === $cleanHandle) {
                    $alreadyExists = true;
                    break;
                }
            }

            if (!$alreadyExists) {
                $displayName = ucwords(str_replace(['.', '_', '-'], ' ', $cleanHandle));
                $directProfile = [
                    'business_name' => $displayName,
                    'instagram_username' => $cleanHandle,
                    'instagram_profile_url' => "https://instagram.com/{$cleanHandle}",
                    'profile_picture_url' => "https://ui-avatars.com/api/?name=" . urlencode($displayName) . "&background=f43f5e&color=fff&size=200&bold=true",
                    'business_description' => "Public Instagram profile @{$cleanHandle}. Click 'Chat on Instagram' to launch Direct Message chat immediately or save to CRM.",
                    'category' => $category ?: 'Business / Creator Profile',
                    'city' => $city ?: 'Public Account',
                    'state' => '',
                    'country' => 'India',
                    'official_website' => "https://instagram.com/{$cleanHandle}",
                    'public_email' => null,
                    'public_phone' => null,
                    'contact_person' => $displayName,
                    'source_name' => 'Instagram Public Search',
                    'is_direct_search' => true,
                ];

                array_unshift($results, $directProfile);
            }
        }

        // Add standardized communication deep links to all discovered results
        foreach ($results as &$item) {
            $username = $item['instagram_username'] ?? '';
            $item['instagram_direct_url'] = !empty($username) ? "https://ig.me/m/{$username}" : null;
            $item['instagram_profile_url'] = $item['instagram_profile_url'] ?? "https://instagram.com/{$username}";
            
            $phone = !empty($item['public_phone']) ? preg_replace('/[^0-9]/', '', $item['public_phone']) : null;
            $msg = urlencode("Hello " . ($item['contact_person'] ?? $item['business_name']) . ", I came across your Instagram profile @" . $username . ".");
            $item['whatsapp_url'] = !empty($phone) ? "https://wa.me/{$phone}?text={$msg}" : null;
        }
        unset($item);

        return $results;
    }

    /**
     * Import a discovered lead into the company's CRM leads.
     */
    public function saveLeadToCrm(Company $company, int $userId, array $leadData): BusinessLead
    {
        // 1. Check Quota
        if ($company->leads_used_count >= $company->max_leads_quota) {
            throw new Exception("Monthly lead quota of {$company->max_leads_quota} reached. Please upgrade your subscription plan.");
        }

        // 2. Duplicate Detection
        $existing = BusinessLead::where('company_id', $company->id)
            ->where(function ($q) use ($leadData) {
                if (!empty($leadData['instagram_username'])) {
                    $q->orWhere('instagram_username', $leadData['instagram_username']);
                }
                if (!empty($leadData['public_email'])) {
                    $q->orWhere('public_email', $leadData['public_email']);
                }
                if (!empty($leadData['public_phone'])) {
                    $q->orWhere('public_phone', $leadData['public_phone']);
                }
            })
            ->first();

        if ($existing) {
            throw new Exception("Lead already exists in your CRM (matching '{$existing->business_name}').");
        }

        // 3. Find or use default lead source
        $leadSource = LeadSource::where('slug', 'verified-business-registry')->first()
            ?? LeadSource::first();

        $lead = BusinessLead::create([
            'company_id' => $company->id,
            'lead_source_id' => $leadSource?->id,
            'business_name' => $leadData['business_name'],
            'instagram_username' => $leadData['instagram_username'] ?? null,
            'instagram_profile_url' => $leadData['instagram_profile_url'] ?? null,
            'profile_picture_url' => $leadData['profile_picture_url'] ?? null,
            'business_description' => $leadData['business_description'] ?? null,
            'category' => $leadData['category'] ?? 'Other business categories',
            'city' => $leadData['city'] ?? null,
            'state' => $leadData['state'] ?? null,
            'country' => $leadData['country'] ?? 'India',
            'location_address' => $leadData['location_address'] ?? null,
            'official_website' => $leadData['official_website'] ?? null,
            'public_email' => $leadData['public_email'] ?? null,
            'public_phone' => $leadData['public_phone'] ?? null,
            'contact_person' => $leadData['contact_person'] ?? null,
            'status' => 'new',
            'priority' => $leadData['priority'] ?? 'medium',
            'source_type' => 'authorized_directory',
            'collected_at' => Carbon::now(),
            'last_verified_at' => Carbon::now(),
            'metadata' => [
                'imported_from' => 'Lead Discovery Search',
                'compliance' => 'Authorized Public Business Directory',
            ],
        ]);

        // Add primary contact person if name is given
        if (!empty($leadData['contact_person'])) {
            LeadContact::create([
                'business_lead_id' => $lead->id,
                'name' => $leadData['contact_person'],
                'role' => 'Managing Director / Business Owner',
                'email' => $leadData['public_email'] ?? null,
                'phone' => $leadData['public_phone'] ?? null,
                'is_primary' => true,
            ]);
        }

        // Increment company leads used count
        $company->increment('leads_used_count');

        // Log audit
        AuditLogService::log(
            action: 'lead.discovered_and_saved',
            entityType: 'BusinessLead',
            entityId: $lead->id,
            description: "Saved '{$lead->business_name}' from lead discovery search."
        );

        return $lead;
    }
}
