<?php

declare(strict_types=1);

use Database\Seeders\PageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->seed(PageSeeder::class);
});

dataset('static pages', [
    'tentang' => fn () => route('tentang'),
    'jadwal' => fn () => route('jadwal'),
    'panduan' => fn () => route('panduan'),
    'syarat' => fn () => route('syarat'),
    'privasi' => fn () => route('privasi'),
    'verifikasi' => fn () => route('verifikasi'),
]);

it('renders static pages from docs templates', function (string $name) {
    $this->get($name)->assertOk()->assertSee('NextUs');
})->with('static pages');
