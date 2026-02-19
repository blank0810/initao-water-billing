# Initao Water Billing System - Demo Feature Guide

> **Purpose:** Personal reference for presenting the system to water district staff.
> **Login as:** Super Admin (`admin@initao-water.gov.ph` / `password`)
> **App URL:** `http://localhost:9000` (Docker) or `http://localhost:8000` (native)

---

## Table of Contents

1. [Pre-Demo Setup Checklist](#1-pre-demo-setup-checklist)
2. [Test Accounts Quick Reference](#2-test-accounts-quick-reference)
3. [Feature Walkthrough](#3-feature-walkthrough)
   - [3.1 Dashboard Overview](#31-dashboard-overview)
   - [3.2 Initial Setup & Configuration](#32-initial-setup--configuration)
   - [3.3 Customer Registration](#33-customer-registration)
   - [3.4 Service Application Workflow](#34-service-application-workflow)
   - [3.5 Service Connection & Meter Assignment](#35-service-connection--meter-assignment)
   - [3.6 Meter Reading (Manual + Scheduling)](#36-meter-reading-manual--scheduling)
   - [3.7 Billing Generation & Adjustments](#37-billing-generation--adjustments)
   - [3.8 Payment Processing & Allocation](#38-payment-processing--allocation)
   - [3.9 Customer Ledger & Statements](#39-customer-ledger--statements)
   - [3.10 Reports & Exports](#310-reports--exports)
   - [3.11 Admin: Users, Roles & Permissions](#311-admin-users-roles--permissions)
   - [3.12 Admin: Geographic & Billing Config](#312-admin-geographic--billing-config)
4. [Open Questions for Discussion](#4-open-questions-for-discussion)

---

## 1. Pre-Demo Setup Checklist

### First-Time Setup (Fresh Machine)

```bash
# 1. Clone the repository
git clone <repository-url>
cd initao-water-billing

# 2. One-command setup (installs everything, migrates, seeds, builds)
npm run setup:fresh
```

This single command does:
- `composer install` (PHP dependencies)
- Copies `.env.example` to `.env` if missing
- Generates app key
- `npm install` (JS dependencies)
- `php artisan migrate:fresh --seed` (fresh database with all seed data)
- `npm run build` (compiles frontend assets)

### What Gets Seeded

| Category | Data Created |
|----------|-------------|
| **Location** | 1 Province (Misamis Oriental), 1 Town (Initao), 16 Barangays, 384 Puroks |
| **Statuses** | 13 statuses (PENDING, ACTIVE, INACTIVE, OVERDUE, etc.) |
| **Users** | 7 test users (1 per role) - all password: `password` |
| **Roles** | 6 roles with 18 permissions across 8 modules |
| **Account Types** | Residential, Commercial |
| **Water Rates** | 13 tiered rate tiers (4 tiers per account type) |
| **Charge Items** | 10 fee templates (Connection Fee, Deposits, etc.) |
| **Adjustment Types** | 5 types (Meter Error, Penalty Waiver, etc.) |
| **Meters** | 25 meters (5 brands: Neptune, Sensus, Badger, Itron, Master Meter) |
| **Connections** | 5 sample connections (3 Residential, 2 Commercial) |
| **Period** | Current month billing period |

### Before Each Demo Session

```bash
# Quick reset to clean state (drops everything and re-seeds)
npm run setup:fresh

# Or if you just want to start the servers
composer dev
```

### Docker Setup (Alternative)

```bash
docker-compose up -d          # Start services
npm run sync:docker            # Install deps + migrate + seed inside container
```

**Service URLs:**
- App: http://localhost:9000
- PhpMyAdmin: http://localhost:8080
- Mailpit (email testing): http://localhost:8025

---

## 2. Test Accounts Quick Reference

| Role | Username | Email | What They Can Do |
|------|----------|-------|-----------------|
| **Super Admin** | `super_admin` | `admin@initao-water.gov.ph` | Full access to everything |
| **Admin** | `admin_user` | `admin@test.com` | User management + all features |
| **Billing Officer** | `billing_officer` | `billing@test.com` | Billing, payments, meter readings |
| **Meter Reader** | `meter_reader` | `meter@test.com` | Meter reading entry only |
| **Cashier** | `cashier` | `cashier@test.com` | Payment processing only |
| **Viewer** | `viewer` | `viewer@test.com` | Read-only access |

> **All passwords:** `password`
>
> **Demo tip:** Login as Super Admin for the full demo. Switch to other roles to show permission-based access.

---

## 3. Feature Walkthrough

### 3.1 Dashboard Overview

**What it does:** Landing page after login showing system overview and quick stats.

**How to demo:**
1. Login as Super Admin (`admin@initao-water.gov.ph` / `password`)
2. You land on the **Dashboard** (`/dashboard`)
3. Point out the sidebar navigation (collapsible with brand logo "MEEDO")
4. Show theme toggle (light/dark mode) in the top-right
5. Show notification bell icon
6. Show user dropdown menu (quick actions: Add User, New Application, User Manual)

**What to highlight:**
- Clean, modern UI with dark mode support
- Role-based sidebar (different roles see different menus)
- The system name "MEEDO" and the Initao branding

---

### 3.2 Initial Setup & Configuration

**What it does:** Admin configures the foundational data the system needs before daily operations can begin.

**How to demo:**

#### A. Geographic Configuration
1. Sidebar: **Admin Configuration** > **Geographic** > **Barangays** (`/config/barangays`)
2. Show the 16 pre-seeded barangays of Initao
3. Demonstrate adding a new barangay (if needed)
4. Navigate to **Areas** (`/config/areas`) - service zones for meter reader assignment
5. Navigate to **Puroks** (`/config/puroks`) - sub-village zones (24 per barangay)

#### B. Water Rates Configuration
1. Sidebar: **Admin Configuration** > **Water Rates** (`/config/water-rates`)
2. Show tiered pricing structure:
   - **Residential:** 0-10 cu.m = P100 flat, 11-20 = P11/cu.m, 21-30 = P12/cu.m, 31+ = P13/cu.m
   - **Commercial:** Double residential rates
3. Explain rates are per-period (monthly)

#### C. Account Types
1. Sidebar: **Admin Configuration** > **Billing Configuration** > **Account Types** (`/config/account-types`)
2. Show Residential and Commercial types
3. Can add Government, Non-Profit, etc.

#### D. Charge Item Templates (Application Fees)
1. Sidebar: **Admin Configuration** > **Billing Configuration** > **Application Fee Templates** (`/config/charge-items`)
2. Show the 10 pre-configured fees:
   - Connection Fee (P500), Service Deposit (P300), Meter Deposit (P200)
   - Application Fee (P50), Installation (P800)
   - Reconnection (P300), Late Payment (P50), etc.

#### E. Billing Periods
1. Sidebar: **Rate** (`/rate/management`)
2. Show current month period is pre-created
3. Demonstrate period management (create, close, open)
4. Explain: rates are copied to each new period

**What to highlight:**
- All reference data is pre-seeded and ready to use
- Water rates support tiered pricing per account type
- Periods are monthly billing cycles that control when billing happens
- Charge items are templates — actual charges are created per customer/application

---

### 3.3 Customer Registration

**What it does:** Register new water service customers into the system.

**How to demo:**
1. Sidebar: **Customer Management** > **Customer List** (`/customer/list`)
2. Show the customer DataTable (search, sort, paginate)
3. Show sample seeded customers (Juan Santos Dela Cruz, Maria Garcia Santos, etc.)
4. Click on a customer name to view details (`/customer/details/{id}`)
5. Show customer detail page:
   - Personal info (name, contact, ID)
   - Address (Province > Town > Barangay > Purok)
   - Service connections list
   - Resolution number (auto-generated: `INITAO-JSC-1234567890`)

**How to add a new customer:**
1. From **Connection Management** > **New Application** (this creates customer + application together)
2. Or directly from the customer list if adding just a customer record

**What to highlight:**
- Customer names are stored in UPPERCASE (validation rule)
- Auto-generated resolution numbers for unique identification
- One customer can have multiple service connections
- Address uses the Philippine hierarchy: Province > Town > Barangay > Purok

---

### 3.4 Service Application Workflow

**What it does:** Full lifecycle from a customer applying for water service to getting connected. This is the core workflow.

**How to demo:**

#### Step 1: Create New Application
1. Sidebar: **Connection Management** > **New Application** (`/connection/service-application/create`)
2. Fill in customer information:
   - Search existing customer OR create new one
   - Select address (Barangay, Purok)
   - Select Account Type (Residential/Commercial)
3. System auto-generates application charges from fee templates
4. Submit the application

#### Step 2: View Applications List
1. Sidebar: **Connection Management** > **Applications** (`/connection/service-application`)
2. Show application statuses: PENDING, VERIFIED, PAID, SCHEDULED, CONNECTED, REJECTED, CANCELLED
3. Click on an application to see full details

#### Step 3: Verify Application
1. Open a PENDING application (`/connection/service-application/{id}`)
2. Click **Verify** button
3. Show the timeline tracking all actions
4. View Order of Payment (list of charges)

#### Step 4: Process Application Payment
1. Click **Process Payment** (available to Cashier role)
2. Record payment amount
3. System creates payment receipt and allocates to application charges
4. Show receipt generation

#### Step 5: Schedule Installation
1. Click **Schedule** button
2. Set installation date
3. Application moves to SCHEDULED status

#### Step 6: Complete Connection
1. After installation, click **Complete Connection**
2. System creates the ServiceConnection record
3. Assign a meter to the connection
4. Connection becomes ACTIVE

#### Additional Actions:
- **Print Application** - printable application form
- **Print Contract** - printable service contract
- **Reject** - reject with reason tracking
- **Cancel** - cancel the application
- **Timeline** - view complete audit trail of all actions

**What to highlight:**
- Complete workflow with status tracking at every step
- Each action is logged with who did it and when
- Payment must be processed before connection can be completed
- Multiple fee templates auto-applied based on configuration
- Printable application and contract documents

---

### 3.5 Service Connection & Meter Assignment

**What it does:** Manage active water service connections and their meters.

**How to demo:**

#### View Active Connections
1. Sidebar: **Connection Management** > **Active Connections** (`/customer/service-connection`)
2. Show list of active connections with account numbers
3. Filter by status: ACTIVE, SUSPENDED, DISCONNECTED

#### Connection Details
1. Click on a connection to view details (`/customer/service-connection/{id}`)
2. Show:
   - Account number (e.g., `RES-202602-00001`)
   - Account type (Residential/Commercial)
   - Current meter assignment
   - Billing history
   - Outstanding balance

#### Meter Assignment
1. From connection details, click **Assign Meter**
2. Select from available meters (dropdown shows unassigned meters)
3. Set installation date
4. Meter is now assigned to this connection

#### Meter Change
1. To change a meter, first **Remove** the current meter (set removal date)
2. Then **Assign** a new meter
3. System tracks meter change history
4. Bills during meter change period handle split consumption (old meter + new meter)

#### Connection Actions
- **Suspend** - temporarily cut off service (e.g., non-payment)
- **Disconnect** - permanent disconnection
- **Reconnect** - restore previously suspended/disconnected service
- **Print Statement** - printable statement of account

**What to highlight:**
- Account numbers are auto-generated with type prefix (RES/COM)
- Meter change handling preserves billing accuracy
- Connection lifecycle: ACTIVE > SUSPENDED > DISCONNECTED > RECONNECTED
- Each action updates status and creates audit trail

---

### 3.6 Meter Reading (Manual + Scheduling)

**What it does:** Record periodic water meter readings, schedule meter reader routes.

**How to demo:**

#### Meter Inventory
1. Sidebar: **Meter** (`/meter/management`)
2. Show meter inventory with stats (total, assigned, available, faulty)
3. Show 25 pre-seeded meters across 5 brands
4. Demonstrate adding a new meter
5. Mark a meter as faulty

#### Meter Assignment Management
1. From Meter page, navigate to assignment tab
2. Show which meters are assigned to which connections
3. Show unassigned connections (need meters)
4. Show available meters (ready to assign)

#### Reading Schedules (for organized reading operations)
1. From Billing/Meter page, manage reading schedules
2. Create a schedule for current period:
   - Select billing period
   - Select area
   - Assign meter reader
   - Set reading date
3. Schedule lifecycle: PENDING > STARTED > COMPLETED (or DELAYED)
4. Download schedule template (for field use)

#### Area & Reader Assignment
1. Create service areas (geographic zones)
2. Assign connections to areas
3. Assign meter readers to areas
4. This links readers to connections for their routes

#### Recording Readings
1. From billing management, select a connection
2. Enter current meter reading
3. System calculates consumption: Current Reading - Previous Reading
4. Preview bill before generating

#### Mobile Upload (API)
- Meter readers can upload readings via mobile app
- Bulk upload support (JSON/CSV)
- Processing workflow with validation

**What to highlight:**
- Organized reading schedule system for field operations
- Area-based assignment connects readers to connections
- Automatic consumption calculation
- Meter change during a period handles split readings
- Mobile app integration for field meter readers

---

### 3.7 Billing Generation & Adjustments

**What it does:** Generate monthly water bills and make adjustments when needed.

**How to demo:**

#### Bill Generation
1. Sidebar: **Billing** (`/billing/management`)
2. Show billing overview with period selection
3. Select current billing period
4. View billable connections (connections with readings but no bill yet)
5. Select a connection and click **Preview Bill**:
   - Shows consumption calculation
   - Shows rate tier applied
   - Shows computed water amount
6. Click **Generate Bill** to create the bill
7. Show generated bill details:
   - Connection, Period, Consumption, Water Amount
   - Due date
   - Adjustment total (starts at P0)

#### View Billed Consumers
1. From billing page, view consumers already billed for the period
2. Click to see individual bill details
3. Print bill (`/water-bills/{billId}/print`)

#### Bill Adjustments
1. From billing page, navigate to adjustments tab
2. Select a bill to adjust
3. Two types of adjustments:

   **Consumption Adjustment:**
   - Changes the consumption reading
   - Recalculates the entire bill amount based on rate tiers
   - Use for: meter reading errors

   **Amount Adjustment:**
   - Adds credit or debit to the bill
   - Select adjustment type: Meter Error, Penalty Waiver, Discount, Penalty, Correction
   - Enter amount and reason
   - Updates the bill's `adjustment_total`

4. **Void Adjustment** - undo a previous adjustment
5. **Recompute Bill** - recalculate a single bill's amounts
6. **Recompute Period** - recalculate all bills in a period

#### Billing Summary & Analytics
1. Navigate to billing overall data
2. Show billing summary with totals, averages, distributions

**What to highlight:**
- **Recalculate vs Adjustment** (important distinction):
  - **Recalculate:** Re-runs the bill computation on an existing bill in an OPEN period. Fixes the base amount.
  - **Adjustment:** Adds a credit or debit entry. Works on both open and closed periods. Shows up as a separate line item on the next bill.
- Adjustments have types (credit vs debit direction)
- Every adjustment requires a reason and tracks who made it
- Void capability for incorrect adjustments
- Period-level recompute for bulk corrections

---

### 3.8 Payment Processing & Allocation

**What it does:** Process customer payments and allocate across outstanding bills/charges.

**How to demo:**

#### Payment Management
1. Sidebar: **Payment Management** (`/customer/payment-management`)
2. Show payment dashboard with statistics:
   - Total collections
   - Transaction count
   - My transactions (current user)
3. View pending payments list
4. Search/filter payments

#### Process a Water Bill Payment
1. From a customer's billing details, click **Pay**
2. Or navigate to Payment Processing > Process Payment
3. Enter payment details:
   - Amount received
   - Payment automatically generates receipt number
4. System allocates payment across outstanding bills (oldest first)
5. Creates customer ledger entries (CREDIT)
6. Generates payment receipt

#### View Payment Receipt
1. After processing, view receipt (`/payment/receipt/{id}`)
2. Shows: Receipt number, payer, amount, date, processor
3. Shows allocation breakdown (which bills were paid)

#### Payment Cancellation / Void
1. Find the payment in the payment list
2. Click **Cancel Payment** (requires `payments.void` permission)
3. Enter cancellation reason
4. System:
   - Marks payment as CANCELLED
   - Records who cancelled and when
   - Reverses the ledger entries (creates debit entries)
   - Restores outstanding balances on affected bills

#### My Transactions
1. View transactions processed by current logged-in user
2. Export as CSV or PDF

**What to highlight:**
- Payment allocates across multiple bills automatically
- Double-entry ledger: every payment creates credit entries
- Cancellation is fully reversible with audit trail
- Receipt generation with unique receipt numbers
- Export capability for transaction records

---

### 3.9 Customer Ledger & Statements

**What it does:** Double-entry accounting ledger tracking all financial transactions per customer.

**How to demo:**
1. Sidebar: **Ledger** (`/ledger/management`)
2. Select a customer/connection
3. Show ledger entries with:
   - Date, Period, Type (BILL/CHARGE/PAYMENT), Debit, Credit
   - Running balance
   - Source reference (which bill/charge/payment)
4. Filter by connection or period
5. Export ledger as PDF or CSV

#### From Customer Details
1. Go to Customer Details > Ledger tab
2. View transaction history per connection
3. Print Statement of Account

**What to highlight:**
- Polymorphic source tracking: each entry links to its source (Bill, Charge, or Payment)
- Per-connection ledger for customers with multiple connections
- Running balance calculation
- Audit trail with user tracking
- Exportable as PDF/CSV

---

### 3.10 Reports & Exports

**What it does:** Generate operational and financial reports for management.

**How to demo:**
1. Sidebar: **Reports** (`/reports`)
2. Walk through each report:

| Report | URL | What It Shows |
|--------|-----|---------------|
| **Aging of Accounts** | `/reports/aging-accounts` | Overdue bills grouped by age (30/60/90+ days) |
| **Consumer Master List** | `/reports/consumer-master-list` | Complete customer listing with addresses and status |
| **Monthly Billing Summary** | `/reports/billing-summary` | Total bills, amounts, consumption for a period |
| **Monthly Collection Summary** | `/reports/monthly-collection` | Daily/weekly payment totals |
| **Summary Status Report** | `/reports/summary-status` | Distribution of customer/connection statuses |
| **Abstract of Collection** | `/reports/abstract-collection` | Detailed collection transactions (printable) |
| **Water Bill History** | `/reports/water-bill-history` | Complete bill listing with all details |
| **Billing Statement** | `/reports/billing-statement` | Individual customer statement of account |

3. Show filtering capabilities (by period, area, status)
4. Demonstrate export to PDF and CSV
5. Show printable formats (optimized for printing)

**What to highlight:**
- 8 comprehensive reports covering all operational needs
- All reports support period/area/status filtering
- Export to PDF and CSV
- Print-optimized layouts
- DataTables with sorting and pagination

---

### 3.11 Admin: Users, Roles & Permissions

**What it does:** Manage system users and control what each role can access.

**How to demo:**

#### User Management
1. Sidebar: **User Management** > **Add User** (`/user/add`)
2. Show user creation form:
   - Username (auto-suggested)
   - Name, Email, Password
   - Select Role
   - Assign Area (for meter readers)
3. Navigate to **User List** (`/user/list`)
4. Show user DataTable with role badges
5. Edit/delete users

#### Role Management
1. Sidebar: **Admin Configuration** > **Access Control** > **Roles** (`/admin/roles`)
2. Show 6 pre-configured roles
3. Click on a role to see its permissions
4. Show users assigned to each role

#### Permission Management
1. Navigate to **Permissions** (`/admin/permissions`)
2. Show 18 permissions grouped by module:
   - Customers: view, manage
   - Payments: view, process, void
   - Billing: view
   - Meters: view, manage
   - Settings: manage
   - Users: view, manage
   - Config Geographic: manage
   - Config Billing: manage

#### Permission Matrix
1. Navigate to **Permission Matrix** (`/admin/role-permissions`)
2. Show the role-permission matrix grid
3. Toggle individual permissions on/off
4. Bulk permission assignment

#### Demo Role-Based Access
1. Logout and login as different roles to show how the sidebar changes:
   - **Cashier** (`cashier@test.com`) - only sees Payment Management
   - **Meter Reader** (`meter@test.com`) - only sees Meter Management
   - **Viewer** (`viewer@test.com`) - read-only access everywhere

**What to highlight:**
- 6 roles covering all water district staff positions
- Granular permission control (18 permissions, 8 modules)
- Visual permission matrix for easy administration
- Role-based sidebar navigation (users only see what they can access)
- Super Admin bypasses all permission checks

---

### 3.12 Admin: Geographic & Billing Config

**What it does:** Configure geographic areas and billing parameters.

**How to demo:**

#### Geographic Config
1. **Barangays** (`/config/barangays`) - View/manage the 16 barangays
2. **Areas** (`/config/areas`) - Create service zones, assign connections to areas
3. **Puroks** (`/config/puroks`) - Manage sub-village zones

#### Billing Config
1. **Water Rates** (`/config/water-rates`) - Configure tiered pricing per account type
2. **Account Types** (`/config/account-types`) - Manage connection classifications
3. **Charge Items** (`/config/charge-items`) - Manage fee templates for applications

#### Rate Management
1. Sidebar: **Rate** (`/rate/management`)
2. Show rate management with period selection
3. Copy rates from previous period to new period
4. Upload rates via CSV
5. Download rate template

#### Period Management
1. From Rate page, manage billing periods
2. Create new period (monthly)
3. Close period (finalizes, prevents changes)
4. Open period (unlocks for corrections)

#### Activity Log (Super Admin Only)
1. Sidebar: **Activity Log** (`/admin/activity-log`)
2. Show audit trail of all significant system actions
3. Who did what, when

**What to highlight:**
- All configuration is admin-managed, no developer needed
- Rate copying between periods saves time
- CSV upload for bulk rate updates
- Period closure prevents accidental changes to finalized data
- Complete audit trail for compliance

---

## 4. Open Questions for Discussion

> These are topics to discuss with the water district during or after the demo. The answers will determine how we configure or enhance certain features.

---

### Question 1: Initial Data & Seeder Setup

**Context:** The system comes pre-loaded with reference data (barangays, puroks, statuses, water rates, charge items, etc.). All of this was configured based on common water district setups.

**What to discuss:**
- Are the **16 barangays** correct and complete? Any missing or differently named?
- Are the **purok naming conventions** (Purok 1-A through 12-B) accurate? Some barangays might have different purok structures.
- Are the **charge item amounts** correct? (Connection Fee P500, Deposit P300, etc.)
- Do they have additional fee types beyond what we've configured?
- Are the **water rate tiers** and amounts accurate for their current pricing?

**Action needed:** Adjust seeder data to match actual water district data before production deployment.

---

### Question 2: Billing Period & Water Rate Management

**Context:** The system creates billing periods monthly. Each period has its own water rates. Currently, rates must be set up (or copied from previous period) each time a new period is created.

**What to discuss:**
- **Do they change water rates frequently?**
  - If rates are stable (same for months/years), we can set up **auto-create period with auto-copy rates** at the end of each month. No manual intervention needed.
  - If rates change often, they'll want to review/update rates each period before billing.

- **Period auto-creation preference:**
  - **Option A: Automatic** - System auto-creates the next month's period at end of current month, copies rates from previous period. Staff only intervenes when rates change.
  - **Option B: Manual** - Staff manually creates each period and sets rates. More control but more work.

**Related: Period Closing**
- **Should periods auto-close?**
  - At the end of each month (or after all bills are generated), should the system automatically close the previous period?
  - Or do they want to manually close it after verifying everything is correct?
  - Auto-close prevents accidental changes; manual close gives them time to make corrections.

---

### Question 3: Customer Approval Process

**Context:** When a customer applies for a water service connection, the application currently goes through a multi-step approval workflow (PENDING > VERIFIED > PAID > SCHEDULED > CONNECTED).

**What to discuss:**
- **Do they have a formal approval process?**
  - If yes, the current workflow is appropriate (verification step, payment, scheduling).
  - If not, we can simplify to **auto-approve upon application** - skip the verification step and go directly to payment processing.

- **Who approves?**
  - Does a specific person/role need to verify applications?
  - Or can anyone with the right role process it?

- **What documents do they require?**
  - ID verification?
  - Proof of land ownership/tenancy?
  - Barangay clearance?
  - Should we add a document upload feature?

---

### Question 4: Bill Adjustments vs Recalculation

**Context:** The system supports two different ways to correct bills. It's important the staff understands the difference.

**Explain to them:**

**Recalculate (Recompute):**
- Re-runs the bill computation on an existing bill
- Only works on bills in an **OPEN (non-closed) period**
- Fixes the base water amount by re-applying current rates to the consumption
- Use when: rate was wrong, consumption was recorded correctly but calculated wrong
- The bill amount changes directly

**Adjustment:**
- Adds a separate credit or debit entry to the bill
- Works on **both open AND closed periods**
- Does NOT change the base water amount - adds to `adjustment_total`
- Types: Meter Error (credit), Penalty Waiver (credit), Discount (credit), Penalty (debit), Correction (credit/debit)
- Depending on the adjustment type, it can be a **debit** (customer owes more) or **credit** (customer owes less)
- The adjustment shows as a separate line item and carries forward to the next bill

**What to discuss:**
- **Is there a memo or pre-requisite for adjustments?**
  - Do they require a written memo/request before making an adjustment?
  - Does a supervisor need to approve adjustments?
  - Should we add an adjustment approval workflow?
- **What's their current process for handling billing errors?**
- **Do they need a threshold?** (e.g., adjustments over P500 require supervisor approval)

---

### Question 5: Automation Preferences

**Context:** Several processes in the system can be either manual or automated. We need their preference.

**What to discuss:**

| Process | Manual | Automated | Current |
|---------|--------|-----------|---------|
| Period creation | Staff creates each month | Auto-create at month end | Manual |
| Rate copying | Staff copies rates to new period | Auto-copy when period created | Manual |
| Period closing | Staff manually closes after review | Auto-close at end of month | Manual |
| Penalty application | Staff runs penalty process | Auto-apply after due date | Manual |
| Overdue status | Staff marks overdue bills | Auto-mark after due date passes | Manual |

- Which of these do they want automated?
- What's their comfort level with automation vs manual control?
- Do they want notifications when automated actions occur?

---

### Summary of Decisions Needed

| # | Topic | Decision Required |
|---|-------|------------------|
| 1 | Seeder data | Verify barangays, puroks, rates, fees match reality |
| 2 | Period creation | Auto-create monthly or manual? |
| 3 | Rate management | Auto-copy or manual per period? |
| 4 | Period closing | Auto-close or manual? |
| 5 | Customer approval | Full workflow or auto-approve? |
| 6 | Adjustment memo | Require written memo/approval? |
| 7 | Automation level | Which processes to automate? |

---

## Quick Navigation Reference

| Feature | Sidebar Path | URL |
|---------|-------------|-----|
| Dashboard | Dashboard | `/dashboard` |
| Customer List | Customer Management > Customer List | `/customer/list` |
| Customer Details | (click customer) | `/customer/details/{id}` |
| New Application | Connection Management > New Application | `/connection/service-application/create` |
| Applications List | Connection Management > Applications | `/connection/service-application` |
| Active Connections | Connection Management > Active Connections | `/customer/service-connection` |
| Payment Management | Payment Management | `/customer/payment-management` |
| Billing | Billing | `/billing/management` |
| Meter Management | Meter | `/meter/management` |
| Rate Management | Rate | `/rate/management` |
| Ledger | Ledger | `/ledger/management` |
| Reports | Reports | `/reports` |
| User List | User Management > User List | `/user/list` |
| Add User | User Management > Add User | `/user/add` |
| Barangays Config | Admin Configuration > Geographic > Barangays | `/config/barangays` |
| Areas Config | Admin Configuration > Geographic > Areas | `/config/areas` |
| Puroks Config | Admin Configuration > Geographic > Puroks | `/config/puroks` |
| Water Rates Config | Admin Configuration > Water Rates | `/config/water-rates` |
| Account Types | Admin Configuration > Billing Config > Account Types | `/config/account-types` |
| Charge Items | Admin Configuration > Billing Config > Fee Templates | `/config/charge-items` |
| Roles | Admin Configuration > Access Control > Roles | `/admin/roles` |
| Permissions | Admin Configuration > Access Control > Permissions | `/admin/permissions` |
| Permission Matrix | Admin Configuration > Access Control > Permission Matrix | `/admin/role-permissions` |
| Activity Log | Activity Log (Super Admin only) | `/admin/activity-log` |
| User Manual | (user dropdown) | `/user-manual` |

---

_Last updated: 2026-02-11_
_System: Initao Water Billing System (MEEDO)_
_Stack: Laravel 12, MySQL, Alpine.js, Tailwind CSS_
