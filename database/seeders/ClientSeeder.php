<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $creatorId = User::where('email', 'admin@boon.local')->value('id')
            ?? User::query()->value('id');

        $clients = [
            [
                'name' => 'Ahmed Ali',
                'name_ar' => 'أحمد علي',
                'phone' => '01000000001',
                'address_ar' => 'القاهرة',
                'credit_limit' => 15000,
            ],
            [
                'name' => 'Mohamed Hassan',
                'name_ar' => 'محمد حسن',
                'phone' => '01000000002',
                'address_ar' => 'الجيزة',
                'credit_limit' => 10000,
            ],
            [
                'name' => 'Mostafa Mahmoud',
                'name_ar' => 'مصطفى محمود',
                'phone' => '01000000003',
                'address_ar' => 'الإسكندرية',
                'credit_limit' => 12000,
            ],
        ];

        foreach ($clients as $client) {
            Client::firstOrCreate(
                ['phone' => $client['phone']],
                [
                    'name' => $client['name'],
                    'name_ar' => $client['name_ar'],
                    'address_ar' => $client['address_ar'],
                    'credit_limit' => $client['credit_limit'],
                    'total_debt' => 0,
                    'is_active' => true,
                    'created_by' => $creatorId,
                ]
            );
        }

        $this->command->info('Clients seeded successfully!');
    }
}
