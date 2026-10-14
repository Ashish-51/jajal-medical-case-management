<?php

namespace Tests\Feature;

use App\Models\CaseRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicalCaseManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test 1: Valid login.
     * A seeded or factory-created user can log in with valid credentials and receives the expected authenticated result.
     */
    public function test_valid_login(): void
    {
        $user = User::factory()->create([
            'email' => 'doctor@example.com',
            'password' => 'Password123!',
            'role' => 'admin',
        ]);

        $response = $this->post('/login', [
            'email' => 'doctor@example.com',
            'password' => 'Password123!',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test 2: Invalid login.
     * Invalid credentials are rejected. Confirm the appropriate redirect or API 401 response.
     */
    public function test_invalid_login(): void
    {
        $user = User::factory()->create([
            'email' => 'doctor@example.com',
            'password' => 'Password123!',
        ]);

        // Web rejection
        $response = $this->post('/login', [
            'email' => 'doctor@example.com',
            'password' => 'WrongPassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();

        // API rejection returns HTTP 401
        $apiResponse = $this->postJson('/api/login', [
            'email' => 'doctor@example.com',
            'password' => 'WrongPassword',
        ]);

        $apiResponse->assertStatus(401)
                    ->assertJson([
                        'success' => false,
                        'message' => 'Invalid email or password.',
                    ]);
    }

    /**
     * Test 3: Guest protection.
     * An unauthenticated user cannot access protected web case pages or protected API endpoints.
     */
    public function test_guest_protection(): void
    {
        // Protected web routes redirect to login
        $webDashboard = $this->get('/dashboard');
        $webDashboard->assertRedirect(route('login'));

        $webCases = $this->get('/cases');
        $webCases->assertRedirect(route('login'));

        $webCreate = $this->get('/cases/create');
        $webCreate->assertRedirect(route('login'));

        // Protected API endpoints return HTTP 401
        $apiCases = $this->getJson('/api/cases');
        $apiCases->assertStatus(401)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Unauthenticated.',
                 ]);
    }

    /**
     * Test 4: Admin can create a case.
     * Authenticate as Admin, submit valid case data, verify successful creation, verify the database record and ensure created_by is assigned correctly.
     */
    public function test_admin_can_create_a_case(): void
    {
        $admin = User::factory()->admin()->create();

        $caseData = [
            'case_number' => 'CASE-TEST-001',
            'patient_reference' => 'PAT-9912-T',
            'surgeon_name' => 'Dr. Robert Shaw',
            'implant_type' => 'Titanium Hip Stem 14mm',
            'status' => 'Planning',
            'priority' => 'High',
            'surgery_date' => '2026-11-20',
        ];

        // Web Creation
        $response = $this->actingAs($admin)->post('/cases', $caseData);

        $createdCase = CaseRecord::where('case_number', 'CASE-TEST-001')->first();
        $this->assertNotNull($createdCase);
        $this->assertEquals($admin->id, $createdCase->created_by);
        $this->assertEquals('Dr. Robert Shaw', $createdCase->surgeon_name);
        $response->assertRedirect(route('cases.show', $createdCase));

        // API Creation
        $apiAdmin = User::factory()->admin()->create();
        $apiData = [
            'case_number' => 'CASE-TEST-002',
            'patient_reference' => 'PAT-8812-Y',
            'surgeon_name' => 'Dr. Clara Oswald',
            'implant_type' => 'Ceramic Ankle Prosthesis',
            'status' => 'Approved',
            'priority' => 'Medium',
            'surgery_date' => '2026-12-05',
        ];

        \Laravel\Sanctum\Sanctum::actingAs($apiAdmin, ['*']);
        $apiResponse = $this->postJson('/api/cases', $apiData);

        $apiResponse->assertStatus(201)
                    ->assertJsonPath('success', true)
                    ->assertJsonPath('data.case_number', 'CASE-TEST-002')
                    ->assertJsonPath('data.created_by', $apiAdmin->id);

        $this->assertDatabaseHas('cases', [
            'case_number' => 'CASE-TEST-002',
            'created_by' => $apiAdmin->id,
        ]);
    }

    /**
     * Test 5: Duplicate case number is rejected.
     * Attempt to create a case using an existing case number. Verify HTTP 422 for the API and appropriate field validation feedback for the web form.
     */
    public function test_duplicate_case_number_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();

        CaseRecord::factory()->create([
            'case_number' => 'CASE-DUPLICATE-99',
            'created_by' => $admin->id,
        ]);

        $duplicatePayload = [
            'case_number' => 'CASE-DUPLICATE-99',
            'patient_reference' => 'PAT-5555',
            'surgeon_name' => 'Dr. Duplicate',
            'implant_type' => 'Spinal Rod',
            'status' => 'Planning',
            'priority' => 'Low',
        ];

        // Web rejection
        $webResponse = $this->actingAs($admin)->post('/cases', $duplicatePayload);
        $webResponse->assertSessionHasErrors(['case_number']);

        // API rejection returns HTTP 422 with field-level errors
        $token = $admin->createToken('test-token')->plainTextToken;
        $apiResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
                            ->postJson('/api/cases', $duplicatePayload);

        $apiResponse->assertStatus(422)
                    ->assertJson([
                        'success' => false,
                        'message' => 'Validation failed.',
                    ])
                    ->assertJsonValidationErrors(['case_number']);
    }

    /**
     * Test 6: Viewer cannot delete a case.
     * Authenticate as Viewer, attempt deletion, verify HTTP 403 and verify the case still exists.
     */
    public function test_viewer_cannot_delete_a_case(): void
    {
        $admin = User::factory()->admin()->create();
        $viewer = User::factory()->viewer()->create();

        $case = CaseRecord::factory()->create([
            'case_number' => 'CASE-NODELETE-01',
            'created_by' => $admin->id,
        ]);

        // Viewer web deletion attempt
        $webResponse = $this->actingAs($viewer)->delete("/cases/{$case->id}");
        $webResponse->assertStatus(403);
        $this->assertDatabaseHas('cases', ['id' => $case->id]);

        // Viewer API deletion attempt
        $viewerToken = $viewer->createToken('viewer-token')->plainTextToken;
        $apiResponse = $this->withHeader('Authorization', 'Bearer ' . $viewerToken)
                            ->deleteJson("/api/cases/{$case->id}");

        $apiResponse->assertStatus(403)
                    ->assertJson([
                        'success' => false,
                        'message' => 'This action is unauthorized.',
                    ]);

        $this->assertDatabaseHas('cases', ['id' => $case->id]);
    }

    /**
     * Additional Test 7: Viewer cannot create or edit a case (Web and API).
     */
    public function test_viewer_cannot_create_or_update_a_case(): void
    {
        $viewer = User::factory()->viewer()->create();
        $admin = User::factory()->admin()->create();
        $case = CaseRecord::factory()->create(['created_by' => $admin->id]);

        // Attempt Create Web
        $this->actingAs($viewer)->get('/cases/create')->assertStatus(403);
        $this->actingAs($viewer)->post('/cases', [
            'case_number' => 'CASE-FORBIDDEN-01',
            'patient_reference' => 'PAT-0000',
            'surgeon_name' => 'Dr. Forbidden',
            'implant_type' => 'Illegal Mesh',
            'status' => 'Draft',
            'priority' => 'Low',
        ])->assertStatus(403);

        // Attempt Edit Web
        $this->actingAs($viewer)->get("/cases/{$case->id}/edit")->assertStatus(403);
        $this->actingAs($viewer)->put("/cases/{$case->id}", [
            'case_number' => $case->case_number,
            'patient_reference' => 'PAT-0000',
            'surgeon_name' => 'Dr. Forbidden',
            'implant_type' => 'Modified Type',
            'status' => 'Completed',
            'priority' => 'High',
        ])->assertStatus(403);

        // Attempt API Create & Update
        $token = $viewer->createToken('viewer-token')->plainTextToken;
        $this->withHeader('Authorization', 'Bearer ' . $token)
             ->postJson('/api/cases', [
                 'case_number' => 'CASE-FORBIDDEN-02',
                 'patient_reference' => 'PAT-0000',
                 'surgeon_name' => 'Dr. Forbidden',
                 'implant_type' => 'Illegal Mesh',
                 'status' => 'Draft',
                 'priority' => 'Low',
             ])->assertStatus(403);

        $this->withHeader('Authorization', 'Bearer ' . $token)
             ->putJson("/api/cases/{$case->id}", [
                 'case_number' => $case->case_number,
                 'patient_reference' => 'PAT-MOD',
                 'surgeon_name' => 'Dr. Mod',
                 'implant_type' => 'Modified Mesh',
                 'status' => 'Completed',
                 'priority' => 'High',
             ])->assertStatus(403);
    }

    /**
     * Additional Test 8: Admin can update and delete a case.
     */
    public function test_admin_can_update_and_delete_a_case(): void
    {
        $admin = User::factory()->admin()->create();
        $case = CaseRecord::factory()->create([
            'case_number' => 'CASE-UPDATE-01',
            'surgeon_name' => 'Initial Surgeon',
            'status' => 'Planning',
            'created_by' => $admin->id,
        ]);

        // Update Case
        $response = $this->actingAs($admin)->put("/cases/{$case->id}", [
            'case_number' => 'CASE-UPDATE-01', // Keeps existing case number
            'patient_reference' => 'PAT-UPDATED',
            'surgeon_name' => 'Dr. Updated Surgeon',
            'implant_type' => 'Updated Ceramic Knee',
            'status' => 'Approved',
            'priority' => 'High',
            'surgery_date' => '2026-11-30',
        ]);

        $response->assertRedirect(route('cases.show', $case));
        $this->assertDatabaseHas('cases', [
            'id' => $case->id,
            'surgeon_name' => 'Dr. Updated Surgeon',
            'status' => 'Approved',
        ]);

        // Delete Case
        $deleteResponse = $this->actingAs($admin)->delete("/cases/{$case->id}");
        $deleteResponse->assertRedirect(route('cases.index'));
        $this->assertDatabaseMissing('cases', ['id' => $case->id]);
    }

    /**
     * Additional Test 9: Search and status filter behavior.
     */
    public function test_case_search_and_filters(): void
    {
        $admin = User::factory()->admin()->create();

        CaseRecord::factory()->create([
            'case_number' => 'CASE-TARGET-ALPHA',
            'surgeon_name' => 'Dr. Christopher Vance',
            'status' => 'Planning',
            'created_by' => $admin->id,
        ]);

        CaseRecord::factory()->create([
            'case_number' => 'CASE-OTHER-BETA',
            'surgeon_name' => 'Dr. Elena Rostova',
            'status' => 'Completed',
            'created_by' => $admin->id,
        ]);

        // Search by case number
        $responseSearch = $this->actingAs($admin)->get('/cases?search=TARGET');
        $responseSearch->assertSee('CASE-TARGET-ALPHA');
        $responseSearch->assertDontSee('CASE-OTHER-BETA');

        // Filter by status
        $responseStatus = $this->actingAs($admin)->get('/cases?status=Completed');
        $responseStatus->assertSee('CASE-OTHER-BETA');
        $responseStatus->assertDontSee('CASE-TARGET-ALPHA');
    }

    /**
     * Additional Test 10: Bonus A - Engineer can create and update, but cannot delete.
     */
    public function test_engineer_permissions(): void
    {
        $engineer = User::factory()->engineer()->create();
        $admin = User::factory()->admin()->create();
        $case = CaseRecord::factory()->create(['created_by' => $admin->id]);

        // Engineer can create
        $createRes = $this->actingAs($engineer)->post('/cases', [
            'case_number' => 'CASE-ENG-001',
            'patient_reference' => 'PAT-ENG-1',
            'surgeon_name' => 'Dr. Engineer',
            'implant_type' => 'Custom Titanium Plate',
            'status' => 'Planning',
            'priority' => 'Medium',
        ]);
        $createRes->assertRedirect();
        $this->assertDatabaseHas('cases', ['case_number' => 'CASE-ENG-001']);

        // Engineer can update
        $updateRes = $this->actingAs($engineer)->put("/cases/{$case->id}", [
            'case_number' => $case->case_number,
            'patient_reference' => 'PAT-MOD-ENG',
            'surgeon_name' => 'Dr. Modified',
            'implant_type' => 'Modified Specs',
            'status' => 'In Review',
            'priority' => 'High',
        ]);
        $updateRes->assertRedirect();

        // Engineer CANNOT delete
        $deleteRes = $this->actingAs($engineer)->delete("/cases/{$case->id}");
        $deleteRes->assertStatus(403);
        $this->assertDatabaseHas('cases', ['id' => $case->id]);
    }

    /**
     * Additional Test 11: Sanctum API token issuance and revocation.
     */
    public function test_api_token_issuance_and_revocation(): void
    {
        $user = User::factory()->create([
            'email' => 'api_tester@jajalmedical.com',
            'password' => 'SecurePass123!',
            'role' => 'admin',
        ]);

        // Login & Receive Token
        $loginRes = $this->postJson('/api/login', [
            'email' => 'api_tester@jajalmedical.com',
            'password' => 'SecurePass123!',
        ]);

        $loginRes->assertStatus(200)
                 ->assertJsonPath('success', true)
                 ->assertJsonStructure(['data' => ['token', 'token_type', 'user']]);

        $token = $loginRes->json('data.token');

        // Access protected API with token
        $casesRes = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->getJson('/api/cases');
        $casesRes->assertStatus(200)
                 ->assertJsonPath('success', true);

        // Logout & Revoke token
        $logoutRes = $this->withHeader('Authorization', 'Bearer ' . $token)
                          ->postJson('/api/logout');
        $logoutRes->assertStatus(200)
                  ->assertJsonPath('success', true);

        // Subsequent call with revoked token returns 401
        $this->flushSession();
        $this->app['auth']->forgetGuards();

        $subsequentRes = $this->withHeader('Authorization', 'Bearer ' . $token)
                              ->getJson('/api/cases');
        $subsequentRes->assertStatus(401);
    }

    /**
     * Additional Test 12: Dashboard counts match database queries exactly.
     */
    public function test_dashboard_counts_match_database(): void
    {
        $admin = User::factory()->admin()->create();

        CaseRecord::factory()->create(['status' => 'Planning', 'created_by' => $admin->id]);
        CaseRecord::factory()->create(['status' => 'Planning', 'created_by' => $admin->id]);
        CaseRecord::factory()->create(['status' => 'Approved', 'created_by' => $admin->id]);
        CaseRecord::factory()->create(['status' => 'In Review', 'created_by' => $admin->id]);
        CaseRecord::factory()->create(['status' => 'Completed', 'created_by' => $admin->id]);
        CaseRecord::factory()->create(['status' => 'Draft', 'created_by' => $admin->id]);
        CaseRecord::factory()->create(['status' => 'Cancelled', 'created_by' => $admin->id]);

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertStatus(200);

        // 7 total cases
        $response->assertViewHas('totalCases', 7);
        $response->assertViewHas('planningCount', 2);
        $response->assertViewHas('approvedCount', 1);
        $response->assertViewHas('inReviewCount', 1);
        $response->assertViewHas('completedCount', 1);
        $response->assertViewHas('draftCount', 1);
        $response->assertViewHas('cancelledCount', 1);
    }
}
