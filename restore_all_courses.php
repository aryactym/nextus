<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Restore semua courses yang soft-delete
$deleted = \App\Domain\Catalog\Models\Course::onlyTrashed()->get();
echo "Courses yang di-restore:\n";
foreach ($deleted as $c) {
  $c->restore();
  echo "  - " . $c->slug . " (ID " . $c->id . ")\n";
}

echo "\nTotal di-restore: " . $deleted->count() . "\n";
