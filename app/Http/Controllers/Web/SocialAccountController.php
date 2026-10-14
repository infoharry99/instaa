<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ConnectedSocialAccount;
use App\Services\AuditLogService;
use App\Services\InstagramGraphApiService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SocialAccountController extends Controller
{
    protected InstagramGraphApiService $graphService;

    public function __construct(InstagramGraphApiService $graphService)
    {
        $this->graphService = $graphService;
    }

    public function index()
    {
        $user = Auth::user();
        $company = $user->company;

        $accounts = ConnectedSocialAccount::where('company_id', $company->id)->get();
        $metaAppConfigured = !empty(env('META_APP_ID')) && !empty(env('META_APP_SECRET'));

        return view('social.index', compact('accounts', 'metaAppConfigured', 'company'));
    }

    public function redirectToMeta(Request $request)
    {
        $state = Str::random(40);
        $request->session()->put('meta_oauth_state', $state);

        $authUrl = $this->graphService->getAuthorizationUrl($state);

        return redirect()->away($authUrl);
    }

    public function handleMetaCallback(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;

        if ($request->has('error')) {
            return redirect()->route('social.index')
                ->with('error', 'Meta Authorization was denied or canceled: ' . $request->get('error_description'));
        }

        $code = $request->get('code');
        $state = $request->get('state');

        if (!$code) {
            return redirect()->route('social.index')->with('error', 'Missing authorization code from Meta.');
        }

        try {
            // Exchange code for short-lived token
            $tokenData = $this->graphService->exchangeCodeForToken($code);
            $shortToken = $tokenData['access_token'];

            // Exchange for 60-day long-lived token
            $longTokenData = $this->graphService->getLongLivedToken($shortToken);
            $longToken = $longTokenData['access_token'];
            $expiresIn = $longTokenData['expires_in'] ?? 5184000; // ~60 days

            // Retrieve connected IG Business accounts
            $igAccounts = $this->graphService->getConnectedInstagramAccounts($longToken);

            if (empty($igAccounts)) {
                return redirect()->route('social.index')
                    ->with('warning', 'Connected to Meta, but no Instagram Business or Creator account was linked to your Facebook Pages. Please convert your Instagram account to Professional/Business and link it to a Page.');
            }

            // Ensure profile_picture_url is TEXT in database
            try {
                \Illuminate\Support\Facades\DB::statement('ALTER TABLE connected_social_accounts MODIFY profile_picture_url TEXT NULL');
            } catch (\Throwable $t) {
                // Ignore if DB user lacks ALTER privilege
            }

            foreach ($igAccounts as $acc) {
                $profilePic = $acc['profile_picture_url'] ?? null;
                $payload = [
                    'user_id' => $user->id,
                    'username' => $acc['username'],
                    'name' => $acc['name'],
                    'profile_picture_url' => $profilePic,
                    'account_type' => 'BUSINESS',
                    'access_token' => $acc['page_token'],
                    'token_expires_at' => Carbon::now()->addSeconds($expiresIn),
                    'is_connected' => true,
                    'scopes' => ['instagram_basic', 'pages_read_engagement', 'business_management'],
                    'meta_data' => [
                        'followers_count' => $acc['followers_count'],
                        'follows_count' => $acc['follows_count'],
                        'media_count' => $acc['media_count'],
                        'page_id' => $acc['page_id'],
                        'page_name' => $acc['page_name'],
                        'full_profile_picture_url' => $profilePic,
                    ],
                ];

                try {
                    ConnectedSocialAccount::updateOrCreate(
                        [
                            'company_id' => $company->id,
                            'platform' => 'instagram',
                            'account_id' => $acc['ig_id'],
                        ],
                        $payload
                    );
                } catch (\Throwable $dbEx) {
                    // Truncation fallback if column is still VARCHAR(255) and ALTER failed
                    $payload['profile_picture_url'] = !empty($profilePic) ? substr($profilePic, 0, 250) : null;
                    ConnectedSocialAccount::updateOrCreate(
                        [
                            'company_id' => $company->id,
                            'platform' => 'instagram',
                            'account_id' => $acc['ig_id'],
                        ],
                        $payload
                    );
                }
            }

            AuditLogService::log(
                action: 'oauth.instagram_connected',
                description: "Connected Instagram Business account(s) for company '{$company->name}'."
            );

            return redirect()->route('social.index')
                ->with('success', 'Official Instagram Business Account connected successfully!');
        } catch (Exception $e) {
            return redirect()->route('social.index')->with('error', $e->getMessage());
        }
    }

    public function disconnect(ConnectedSocialAccount $account)
    {
        $user = Auth::user();
        if ($account->company_id !== $user->company_id) {
            abort(403);
        }

        $accUsername = $account->username;
        $account->delete();

        AuditLogService::log(
            action: 'oauth.instagram_disconnected',
            description: "Disconnected Instagram account '@{$accUsername}'."
        );

        return back()->with('info', "Instagram account '@{$accUsername}' has been disconnected.");
    }

    public function connectDevelopmentAccount(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;

        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'account_type' => ['required', 'in:BUSINESS,CREATOR'],
            'followers_count' => ['nullable', 'integer'],
        ]);

        $igUsername = ltrim($validated['username'], '@');

        ConnectedSocialAccount::updateOrCreate(
            [
                'company_id' => $company->id,
                'platform' => 'instagram',
                'account_id' => 'dev_' . md5($igUsername),
            ],
            [
                'user_id' => $user->id,
                'username' => $igUsername,
                'name' => $validated['name'],
                'profile_picture_url' => "https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=200&auto=format&fit=crop&q=80",
                'account_type' => $validated['account_type'],
                'access_token' => 'mock_dev_token_' . Str::random(32),
                'token_expires_at' => Carbon::now()->addDays(60),
                'is_connected' => true,
                'scopes' => ['instagram_basic', 'instagram_manage_messages'],
                'meta_data' => [
                    'followers_count' => $validated['followers_count'] ?? 12500,
                    'follows_count' => 340,
                    'media_count' => 88,
                    'connection_mode' => 'Development / Sandbox Verified',
                ],
            ]
        );

        AuditLogService::log(
            action: 'oauth.dev_account_linked',
            description: "Linked development Instagram account '@{$igUsername}'."
        );

        return back()->with('success', "Instagram account '@{$igUsername}' linked successfully!");
    }
}
