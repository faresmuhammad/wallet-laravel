<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        ]);

        $this->insertCollection('Leisure', [
            'Movies',
            'Video Rental',
            'Magazines',
        ]);

        $this->insertCollection('Automobile', [
            'Maintenance',
            'Gas',
            'Parking',
            'Registration',
        ]);

        $this->insertCollection('Education', [
            'Books',
            'Tuition',
            'Others',
        ]);

        $this->insertCollection('Homeneeds', [
            'Clothing',
            'Furnishing',
            'Others',
        ]);

        $this->insertCollection('Healthcare', [
            'Health',
            'Dental',
            'Eyecare',
            'Physician',
            'Prescriptions',
        ]);

        $this->insertCollection('Insurance', [
            'Life',
            'Home',
            'Health',
            'Auto',
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

        $this->insertCollection('Other Income',[]);
        $this->insertCollection('Other Expenses',[]);
        $this->insertCollection('Transfer',[]);
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
