<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExpenseCategory;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Transportation',
                'name_ar' => 'نقل ومواصلات',
                'description' => 'Transportation and delivery expenses',
            ],
            [
                'name' => 'Utilities',
                'name_ar' => 'الكهرباء والماء والغاز',
                'description' => 'Electricity, water, and gas bills',
            ],
            [
                'name' => 'Salaries',
                'name_ar' => 'الرواتب والأجور',
                'description' => 'Employee salaries and wages',
            ],
            [
                'name' => 'Rent',
                'name_ar' => 'الإيجار',
                'description' => 'Rent and property expenses',
            ],
            [
                'name' => 'Maintenance',
                'name_ar' => 'الصيانة والإصلاحات',
                'description' => 'Maintenance and repair costs',
            ],
            [
                'name' => 'Marketing',
                'name_ar' => 'التسويق والإعلان',
                'description' => 'Marketing and advertising expenses',
            ],
            [
                'name' => 'Office Supplies',
                'name_ar' => 'المستلزمات المكتبية',
                'description' => 'Office supplies and stationery',
            ],
            [
                'name' => 'Insurance',
                'name_ar' => 'التأمين',
                'description' => 'Insurance premiums',
            ],
            [
                'name' => 'Taxes',
                'name_ar' => 'الضرائب والرسوم',
                'description' => 'Taxes and government fees',
            ],
            [
                'name' => 'Other',
                'name_ar' => 'مصروفات أخرى',
                'description' => 'Other miscellaneous expenses',
            ],
        ];

        foreach ($categories as $category) {
            ExpenseCategory::firstOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}
