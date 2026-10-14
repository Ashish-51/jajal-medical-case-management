# Database Design Documentation: Jajal Medical Case Management System

## 1. Overview
The Jajal Medical Case Management System uses a relational database architecture (MySQL in production / local environment, SQLite for in-memory isolated automated testing). The system tracks implant-related medical cases from early draft through planning, clinical review, approval, and surgical completion.

---

## 2. Entity Relationship Model

```
+---------------------------------------+
|                 users                 |
+---------------------------------------+
| id                  (PK, BIGINT)      |
| name                (VARCHAR 255)     |
| email               (VARCHAR 255, UQ) |
| password            (VARCHAR 255)     |
| role                (VARCHAR 50, IDX) | <--- 'admin', 'viewer', 'engineer'
| email_verified_at   (TIMESTAMP, NULL) |
| remember_token      (VARCHAR 100)     |
| created_at          (TIMESTAMP)       |
| updated_at          (TIMESTAMP)       |
+---------------------------------------+
                   | 1
                   |
                   | hasMany / created_by (FK)
                   v N
+---------------------------------------+
|                 cases                 |
+---------------------------------------+
| id                  (PK, BIGINT)      |
| case_number         (VARCHAR 50, UQ)  | <--- e.g. CASE-1001 (Unique Index)
| patient_reference   (VARCHAR 100)     | <--- Dummy alphanumeric ref, NEVER real PII
| surgeon_name        (VARCHAR 150)     |
| implant_type        (VARCHAR 200)     |
| status              (ENUM, INDEX)     | <--- 'Draft','Planning','In Review','Approved','Completed','Cancelled'
| priority            (ENUM)            | <--- 'Low','Medium','High'
| surgery_date        (DATE, NULL)      |
| created_by          (FK -> users.id)  | <--- ON DELETE RESTRICT (Prevents orphaned cases)
| created_at          (TIMESTAMP)       |
| updated_at          (TIMESTAMP)       |
+---------------------------------------+
```

---

## 3. Schema Definitions

### 3.1 Users Table (`users`)
Managed by Laravel's migration system (`0001_01_01_000000_create_users_table.php`).

| Column | Type | Nullable | Attributes / Constraints | Description |
|---|---|---|---|---|
| `id` | `BIGINT UNSIGNED` | No | Auto Increment, Primary Key | Unique user record identifier |
| `name` | `VARCHAR(255)` | No | — | Full name of the user |
| `email` | `VARCHAR(255)` | No | `UNIQUE INDEX` | User login email address |
| `password` | `VARCHAR(255)` | No | — | Bcrypt-hashed password |
| `role` | `VARCHAR(50)` | No | Default: `'viewer'`, `INDEX` | System role: `'admin'`, `'viewer'`, or `'engineer'` |
| `email_verified_at` | `TIMESTAMP` | Yes | — | Timestamp of email verification |
| `remember_token` | `VARCHAR(100)` | Yes | — | Web "Remember Me" session token |
| `created_at` | `TIMESTAMP` | Yes | — | Record creation timestamp |
| `updated_at` | `TIMESTAMP` | Yes | — | Record last modified timestamp |

### 3.2 Cases Table (`cases`)
Managed by migration `2026_10_14_070000_create_cases_table.php`.

| Column | Type | Nullable | Attributes / Constraints | Description |
|---|---|---|---|---|
| `id` | `BIGINT UNSIGNED` | No | Auto Increment, Primary Key | Unique database ID |
| `case_number` | `VARCHAR(50)` | No | `UNIQUE INDEX` | External case code (e.g. `CASE-1001`) |
| `patient_reference` | `VARCHAR(100)` | No | — | Anonymized patient reference (e.g. `PAT-8492-X1`) |
| `surgeon_name` | `VARCHAR(150)` | No | — | Lead operating surgeon name |
| `implant_type` | `VARCHAR(200)` | No | — | Specific prosthesis or surgical implant |
| `status` | `ENUM` | No | `INDEX` | `'Draft'`, `'Planning'`, `'In Review'`, `'Approved'`, `'Completed'`, `'Cancelled'` |
| `priority` | `ENUM` | No | Default: `'Medium'` | `'Low'`, `'Medium'`, `'High'` |
| `surgery_date` | `DATE` | Yes | — | Scheduled date of implantation |
| `created_by` | `BIGINT UNSIGNED` | No | `FOREIGN KEY -> users.id` (`ON DELETE RESTRICT`) | Authenticated creator |
| `created_at` | `TIMESTAMP` | Yes | — | Automatic Eloquent timestamp |
| `updated_at` | `TIMESTAMP` | Yes | — | Automatic Eloquent timestamp |

### 3.3 Personal Access Tokens Table (`personal_access_tokens`)
Managed by Laravel Sanctum (`2026_10_14_064750_create_personal_access_tokens_table.php`).
Stores SHA-256 hashed bearer tokens for REST API authorization.

---

## 4. Key Relationships and Integrity Constraints

1. **User to Cases (`1:N`)**:
   - `User::cases()` defines `hasMany(CaseRecord::class, 'created_by')`.
   - `CaseRecord::creator()` defines `belongsTo(User::class, 'created_by')`.
   - Foreign Key constraint enforces `ON DELETE RESTRICT` ensuring cases cannot silently lose their audit trail.
2. **Case Number Uniqueness**:
   - Enforced at the database layer with a `UNIQUE INDEX` on `cases.case_number`.
   - Enforced at the application layer via `StoreCaseRequest` and `UpdateCaseRequest` (using `Rule::unique()->ignore($id)`).
3. **Status Index**:
   - `status` column has a dedicated B-tree index enabling high-performance status filtering and dashboard counting queries.
