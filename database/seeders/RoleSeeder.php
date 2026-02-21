<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin role
        Role::firstOrCreate(
            ['name' => 'admin'],
            [
                'name_ar' => 'مدير',
                'description' => 'Administrator with full system access',
            ]
        );

        // Create sales role
        Role::firstOrCreate(
            ['name' => 'sales'],
            [
                'name_ar' => 'مبيعات',
                'description' => 'Sales person - create invoices, quick sales, manage clients',
            ]
        );

        // Create accountant role
        Role::firstOrCreate(
            ['name' => 'accountant'],
            [
                'name_ar' => 'محاسب',
                'description' => 'Accountant - manage payments, reports, expenses',
            ]
        );
    }
}
