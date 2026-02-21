<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles first
        $this->call(RoleSeeder::class);

        // Get roles
        $adminRole = Role::where('name', 'admin')->first();
        $salesRole = Role::where('name', 'sales')->first();
        $accountantRole = Role::where('name', 'accountant')->first();

        // Create admin user
        User::firstOrCreate(
            ['email' => 'admin@boon.local'],
            [
                'name' => 'مدير النظام',
                'password' => bcrypt('password'),
                'role_id' => $adminRole?->id,
            ]
        );

        // Create sales user
        User::firstOrCreate(
            ['email' => 'sales@boon.local'],
            [
                'name' => 'موظف المبيعات',
                'password' => bcrypt('password'),
                'role_id' => $salesRole?->id,
            ]
        );

        // Create accountant user
        User::firstOrCreate(
            ['email' => 'accountant@boon.local'],
            [
                'name' => 'المحاسب',
                'password' => bcrypt('password'),
                'role_id' => $accountantRole?->id,
            ]
        );
    }
}
