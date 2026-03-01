<?php

namespace Database\Seeders;

use App\Models\MainCategory;
use App\Models\SubCategory;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CategorySeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ==================== COFFEE CATEGORY ====================
        $coffeeMainCategory = MainCategory::firstOrCreate(
            ['name' => 'Coffee'],
            [
                'name_ar' => 'البن',
                'description' => 'Coffee and coffee-related products',
                'description_ar' => 'منتجات البن والقهوة',
            ]
        );

        // Coffee Sub Categories
        $coffeeSubCategories = [
            ['name' => 'Arabic Coffee', 'name_ar' => 'قهوة عربية'],
            ['name' => 'Turkish Coffee', 'name_ar' => 'قهوة تركية'],
            ['name' => 'Espresso', 'name_ar' => 'إسبريسو'],
            ['name' => 'Americano', 'name_ar' => 'أمريكانو'],
            ['name' => 'Coffee Beans', 'name_ar' => 'حبوب القهوة'],
            ['name' => 'Ground Coffee', 'name_ar' => 'قهوة مطحونة'],
            ['name' => 'Instant Coffee', 'name_ar' => 'قهوة سريعة الذوبان'],
            ['name' => 'Decaf Coffee', 'name_ar' => 'قهوة منزوعة الكافيين'],
            ['name' => 'Premium Coffee', 'name_ar' => 'قهوة فاخرة'],
            ['name' => 'Coffee Blends', 'name_ar' => 'مزجات البن'],
        ];

        foreach ($coffeeSubCategories as $subCat) {
            SubCategory::firstOrCreate(
                [
                    'main_category_id' => $coffeeMainCategory->id,
                    'name' => $subCat['name'],
                ],
                [
                    'name_ar' => $subCat['name_ar'],
                ]
            );
        }

        // ==================== PERFUME & FRAGRANCE CATEGORY ====================
        $perfumeMainCategory = MainCategory::firstOrCreate(
            ['name' => 'Perfumes & Fragrances'],
            [
                'name_ar' => 'العطور والروائح',
                'description' => 'Perfumes, fragrance oils, and fragrance products',
                'description_ar' => 'العطور وزيوت العطر ومنتجات الروائح',
            ]
        );

        // Perfume Sub Categories
        $perfumeSubCategories = [
            ['name' => 'Eau de Parfum', 'name_ar' => 'عطر'],
            ['name' => 'Eau de Toilette', 'name_ar' => 'كولونيا'],
            ['name' => 'Fragrance Oils', 'name_ar' => 'زيوت عطرية'],
            ['name' => 'Musk', 'name_ar' => 'مسك'],
            ['name' => 'Oud', 'name_ar' => 'عود'],
            ['name' => 'Rose Water', 'name_ar' => 'ماء الورد'],
            ['name' => 'Orange Blossom', 'name_ar' => 'ماء زهر'],
            ['name' => 'Incense & Bakhoor', 'name_ar' => 'بخور'],
            ['name' => 'Body Spray', 'name_ar' => 'بخاخ الجسم'],
            ['name' => 'Attars', 'name_ar' => 'العطارة'],
            ['name' => 'Floral Scents', 'name_ar' => 'روائح زهرية'],
            ['name' => 'Oriental Scents', 'name_ar' => 'روائح شرقية'],
            ['name' => 'Fresh Scents', 'name_ar' => 'روائح منعشة'],
        ];

        foreach ($perfumeSubCategories as $subCat) {
            SubCategory::firstOrCreate(
                [
                    'main_category_id' => $perfumeMainCategory->id,
                    'name' => $subCat['name'],
                ],
                [
                    'name_ar' => $subCat['name_ar'],
                ]
            );
        }

        // ==================== HERBS & SPICES CATEGORY ====================
        $herbsMainCategory = MainCategory::firstOrCreate(
            ['name' => 'Herbs & Spices'],
            [
                'name_ar' => 'الأعشاب والبهارات',
                'description' => 'Herbs, spices, and medicinal plants',
                'description_ar' => 'الأعشاب والبهارات والنباتات الطبية',
            ]
        );

        // Herbs & Spices Sub Categories
        $herbsSubCategories = [
            ['name' => 'Herbs', 'name_ar' => 'أعشاب'],
            ['name' => 'Spices', 'name_ar' => 'بهارات'],
            ['name' => 'Medicinal Herbs', 'name_ar' => 'أعشاب طبية'],
            ['name' => 'Tea & Herbal Tea', 'name_ar' => 'شاي وشاي أعشاب'],
            ['name' => 'Dried Flowers', 'name_ar' => 'زهور مجففة'],
            ['name' => 'Dried Fruits', 'name_ar' => 'فواكه مجففة'],
            ['name' => 'Grains & Seeds', 'name_ar' => 'حبوب وبذور'],
            ['name' => 'Spice Blends', 'name_ar' => 'مزجات البهارات'],
            ['name' => 'Honey & Syrups', 'name_ar' => 'عسل وشرائط'],
        ];

        foreach ($herbsSubCategories as $subCat) {
            SubCategory::firstOrCreate(
                [
                    'main_category_id' => $herbsMainCategory->id,
                    'name' => $subCat['name'],
                ],
                [
                    'name_ar' => $subCat['name_ar'],
                ]
            );
        }

        // ==================== EQUIPMENT & ACCESSORIES CATEGORY ====================
        $equipmentMainCategory = MainCategory::firstOrCreate(
            ['name' => 'Equipment & Accessories'],
            [
                'name_ar' => 'المعدات والأدوات',
                'description' => 'Coffee makers, brewing equipment, and accessories',
                'description_ar' => 'آلات صنع القهوة والمعدات والأدوات',
            ]
        );

        // Equipment Sub Categories
        $equipmentSubCategories = [
            ['name' => 'Coffee Makers', 'name_ar' => 'صانعات القهوة'],
            ['name' => 'Filters & Paper', 'name_ar' => 'مرشحات وورق'],
            ['name' => 'Grinders', 'name_ar' => 'طاحونات'],
            ['name' => 'Scales', 'name_ar' => 'موازين'],
            ['name' => 'Cups & Mugs', 'name_ar' => 'أكواب'],
            ['name' => 'Spoons & Stirrers', 'name_ar' => 'ملاعق وأدوات تقليب'],
            ['name' => 'Thermometers', 'name_ar' => 'ترمومترات'],
            ['name' => 'Storage Containers', 'name_ar' => 'علب التخزين'],
        ];

        foreach ($equipmentSubCategories as $subCat) {
            SubCategory::firstOrCreate(
                [
                    'main_category_id' => $equipmentMainCategory->id,
                    'name' => $subCat['name'],
                ],
                [
                    'name_ar' => $subCat['name_ar'],
                ]
            );
        }

        $this->command->info('Categories seeded successfully!');
    }
}
