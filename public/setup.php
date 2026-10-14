<?php
/**
 * Standalone Shared Hosting Setup Wizard
 * Runs without Composer or Laravel framework dependencies.
 */

error_reporting(E_ALL);
ini_ini: ini_set('display_errors', 1);

$secretKey = 'instalead2026';
$providedKey = $_GET['key'] ?? $_POST['key'] ?? '';
$isAuthorized = ($providedKey === $secretKey);

$baseDir = dirname(__DIR__);
$envFile = $baseDir . '/.env';
$vendorDir = $baseDir . '/vendor';
$storageDir = $baseDir . '/storage';
$cacheDir = $baseDir . '/bootstrap/cache';

$message = '';
$messageType = '';

// Handle actions
if ($isAuthorized && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Action 1: Create .env from .env.example
    if ($action === 'create_env') {
        if (!file_exists($envFile) && file_exists($baseDir . '/.env.example')) {
            copy($baseDir . '/.env.example', $envFile);
            $message = '.env created from .env.example successfully!';
            $messageType = 'success';
        }
    }

    // Action 2: Update Database settings in .env
    if ($action === 'save_db_env') {
        $dbHost = $_POST['db_host'] ?? '127.0.0.1';
        $dbPort = $_POST['db_port'] ?? '3306';
        $dbName = $_POST['db_name'] ?? '';
        $dbUser = $_POST['db_user'] ?? '';
        $dbPass = $_POST['db_pass'] ?? '';

        if (file_exists($envFile)) {
            $content = file_get_contents($envFile);
            $content = preg_replace('/DB_HOST=.*/', "DB_HOST={$dbHost}", $content);
            $content = preg_replace('/DB_PORT=.*/', "DB_PORT={$dbPort}", $content);
            $content = preg_replace('/DB_DATABASE=.*/', "DB_DATABASE={$dbName}", $content);
            $content = preg_replace('/DB_USERNAME=.*/', "DB_USERNAME={$dbUser}", $content);
            $content = preg_replace('/DB_PASSWORD=.*/', "DB_PASSWORD=\"{$dbPass}\"", $content);
            file_put_contents($envFile, $content);
            $message = 'Database credentials saved to .env!';
            $messageType = 'success';
        }
    }

    // Action 3: Import SQL Dump into Database using native PDO
    if ($action === 'import_sql') {
        $sqlFile = $baseDir . '/database/instagram_crm.sql';
        $dbHost = $_POST['db_host'] ?? 'localhost';
        $dbName = $_POST['db_name'] ?? '';
        $dbUser = $_POST['db_user'] ?? '';
        $dbPass = $_POST['db_pass'] ?? '';

        if (!file_exists($sqlFile)) {
            $message = 'SQL file database/instagram_crm.sql not found!';
            $messageType = 'error';
        } else {
            try {
                $pdo = new PDO("mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4", $dbUser, $dbPass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                ]);

                $sql = file_get_contents($sqlFile);
                $pdo->exec($sql);

                $message = 'Database tables & initial seed data imported successfully via SQL file!';
                $messageType = 'success';
            } catch (Exception $e) {
                $message = 'Database Import Error: ' . $e->getMessage();
                $messageType = 'error';
            }
        }
    }

    // Action 4: Fix permissions
    if ($action === 'fix_permissions') {
        @chmod($storageDir, 0775);
        @chmod($cacheDir, 0775);
        $message = 'Permissions updated for storage and cache directories.';
        $messageType = 'success';
    }
}

