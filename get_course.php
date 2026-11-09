<?php

use App\Domain\Catalog\Models\Course;

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$course = Course::find(1);
echo 'Course ID 1: '.$course->slug.' - '.$course->getTranslation('title', 'id').PHP_EOL;
