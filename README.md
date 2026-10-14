# Jajal Medical Case Management System

A production-minded, full-stack Medical Case Management Web Application developed for the **Jajal Medical Software Developer Intern Practical Assignment**.

This application enables authorized hospital administrators and surgical engineers to register, track, and monitor surgical implant cases from initial draft through planning, clinical review, approval, and procedural completion.

---

## 1. Problem Statement & Scenario
A medical device enterprise requires a secure, auditable administrative case-tracking system to manage patient-implant lifecycles. Authorized clinical staff log in, review case statistics on an interactive dashboard, search and filter cases across surgical pipelines, and safely progress case statuses according to role-based access controls (RBAC).

> [!IMPORTANT]
> **Data Privacy Notice:** All records utilize dummy references (e.g. `PAT-8492-X1`). Never use real patient names, HIPAA-identifiable data, or actual medical records. This application is an administrative tracking tool, not a clinical diagnostic system.

---

## 2. Key Features
- **Role-Based Access Control (RBAC):**
  - **Admin:** Full CRUD privileges (Create, View, Edit status/details, and Delete cases) + Admin API access.
  - **Viewer:** Read-only access (Dashboard, Search & Filter cases, View full case details). Server-side rejection (HTTP 403) for create/edit/delete attempts.
  - **Engineer (Bonus A):** Technical specialist permissions (Create, View, and Edit cases/status; cannot delete).
- **Interactive Dashboard:**
  - 5 live metric cards (**Total Cases**, **Planning**, **In Review**, **Approved**, **Completed**) calculated directly from real database queries (with Draft and Cancelled accurately contributing to Total Cases).
  - Recent cases activity feed with immediate detail links.
- **Search & Filter Pipeline:**
  - Pagination at exactly **10 records per page**, preserving active search and filter parameters across page links.
  - Case Number search and Surgeon Name search.
  - Status filter across all 6 allowed values.
  - Multi-column sorting with ascending/descending order allowlisting.
- **Robust Validation & Security:**
  - Unique Case Number validation (with proper ID exclusion on updates).
  - Strict server-side validation using Laravel Form Requests.
  - Mass assignment protection via Eloquent `$fillable`.
  - Automatic `created_by` foreign key assignment preventing identity spoofing.
  - CSRF-protected web forms with double-confirmation dialogs for destructive deletions.
- **Sanctum REST API:**
  - Token-based API authentication (`/api/login`, `/api/logout`).
  - Consistent JSON response contract (`success`, `data`, `message`, `errors`).
  - Complete case endpoints matching web authorization policies.
- **External Public Holiday Integration (Bonus C):**
  - Live query to the Nager.Date API highlighting public holidays coinciding with scheduled surgery dates.
  - Fault-tolerant design with reasonable timeout, preventing external API failures from blocking local operations.

---

## 3. Technology Stack & Actual Versions
- **Backend Framework:** Laravel 12.70.0
- **Language / Runtime:** PHP 8.2.12 (CLI)
- **Database:** MySQL / MariaDB 10.4.32 (with SQLite for isolated feature test suite)
- **API Authentication:** Laravel Sanctum 4.3.3
- **Frontend / Styling:** Laravel Blade, Tailwind CSS 4.0, Vite 7.3.7, Lucide Icons
- **Testing Suite:** PHPUnit 11.5.57

---

## 4. Default Seeded Credentials

The following dummy accounts are provisioned via `DatabaseSeeder`:

| Role | Email Address | Password | Permissions |
|---|---|---|---|
| **Admin** | `admin@jajalmedical.com` | `AdminPass123!` | Full CRUD + API Full Access |
| **Viewer** | `viewer@jajalmedical.com` | `ViewerPass123!` | Read-only Dashboard & Cases |
| **Engineer** | `engineer@jajalmedical.com` | `EngineerPass123!` | Create, View, Edit (No Delete) |

---

## 5. Prerequisites & Local Installation

### Prerequisites
- PHP >= 8.2 with PDO, OpenSSL, cURL, and Mbstring extensions
- Composer >= 2.0
- Node.js >= 18.0 & npm
- MySQL / MariaDB service running on `127.0.0.1:3306`

### Step-by-Step Setup
1. **Clone or Navigate to the Repository:**
   ```bash
   cd c:/Users/ashis/Desktop/practical
   ```

