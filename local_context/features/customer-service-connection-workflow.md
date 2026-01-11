# Customer Service Connection Workflow - User Guide

> **Last Updated:** 2026-01-08
> **System:** Initao Water Billing System
> **Audience:** Office Staff, Cashiers, Field Personnel

---

## Table of Contents

1. [Workflow Overview](#workflow-overview)
2. [UI Pages Reference](#ui-pages-reference)
3. [Scenario 1: New Customer Registration](#scenario-1-new-customer-registration)
4. [Scenario 2: Application Verification](#scenario-2-application-verification)
5. [Scenario 3: Payment Processing](#scenario-3-payment-processing)
6. [Scenario 4: Connection Scheduling & Completion](#scenario-4-connection-scheduling--completion)
7. [Scenario 5: Application Rejection](#scenario-5-application-rejection)
8. [Scenario 6: Connection Management](#scenario-6-connection-management)
9. [Appendix: API Endpoints](#appendix-api-endpoints)

---

## Workflow Overview

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                    CUSTOMER SERVICE CONNECTION WORKFLOW                      │
└─────────────────────────────────────────────────────────────────────────────┘

  REGISTRATION          VERIFICATION         PAYMENT           SCHEDULING
 ┌──────────┐          ┌──────────┐        ┌──────────┐       ┌──────────┐
 │ PENDING  │ ──────▶  │ VERIFIED │ ─────▶ │   PAID   │ ────▶ │SCHEDULED │
 └──────────┘          └──────────┘        └──────────┘       └──────────┘
      │                      │                                      │
      │                      │                                      │
      ▼                      ▼                                      ▼
 ┌──────────┐          ┌──────────┐                           ┌──────────┐
 │CANCELLED │          │ REJECTED │                           │CONNECTED │
 └──────────┘          └──────────┘                           └──────────┘
```

### Status & UI Page Mapping

| Status | Color Badge | Where to Find | Next Action |
|--------|-------------|---------------|-------------|
| PENDING | Yellow | Service Applications → Pending filter | Verify or Reject |
| VERIFIED | Blue | Service Applications → Verified filter | Record Payment |
| PAID | Green | Service Applications → Paid filter | Schedule Connection |
| SCHEDULED | Purple | Service Connections → Scheduled banner | Complete Connection |
| CONNECTED | Teal | Service Connections list | Active service |
| REJECTED | Red | Service Applications → View details | Re-apply (new application) |

---

## UI Pages Reference

### Service Applications

| Page | URL | Purpose |
|------|-----|---------|
| Applications List | `/connection/service-application` | View and manage all applications |
| Application Detail | `/connection/service-application/{id}` | Full details with workflow stepper |

**Key Components:**
- **Stats Row**: Clickable cards showing counts (Total, Pending, Verified, Paid, Scheduled)
- **Search Bar**: Search by application #, customer name, or address
- **Status Filter**: Dropdown to filter by status
- **Action Buttons**: View (eye), Verify (checkmark), Schedule (calendar)

### Service Connections

| Page | URL | Purpose |
|------|-----|---------|
| Connections List | `/customer/service-connection` | View and manage active connections |
| Connection Detail | `/customer/service-connection/{id}` | Account info, meter, balance |

**Key Components:**
- **Scheduled Banner**: Blue gradient showing applications ready for connection
- **Stats Row**: Total, Active, Suspended, Disconnected counts
- **Quick Actions**: View, Suspend, Reconnect buttons

---

## Scenario 1: New Customer Registration

> **Real-World Context:** Maria Santos visits the Initao Water District office to apply for a new water connection at her newly built house.

### Step 1.1: Open Customer Registration

**Navigate to:** Sidebar → Customers → Add Customer
**URL:** `/customer/add`

**What you see:**
- Customer registration form with tabs/sections
- Required fields marked with red asterisk (*)

### Step 1.2: Fill Customer Information

**Actions:**
1. Enter customer name:
   - **First Name**: `MARIA`
   - **Middle Name**: `DELA CRUZ`
   - **Last Name**: `SANTOS`

2. Select customer type:
   - Click the **Type** dropdown
   - Select `RESIDENTIAL` (or Commercial/Industrial)

3. Enter contact information (if available):
   - Phone number
   - Email address

### Step 1.3: Enter Service Address

**Actions:**
1. **Province**: Select `Misamis Oriental` from dropdown
2. **Town/Municipality**: Select `Initao`
3. **Barangay**: Select from dropdown (e.g., `Poblacion`)
4. **Purok**: Select from dropdown (e.g., `Purok 3`)
5. **Landmark**: Enter description (e.g., `Near Barangay Hall`)

### Step 1.4: Select Account Configuration

**Actions:**
1. **Account Type**: Select `Residential` or `Commercial`
2. **Rate**: Select appropriate rate classification

### Step 1.5: Review and Submit

**Actions:**
1. Review all entered information
2. Click the green **Submit** or **Register** button
3. Wait for success message

**Result:**
- Customer record created
- Service Application created with status `PENDING`
- Application Number generated (e.g., `APP-2026-00001`)

### Step 1.6: Provide Customer Receipt

**Give customer:**
- Application Number: `APP-2026-00001`
- Total fees due: ₱3,000.00 (Connection + Survey fees)
- Instructions to wait for verification call

---

## Scenario 2: Application Verification

> **Real-World Context:** Verifier Juan Cruz reviews Maria's application. He checks documents and confirms the application is complete.

### Step 2.1: Open Service Applications

**Navigate to:** Sidebar → Connection → Service Applications
**URL:** `/connection/service-application`

**What you see:**
- Stats row with clickable cards:
  - **Total**: All applications
  - **Pending** (yellow): Awaiting verification
  - **Verified** (blue): Awaiting payment
  - **Paid** (green): Awaiting scheduling
  - **Scheduled** (purple): Ready for connection

### Step 2.2: Filter Pending Applications

**Actions:**
1. Click the **Pending** stat card (yellow)
   - OR use the Status dropdown and select `Pending`
2. Table now shows only PENDING applications

### Step 2.3: Find the Application

**Actions:**
1. Use the **Search bar** to find by:
   - Application number (e.g., `APP-2026-00001`)
   - Customer name (e.g., `SANTOS`)
   - Address

2. Locate the application row in the table

### Step 2.4: Quick Verify (From List)

**Actions:**
1. In the **Actions** column, click the **checkmark icon** (✓)

**Verify Modal appears:**
```
┌─────────────────────────────────────────┐
│  ✓ Verify Application                   │
├─────────────────────────────────────────┤
│  Application: APP-2026-00001            │
│  Customer: MARIA D. SANTOS              │
│  Address: Purok 3, Poblacion            │
├─────────────────────────────────────────┤
│  Verification Checklist:                │
│  ☐ Valid ID has been verified           │
│  ☐ Property documents verified          │
│  ☐ Service address confirmed            │
├─────────────────────────────────────────┤
│  [Cancel]         [Verify Application]  │
└─────────────────────────────────────────┘
```

2. Check all three verification boxes:
   - [x] Valid ID has been verified
   - [x] Property documents verified
   - [x] Service address confirmed

3. Click the blue **Verify Application** button

4. Success message: *"Application verified successfully!"*

### Step 2.4 (Alternative): Verify from Detail Page

**Actions:**
1. Click the **eye icon** (👁) to open Application Detail page
2. Review the 5-step workflow stepper at top
3. Review customer info, address, and fees
4. Click the blue **Verify Application** button
5. Complete the verification modal as above

**Result:**
- Application status changes to `VERIFIED`
- Stepper shows Step 2 (Verified) as current
- Notification created for cashier

---

## Scenario 3: Payment Processing

> **Real-World Context:** Cashier Ana Reyes receives Maria's payment of ₱3,000.00 for connection fees.

### Step 3.1: Open Payment Management

**Navigate to:** Sidebar → Payment Management
**URL:** `/customer/payment-management`

### Step 3.2: Find Customer/Application

**Actions:**
1. Search for customer by name or application number
2. Select the customer's pending charges

### Step 3.3: Process Payment

**Actions:**
1. Enter **Amount Received**: `3000.00`
2. Select **Payment Method**: Cash / Check / etc.
3. Click **Process Payment** button

**Result:**
- Payment recorded
- Receipt number generated (e.g., `OR-2026-00001`)
- Application status changes to `PAID`

### Step 3.4: Verify Payment in Applications

**Navigate to:** Sidebar → Connection → Service Applications

**What you see:**
- Application now shows green `PAID` badge
- Payment info displayed in Application Detail page
- Application fees show "PAID" status

---

## Scenario 4: Connection Scheduling & Completion

> **Real-World Context:** Scheduler Pedro sets Maria's connection date. Field Supervisor Ramon completes the installation.

### Step 4.1: Schedule the Connection

**Navigate to:** Sidebar → Connection → Service Applications
**URL:** `/connection/service-application`

**Actions:**
1. Click **Paid** stat card to filter paid applications
2. Find the application in the table
3. Click the **calendar icon** in Actions column

**Schedule Modal appears:**
```
┌─────────────────────────────────────────┐
│  📅 Schedule Connection                 │
├─────────────────────────────────────────┤
│  Application: APP-2026-00001            │
│  Customer: MARIA D. SANTOS              │
│  Address: Purok 3, Poblacion            │
├─────────────────────────────────────────┤
│  Connection Date:                       │
│  ┌─────────────────────────────────┐    │
│  │ 📅  January 15, 2026            │    │
│  └─────────────────────────────────┘    │
├─────────────────────────────────────────┤
│  [Cancel]         [Schedule Connection] │
└─────────────────────────────────────────┘
```

4. Select a **Connection Date** (must be today or future)
5. Click the blue **Schedule Connection** button
6. Success message: *"Connection scheduled for January 15, 2026"*

**Result:**
- Application status changes to `SCHEDULED`
- Scheduled date banner appears on Service Connections page

### Step 4.2: View Scheduled Applications

**Navigate to:** Sidebar → Connection → Service Connections
**URL:** `/customer/service-connection`

**What you see:**
- Blue gradient **Scheduled for Connection** banner at top
- Shows count of applications ready to be connected
- Mini cards showing customer name and scheduled date

### Step 4.3: Complete the Connection

**Actions:**
1. In the scheduled banner, click **View Scheduled** button
2. Find the application in the modal
3. Click the green **Complete** button

**OR from banner mini-cards:**
1. Click the green **Connect** button on the application card

**Complete Connection Modal appears:**
```
┌─────────────────────────────────────────┐
│  🔌 Complete Connection                 │
├─────────────────────────────────────────┤
│  Application: APP-2026-00001            │
│  Customer: MARIA D. SANTOS              │
│  Address: Purok 3, Poblacion            │
│  Scheduled: January 15, 2026            │
├─────────────────────────────────────────┤
│  Account Type: [Residential      ▼]     │
│  Rate:         [Standard Rate    ▼]     │
├─────────────────────────────────────────┤
│  Meter Assignment:                      │
│  Select Meter: [MTR-2024-001     ▼]     │
│  Initial Reading: [0.000           ]    │
├─────────────────────────────────────────┤
│  [Cancel]           [Complete Connection]│
└─────────────────────────────────────────┘
```

4. Select **Account Type** from dropdown
5. Select **Rate** from dropdown
6. Select **Meter** from available meters dropdown
7. Enter **Initial Reading** (usually `0.000` for new meters)
8. Click the green **Complete Connection** button

**Result:**
- Service Connection created with status `ACTIVE`
- Account number generated (e.g., `2026-POBA-00001`)
- Meter assigned to connection
- Application status changes to `CONNECTED`
- Customer can now receive monthly bills

---

## Scenario 5: Application Rejection

> **Real-World Context:** Verifier discovers invalid documents and rejects the application.

### Step 5.1: Open Rejection Modal

**Navigate to:** Application Detail page or Applications List

**Actions (from List):**
1. Click the **eye icon** to view application details
2. Click the red **Reject** button

**Actions (from Detail):**
1. In the Actions section, click red **Reject** button

**Reject Modal appears:**
```
┌─────────────────────────────────────────┐
│  ❌ Reject Application                  │
├─────────────────────────────────────────┤
│  Application: APP-2026-00001            │
│  Customer: MARIA D. SANTOS              │
│  Current Status: PENDING                │
├─────────────────────────────────────────┤
│  Quick Select Reason:                   │
│  [Invalid Documents] [Incomplete Info]  │
│  [Duplicate Application] [Other]        │
├─────────────────────────────────────────┤
│  Rejection Reason: *                    │
│  ┌─────────────────────────────────┐    │
│  │ Invalid property documents.     │    │
│  │ Applicant does not have proof   │    │
│  │ of ownership or lease.          │    │
│  └─────────────────────────────────┘    │
│  (minimum 10 characters)                │
├─────────────────────────────────────────┤
│  [Cancel]             [Reject Application]│
└─────────────────────────────────────────┘
```

### Step 5.2: Enter Rejection Reason

**Actions:**
1. Click a **Quick Select** button to auto-fill reason
   - OR type custom reason in the text area
2. Ensure reason is at least 10 characters
3. Click the red **Reject Application** button

**Result:**
- Application status changes to `REJECTED`
- Rejection reason saved and displayed
- Customer can re-apply with new application

---

## Scenario 6: Connection Management

> **Real-World Context:** Managing active connections - suspending for non-payment, reconnecting after payment, or permanent disconnection.

### Step 6.1: View Connection Details

**Navigate to:** Sidebar → Connection → Service Connections
**URL:** `/customer/service-connection`

**Actions:**
1. Find the connection in the table
2. Click the **eye icon** to view details

**What you see on Detail page:**
- **Account Info Card**: Customer, type, rate, start date
- **Meter Info Card**: Serial number, brand, install reading
- **Balance Card**: Total billed, total paid, outstanding balance
- **Meter History Table**: All meter assignments
- **Action Buttons**: Based on current status

### Step 6.2: Suspend a Connection

**When:** Non-payment, maintenance, customer request

**Actions:**
1. On Connection Detail page, click yellow **Suspend** button
2. Suspend Modal appears:
   ```
   ┌─────────────────────────────────────────┐
   │  ⏸ Suspend Connection                  │
   ├─────────────────────────────────────────┤
   │  ⚠ This will temporarily suspend       │
   │  water service for this connection.    │
   ├─────────────────────────────────────────┤
   │  Suspension Reason: *                  │
   │  ┌─────────────────────────────────┐   │
   │  │ Non-payment for 3 months.       │   │
   │  │ Balance: P1,500.00              │   │
   │  └─────────────────────────────────┘   │
   ├─────────────────────────────────────────┤
   │  [Cancel]         [Suspend Connection] │
   └─────────────────────────────────────────┘
   ```
3. Enter suspension reason
4. Click **Suspend Connection** button

**Result:**
- Connection status changes to `SUSPENDED`
- Yellow suspension banner appears on detail page
- Water service temporarily stopped

### Step 6.3: Reconnect a Suspended Connection

**When:** Customer pays outstanding balance

**Actions:**
1. On Connection Detail page (status: SUSPENDED)
2. Click green **Reconnect** button
3. Confirm reconnection in dialog

**Result:**
- Connection status changes to `ACTIVE`
- Water service restored

### Step 6.4: Permanently Disconnect

**When:** Long-term non-payment, abandoned property

**Actions:**
1. Click red **Disconnect** button
2. Disconnect Modal appears with warning:
   ```
   ┌─────────────────────────────────────────┐
   │  🔌 Disconnect Connection              │
   ├─────────────────────────────────────────┤
   │  ⚠ WARNING: This action cannot be      │
   │  undone. The connection will be        │
   │  permanently disconnected.             │
   ├─────────────────────────────────────────┤
   │  Disconnection Reason: *               │
   │  ┌─────────────────────────────────┐   │
   │  │ Non-payment for 6+ months.      │   │
   │  │ Customer abandoned property.    │   │
   │  └─────────────────────────────────┘   │
   ├─────────────────────────────────────────┤
   │  [Cancel]                 [Disconnect] │
   └─────────────────────────────────────────┘
   ```
3. Enter disconnection reason
4. Confirm the action
5. Click **Disconnect** button

**Result:**
- Connection status changes to `DISCONNECTED`
- Connection is permanently closed
- No further actions available

### Connection Status Flow

```
    ┌────────┐     ┌───────────┐     ┌────────────────┐
    │ ACTIVE │ ──▶ │ SUSPENDED │ ──▶ │ DISCONNECTED   │
    └────────┘     └───────────┘     └────────────────┘
        ▲               │
        │               │
        └───────────────┘
         (Reconnection)
```

---

## Appendix: API Endpoints

> **For Developers:** Technical reference for backend integration.

### Service Application Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/connection/service-application` | List applications |
| GET | `/connection/service-application/{id}` | View application |
| POST | `/connection/service-application/{id}/verify` | Verify application |
| POST | `/connection/service-application/{id}/payment` | Record payment |
| POST | `/connection/service-application/{id}/schedule` | Schedule connection |
| POST | `/connection/service-application/{id}/reject` | Reject application |
| GET | `/connection/service-application/{id}/timeline` | Get timeline |

### Service Connection Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/customer/service-connection` | List connections |
| GET | `/customer/service-connection/{id}` | View connection |
| POST | `/customer/service-connection/complete` | Complete connection |
| POST | `/customer/service-connection/{id}/suspend` | Suspend |
| POST | `/customer/service-connection/{id}/reconnect` | Reconnect |
| POST | `/customer/service-connection/{id}/disconnect` | Disconnect |
| GET | `/customer/service-connection/{id}/balance` | Get balance |

### Meter Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/customer/service-connection/{id}/assign-meter` | Assign meter |
| POST | `/customer/service-connection/meter/{id}/remove` | Remove meter |
| GET | `/customer/service-connection/meters/available` | Available meters |

---

*Document updated: 2026-01-08*
*System: Initao Water Billing System*
