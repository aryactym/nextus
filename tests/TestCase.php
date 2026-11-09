<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Run migrations untuk test DB
        $this->artisan('migrate:fresh', ['--seed' => true]);
    }
}
