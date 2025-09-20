<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            'Progress Notes',
            'Enquiry',
            'Feedback',
            'Incident',
            'Injury',
            'Mileage',
        ];

        foreach ($types as $type) {
            DB::table('notes')->insert([
                'type' => $type,
                'body' => $type === 'Incident'
                    ? "**Date Lodged:**\n**Date of Incident:**\n**Incident time:**\n**Location of Incident:**\n**Staff completing the Incident Report:**\n**Reporting Staff Phone Number:**\n**Supervisior for Notification:**\n**Witness Name:**\n**Witness Contact Number:**\n**Witness Contact Email:**\n**Incident Type:**\n**Incident Category:**\n**Detail of Incident**"
                    : "Sample body for {$type}",
                'status' => false,
            ]);
        }
    }
}
