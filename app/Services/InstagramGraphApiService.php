<?php

namespace App\Services;

use App\Models\ConnectedSocialAccount;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InstagramGraphApiService
{
    protected string $appId;
    protected string $appSecret;
    protected string $redirectUri;
    protected string $graphVersion;
    protected string $graphBaseUrl;

    public function __construct()
    {
        $this->appId = config('services.meta.app_id', env('META_APP_ID', ''));
        $this->appSecret = config('services.meta.app_secret', env('META_APP_SECRET', ''));
        $this->redirectUri = config('services.meta.redirect_uri', env('META_REDIRECT_URI', 'http://localhost:8000/oauth/instagram/callback'));
        $this->graphVersion = config('services.meta.graph_version', env('META_GRAPH_VERSION', 'v21.0'));
        $this->graphBaseUrl = "https://graph.facebook.com/{$this->graphVersion}";
    }

    /**
     * Generate Meta OAuth URL with required professional permissions.
     */
    public function getAuthorizationUrl(string $state = ''): string
    {
        $scopes = [
            'instagram_basic',
            'instagram_manage_insights',
            'pages_show_list',
            'pages_read_engagement',
            'business_management',
        ];

        $params = http_build_query([
            'client_id' => $this->appId,
            'redirect_uri' => $this->redirectUri,
            'scope' => implode(',', $scopes),
            'response_type' => 'code',
            'state' => $state,
        ]);

        return "https://www.facebook.com/{$this->graphVersion}/dialog/oauth?{$params}";
    }

    /**
     * Exchange short-lived authorization code for access token.
     */
    public function exchangeCodeForToken(string $code): array
    {
        $response = Http::get("{$this->graphBaseUrl}/oauth/access_token", [
            'client_id' => $this->appId,
            'client_secret' => $this->appSecret,
            'redirect_uri' => $this->redirectUri,
            'code' => $code,
        ]);

        if ($response->failed()) {
            Log::error('Meta OAuth Token Exchange Failed', ['body' => $response->json()]);
            throw new Exception($response->json('error.message', 'Failed to exchange authorization code with Meta.'));
        }

        return $response->json();
    }

    /**
     * Exchange short-lived token for 60-day long-lived token.
     */
    public function getLongLivedToken(string $shortLivedToken): array
    {
        $response = Http::get("{$this->graphBaseUrl}/oauth/access_token", [
            'grant_type' => 'fb_exchange_token',
            'client_id' => $this->appId,
            'client_secret' => $this->appSecret,
            'fb_exchange_token' => $shortLivedToken,
        ]);

        if ($response->failed()) {
            Log::error('Meta Long Lived Token Exchange Failed', ['body' => $response->json()]);
            throw new Exception($response->json('error.message', 'Failed to retrieve long-lived Meta token.'));
        }

        return $response->json();
    }

    /**
     * Fetch connected Facebook Pages and their linked Instagram Business accounts.
     */
    public function getConnectedInstagramAccounts(string $userAccessToken): array
    {
        $response = Http::get("{$this->graphBaseUrl}/me/accounts", [
            'access_token' => $userAccessToken,
            'fields' => 'id,name,access_token,instagram_business_account{id,username,name,profile_picture_url,biography,website,followers_count,follows_count,media_count}',
        ]);

        if ($response->failed()) {
            throw new Exception($response->json('error.message', 'Could not fetch accounts from Meta.'));
        }

        $pages = $response->json('data', []);
        $accounts = [];

        foreach ($pages as $page) {
            if (isset($page['instagram_business_account'])) {
                $ig = $page['instagram_business_account'];
                $accounts[] = [
                    'page_id' => $page['id'],
                    'page_name' => $page['name'],
                    'ig_id' => $ig['id'],
                    'username' => $ig['username'] ?? '',
                    'name' => $ig['name'] ?? $page['name'],
                    'profile_picture_url' => $ig['profile_picture_url'] ?? null,
                    'biography' => $ig['biography'] ?? null,
                    'website' => $ig['website'] ?? null,
                    'followers_count' => $ig['followers_count'] ?? 0,
                    'follows_count' => $ig['follows_count'] ?? 0,
                    'media_count' => $ig['media_count'] ?? 0,
                    'page_token' => $page['access_token'] ?? $userAccessToken,
                ];
            }
        }

        return $accounts;
    }

    /**
     * Get details for an Instagram Business Account via Graph API.
     */
    public function getBusinessAccountDetails(string $igUserId, string $accessToken): array
    {
        $response = Http::get("{$this->graphBaseUrl}/{$igUserId}", [
            'access_token' => $accessToken,
            'fields' => 'id,username,name,biography,website,profile_picture_url,followers_count,follows_count,media_count',
        ]);

        if ($response->failed()) {
            Log::warning("Graph API query failed for {$igUserId}", ['res' => $response->json()]);
            return [];
        }

        return $response->json();
    }
}
