<?php

namespace Tests;

use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed roles and permissions for all tests
        // Using firstOrCreate in the seeder prevents duplicate key errors
        $this->seed(RolesAndPermissionsSeeder::class);
    }
}
