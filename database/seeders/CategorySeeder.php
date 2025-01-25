<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{


    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $this->insertCollection('Bills', [
            'Telephone',
            'Electricity',
            'Gas',
            'Internet',
            'Rent',
            'Cable TV',
            'Water',
        ]);

        $this->insertCollection('Food', [
            'Groceries',
            'Dining out',
            'Talabat',
        ]);

        $this->insertCollection('Transportation', [
            'Fuel',
            'Public Transport',
            'Taxi',
            'Car Maintenance',
        ]);

        $this->insertCollection('Education', [
            'Books',
            'Courses',
        ]);

        $this->insertCollection('Home Needs', [
            'Clothing',
            'Furnishing',
            'Equipment',
        ]);

        $this->insertCollection('Healthcare', [
            'Health',
            'Dental',
            'Eyecare',
            'Physician',
            'Prescriptions',
        ]);


        $this->insertCollection('Vacation', [
            'Travel',
            'Lodging',
            'Sightseeing',
        ]);

        $this->insertCollection('Taxes', [
            'Income Tax',
            'House Tax',
            'Water Tax',
            'Others',
        ]);

        $this->insertCollection('Miscellaneous', []);
        $this->insertCollection('Gifts', []);
        $this->insertCollection('Income', [
            'Salary',
            'Reimbursement/Refunds',
            'Investment Income',
        ]);

        $this->insertCollection('Other Income', []);
        $this->insertCollection('Other Expenses', []);
        $this->insertCollection('Transfer', []);
    }

    private function categories(Category $parent, array $children): array
    {
        $result = [];
        foreach ($children as $child) {
            $result[] = [
                'name' => $child,
                'parent_id' => $parent->id
            ];
        }
        return $result;
    }

    private function insertCollection(string $parent, array $children)
    {
        $parentCategory = Category::create([
            'name' => $parent
        ]);
        if (!empty($children)) {
            $subcategories = $this->categories($parentCategory, $children);

            foreach ($subcategories as $subcategory) {
                Category::create($subcategory);
            }
        }
    }

}
