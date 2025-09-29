<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\InvoiceCategory;

class InvoiceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
      public function run(): void
    {
        $categories = [
            'NDIA category',
            'Coordination of supports',
            'Establishment Fee',
            'Early Childhood Therapy',
            'Financial Intermediary Setup Cost',
            'Financial Intermediary Monthly Processing',
            'Meal Preperation',
            'School Leaver Employment Support',
            'Sleepover',
            'NDIS Supported Independent Living - Standard Needs',
            'Therapeutic Supports',
            'Financial Intermediary and Service Set-up Fee ',
            'Short Term Accomodation and Assistance 1:2 Weekday',
            'Short Term Accomodation and Assistance 1:2 Saturday',
            'Short Term Accomodation and Assistance 1:2 Sunday',
            'Short Term Accomodation and Assistance 1:2 PH',
            'Short Term Accomodation and Assistance 1:1 Weekday',
            'Short Term Accomodation and Assistance 1:1 Saturday',
            'Short Term Accomodation and Assistance 1:1 Sunday',
            'Short Term Accomodation and Assistance 1:1 PH',
            'Short Term Accomodation and Assistance 1:4 Weekday',
            'Short Term Accomodation and Assistance 1:4 Saturday',
            'Short Term Accomodation and Assistance 1:4 Sunday',
            'Short Term Accomodation and Assistance 1:4 PH',
            'Transport',
            'Individual Therapy/Training',
            'Short Term Accomodation 1:3 Weekday',
            'Short Term Accomodation 1:3 Saturday',
            'Short Term Accomodation 1:3 Sunday',
            'Short Term Accomodation 1:3 PH',
            'Assistance With Self-Care - Night-Time Sleepover',
            'Shared Independent Living - Standard (1:4)',
            'Shared Independent Living - Standard (1:2)',
        ];

        foreach ($categories as $category) {
            InvoiceCategory::create([
                'name' => $category,
                'status' => 'Active',
            ]);
        }
    }
}
