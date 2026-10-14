<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Pre-flight check for Shared Hosting: Verify vendor autoloader exists
if (!file_exists(__DIR__.'/../vendor/autoload.php')) {
    http_response_code(500);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Setup Required - InstaLead CRM</title>
        <style>
            body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #0f172a; color: #f8fafc; margin: 0; padding: 40px 20px; display: flex; align-items: center; justify-content: center; min-height: 80vh; }
            .card { background: #1e293b; border: 1px solid #334155; border-radius: 16px; padding: 32px; max-width: 620px; width: 100%; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.5); }
            h1 { color: #f43f5e; margin-top: 0; font-size: 22px; display: flex; align-items: center; gap: 8px; }
            p { font-size: 14px; line-height: 1.6; color: #cbd5e1; }
            ol { font-size: 14px; line-height: 1.8; color: #e2e8f0; padding-left: 20px; }
            code { background: #090d16; color: #38bdf8; padding: 2px 6px; border-radius: 6px; font-size: 13px; font-family: monospace; }
            .badge { display: inline-block; background: #f43f5e22; color: #f43f5e; padding: 4px 10px; border-radius: 999px; font-size: 12px; font-weight: 600; margin-bottom: 12px; }
            .btn { display: inline-block; background: linear-gradient(135deg, #f43f5e, #9333ea); color: #fff; padding: 10px 20px; border-radius: 10px; text-decoration: none; font-size: 13px; font-weight: 600; margin-top: 15px; }
        </style>
    </head>
    <body>
        <div class="card">
            <span class="badge">Missing Dependencies Detected</span>
            <h1>📦 The "vendor" folder was not found on your server</h1>
            <p>Git excludes the <code>vendor/</code> directory by default, so cloning from GitHub does not automatically include third-party PHP packages.</p>
            <p><strong>To resolve this in 2 minutes:</strong></p>
            <ol>
                <li>On your local computer, zip the <code>vendor</code> folder inside <code>d:\InstagramProject</code>.</li>
                <li>In your cPanel / hosting File Manager, upload and extract <code>vendor.zip</code> into your project directory.</li>
                <li>Make sure you also create or upload your <code>.env</code> file.</li>
            </ol>
            <p style="margin-top: 20px; font-size: 12px; color: #94a3b8;">
                Current PHP Version: <strong><?= PHP_VERSION ?></strong> (Required: PHP 8.2+)
            </p>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Pre-flight check: Verify .env exists
if (!file_exists(__DIR__.'/../.env')) {
    if (file_exists(__DIR__.'/../.env.example')) {
        copy(__DIR__.'/../.env.example', __DIR__.'/../.env');
    }
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
