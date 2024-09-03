<?php

namespace Database\Seeders;

use App\Models\SubCategory;
use Illuminate\Database\Seeder;

class SubCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // data from https://familybudgetexpert.com/budget-categories/

        SubCategory::factory()
            ->forCategory('Income')
            ->createMany([
                ['name' => 'Salary'],
                ['name' => 'Self-employed Income'],
                ['name' => 'Bonus'],
                ['name' => 'Tips'],
                ['name' => 'Regular Monthly Income'],
                ['name' => 'Tax Refund'],
                ['name' => 'Gifts Received'],
                ['name' => 'Alimony Received'],
                ['name' => 'Child Support Received'],
                ['name' => 'Rental Income'],
                ['name' => 'Dividend Income'],
                ['name' => 'Interest Earned'],
            ]);

        SubCategory::factory()
            ->forCategory('Housing')
            ->createMany([
                ['name' => 'Mortgage/Rent'],
                ['name' => 'Homeowners Association (HOA Fees)'],
                ['name' => 'Homeowners Insurance'],
                ['name' => 'Property Insurance'],
                ['name' => 'Home Repairs/Maintenance'],
                ['name' => 'Property Taxes'],
                ['name' => 'Home Improvement'],
                ['name' => 'Furnishings'],
            ]);

        SubCategory::factory()
            ->forCategory('Home Services')
            ->createMany([
                ['name' => 'House Cleaning'],
                ['name' => 'Lawn Care'],
                ['name' => 'Security System'],
                ['name' => 'Pest Control'],
            ]);

        SubCategory::factory()
            ->forCategory('Utilities')
            ->createMany([
                ['name' => 'Natural Gas/Electricity'],
                ['name' => 'Landline/Home phone'],
                ['name' => 'Mobile Phone'],
                ['name' => 'Home Internet'],
                ['name' => 'Garbage'],
                ['name' => 'Recycling'],
                ['name' => 'Water'],
                ['name' => 'Sewer'],
            ]);

        SubCategory::factory()
            ->forCategory('Household Items')
            ->createMany([
                ['name' => 'Cleaning Supplies'],
                ['name' => 'Paper Products'],
                ['name' => 'Tools'],
                ['name' => 'Toiletries'],
                ['name' => 'Laundry Supplies'],
                ['name' => 'Postage'],
                ['name' => 'Furniture'],
                ['name' => 'Home Décoration'],
                ['name' => 'Pool Supplies'],
            ]);

        SubCategory::factory()
            ->forCategory('Food Expenses')
            ->createMany([
                ['name' => 'Groceries'],
                ['name' => 'Fast Food'],
                ['name' => 'Coffee Shops'],
                ['name' => 'Breakfast'],
                ['name' => 'Lunch'],
                ['name' => 'Dinner'],
                ['name' => 'Drinks'],
                ['name' => 'Snacks'],
            ]);

        SubCategory::factory()
            ->forCategory('Transportation')
            ->createMany([
                ['name' => 'Car Payment/Lease Payments'],
                ['name' => 'Car Insurance'],
                ['name' => 'Gas'],
                ['name' => 'Oil Change'],
                ['name' => 'Maintenance Repairs'],
                ['name' => 'Personal Property Taxes'],
                ['name' => 'Registration'],
                ['name' => 'Public Transportation'],
                ['name' => 'Ride Sharing'],
                ['name' => 'Tolls'],
                ['name' => 'Parking'],
                ['name' => 'Roadside Assistance'],
                ['name' => 'Parking Fees'],
                ['name' => 'Public Transit'],
            ]);

        SubCategory::factory()
            ->forCategory('Medical Health')
            ->createMany([
                ['name' => 'Health Insurance'],
                ['name' => 'Dental Insurance'],
                ['name' => 'Vision Insurance'],
                ['name' => 'Prescription Medications'],
                ['name' => 'Doctor Bills'],
                ['name' => 'Dental Appointments'],
                ['name' => 'Hospital Bills'],
                ['name' => 'Health Care Costs'],
                ['name' => 'Optometrist'],
                ['name' => 'Glasses/Contacts'],
                ['name' => 'Chiropractor Visits'],
                ['name' => 'Vitamins/Supplements'],
            ]);

        SubCategory::factory()
            ->forCategory('Insurance')
            ->createMany([
                ['name' => 'Life Insurance'],
                ['name' => 'Disability Insurance'],
                ['name' => 'Long-term Care Insurance'],
                ['name' => 'Umbrella Policy'],
                ['name' => 'Identity Theft'],
            ]);

        SubCategory::factory()
            ->forCategory('Kids')
            ->createMany([
                ['name' => 'Tuition'],
                ['name' => 'Daycare'],
                ['name' => 'Babysitter/Nanny'],
                ['name' => 'Baby Necessities, formula'],
                ['name' => 'Summer Camp'],
                ['name' => 'School or Extra-Curricular Activities'],
                ['name' => 'School Supplies'],
                ['name' => 'School Lunches'],
                ['name' => 'Lessons'],
                ['name' => 'Allowance'],
                ['name' => 'Toys'],
                ['name' => "Kids' Discretionary Spending"],
                ['name' => 'Child Support'],
                ['name' => 'Clothing'],
            ]);

        SubCategory::factory()
            ->forCategory('Pets')
            ->createMany([
                ['name' => 'Veterinarian'],
                ['name' => 'Pet Food'],
                ['name' => 'Pet Medication'],
                ['name' => 'Pet Toys/Beds'],
                ['name' => 'Pet Accessories'],
                ['name' => 'Pet Grooming'],
                ['name' => 'Pet Insurance'],
            ]);

        SubCategory::factory()
            ->forCategory('Subscriptions/Streaming Services')
            ->createMany([
                ['name' => 'Netflix, Hulu'],
                ['name' => 'Amazon Prime'],
                ['name' => 'Music'],
                ['name' => 'Sports TV Subscription'],
                ['name' => 'Software Subscriptions'],
                ['name' => 'Magazines'],
                ['name' => 'Professional Society Annual Fees'],
            ]);

        SubCategory::factory()
            ->forCategory('Clothing')
            ->createMany([
                ['name' => 'Work Clothing'],
                ['name' => 'Athletic Clothing'],
                ['name' => 'Leisure Clothing'],
                ['name' => 'Alterations'],
                ['name' => 'Dry Cleaning'],
            ]);

        SubCategory::factory()
            ->forCategory('Personal Care')
            ->createMany([
                ['name' => 'Haircuts'],
                ['name' => 'Hair Coloring'],
                ['name' => 'Hair Products'],
                ['name' => 'Cosmetics'],
                ['name' => 'Nail Salon'],
                ['name' => 'Eyebrows'],
                ['name' => 'Massages'],
                ['name' => 'Spa Services'],
                ['name' => 'Grooming'],
                ['name' => 'Gym Membership'],
                ['name' => 'Counseling Therapy'],
            ]);

        SubCategory::factory()
            ->forCategory('Personal Development')
            ->createMany([
                ['name' => 'Books'],
                ['name' => 'Personal Coach'],
                ['name' => 'Self-Improvement'],
                ['name' => 'Conferences'],
                ['name' => 'Online Courses'],
                ['name' => 'In-Person Courses'],
            ]);

        SubCategory::factory()
            ->forCategory('Financial Fees')
            ->createMany([
                ['name' => 'Financial Advisor'],
                ['name' => 'Lawyer/Attorney Fees'],
                ['name' => 'Tax Professional'],
                ['name' => 'Business Consultant'],
            ]);

        SubCategory::factory()
            ->forCategory('Recreation')
            ->createMany([
                ['name' => 'Movies'],
                ['name' => 'Concert Tickets'],
                ['name' => 'Hobbies/Crafts'],
                ['name' => 'Hosting Parties'],
                ['name' => 'Books'],
                ['name' => 'Entertainment'],
                ['name' => 'Sporting Events'],
            ]);

        SubCategory::factory()
            ->forCategory('Travel')
            ->createMany([
                ['name' => 'Vacation'],
                ['name' => 'Vacation Food'],
                ['name' => 'Vacation Activities'],
                ['name' => 'Trips To See Family'],
                ['name' => 'Trips For Weddings, Bachelor/Bachelorette Parties'],
                ['name' => 'Souvenirs'],
                ['name' => 'Baggage Fees'],
                ['name' => 'TSA Precheck/Global Entry'],
            ]);

        SubCategory::factory()
            ->forCategory('Technology')
            ->createMany([
                ['name' => 'Mobile Phone'],
                ['name' => 'Computer/Computer Accessories'],
                ['name' => 'Speaker System'],
                ['name' => 'WI-FI Mesh System/WI-FI Extender'],
                ['name' => 'Smart Home'],
                ['name' => 'Gaming System/Video Games/Gaming Accessories'],
            ]);

        SubCategory::factory()
            ->forCategory('Gifts')
            ->createMany([
                ['name' => 'Family Birthday Gifts'],
                ['name' => 'Friend Birthday Gifts'],
                ['name' => 'Wedding Shower Gifts'],
                ['name' => 'Anniversary Gifts'],
                ['name' => 'Baby Shower Gifts'],
                ['name' => 'Teacher Gifts'],
                ['name' => 'Service-Person Gifts'],
                ['name' => 'Thank You Gifts'],
                ['name' => 'Holiday Gifts'],
                ['name' => 'Special Occasions'],
            ]);

        SubCategory::factory()
            ->forCategory('Charitable Giving')
            ->createMany([
                ['name' => 'Charity Donations'],
                ['name' => 'Tithing'],
                ['name' => 'Religious'],
                ['name' => 'Community'],
                ['name' => 'Political'],
                ['name' => 'Non-Cash Donations'],
            ]);

        SubCategory::factory()
            ->forCategory('Savings Goals/Investing')
            ->createMany([
                ['name' => 'College Savings'],
                ['name' => 'Retirement Savings'],
                ['name' => 'New Car Savings'],
                ['name' => 'Health Savings Account'],
                ['name' => 'Emergency Fund'],
                ['name' => 'Brokerage Investments'],
                ['name' => 'Traditional/Roth IRA'],
                ['name' => 'Down Payment Savings'],
            ]);

        SubCategory::factory()
            ->forCategory('Debt Payment')
            ->createMany([
                ['name' => 'Credit Card Debt'],
                ['name' => 'Student Loan Debt'],
                ['name' => 'Medical Debt'],
                ['name' => 'Personal Loans'],
                ['name' => 'Payment Plans '],
                ['name' => 'Auto Loan Payments'],
                ['name' => 'Back Taxes'],
                ['name' => 'Past Due Bills'],
                ['name' => 'Alimony'],
                ['name' => 'Other Debt Repayment'],
            ]);

    }
}
