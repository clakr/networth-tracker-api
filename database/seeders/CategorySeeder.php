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
        Category::factory()
            ->income()
            ->createMany([
                ['name' => 'Income'],
            ]);

        Category::factory()
            ->expense()
            ->createMany([
                ['name' => 'Housing'],
                ['name' => 'Home Services'],
                ['name' => 'Utilities'],
                ['name' => 'Household Items'],
                ['name' => 'Food Expenses'],
                ['name' => 'Transportation'],
                ['name' => 'Medical Health'],
                ['name' => 'Insurance'],
                ['name' => 'Kids'],
                ['name' => 'Pets'],
                ['name' => 'Subscriptions/Streaming Services'],
                ['name' => 'Clothing'],
                ['name' => 'Personal Care'],
                ['name' => 'Personal Development'],
                ['name' => 'Financial Fees'],
                ['name' => 'Recreation'],
                ['name' => 'Travel'],
                ['name' => 'Technology'],
                ['name' => 'Gifts'],
                ['name' => 'Charitable Giving'],
                ['name' => 'Savings Goals/Investing'],
                ['name' => 'Debt Payment'],
            ]);
    }
}
