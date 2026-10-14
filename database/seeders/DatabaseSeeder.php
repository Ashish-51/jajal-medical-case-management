<?php

namespace Database\Seeders;

use App\Models\CaseRecord;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@jajalmedical.com'],
            [
                'name' => 'Dr. Admin Director',
                'password' => Hash::make('AdminPass123!'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // 2. Create Viewer User
        $viewer = User::firstOrCreate(
            ['email' => 'viewer@jajalmedical.com'],
            [
                'name' => 'Jane Viewer Reviewer',
                'password' => Hash::make('ViewerPass123!'),
                'role' => 'viewer',
                'email_verified_at' => now(),
            ]
        );

        // 3. Create Engineer User (Bonus A)
        $engineer = User::firstOrCreate(
            ['email' => 'engineer@jajalmedical.com'],
            [
                'name' => 'Alex Engineer Specialist',
                'password' => Hash::make('EngineerPass123!'),
                'role' => 'engineer',
                'email_verified_at' => now(),
            ]
        );

        // 4. Seed at least 25 realistic dummy cases
        $casesData = [
            [
                'case_number' => 'CASE-1001',
                'patient_reference' => 'PAT-8492-X1',
                'surgeon_name' => 'Dr. Marcus Vance',
                'implant_type' => 'Total Knee System - Titanium Cruciate-Retaining',
                'status' => 'Planning',
                'priority' => 'High',
                'surgery_date' => now()->addDays(5)->format('Y-m-d'),
                'created_by' => $admin->id,
            ],
            [
                'case_number' => 'CASE-1002',
                'patient_reference' => 'PAT-5821-B2',
                'surgeon_name' => 'Dr. Elena Rostova',
                'implant_type' => 'Ceramic Hip Prosthesis - 36mm Delta Head',
                'status' => 'Approved',
                'priority' => 'High',
                'surgery_date' => now()->addDays(8)->format('Y-m-d'),
                'created_by' => $admin->id,
            ],
            [
                'case_number' => 'CASE-1003',
                'patient_reference' => 'PAT-3129-C3',
                'surgeon_name' => 'Dr. Christopher Chen',
                'implant_type' => 'Spinal Fusion Cage - Expandable PEEK',
                'status' => 'In Review',
                'priority' => 'Medium',
                'surgery_date' => now()->addDays(14)->format('Y-m-d'),
                'created_by' => $admin->id,
            ],
            [
                'case_number' => 'CASE-1004',
                'patient_reference' => 'PAT-9014-D4',
                'surgeon_name' => 'Dr. Amara Patel',
                'implant_type' => 'Anterior Cervical Plate System - 4-Level',
                'status' => 'Completed',
                'priority' => 'Medium',
                'surgery_date' => now()->subDays(3)->format('Y-m-d'),
                'created_by' => $admin->id,
            ],
            [
                'case_number' => 'CASE-1005',
                'patient_reference' => 'PAT-4402-E5',
                'surgeon_name' => 'Dr. David O\'Connor',
                'implant_type' => 'Reverse Shoulder Arthroplasty Modular',
                'status' => 'Draft',
                'priority' => 'Low',
                'surgery_date' => null,
                'created_by' => $admin->id,
            ],
            [
                'case_number' => 'CASE-1006',
                'patient_reference' => 'PAT-7731-F6',
                'surgeon_name' => 'Dr. Sophia Martinez',
                'implant_type' => 'Intramedullary Femoral Nail - Trochanteric Entry',
                'status' => 'Cancelled',
                'priority' => 'Low',
                'surgery_date' => null,
                'created_by' => $admin->id,
            ],
            [
                'case_number' => 'CASE-1007',
                'patient_reference' => 'PAT-6629-G7',
                'surgeon_name' => 'Dr. Tariq Al-Mansoor',
                'implant_type' => 'Bipolar Hip Hemiarthroplasty Stem',
                'status' => 'Planning',
                'priority' => 'Medium',
                'surgery_date' => now()->addDays(20)->format('Y-m-d'),
                'created_by' => $engineer->id,
            ],
            [
                'case_number' => 'CASE-1008',
                'patient_reference' => 'PAT-1945-H8',
                'surgeon_name' => 'Dr. Ingrid Lindholm',
                'implant_type' => 'Dynamic Hip Screw Plate System 135 deg',
                'status' => 'Approved',
                'priority' => 'High',
                'surgery_date' => now()->addDays(4)->format('Y-m-d'),
                'created_by' => $admin->id,
            ],
            [
                'case_number' => 'CASE-1009',
                'patient_reference' => 'PAT-8371-J9',
                'surgeon_name' => 'Dr. Marcus Vance',
                'implant_type' => 'Posterior Lumbar Interbody Fusion Cage (PLIF)',
                'status' => 'In Review',
                'priority' => 'High',
                'surgery_date' => now()->addDays(12)->format('Y-m-d'),
                'created_by' => $engineer->id,
            ],
            [
                'case_number' => 'CASE-1010',
                'patient_reference' => 'PAT-2480-K1',
                'surgeon_name' => 'Dr. Elena Rostova',
                'implant_type' => 'Total Ankle Arthroplasty Three-Component',
                'status' => 'Completed',
                'priority' => 'Low',
                'surgery_date' => now()->subDays(10)->format('Y-m-d'),
                'created_by' => $admin->id,
            ],
            [
                'case_number' => 'CASE-1011',
                'patient_reference' => 'PAT-9512-L2',
                'surgeon_name' => 'Dr. Christopher Chen',
                'implant_type' => 'Distal Radius Volar Locking Plate 2.4mm',
                'status' => 'Draft',
                'priority' => 'Low',
                'surgery_date' => null,
                'created_by' => $admin->id,
            ],
            [
                'case_number' => 'CASE-1012',
                'patient_reference' => 'PAT-6304-M3',
                'surgeon_name' => 'Dr. Amara Patel',
                'implant_type' => 'Cranial Reconstruction Mesh - 3D Printed Titanium',
                'status' => 'Planning',
                'priority' => 'High',
                'surgery_date' => now()->addDays(25)->format('Y-m-d'),
                'created_by' => $engineer->id,
            ],
            [
                'case_number' => 'CASE-1013',
                'patient_reference' => 'PAT-7128-N4',
                'surgeon_name' => 'Dr. David O\'Connor',
                'implant_type' => 'Tibial Plateau Fracture Buttress Plate',
                'status' => 'In Review',
                'priority' => 'Medium',
                'surgery_date' => now()->addDays(18)->format('Y-m-d'),
                'created_by' => $admin->id,
            ],
            [
                'case_number' => 'CASE-1014',
                'patient_reference' => 'PAT-4819-P5',
                'surgeon_name' => 'Dr. Sophia Martinez',
                'implant_type' => 'Custom Mandibular Reconstruction Plate',
                'status' => 'Approved',
                'priority' => 'High',
                'surgery_date' => now()->addDays(9)->format('Y-m-d'),
                'created_by' => $engineer->id,
            ],
            [
                'case_number' => 'CASE-1015',
                'patient_reference' => 'PAT-3382-Q6',
                'surgeon_name' => 'Dr. Tariq Al-Mansoor',
                'implant_type' => 'Cementless Femoral Hip Stem with HA Coating',
                'status' => 'Completed',
                'priority' => 'Medium',
                'surgery_date' => now()->subDays(15)->format('Y-m-d'),
                'created_by' => $admin->id,
            ],
            [
                'case_number' => 'CASE-1016',
                'patient_reference' => 'PAT-5921-R7',
                'surgeon_name' => 'Dr. Ingrid Lindholm',
                'implant_type' => 'Unicompartmental Knee Resurfacing Prosthesis',
                'status' => 'Cancelled',
                'priority' => 'Medium',
                'surgery_date' => null,
                'created_by' => $admin->id,
            ],
            [
                'case_number' => 'CASE-1017',
                'patient_reference' => 'PAT-8201-S8',
                'surgeon_name' => 'Dr. Marcus Vance',
                'implant_type' => 'Occipito-Cervical Spinal Fixation System',
                'status' => 'Planning',
                'priority' => 'High',
                'surgery_date' => now()->addDays(30)->format('Y-m-d'),
                'created_by' => $admin->id,
            ],
            [
                'case_number' => 'CASE-1018',
                'patient_reference' => 'PAT-1149-T9',
                'surgeon_name' => 'Dr. Elena Rostova',
                'implant_type' => 'Radial Head Replacement Modular System',
                'status' => 'In Review',
                'priority' => 'Low',
                'surgery_date' => now()->addDays(22)->format('Y-m-d'),
                'created_by' => $engineer->id,
            ],
            [
                'case_number' => 'CASE-1019',
                'patient_reference' => 'PAT-7493-U1',
                'surgeon_name' => 'Dr. Christopher Chen',
                'implant_type' => 'Thoracolumbar Deformity Correction Rod System',
                'status' => 'Approved',
                'priority' => 'High',
                'surgery_date' => now()->addDays(11)->format('Y-m-d'),
                'created_by' => $admin->id,
            ],
            [
                'case_number' => 'CASE-1020',
                'patient_reference' => 'PAT-6610-V2',
                'surgeon_name' => 'Dr. Amara Patel',
                'implant_type' => 'Acetabular Revision Reinforcement Cage',
                'status' => 'Completed',
                'priority' => 'High',
                'surgery_date' => now()->subDays(7)->format('Y-m-d'),
                'created_by' => $admin->id,
            ],
            [
                'case_number' => 'CASE-1021',
                'patient_reference' => 'PAT-2834-W3',
                'surgeon_name' => 'Dr. David O\'Connor',
                'implant_type' => 'Clavicle Hook Plate for AC Joint Dislocation',
                'status' => 'Draft',
                'priority' => 'Low',
                'surgery_date' => null,
                'created_by' => $admin->id,
            ],
            [
                'case_number' => 'CASE-1022',
                'patient_reference' => 'PAT-5509-Y4',
                'surgeon_name' => 'Dr. Sophia Martinez',
                'implant_type' => 'Subtalar Arthroereisis Sinus Tarsi Implant',
                'status' => 'Planning',
                'priority' => 'Medium',
                'surgery_date' => now()->addDays(16)->format('Y-m-d'),
                'created_by' => $engineer->id,
            ],
            [
                'case_number' => 'CASE-1023',
                'patient_reference' => 'PAT-9912-Z5',
                'surgeon_name' => 'Dr. Tariq Al-Mansoor',
                'implant_type' => 'Proximal Humerus Internal Locking System (PHILOS)',
                'status' => 'In Review',
                'priority' => 'Medium',
                'surgery_date' => now()->addDays(7)->format('Y-m-d'),
                'created_by' => $admin->id,
            ],
            [
                'case_number' => 'CASE-1024',
                'patient_reference' => 'PAT-3741-AA',
                'surgeon_name' => 'Dr. Ingrid Lindholm',
                'implant_type' => 'Bicondylar Total Knee Replacement Porous Coated',
                'status' => 'Approved',
                'priority' => 'High',
                'surgery_date' => now()->addDays(3)->format('Y-m-d'),
                'created_by' => $admin->id,
            ],
            [
                'case_number' => 'CASE-1025',
                'patient_reference' => 'PAT-4830-BB',
                'surgeon_name' => 'Dr. Marcus Vance',
                'implant_type' => 'Cannulated Cancellous Screw Fixation 6.5mm',
                'status' => 'Completed',
                'priority' => 'Low',
                'surgery_date' => now()->subDays(20)->format('Y-m-d'),
                'created_by' => $engineer->id,
            ],
        ];

        foreach ($casesData as $case) {
            CaseRecord::updateOrCreate(
                ['case_number' => $case['case_number']],
                $case
            );
        }
    }
}
