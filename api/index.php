<?php

// Prepare writable /tmp SQLite DB for Vercel Serverless
$dbPath = '/tmp/database.sqlite';
if (!file_exists($dbPath) && file_exists(__DIR__ . '/../database/database.sqlite')) {
    @copy(__DIR__ . '/../database/database.sqlite', $dbPath);
}

// Forward Vercel request to Laravel public entrypoint
require __DIR__ . '/../public/index.php';
