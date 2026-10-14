<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DeployController extends Controller
{
    /**
     * Secret key verification to prevent unauthorized public access on shared hosting.
     */
    protected function verifySecret(Request $request): bool
    {
        $expectedKey = env('DEPLOY_SECRET_KEY', 'instalead2026');
        $providedKey = $request->get('key', $request->header('X-DEPLOY-KEY'));

        return !empty($providedKey) && hash_equals($expectedKey, (string)$providedKey);
    }

    /**
     * Dashboard / Status page for shared hosting operations.
     */
    public function index(Request $request)
    {
        if (!$this->verifySecret($request)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Please provide valid ?key=YOUR_SECRET_KEY in the URL (configured in .env as DEPLOY_SECRET_KEY or use default "instalead2026").',
            ], 403);
        }

        // Test DB connection
        $dbStatus = 'Not Connected';
        $tables = [];
        try {
            DB::connection()->getPdo();
            $dbStatus = 'Connected to: ' . DB::connection()->getDatabaseName();
            $tables = DB::select('SHOW TABLES');
        } catch (\Exception $e) {
            $dbStatus = 'Connection Failed: ' . $e->getMessage();
        }

        return response()->json([
            'status' => 'ready',
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'database_status' => $dbStatus,
            'tables_count' => count($tables),
            'storage_writable' => is_writable(storage_path()),
            'bootstrap_cache_writable' => is_writable(base_path('bootstrap/cache')),
            'available_commands' => [
                'Run Migrations' => url('/deploy/migrate?key=' . $request->get('key')),
                'Run Migrations & Seed' => url('/deploy/migrate-seed?key=' . $request->get('key')),
                'Storage Link' => url('/deploy/storage-link?key=' . $request->get('key')),
                'Clear Caches' => url('/deploy/clear-cache?key=' . $request->get('key')),
                'Composer Info / Exec' => url('/deploy/composer?key=' . $request->get('key')),
            ],
        ]);
    }

    /**
     * Run Artisan migrate.
     */
    public function migrate(Request $request)
    {
        if (!$this->verifySecret($request)) {
            return response()->json(['error' => 'Unauthorized key.'], 403);
        }

        try {
            Artisan::call('migrate', ['--force' => true]);
            $output = Artisan::output();

            return response()->json([
                'success' => true,
                'command' => 'php artisan migrate --force',
                'output' => $output ?: 'Migrations ran successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Run Artisan migrate --seed.
     */
    public function migrateSeed(Request $request)
    {
        if (!$this->verifySecret($request)) {
            return response()->json(['error' => 'Unauthorized key.'], 403);
        }

        try {
            Artisan::call('migrate', ['--force' => true]);
            $migrateOutput = Artisan::output();

            Artisan::call('db:seed', ['--force' => true]);
            $seedOutput = Artisan::output();

            return response()->json([
                'success' => true,
                'command' => 'php artisan migrate --seed --force',
                'output' => $migrateOutput . "\n" . $seedOutput,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Run Artisan storage:link.
     */
    public function storageLink(Request $request)
    {
        if (!$this->verifySecret($request)) {
            return response()->json(['error' => 'Unauthorized key.'], 403);
        }

        try {
            Artisan::call('storage:link');
            $output = Artisan::output();

            return response()->json([
                'success' => true,
                'command' => 'php artisan storage:link',
                'output' => $output ?: 'Storage link created.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Clear application caches.
     */
    public function clearCache(Request $request)
    {
        if (!$this->verifySecret($request)) {
            return response()->json(['error' => 'Unauthorized key.'], 403);
        }

        try {
            Artisan::call('optimize:clear');
            $output = Artisan::output();

            return response()->json([
                'success' => true,
                'command' => 'php artisan optimize:clear',
                'output' => $output ?: 'Caches cleared.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Composer Install execution handler for shared hosting.
     */
    public function composerInstall(Request $request)
    {
        if (!$this->verifySecret($request)) {
            return response()->json(['error' => 'Unauthorized key.'], 403);
        }

        // Check if shell execution functions are allowed on this shared host
        $disabledFunctions = explode(',', ini_get('disable_functions'));
        $disabledFunctions = array_map('trim', $disabledFunctions);

        if (in_array('exec', $disabledFunctions) && in_array('shell_exec', $disabledFunctions)) {
            return response()->json([
                'success' => false,
                'notice' => 'On shared hosting, PHP exec() and shell_exec() are disabled by default for security.',
                'solution' => 'Best Practice for Shared Hosting: Keep the complete "vendor" directory uploaded to your server so you do not need to run composer install on the server.',
            ]);
        }

        // Attempt running composer if permitted
        $baseDir = base_path();
        $output = [];
        $returnVar = null;

        $command = "cd {$baseDir} && composer install --no-dev --optimize-autoloader 2>&1";
        if (function_exists('exec')) {
            exec($command, $output, $returnVar);
            return response()->json([
                'success' => $returnVar === 0,
                'command' => $command,
                'return_code' => $returnVar,
                'output' => implode("\n", $output),
            ]);
        } elseif (function_exists('shell_exec')) {
            $output = shell_exec($command);
            return response()->json([
                'success' => true,
                'command' => $command,
                'output' => $output,
            ]);
        }

        return response()->json(['error' => 'No shell execution functions available.'], 500);
    }

    /**
     * Clear all seeded dummy leads, mock social accounts, and sample CRM data.
     */
    public function clearDummyData(Request $request)
    {
        if (!$this->verifySecret($request)) {
            return response()->json(['error' => 'Unauthorized key.'], 403);
        }

        try {
            $deletedSocial = \App\Models\ConnectedSocialAccount::where('access_token', 'like', 'EAAXmock%')
                ->orWhere('username', 'omnimediagrowth')
                ->delete();

            $deletedLeads = \App\Models\BusinessLead::query()->delete();
            $deletedLists = \App\Models\LeadList::query()->delete();
            $deletedFollowups = \App\Models\FollowUp::query()->delete();
            $deletedLogs = \App\Models\CommunicationLog::query()->delete();

            \App\Models\Company::query()->update(['leads_used_count' => 0]);

            return response()->json([
                'success' => true,
                'message' => 'Dummy and sample content has been cleared completely.',
                'cleared' => [
                    'mock_social_accounts_removed' => $deletedSocial,
                    'dummy_leads_removed' => $deletedLeads,
                    'dummy_lists_removed' => $deletedLists,
                    'dummy_followups_removed' => $deletedFollowups,
                    'dummy_communication_logs_removed' => $deletedLogs,
                    'leads_counter_reset' => 0,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