// Diagnostics
$phpVersion = PHP_VERSION;
$phpOk = version_compare($phpVersion, '8.2.0', '>=');
$hasPdo = extension_loaded('pdo_mysql');
$hasMbstring = extension_loaded('mbstring');
$hasOpenssl = extension_loaded('openssl');
$hasVendor = file_exists($vendorDir . '/autoload.php');
$hasEnv = file_exists($envFile);
$isStorageWritable = is_writable($storageDir);
$isCacheWritable = is_writable($cacheDir);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shared Hosting Diagnostic & Setup Wizard</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #0b0f19; color: #f1f5f9; padding: 30px 15px; margin: 0; }
        .container { max-width: 760px; margin: 0 auto; }
        .card { background: #131b2e; border: 1px solid #1e293b; border-radius: 16px; padding: 24px; margin-bottom: 24px; box-shadow: 0 10px 25px rgba(0,0,0,0.4); }
        h1 { font-size: 24px; margin-top: 0; color: #fff; display: flex; align-items: center; gap: 8px; }
        h2 { font-size: 16px; margin-top: 0; margin-bottom: 16px; color: #cbd5e1; border-bottom: 1px solid #1e293b; padding-bottom: 10px; }
        .check-item { display: flex; align-items: center; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #1e293b55; font-size: 13px; }
        .badge { padding: 4px 10px; border-radius: 999px; font-size: 11px; font-weight: bold; }
        .badge-success { background: #064e3b; color: #34d399; }
        .badge-error { background: #881337; color: #f43f5e; }
        .badge-warning { background: #78350f; color: #fbbf24; }
        .msg { padding: 14px; border-radius: 10px; margin-bottom: 20px; font-size: 13px; }
        .msg-success { background: #064e3b; color: #34d399; border: 1px solid #059669; }
        .msg-error { background: #881337; color: #f43f5e; border: 1px solid #e11d48; }
        input[type="text"], input[type="password"] { width: 100%; box-sizing: border-box; background: #090d16; border: 1px solid #334155; color: #fff; padding: 10px; border-radius: 8px; font-size: 13px; margin-top: 4px; margin-bottom: 12px; }
        button { background: linear-gradient(135deg, #f43f5e, #a855f7); color: #fff; border: 0; padding: 10px 18px; border-radius: 8px; font-weight: 600; font-size: 13px; cursor: pointer; transition: opacity 0.2s; }
        button:hover { opacity: 0.9; }
        code { background: #020617; color: #38bdf8; padding: 2px 6px; border-radius: 4px; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>🛠️ InstaLead Shared Hosting Diagnostics</h1>
            <p style="font-size: 13px; color: #94a3b8; line-height: 1.6;">
                This standalone tool inspects your hosting environment and allows you to configure database credentials and import tables even without SSH access.
            </p>

            <?php if (!empty($message)): ?>
                <div class="msg msg-<?= $messageType ?>"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <h2>1. Server Health Checklist</h2>
            <div class="check-item">
                <span>PHP Version (&ge; 8.2 required)</span>
                <?php if ($phpOk): ?>
                    <span class="badge badge-success"><?= $phpVersion ?> (Compatible)</span>
                <?php else: ?>
                    <span class="badge badge-error"><?= $phpVersion ?> (Switch to PHP 8.2 in cPanel MultiPHP Manager)</span>
                <?php endif; ?>
            </div>

            <div class="check-item">
                <span>Vendor Folder (<code>/vendor/autoload.php</code>)</span>
                <?php if ($hasVendor): ?>
                    <span class="badge badge-success">Installed</span>
                <?php else: ?>
                    <span class="badge badge-error">Missing (Upload vendor.zip and extract here)</span>
                <?php endif; ?>
            </div>

            <div class="check-item">
                <span>Environment File (<code>.env</code>)</span>
                <?php if ($hasEnv): ?>
                    <span class="badge badge-success">Exists</span>
                <?php else: ?>
                    <span class="badge badge-warning">Missing</span>
                <?php endif; ?>
            </div>

            <div class="check-item">
                <span>MySQL Extension (<code>pdo_mysql</code>)</span>
                <span class="badge <?= $hasPdo ? 'badge-success' : 'badge-error' ?>"><?= $hasPdo ? 'Active' : 'Missing' ?></span>
            </div>

            <div class="check-item">
                <span>Storage Directory Writable (<code>storage/</code>)</span>
                <span class="badge <?= $isStorageWritable ? 'badge-success' : 'badge-error' ?>"><?= $isStorageWritable ? 'Writable' : 'Permission Denied (chmod 775)' ?></span>
            </div>

            <div class="check-item">
                <span>Cache Directory Writable (<code>bootstrap/cache/</code>)</span>
                <span class="badge <?= $isCacheWritable ? 'badge-success' : 'badge-error' ?>"><?= $isCacheWritable ? 'Writable' : 'Permission Denied (chmod 775)' ?></span>
            </div>
        </div>

        <!-- Authorized Actions -->
        <div class="card">
            <h2>2. Quick Actions & Database Setup</h2>

            <?php if (!$isAuthorized): ?>
                <p style="font-size: 13px; color: #f43f5e;">
                    Enter deployment secret key to unlock actions (default: <code>instalead2026</code>):
                </p>
                <form method="GET">
                    <input type="password" name="key" placeholder="Enter secret key..." style="max-width: 300px;">
                    <br>
                    <button type="submit">Unlock Actions</button>
                </form>
            <?php else: ?>

                <?php if (!$hasEnv): ?>
                    <form method="POST" style="margin-bottom: 20px;">
                        <input type="hidden" name="key" value="<?= htmlspecialchars($providedKey) ?>">
                        <input type="hidden" name="action" value="create_env">
                        <button type="submit">Generate .env from .env.example</button>
                    </form>
                <?php endif; ?>

                <form method="POST" style="margin-top: 15px;">
                    <input type="hidden" name="key" value="<?= htmlspecialchars($providedKey) ?>">
                    <input type="hidden" name="action" value="import_sql">
                    <h3 style="font-size: 14px; margin-bottom: 8px;">Import <code>database/instagram_crm.sql</code> into Live MySQL:</h3>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <div>
                            <label style="font-size: 11px; color: #94a3b8;">Database Host</label>
                            <input type="text" name="db_host" value="localhost">
                        </div>
                        <div>
                            <label style="font-size: 11px; color: #94a3b8;">Database Name</label>
                            <input type="text" name="db_name" placeholder="e.g. u123456_instacrm" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <div>
                            <label style="font-size: 11px; color: #94a3b8;">Database User</label>
                            <input type="text" name="db_user" placeholder="e.g. u123456_dbuser" required>
                        </div>
                        <div>
                            <label style="font-size: 11px; color: #94a3b8;">Database Password</label>
                            <input type="password" name="db_pass">
                        </div>
                    </div>

                    <button type="submit">Execute SQL Import</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
