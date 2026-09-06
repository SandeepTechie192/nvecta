<?php

// Configure application default keys and environment variables for Vercel
$appKey = getenv('APP_KEY') ?: 'base64:wAdGygv1Lq1q8Nn2GRiYjdsncQiLMxn/Y8MHRVwWUC4=';
putenv("APP_KEY={$appKey}");
$_ENV['APP_KEY'] = $appKey;

$openAiBaseUrl = getenv('OPENAI_BASE_URL') ?: 'https://openrouter.ai/api/v1';
putenv("OPENAI_BASE_URL={$openAiBaseUrl}");
$_ENV['OPENAI_BASE_URL'] = $openAiBaseUrl;

$openAiSummaryModel = getenv('OPENAI_SUMMARY_MODEL') ?: 'openrouter/free';
putenv("OPENAI_SUMMARY_MODEL={$openAiSummaryModel}");
$_ENV['OPENAI_SUMMARY_MODEL'] = $openAiSummaryModel;

putenv('LOG_CHANNEL=stderr');
$_ENV['LOG_CHANNEL'] = 'stderr';

putenv('LOG_PATH=/tmp/laravel.log');
$_ENV['LOG_PATH'] = '/tmp/laravel.log';

putenv('VIEW_COMPILED_PATH=/tmp');
$_ENV['VIEW_COMPILED_PATH'] = '/tmp';

putenv('APP_CONFIG_CACHE=/tmp/config.php');
$_ENV['APP_CONFIG_CACHE'] = '/tmp/config.php';

putenv('APP_SERVICES_CACHE=/tmp/services.php');
$_ENV['APP_SERVICES_CACHE'] = '/tmp/services.php';

putenv('APP_PACKAGES_CACHE=/tmp/packages.php');
$_ENV['APP_PACKAGES_CACHE'] = '/tmp/packages.php';

putenv('APP_ROUTES_CACHE=/tmp/routes.php');
$_ENV['APP_ROUTES_CACHE'] = '/tmp/routes.php';

putenv('APP_EVENTS_CACHE=/tmp/events.php');
$_ENV['APP_EVENTS_CACHE'] = '/tmp/events.php';

// Prepare writable /tmp SQLite DB for Vercel Serverless
$dbPath = '/tmp/database.sqlite';
if (!file_exists($dbPath) && file_exists(__DIR__ . '/../database/database.sqlite')) {
    @copy(__DIR__ . '/../database/database.sqlite', $dbPath);
}

putenv('DB_CONNECTION=sqlite');
$_ENV['DB_CONNECTION'] = 'sqlite';

putenv("DB_DATABASE={$dbPath}");
$_ENV['DB_DATABASE'] = $dbPath;

// Forward Vercel request to Laravel public entrypoint
require __DIR__ . '/../public/index.php';
