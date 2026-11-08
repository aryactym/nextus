<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Courses WITH soft delete:\n";
$all = \App\Domain\Catalog\Models\Course::withTrashed()->withoutGlobalScope('organization')->get(['id', 'uuid', 'slug', 'deleted_at']);
foreach ($all as $c) {
  echo '  ID ' . $c->id . ': ' . $c->slug . ' (deleted: ' . ($c->deleted_at ? 'YES' : 'NO') . ')' . PHP_EOL;
}
