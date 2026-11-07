<?php

declare(strict_types=1);

use App\Domain\Catalog\Models\Course;
use Database\Seeders\DemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('seeds fifteen published demo courses', function () {
    Storage::fake('uploads');

    $this->seed(DemoSeeder::class);

    expect(Course::withoutGlobalScope('organization')->count())->toBe(15)
        ->and(Course::withoutGlobalScope('organization')->where('status', 'published')->count())->toBe(15);
});

it('shows the second catalog page after seeding', function () {
    Storage::fake('uploads');
    $this->seed(DemoSeeder::class);

    $this->get(route('catalog.index'))
        ->assertOk()
        ->assertSee('Menampilkan 12 dari 15 kursus');

    $this->get(route('catalog.index', ['page' => 2]))
        ->assertOk()
        ->assertSee('Menampilkan 3 dari 15 kursus');
});
