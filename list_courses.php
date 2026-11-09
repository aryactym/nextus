<?php

use App\Domain\Catalog\Models\Course;

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$courses = Course::withoutGlobalScope('organization')->get(['id', 'uuid', 'slug', 'title']);
echo 'Total courses: '.$courses->count().PHP_EOL.PHP_EOL;
foreach ($courses as $c) {
    echo $c->slug.' (ID: '.$c->id.', UUID: '.$c->uuid.')'.PHP_EOL;
}
