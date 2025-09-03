<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StaffDocumentCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run(): void
    {
        $now = Carbon::now();

        $data = [
            ['name' => 'Aged Care Assessment', 'status' => 'Active', 'is_staff_doc' => 1, 'is_competencies' => 1, 'is_qualifications' => 0, 'is_compliance' => 0, 'is_kpi' => 0, 'is_other' => 0],
            ['name' => 'Autism Assessment', 'status' => 'Active', 'is_staff_doc' => 1, 'is_competencies' => 1, 'is_qualifications' => 0, 'is_compliance' => 0, 'is_kpi' => 0, 'is_other' => 0],
            ['name' => 'Hoist Training', 'status' => 'Active', 'is_staff_doc' => 1, 'is_competencies' => 1, 'is_qualifications' => 0, 'is_compliance' => 0, 'is_kpi' => 0, 'is_other' => 0],
            ['name' => 'Food Handling', 'status' => 'Active', 'is_staff_doc' => 1, 'is_competencies' => 1, 'is_qualifications' => 0, 'is_compliance' => 0, 'is_kpi' => 0, 'is_other' => 0],
            ['name' => 'Manual Handling', 'status' => 'Active', 'is_staff_doc' => 1, 'is_competencies' => 1, 'is_qualifications' => 0, 'is_compliance' => 0, 'is_kpi' => 0, 'is_other' => 0],
            ['name' => 'Medication Handling', 'status' => 'Active', 'is_staff_doc' => 1, 'is_competencies' => 1, 'is_qualifications' => 0, 'is_compliance' => 0, 'is_kpi' => 0, 'is_other' => 0],
            ['name' => 'Suctioning Care', 'status' => 'Active', 'is_staff_doc' => 1, 'is_competencies' => 1, 'is_qualifications' => 0, 'is_compliance' => 0, 'is_kpi' => 0, 'is_other' => 0],
            ['name' => 'Online Training', 'status' => 'Active', 'is_staff_doc' => 1, 'is_competencies' => 1, 'is_qualifications' => 0, 'is_compliance' => 0, 'is_kpi' => 0, 'is_other' => 0],
            ['name' => 'Complex Physical Care Assessment', 'status' => 'Active', 'is_staff_doc' => 1, 'is_competencies' => 1, 'is_qualifications' => 0, 'is_compliance' => 0, 'is_kpi' => 0, 'is_other' => 0],
            ['name' => 'Dementia Care Assessment', 'status' => 'Active', 'is_staff_doc' => 1, 'is_competencies' => 1, 'is_qualifications' => 0, 'is_compliance' => 0, 'is_kpi' => 0, 'is_other' => 0],
            ['name' => 'Paediatric/Children Assessment', 'status' => 'Active', 'is_staff_doc' => 1, 'is_competencies' => 1, 'is_qualifications' => 0, 'is_compliance' => 0, 'is_kpi' => 0, 'is_other' => 0],
            ['name' => 'Wheelchair Handling', 'status' => 'Active', 'is_staff_doc' => 1, 'is_competencies' => 1, 'is_qualifications' => 0, 'is_compliance' => 0, 'is_kpi' => 0, 'is_other' => 0],
            ['name' => 'Certificate III Disability Work', 'status' => 'Active', 'is_staff_doc' => 1, 'is_competencies' => 0, 'is_qualifications' => 1, 'is_compliance' => 0, 'is_kpi' => 0, 'is_other' => 0],
            ['name' => 'Certificate IV Disability Work', 'status' => 'Active', 'is_staff_doc' => 1, 'is_competencies' => 0, 'is_qualifications' => 1, 'is_compliance' => 0, 'is_kpi' => 0, 'is_other' => 0],
            ['name' => 'Diploma Disability Studies', 'status' => 'Active', 'is_staff_doc' => 1, 'is_competencies' => 0, 'is_qualifications' => 1, 'is_compliance' => 0, 'is_kpi' => 0, 'is_other' => 0],
            ['name' => 'Certificate IV Workplace Assessment & Training', 'status' => 'Active', 'is_staff_doc' => 1, 'is_competencies' => 0, 'is_qualifications' => 1, 'is_compliance' => 0, 'is_kpi' => 0, 'is_other' => 0],
            ['name' => 'Certificate III in Ageing Support', 'status' => 'Active', 'is_staff_doc' => 1, 'is_competencies' => 0, 'is_qualifications' => 1, 'is_compliance' => 0, 'is_kpi' => 0, 'is_other' => 0],
            ['name' => 'Certificate IV in Ageing Support', 'status' => 'Active', 'is_staff_doc' => 1, 'is_competencies' => 0, 'is_qualifications' => 1, 'is_compliance' => 0, 'is_kpi' => 0, 'is_other' => 0],
            ['name' => 'Diploma Aged care Services', 'status' => 'Active', 'is_staff_doc' => 1, 'is_competencies' => 0, 'is_qualifications' => 1, 'is_compliance' => 0, 'is_kpi' => 0, 'is_other' => 0],
            ['name' => 'Enrolled Nurse', 'status' => 'Active', 'is_staff_doc' => 1, 'is_competencies' => 0, 'is_qualifications' => 1, 'is_compliance' => 0, 'is_kpi' => 0, 'is_other' => 0],
            ['name' => 'Other Certificates', 'status' => 'Active', 'is_staff_doc' => 1, 'is_competencies' => 0, 'is_qualifications' => 1, 'is_compliance' => 0, 'is_kpi' => 0, 'is_other' => 0],
            ['name' => 'Overseas Nurse/Doctor', 'status' => 'Active', 'is_staff_doc' => 1, 'is_competencies' => 0, 'is_qualifications' => 1, 'is_compliance' => 0, 'is_kpi' => 0, 'is_other' => 0],
            ['name' => 'Overseas Therapist', 'status' => 'Active', 'is_staff_doc' => 1, 'is_competencies' => 0, 'is_qualifications' => 1, 'is_compliance' => 0, 'is_kpi' => 0, 'is_other' => 0],
            ['name' => 'Registered Nurse', 'status' => 'Active', 'is_staff_doc' => 1, 'is_competencies' => 0, 'is_qualifications' => 1, 'is_compliance' => 0, 'is_kpi' => 0, 'is_other' => 0],
            ['name' => 'COVID-19 Compliance', 'status' => 'Active', 'is_staff_doc' => 1, 'is_competencies' => 0, 'is_qualifications' => 0, 'is_compliance' => 1, 'is_kpi' => 0, 'is_other' => 0],
            ['name' => 'First Aid Certificate', 'status' => 'Active', 'is_staff_doc' => 1, 'is_competencies' => 0, 'is_qualifications' => 0, 'is_compliance' => 1, 'is_kpi' => 0, 'is_other' => 0],
            ['name' => 'NDIS Worker Check (NDISWC)', 'status' => 'Active', 'is_staff_doc' => 1, 'is_competencies' => 0, 'is_qualifications' => 0, 'is_compliance' => 1, 'is_kpi' => 0, 'is_other' => 0],
            ['name' => 'Police Check', 'status' => 'Active', 'is_staff_doc' => 1, 'is_competencies' => 0, 'is_qualifications' => 0, 'is_compliance' => 1, 'is_kpi' => 0, 'is_other' => 0],
            ['name' => 'Visa Documentation', 'status' => 'Active', 'is_staff_doc' => 1, 'is_competencies' => 0, 'is_qualifications' => 0, 'is_compliance' => 1, 'is_kpi' => 0, 'is_other' => 0],
            ['name' => 'Drivers License C', 'status' => 'Active', 'is_staff_doc' => 1, 'is_competencies' => 0, 'is_qualifications' => 0, 'is_compliance' => 0, 'is_kpi' => 1, 'is_other' => 0],
            ['name' => 'Drivers License L R', 'status' => 'Active', 'is_staff_doc' => 1, 'is_competencies' => 0, 'is_qualifications' => 0, 'is_compliance' => 0, 'is_kpi' => 1, 'is_other' => 0],
            ['name' => 'Drivers License International', 'status' => 'Active', 'is_staff_doc' => 1, 'is_competencies' => 0, 'is_qualifications' => 0, 'is_compliance' => 0, 'is_kpi' => 1, 'is_other' => 0],
            ['name' => 'Car Registration', 'status' => 'Active', 'is_staff_doc' => 1, 'is_competencies' => 0, 'is_qualifications' => 0, 'is_compliance' => 0, 'is_kpi' => 1, 'is_other' => 0],
            ['name' => 'Comprehensive Car Insurance', 'status' => 'Active', 'is_staff_doc' => 1, 'is_competencies' => 0, 'is_qualifications' => 0, 'is_compliance' => 0, 'is_kpi' => 1, 'is_other' => 0],
            ['name' => 'Resume', 'status' => 'Active', 'is_staff_doc' => 1, 'is_competencies' => 0, 'is_qualifications' => 0, 'is_compliance' => 0, 'is_kpi' => 0, 'is_other' => 1],
        ];

        foreach ($data as &$row) {
            $row['created_at'] = $now;
            $row['updated_at'] = $now;
        }

        DB::table('document_categories')->insert($data);
    }
}
