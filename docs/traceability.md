# Requirements Traceability Matrix: Jajal Medical Case Management System

This document maps the technical and functional requirements specified for the Jajal Medical Software Developer Intern Practical Assignment to their actual code implementations and automated test verifications.

| Requirement ID | Specification Requirement | Architecture & Implementation | Verification Test |
|---|---|---|---|
| **REQ-001** | Admin shall be able to create a case | `App\Http\Controllers\CaseController::store`, `App\Http\Controllers\Api\CaseController::store`, `App\Policies\CasePolicy::create` | `MedicalCaseManagementTest::test_admin_can_create_a_case` |
| **REQ-002** | Case Number shall be unique | Database Unique Index `cases_case_number_unique`, `App\Http\Requests\StoreCaseRequest`, `App\Http\Requests\UpdateCaseRequest` | `MedicalCaseManagementTest::test_duplicate_case_number_is_rejected` |
| **REQ-003** | Only Admin shall delete cases (Viewer cannot delete) | `App\Policies\CasePolicy::delete`, `App\Http\Controllers\CaseController::destroy`, `App\Http\Controllers\Api\CaseController::destroy` | `MedicalCaseManagementTest::test_viewer_cannot_delete_a_case` |
| **REQ-004** | Valid login and session/token generation | `App\Http\Controllers\Auth\AuthenticatedSessionController::store`, `App\Http\Controllers\Api\AuthController::login` | `MedicalCaseManagementTest::test_valid_login`, `MedicalCaseManagementTest::test_api_token_issuance_and_revocation` |
| **REQ-005** | Invalid login rejection with proper status | Form validation in `AuthenticatedSessionController`, HTTP 401 in `Api\AuthController` | `MedicalCaseManagementTest::test_invalid_login` |
| **REQ-006** | Guest protection on protected web & API endpoints | `auth` web middleware, `auth:sanctum` API middleware, `bootstrap/app.php` exception handler | `MedicalCaseManagementTest::test_guest_protection` |
| **REQ-007** | Viewer cannot create or edit cases | `App\Policies\CasePolicy` (`create`, `update`), `StoreCaseRequest`, `UpdateCaseRequest` | `MedicalCaseManagementTest::test_viewer_cannot_create_or_update_a_case` |
| **REQ-008** | Admin can update and delete cases | `App\Http\Controllers\CaseController` (`update`, `destroy`), `App\Http\Controllers\Api\CaseController` | `MedicalCaseManagementTest::test_admin_can_update_and_delete_a_case` |
| **REQ-009** | Search by case number / surgeon and filter by status | Eloquent Scopes `scopeSearch` and `scopeWithStatus` in `App\Models\CaseRecord`, `CaseController::index` | `MedicalCaseManagementTest::test_case_search_and_filters` |
| **REQ-010** | Live Dashboard metrics from real database queries | `App\Http\Controllers\DashboardController::index` with `CaseRecord::where('status', ...)->count()` | `MedicalCaseManagementTest::test_dashboard_counts_match_database` |
| **REQ-011** | Engineer role permissions (Bonus A) | `App\Policies\CasePolicy` granting create/update and denying delete to `engineer` role | `MedicalCaseManagementTest::test_engineer_permissions` |
| **REQ-012** | REST API Token Revocation upon Logout | `App\Http\Controllers\Api\AuthController::logout` revoking Sanctum access token | `MedicalCaseManagementTest::test_api_token_issuance_and_revocation` |