2. **Install PHP Dependencies:**
   ```bash
   composer install
   ```

3. **Install & Build Frontend Assets:**
   ```bash
   npm install
   npm run build
   ```

4. **Environment Configuration:**
   Copy `.env.example` to `.env` (or verify existing `.env`):
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database Migration & Seeding:**
   Ensure MySQL has the `jajal_medical` database created:
   ```bash
   php artisan migrate:fresh --seed
   ```
   *This seeds the Admin, Viewer, Engineer accounts and 25 realistic dummy medical cases.*

6. **Start the Local Development Server:**
   ```bash
   php artisan serve
   ```
   The application will be accessible at [http://localhost:8000](http://localhost:8000).

---

## 6. Automated Testing

Run the automated test suite with database isolation using RefreshDatabase:
```bash
php artisan test
```

### Verification Results
All 12 feature tests pass successfully:
- `test_valid_login`: User logs in and authenticates session.
- `test_invalid_login`: Invalid credentials rejected (Web session error, API 401).
- `test_guest_protection`: Unauthenticated guests redirected on web and rejected with 401 on API.
- `test_admin_can_create_a_case`: Verifies database insertion and creator relation on Web and API.
- `test_duplicate_case_number_is_rejected`: Unique database constraint and Form Request validation (HTTP 422).
- `test_viewer_cannot_delete_a_case`: Viewer receives 403 Forbidden; record remains intact.
- `test_viewer_cannot_create_or_update_a_case`: Viewer receives 403 on create and edit attempts.
- `test_admin_can_update_and_delete_a_case`: Verifies update and permanent deletion by Admin.
- `test_case_search_and_filters`: Verifies case number/surgeon search and status filters.
- `test_engineer_permissions`: Verifies Engineer can create/edit but receives 403 on delete.
- `test_api_token_issuance_and_revocation`: Verifies Sanctum token lifecycle and revocation.
- `test_dashboard_counts_match_database`: Verifies live database counts for all five metric cards.

---

## 7. REST API Documentation

### Endpoints Overview

| Method | Endpoint | Authorization | Description |
|---|---|---|---|
| `POST` | `/api/login` | Public | Authenticate and obtain Sanctum Bearer token |
| `POST` | `/api/logout` | Authenticated | Revoke current Bearer token |
| `GET` | `/api/cases` | Admin, Viewer, Engineer | Paginated case listing with search & filters |
| `POST` | `/api/cases` | Admin, Engineer | Create a new case (Creator set automatically) |
| `GET` | `/api/cases/{id}` | Admin, Viewer, Engineer | View full case details and creator audit info |
| `PUT` | `/api/cases/{id}` | Admin, Engineer | Update case details and status |
| `DELETE`| `/api/cases/{id}` | Admin Only | Delete case permanently (HTTP 403 for others) |

### API Postman Collection
An importable Postman / Thunder Client collection is included in the repository at:
[`docs/jajal_medical_api_collection.json`](file:///c:/Users/ashis/Desktop/practical/docs/jajal_medical_api_collection.json)

### Example API Request: Authenticate
```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@jajalmedical.com","password":"AdminPass123!"}'
```

### Example API Request: Create Case (Bearer Token)
```bash
curl -X POST http://127.0.0.1:8000/api/cases \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer <TOKEN>" \
  -d '{
    "case_number": "CASE-9050",
    "patient_reference": "PAT-3382-Q",
    "surgeon_name": "Dr. Christopher Chen",
    "implant_type": "Spinal Fusion Cage - Expandable PEEK",
    "status": "Planning",
    "priority": "High",
    "surgery_date": "2026-11-15"
  }'
```

---

## 8. Documentation Reference
- [Database Architecture & Schema Documentation](file:///c:/Users/ashis/Desktop/practical/docs/database.md)
- [Requirements Traceability Matrix](file:///c:/Users/ashis/Desktop/practical/docs/traceability.md)
- [AWS Cloud Deployment Plan](file:///c:/Users/ashis/Desktop/practical/docs/deployment.md)

---

## 9. Git Repository & Publishing
The project is initialized as a local Git repository with logical, structured commits:
- Remote URL Placeholder: `https://github.com/your-username/jajal-medical-case-management.git`
- To push to a remote repository:
  ```bash
  git remote add origin <REMOTE_URL>
  git branch -M main
  git push -u origin main
  ```
