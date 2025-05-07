<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // ایجاد نقش‌های اصلی سیستم
        Role::create(['name' => 'owner']);
        Role::create(['name' => 'financial']);
        Role::create(['name' => 'reception']);
        Role::create(['name' => 'technician']);
        Role::create(['name' => 'customer']);
        
        $this->command->info('Roles seeded successfully!');
    }
}