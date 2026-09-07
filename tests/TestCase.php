<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // تشغيل Seeder الأدوار والصلاحيات قبل كل اختبار
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }
}