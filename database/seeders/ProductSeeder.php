<?php

namespace Database\Seeders;

use App\Models\MainCategory;
use App\Models\Product;
use App\Models\SubCategory;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $creatorId = User::where('email', 'admin@boon.local')->value('id')
            ?? User::query()->value('id');

        $mainCategory = MainCategory::firstOrCreate(
            ['name' => 'Coffee'],
            [
                'name_ar' => 'البن',
                'description' => 'Coffee and coffee-related products',
                'description_ar' => 'منتجات البن والقهوة',
            ]
        );

        $subCategory = SubCategory::firstOrCreate(
            [
                'main_category_id' => $mainCategory->id,
                'name' => 'Coffee Grains',
            ],
            [
                'name_ar' => 'حبوب بن',
                'description' => 'Coffee bean grains category',
                'description_ar' => 'فئة حبوب البن',
            ]
        );

        Product::firstOrCreate(
            ['sku' => 'BN-BR-0001'],
            [
                'name' => 'Brazilian Coffee Beans',
                'name_ar' => 'بن برازيلي',
                'main_category_id' => $mainCategory->id,
                'sub_category_id' => $subCategory->id,
                'purchase_price_per_kg' => 180,
                'selling_price_per_kg' => 240,
                'current_stock_kg' => 50,
                'minimum_stock_alert' => 5,
                'supplier_id' => null,
                'notes' => 'منتج تجريبي من السيدر',
                'created_by' => $creatorId,
            ]
        );

        $this->command->info('Product seeded successfully!');
    }
}
