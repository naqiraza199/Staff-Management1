<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DocumentCategory;

class DocumentCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'ACAT Assessment',
            'Admission Document',
            'Agreement',
            'Asthma Management Plan',
            'Behaviour Management Plan',
            'Client Profile',
            'Consent to Share Information',
            'CDC Agreement',
            'Diabetes Management Plan',
            'Domestic Assistance',
            'Emergency Plan',
            'Entry Records',
            'Epilepsy Management Plan',
            'Falls Risk Assessment',
            'Home Care Agreement',
            'Home Risk Assessment',
            'Home Safety Checklist',
            'Individual Risk Assessment',
            'Intake and Referral Form',
            'Medication Plan',
            'MMSE AMTS',
            'NDIA Agreement',
            'NDIA Costing Document',
            'New aged Care arrangements',
            'Nutrition and Swallowing Plan',
            'Occupational Therapy Report',
            'PAS Assessment',
            'PCP Report',
            'Personal Care Plan',
            'Power of Attorney',
            'Psychologist Report',
            'Public Guardian Document',
            'RN Assessment',
            'Sensory Report',
            'Social Story',
            'Speech Pathologist Report',
            'Support and Respite Plan',
            'Template Daily Service Report',
            'Tube Feeding Plan',
        ];

        foreach ($categories as $category) {
            DocumentCategory::create([
                'name' => $category,
                'status' => 'Active',
            ]);
        }
    }
} 