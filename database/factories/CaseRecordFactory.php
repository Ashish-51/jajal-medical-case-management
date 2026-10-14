<?php

namespace Database\Factories;

use App\Models\CaseRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CaseRecord>
 */
class CaseRecordFactory extends Factory
{
    protected $model = CaseRecord::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $implantTypes = [
            'Total Knee System - Titanium',
            'Ceramic Hip Prosthesis - 36mm',
            'Spinal Fusion Cage - PEEK',
            'Anterior Cervical Plate',
            'Reverse Shoulder Arthroplasty',
            'Intramedullary Femoral Nail',
            'Bipolar Hip Hemiarthroplasty',
            'Dynamic Hip Screw System',
            'Posterior Lumbar Cage - Titanium',
            'Ankle Arthroplasty Implant'
        ];

        $surgeons = [
            'Dr. Marcus Vance',
            'Dr. Elena Rostova',
            'Dr. Christopher Chen',
            'Dr. Amara Patel',
            'Dr. David O\'Connor',
            'Dr. Sophia Martinez',
            'Dr. Tariq Al-Mansoor',
            'Dr. Ingrid Lindholm'
        ];

        static $sequence = 1;
        $caseNum = 'CASE-' . str_pad($sequence++, 4, '0', STR_PAD_LEFT);

        return [
            'case_number' => $caseNum,
            'patient_reference' => 'REF-' . fake()->unique()->numerify('####-###'),
            'surgeon_name' => fake()->randomElement($surgeons),
            'implant_type' => fake()->randomElement($implantTypes),
            'status' => fake()->randomElement(CaseRecord::STATUSES),
            'priority' => fake()->randomElement(CaseRecord::PRIORITIES),
            'surgery_date' => fake()->boolean(80) ? fake()->dateTimeBetween('+1 days', '+6 months')->format('Y-m-d') : null,
            'created_by' => User::factory(),
        ];
    }
}
