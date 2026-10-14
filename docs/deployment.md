# Cloud Deployment Architecture: Jajal Medical Case Management

## 1. Overview
This document outlines the recommended production cloud hosting plan on Amazon Web Services (AWS) for the **Jajal Medical Case Management System**. The application requires high availability, HIPAA-conscious security isolation, encrypted data-at-rest, and automated disaster recovery.

---

## 2. Target AWS Infrastructure Architecture

```
                       [ Internet Users / API Clients ]
                                      |
                                  HTTPS (443)
                                      v
                      [ AWS Route 53 (DNS) + ACM SSL ]
                                      |
                                      v
                 [ AWS Application Load Balancer (ALB) ]
                                      |
                     +----------------+----------------+
                     | (Private Subnet AZ-A)           | (Private Subnet AZ-B)
                     v                                 v
        [ EC2: Nginx + PHP 8.2-FPM ]      [ EC2: Nginx + PHP 8.2-FPM ]
        (Auto-Scaling Group)              (Auto-Scaling Group)
                     |                                 |
                     +----------------+----------------+
                                      |
                                      v
                      [ Amazon RDS MySQL 8.0 / Multi-AZ ]
                          (Encrypted with AWS KMS)
                                      |
                                      +----> Daily Automated Snapshots
                                      |
                      [ Amazon S3 (Encrypted Storage) ]
                          (Future attachment uploads)
```

---

## 3. Component Details

### 3.1 Compute (Amazon EC2 & ALB)
- **Application Servers**: Amazon EC2 `t3.medium` instances running Ubuntu 22.04 LTS with PHP 8.2-FPM, Composer, and Nginx.
- **Load Balancer**: AWS Application Load Balancer (ALB) terminating TLS 1.3 certificates provisioned through AWS Certificate Manager (ACM).
- **Auto-Scaling**: Configured across two Availability Zones (Multi-AZ) to dynamically scale based on CPU (>70%) and HTTP request counts.

### 3.2 Database (Amazon RDS for MySQL)
- **Engine**: MySQL 8.0.x Community Edition or MariaDB 10.4+.
- **Instance**: `db.t3.medium` Multi-AZ deployment for automated failover.
- **Storage**: General Purpose SSD (gp3) with automated storage expansion.
- **Encryption**: KMS-managed customer master key (CMK) for encryption at rest. SSL/TLS enforced for all client connections (`PDO::MYSQL_ATTR_SSL_CA`).

### 3.3 Network & Security Groups
- **VPC Configuration**: Custom VPC (`10.0.0.0/16`) divided into public subnets (ALB only) and isolated private subnets (EC2 and RDS).
- **Security Groups**:
  - `alb-sg`: Allows inbound `80` (redirect to `443`) and `443` from `0.0.0.0/0`.
  - `app-sg`: Inbound `80` allowed strictly from `alb-sg`.
  - `db-sg`: Inbound `3306` allowed strictly from `app-sg`.

### 3.4 Secrets Management
- Environment variables (`APP_KEY`, `DB_PASSWORD`, `SANCTUM_SECRET`) stored in **AWS Systems Manager Parameter Store** or **AWS Secrets Manager**, injected into the systemd environment during boot. Never committed to source control.

---

## 4. Production Deployment & Zero-Downtime Pipeline

1. **Build Step**:
   - Install Composer production dependencies: `composer install --no-dev --optimize-autoloader`.
   - Compile Vite assets: `npm ci && npm run build`.
2. **Pre-Deployment**:
   - Cache configurations: `php artisan config:cache && php artisan route:cache && php artisan view:cache`.
3. **Database Migrations**:
   - Run transactional migrations: `php artisan migrate --force`.
4. **Health Check**:
   - ALB monitors `/up` health route. Unhealthy instances are drained before incoming traffic shifts.
5. **Backups & Retention**:
   - Automated RDS daily snapshots with 30-day retention and point-in-time recovery (PITR).
