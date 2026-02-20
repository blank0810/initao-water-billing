# Legacy Data Migration Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Migrate all historical data (~1.1M records) from the legacy Microsoft Access database (MeedoInitao2.mdb) into the current Laravel/MySQL application, preserving 18 years of billing history.

**Architecture:** A series of Laravel Artisan commands that read exported CSV files (from the Access DB via mdbtools) and insert records into MySQL using chunked DB transactions. Each command handles one entity type, runs idempotently, and validates data integrity before/after. A master orchestrator command runs them in dependency order.

**Tech Stack:** Laravel 12 Artisan Commands, MySQL 8, mdbtools (CSV export), Laravel DB facade (bulk inserts), Pest PHP (verification tests)

---

## Decisions from Brainstorming

| Decision | Choice | Rationale |
|----------|--------|-----------|
| Data scope | Full historical (18 years) | Complete audit trail since Dec 2007 |
| Meter assignments | Synthetic reconstruction | Use `wat_Change` + `wat_Consumer.MeterId` to build `MeterAssignment` chain |
| Ledger approach | Hybrid (cutoff: Jan 2025) | Opening balance before Jan 2025 + full detail after |
| Geography | Manual mapping table | 22 legacy areas → current barangay/purok (user provides mapping) |
| Penalty cutoff | MonthId 206 (Jan 2025) | Periods 206+ get full ledger entries |

---

## Schema Changes Since Initial Plan (Updated 2026-02-20)

| Change | Migration | Impact on Plan |
|--------|-----------|---------------|
| `rate_inc` column **removed** from `water_rates` | `2026_02_17_011906` | **HIGH** — Legacy tiered rates (base+increment) incompatible with flat-rate model. Decision: DO NOT migrate rates. Historical bills carry pre-calculated amounts. See Section 6 |
| `PenaltyConfiguration` table **added** | `2026_02_16_000000` | **MEDIUM** — Added penalty config seeding step to Phase 1 Task 2. Seed with legacy 10% rate effective 2007-12-01 |
| `water_bill_history.photo_path` **added** | `2026_02_17_002354` | **NONE** — Nullable field, defaults to NULL for migrated bills |
| `users.signature_path` **added** | `2026_02_18_000001` | **NONE** — Unrelated to migration |
| `DocumentSignatory` table **added** | `2026_02_18_000002` | **NONE** — Unrelated to migration |
| Performance indexes added | `2026_02_09_000000` | **POSITIVE** — Indexes on `MeterAssignment`, `MeterReading`, `PaymentAllocation`, `CustomerLedger` will speed up migration lookups |

---

## Schema Comparison: Legacy vs Current

### Legend

| Symbol | Meaning |
|--------|---------|
| `=` | Direct mapping, no transformation needed |
| `~` | Mapping exists but requires transformation |
| `+` | New field in target, no legacy source (derived or defaulted) |
| `-` | Legacy field dropped, not migrated |
| `*` | Structural change (one-to-many, table split, etc.) |

---

### 1. Person/Consumer → Customer

**Legacy tables:** `dbo_Person` (3,152 rows) + `wat_Consumer` (3,002 rows)
**Target table:** `customer` (PK: `cust_id`)

| Legacy Field | Legacy Table | → | Target Field | Transform |
|---|---|---|---|---|
| `PersonId` | `dbo_Person` | ~ | `cust_id` | New auto-increment ID; legacy ID tracked in `legacy_id_maps` |
| `LastName` | `dbo_Person` | = | `cust_last_name` | Direct. Handle malformed records where LastName=FirstName=MidName (parse from `PersonName`) |
| `FirstName` | `dbo_Person` | = | `cust_first_name` | Direct |
| `MidName` | `dbo_Person` | = | `cust_middle_name` | Nullable. Sometimes contains "Jr.", "Sr." suffix |
| `SeqName` | `dbo_Person` | - | (dropped) | Suffix like "Jr." — already in MidName for most records |
| `PersonName` | `dbo_Person` | - | (dropped) | Computed "Last, First Mid" — only used as fallback parser |
| `DateInstalled` | `wat_Consumer` | ~ | `create_date` | Format: `MM/DD/YY HH:MM:SS` → `YYYY-MM-DD` datetime |
| `ClassId` | `wat_Consumer` | ~ | `c_type` | `1` → `'Individual'`, `2` → `'Business'`, SubClassId `3` → `'Corporation'` |
| `LocaId` | `wat_Consumer` | ~ | `land_mark` | FK to `wat_Location.LocaId` → resolve to `LocaName` text |
| `Active` | `wat_Consumer` | ~ | `stat_id` | `1` → `Status::ACTIVE`, `0` → `Status::INACTIVE` |
| (none) | | + | `contact_number` | NULL (not in legacy) |
| (none) | | + | `id_type`, `id_number` | NULL (not in legacy) |
| (none) | | + | `resolution_no` | Generated via `CustomerHelper::generateCustomerResolutionNumber()` |
| `ConsuNo` | `wat_Consumer` | - | (stored in ServiceConnection) | Becomes `ServiceConnection.account_no` |
| `AreaId` | `wat_Consumer` | - | (stored in ServiceConnection) | Becomes `ServiceConnection.area_id` |
| `MeterId` | `wat_Consumer` | - | (stored in MeterAssignment) | Becomes `MeterAssignment.meter_id` |
| `TradeId` | `wat_Consumer` | - | (dropped) | Business/trade name FK — no equivalent in current schema |
| `SubClassId` | `wat_Consumer` | - | (partially in c_type) | Used only to determine `c_type` for Government accounts |

**Key structural change:** Legacy separates `Person` (name) from `Consumer` (account). Current merges into single `Customer` with one-to-one from Consumer.

**Data quality issues:**
- ~50 `dbo_Person` records have malformed names (PersonId 381, 508, etc.) where `LastName`, `FirstName`, `MidName` all contain the full name string
- Solution: Detect when `LastName === FirstName`, then parse from `PersonName` format "Last, First Mid"

---

### 2. Geography: Legacy Area System vs Current Address Hierarchy

**Legacy tables:** `dbo_Prov` (1) + `dbo_Town` (2) + `dbo_Brgy` (3) + `dbo_Purok` (5) + `dbo_Zone` (7) + `wat_Area` (22) + `wat_Location` (2,075)
**Target tables:** `province` + `town` + `barangay` (16) + `purok` (24) + `consumer_address`

| Legacy | Current | Issue |
|--------|---------|-------|
| `dbo_Prov` (1: Misamis Oriental) | `province` (1: Misamis Oriental) | **Match** |
| `dbo_Town` (2: Initao, Lugait) | `town` (1: Initao) | Legacy has Lugait too — only Initao relevant |
| `dbo_Brgy` (3 rows: Biga, Poblacion-Initao, Poblacion-Lugait) | `barangay` (16 rows) | **Major gap**: Legacy has only 3 vs. current 16. Legacy consumers use `wat_Area` not `dbo_Brgy` |
| `dbo_Purok` (5 rows) | `purok` (24 rows) | **Major gap**: Legacy has 5 generic vs. current 24 specific |
| `dbo_Zone` (7 rows) | (no equivalent) | Dropped — zones were sub-divisions of puroks |
| `wat_Area` (22 rows) | `area` (seeded) | **The actual grouping used by consumers**. Maps to billing area, not address |
| `wat_Location` (2,075 rows) | `customer.land_mark` | Free-text descriptions ("Near the river", "P-3 Bugwak") → landmark field |

**Structural difference:** Legacy consumers are grouped by `wat_Area` (billing area), NOT by barangay/purok. The current system requires a proper `consumer_address` with barangay + purok FKs. This requires a **manual mapping table** (see Pre-Migration Requirements).

**consumer_address target:**

| Target Field | Source | Transform |
|---|---|---|
| `ca_id` | (auto) | New auto-increment |
| `p_id` | area mapping | `legacy_area_id` → `target_purok_id` from mapping CSV. Nullable |
| `b_id` | area mapping | `legacy_area_id` → `target_barangay_id` from mapping CSV. Default: Poblacion |
| `t_id` | hardcoded | Always Initao town ID |
| `prov_id` | hardcoded | Always Misamis Oriental province ID |
| `stat_id` | | `Status::ACTIVE` |

---

### 3. Consumer → ServiceConnection

**Legacy table:** `wat_Consumer` (3,002 rows)
**Target table:** `ServiceConnection` (PK: `connection_id`)

| Legacy Field | → | Target Field | Transform |
|---|---|---|---|
| `ConsuId` | ~ | `connection_id` | New auto-increment; legacy tracked in `legacy_id_maps` |
| `ConsuNo` | ~ | `account_no` | Integer → zero-padded 6-char string: `str_pad($consuNo, 6, '0', STR_PAD_LEFT)` |
| (from Customer) | = | `customer_id` | FK to newly created `customer.cust_id` |
| (from address) | = | `address_id` | FK to newly created `consumer_address.ca_id` |
| `ClassId` | ~ | `account_type_id` | `1` → Residential `at_id`, `2` → Commercial `at_id` (lookup from `account_type` table) |
| `AreaId` | ~ | `area_id` | FK via `legacy_id_maps` area mapping |
| `DateInstalled` | ~ | `started_at` | `MM/DD/YY` → `YYYY-MM-DD` date |
| `Active` | ~ | `ended_at` | `1` (active) → `NULL`, `0` (inactive) → current date |
| `Active` | ~ | `stat_id` | `1` → `Status::ACTIVE`, `0` → `Status::DISCONNECTED` |
| (none) | + | `change_meter` | Default `false` |
| `SubClassId` | - | (dropped) | Sub-classification not in current schema (Residential/Public Tap/Government/Commercial) |
| `TradeId` | - | (dropped) | Business FK not in current schema |

**Note:** Legacy `SubClassId` values:
- `1` = Residential, `2` = Public Tap, `3` = Government → all map to `account_type` Residential
- `4` = Commercial → maps to `account_type` Commercial

---

### 4. Meters

**Legacy table:** `wat_Meter` (622 rows) + `wat_Brand` (9 rows)
**Target table:** `meter` (PK: `mtr_id`)

| Legacy Field | → | Target Field | Transform |
|---|---|---|---|
| `MeterId` | ~ | `mtr_id` | New auto-increment; legacy tracked |
| `MeterSN` | = | `mtr_serial` | Direct. Empty serials → `"LEGACY-{MeterId}"` |
| `BrandId` | ~ | `mtr_brand` | FK to `wat_Brand.BrandName` → string value (current schema stores brand as string, not FK) |
| (none) | + | `stat_id` | `Status::ACTIVE` |

**Legacy brands (9):** Not Branded, Fujitso, Brand-X, SuperClub, Asian, Ever, Jet, Jet (duplicate), ROYAL
**Current seeder brands:** Neptune, Sensus, Badger, Itron, Master Meter

No overlap — legacy brands are different from seeded test data.

---

### 5. Billing Periods

**Legacy table:** `wat_Month` (219 rows)
**Target table:** `period` (PK: `per_id`)

| Legacy Field | → | Target Field | Transform |
|---|---|---|---|
| `MonthId` | ~ | `per_id` | New auto-increment; legacy tracked |
| `MonthName` | ~ | `per_name` | Direct: "Dec 2007", "Jan 2008", etc. |
| `MonthName` | ~ | `per_code` | Parse "Dec 2007" → `"200712"` (format: `YYYYMM`) |
| `MonthName` | ~ | `start_date` | Parse → first day of month: `"2007-12-01"` |
| `MonthName` | ~ | `end_date` | Parse → last day of month: `"2007-12-31"` |
| `PenRate` | - | (dropped) | Always `10` (10% penalty rate) — not stored per-period in current schema |
| `RateId` | - | (dropped) | Always `1` — rates linked differently in current schema |
| (none) | + | `grace_period` | Default `10` days |
| (none) | + | `is_closed` | `true` for all historical periods |
| (none) | + | `stat_id` | `Status::ACTIVE` |

**Range:** MonthId 1 (Dec 2007) through MonthId 219 (Feb 2026)

---

### 6. Water Rates (FLAT RATE MODEL — `rate_inc` REMOVED)

**Legacy table:** `wat_Range` (8 rows)
**Target table:** `water_rates` (PK: `wr_id`)

> **SCHEMA CHANGE (Feb 2026):** The `rate_inc` column has been **dropped** from `water_rates`. The current system uses a **flat rate model** — a single `rate_val` per cu.m. for the entire consumption range, per account type. The legacy tiered system (base + increment per tier) no longer fits the current schema.

| Legacy Field | → | Target Field | Transform |
|---|---|---|---|
| `RateId` | - | (dropped) | Always 1 — current uses `period_id` NULL for defaults |
| `ClassId` | ~ | `class_id` | Maps to `account_type.at_id`: `1` → Residential, `2` → Commercial |
| `RangeId` | - | (dropped) | Current uses single tier (range_id=1) per class |
| `RangeMin` | - | (dropped) | Current uses 0-999 flat range |
| `RangeMax` | - | (dropped) | Current uses 0-999 flat range |
| `RateVal` | ~ | `rate_val` | **Not directly mappable.** Legacy has tiered base amounts; current has flat per-cu.m rate |
| `RateInc` | - | **COLUMN REMOVED** | `rate_inc` no longer exists in `water_rates` table |
| (none) | + | `period_id` | `NULL` (default rates, not period-specific) |
| (none) | + | `stat_id` | `Status::ACTIVE` |

**Current rate structure (flat model):**
```
Residential: 1 tier, range 0-999, rate_val = P20.00/cu.m
Commercial:  1 tier, range 0-999, rate_val = P40.00/cu.m
```

**Legacy rate structure (tiered model — for reference only):**
```
Residential: 0-1 cu.m = P20 min | 2-30 = P40 + P20/cu.m | 31-50 = P620 + P20/cu.m | 51-900 = P1020 + P20/cu.m
Commercial:  0-1 cu.m = P40 min | 2-30 = P80 + P40/cu.m | 31-50 = P1240 + P40/cu.m | 51-900 = P2040 + P40/cu.m
```

**Migration decision: DO NOT migrate legacy rates.** The rate structures are fundamentally incompatible (tiered vs flat). This is **not a blocker** because:
1. We migrate pre-calculated bill amounts (`AmtBill` → `water_amount`), NOT re-compute them
2. Historical bills already have their correct amounts baked in
3. The current flat rates are what the system uses going forward
4. Legacy rate data is preserved in the Access DB for audit reference

---

### 7. Meter Assignment (Synthetic — no legacy equivalent)

**Legacy source:** `wat_Consumer.MeterId` + `wat_Consumer.DateInstalled` + `wat_Change` (2,016 rows)
**Target table:** `MeterAssignment` (PK: `assignment_id`)

| Target Field | Source | Transform |
|---|---|---|
| `assignment_id` | (auto) | New auto-increment |
| `connection_id` | `wat_Consumer.ConsuId` | Via `legacy_id_maps` connection mapping |
| `meter_id` | `wat_Consumer.MeterId` | Via `legacy_id_maps` meter mapping |
| `installed_at` | `wat_Consumer.DateInstalled` or `wat_Change.DateChanged` | Date of installation or meter swap |
| `removed_at` | `wat_Change.DateChanged` | NULL for current assignment; date for replaced ones |
| `install_read` | `wat_Change.RdngNew` or `0` | New meter's starting reading after swap |
| `removal_read` | `wat_Change.RdngOld` | Old meter's final reading before swap |

**Reconstruction logic:**
- Consumer with **no** `wat_Change` records: 1 assignment (`installed_at` = `DateInstalled`, `removed_at` = NULL)
- Consumer with **N** meter changes (negative `RdngDiff`): N+1 assignments. Each change creates a removed assignment + the final current assignment
- `wat_Change` with positive `RdngDiff` = reading correction, NOT meter swap → skip these

---

### 8. Meter Readings

**Legacy table:** `wat_Read` (275,531 rows)
**Target table:** `MeterReading` (PK: `reading_id`)

| Legacy Field | → | Target Field | Transform |
|---|---|---|---|
| `ReadId` | ~ | `reading_id` | New auto-increment; legacy tracked |
| `ConsuId` | * | `assignment_id` | **Structural change:** Legacy links to consumer directly. Must resolve to correct `MeterAssignment` based on reading date within assignment's `installed_at`/`removed_at` range |
| `MonthId` | ~ | `period_id` | Via `legacy_id_maps` period mapping |
| `DateRead` | ~ | `reading_date` | `MM/DD/YY HH:MM:SS` → `YYYY-MM-DD` date |
| `Reading` | = | `reading_value` | Long Integer → decimal(10,3). Direct value (cumulative reading, not consumption) |
| `ReaderId` | ~ | `meter_reader_id` | Legacy `ReaderId` references `hrd_Emp.EmpId`. Current field references `users.id`. Set to NULL (legacy readers not migrated as users) |
| `DueDate` | - | (dropped) | Stored on bill, not reading |
| `RemId` | - | (dropped) | Reading remark FK — table is empty |
| (none) | + | `is_estimated` | Default `false` |

**Key structural change:** Legacy: `Reading → Consumer (direct)`. Current: `Reading → MeterAssignment → ServiceConnection`. Must resolve the correct assignment per reading date.

---

### 9. Water Bills

**Legacy table:** `wat_Bill` (271,975 rows)
**Target table:** `water_bill_history` (PK: `bill_id`)

| Legacy Field | → | Target Field | Transform |
|---|---|---|---|
| `CustoId` | ~ | `connection_id` | `ConsuId` → `ServiceConnection` via `legacy_id_maps` |
| `MonthId` | ~ | `period_id` | Via `legacy_id_maps` period mapping |
| `ReadId` | ~ | `curr_reading_id` | Via `legacy_id_maps` reading mapping. This is the key field used by `wat_CollBill.AcctId` |
| (derived) | ~ | `prev_reading_id` | **Not in legacy.** Must infer: previous period's reading for the same consumer. Track during migration |
| `VolNet` | = | `consumption` | Long Integer → decimal(12,3). Cubic meters consumed |
| `AmtBill` | = | `water_amount` | Currency → decimal(12,2). Bill amount |
| `BillDate` | ~ | `created_at` | `MM/DD/YY` → `YYYY-MM-DD HH:MM:SS.ffffff` datetime |
| `BillDate` | ~ | `due_date` | `BillDate + 30 days` (based on legacy schedule) |
| `AcctTypeId` | - | (dropped) | Always `1` (Water Bill) — not needed in current schema |
| (none) | + | `adjustment_total` | Default `0` |
| (none) | + | `is_meter_change` | Default `false` |
| (none) | + | `photo_path` | NULL (added Feb 2026 — stores reading photo, not applicable to legacy data) |
| (none) | + | `stat_id` | `Status::ACTIVE` |
| (none) | + | `total_amount` | **STORED column**: auto-computed as `water_amount + adjustment_total` |

**Important:** Legacy bills are uniquely identified by `ReadId` (one bill per reading). The `wat_CollBill.AcctId` field references `ReadId`, NOT a separate bill ID. So our `legacy_id_maps` must map `ReadId → bill_id`.

---

### 10. Penalties

> **SCHEMA CHANGE (Feb 2026):** A new `PenaltyConfiguration` table now stores configurable penalty rates. Legacy had `PenRate=10` on every `wat_Month` record. The current system reads the active rate from `PenaltyConfiguration` where `is_active=true`. Migration must seed a historical penalty config record.

**Legacy table:** `wat_Pen` (27,482 rows)
**Target table:** `CustomerCharge` (PK: `charge_id`)
**Related new table:** `PenaltyConfiguration` (seed with legacy 10% rate)

| Legacy Field | → | Target Field | Transform |
|---|---|---|---|
| `CustoId` | ~ | `customer_id` | Via connection → customer lookup |
| `CustoId` | ~ | `connection_id` | Via `legacy_id_maps` connection mapping |
| `ReadId` | ~ | (metadata only) | Used for collection matching — stored in `legacy_id_maps.metadata` |
| `PenAmt` | = | `unit_amount` | Currency → decimal(10,2). Skip rows where `PenAmt = 0` |
| `PenDate` | ~ | `due_date` | `MM/DD/YY` → `YYYY-MM-DD` date |
| `MonthId` | - | (metadata only) | Not directly mapped — tracked for collection matching |
| `AcctTypeId` | - | (dropped) | Always `2` (Penalty) |
| (none) | + | `charge_item_id` | FK to `ChargeItem` where `code = 'LATE_PENALTY'` |
| (none) | + | `application_id` | NULL |
| (none) | + | `description` | `'Late payment penalty (migrated)'` |
| (none) | + | `quantity` | `1` |
| (none) | + | `stat_id` | `Status::ACTIVE` |
| (none) | + | `total_amount` | **STORED column**: `quantity * unit_amount` |

**Matching key for collections:** `wat_CollPen.AcctId` = `wat_Pen.ReadId`. We store `ReadId` in `legacy_id_maps.metadata` for later lookup.

---

### 11. Miscellaneous Charges

**Legacy table:** `wat_Misc` (7,131 rows) + `wat_MiscItem` (11 items)
**Target table:** `CustomerCharge` (PK: `charge_id`) + `ChargeItem`

#### MiscItem → ChargeItem mapping:

| Legacy MiscItemId | Legacy Name | → | Current ChargeItem Code | Action |
|---|---|---|---|---|
| 1 | Application/Registration fee | → | `APP_PROC` | Map to existing |
| 2 | Reconnection | → | `RECONN_FEE` | Map to existing |
| 3 | Transfer Fee | → | `METER_TRANSFER` | Map to existing |
| 4 | Meter Fee | → | `LEGACY_METER_FEE` | Create new |
| 5 | Lock Wing | → | `LEGACY_LOCK_WING` | Create new |
| 6 | Installation fee | → | `INSTALL_FEE` | Map to existing |
| 7 | Tapping fee | → | `LEGACY_TAPPING_FEE` | Create new |
| 8 | Excavation fee | → | `LEGACY_EXCAVATION_FEE` | Create new |
| 9 | Rec. fee of Temp. disc. line | → | `LEGACY_TEMP_DISC_FEE` | Create new |
| 10 | Others | → | `LEGACY_OTHERS` | Create new |
| 11 | Old accounts | → | `LEGACY_OLD_ACCOUNTS` | Create new |

#### Misc charge field mapping:

| Legacy Field | → | Target Field | Transform |
|---|---|---|---|
| `MiscId` | ~ | `charge_id` | New auto-increment; legacy tracked |
| `CustoId` | ~ | `customer_id` / `connection_id` | Via `legacy_id_maps` |
| `MiscItemId` | ~ | `charge_item_id` | Via ChargeItem mapping above |
| `MiscAmt` | = | `unit_amount` | Currency → decimal(10,2) |
| `MiscDate` | ~ | `due_date` | `MM/DD/YY` → `YYYY-MM-DD` |
| `AcctTypeId` | - | (dropped) | Always `4` (Miscellaneous) |
| `UpdById` | - | (dropped) | Legacy user ID — not migrated |
| (none) | + | `quantity` | `1` |
| (none) | + | `description` | `'Misc charge (migrated)'` |

---

### 12. Receipts → Payments

**Legacy table:** `wat_Rcpt` (144,048 rows)
**Target table:** `Payment` (PK: `payment_id`)

| Legacy Field | → | Target Field | Transform |
|---|---|---|---|
| `RcptId` | ~ | `payment_id` | New auto-increment; legacy tracked |
| `RcptNo` | = | `receipt_no` | Direct string. Unique constraint — handle duplicates with suffix |
| `PayorId` | ~ | `payer_id` | `dbo_Person.PersonId` → `wat_Consumer.PersonId` → `customer.cust_id` via `legacy_id_maps` |
| `RcptDate` | ~ | `payment_date` | `MM/DD/YY` → `YYYY-MM-DD` date |
| `RcptAmt` | = | `amount_received` | Currency → decimal(10,2) |
| `CollectorId` | - | `user_id` | Legacy `hrd_Emp` ref. Set to default migration user ID |
| `Cancelled` | ~ | `stat_id` | `0` → `Status::PAID`, `1` → `Status::CANCELLED` |
| `Cancelled` | ~ | `cancelled_at` | `1` → use `RcptDate`, `0` → NULL |
| (none) | + | `cancellation_reason` | `'Cancelled in legacy system'` if cancelled |
| `StubNo` | - | (dropped) | Receipt booklet number — no equivalent |
| `Printable` | - | (dropped) | Print flag — no equivalent |
| `Printed` | - | (dropped) | Print flag — no equivalent |
| `UpdById` | - | (dropped) | Legacy user ref |
| `UpdTime` | - | (dropped) | Legacy update timestamp |

**Payor resolution chain:** `wat_Rcpt.PayorId` → `dbo_Person.PersonId` → find `wat_Consumer` where `PersonId` matches → use `ConsuId` in `legacy_id_maps` → get `customer.cust_id`

---

### 13. Collections → PaymentAllocation

**Legacy tables:** 5 separate collection tables → single `PaymentAllocation` (PK: `payment_allocation_id`)

#### Source table mapping:

| Legacy Table | Rows | AcctTypeId | → target_type | AcctId references |
|---|---|---|---|---|
| `wat_CollBill` | 286,072 | 1 (Water Bill) | `'BILL'` | `wat_Bill.ReadId` → `water_bill_history.bill_id` |
| `wat_CollPen` | 15,127 | 2 (Penalty) | `'CHARGE'` | `wat_Pen.ReadId` → `CustomerCharge.charge_id` |
| `wat_CollMisc` | 7,385 | 4 (Miscellaneous) | `'CHARGE'` | `wat_Misc.MiscId` → `CustomerCharge.charge_id` |
| `wat_CollOld` | 1,504 | 3 (Old Accounts) | (special) | Old balance entries — handled in ledger |
| `wat_CollDiff` | 13 | 5 (Unapplied) | (skip) | Only 13 rows — ignore or log |

#### Common collection field mapping:

| Legacy Field | → | Target Field | Transform |
|---|---|---|---|
| `RcptId` | ~ | `payment_id` | Via `legacy_id_maps` payment mapping |
| `AcctId` | ~ | `target_id` | Depends on `AcctTypeId` — see resolution below |
| `PaidAmt` | = | `amount_applied` | Currency → decimal(10,2) |
| `Linenum` | - | (dropped) | Line item sequence within receipt |
| `AcctTypeId` | ~ | `target_type` | `1` → `'BILL'`, `2`/`4` → `'CHARGE'` |
| `ItemId` | - | (dropped) | Appears to reference MonthId — redundant |
| (none) | + | `period_id` | Derived from bill's period (for BILL type) |
| (none) | + | `connection_id` | Derived from bill/charge's connection |
| (none) | + | `stat_id` | `Status::PAID` |

#### AcctId resolution by type:

| AcctTypeId | AcctId meaning | Resolution path |
|---|---|---|
| 1 (Bill) | `ReadId` from `wat_Bill` | `legacy_id_maps` where `entity_type='bill'` AND `legacy_id=AcctId` → `new_id` = `bill_id` |
| 2 (Penalty) | `ReadId` from `wat_Pen` | `legacy_id_maps` where `entity_type='penalty'` → search metadata for `read_id=AcctId` → `new_id` = `charge_id` |
| 4 (Misc) | `MiscId` from `wat_Misc` | `legacy_id_maps` where `entity_type='misc_charge'` AND `legacy_id=AcctId` → `new_id` = `charge_id` |

---

### 14. Old Account Balances

**Legacy table:** `wat_Old` (867 rows)
**Target:** `CustomerLedger` opening balance entry

| Legacy Field | → | Target Field | Transform |
|---|---|---|---|
| `CustoId` | ~ | `customer_id` | Via `legacy_id_maps` |
| `OldAmt` | ~ | `debit` | Amount owed as of Dec 31, 2007 |
| `AsOfDate` | ~ | `txn_date` | `12/31/07` → `2007-12-31` |
| `AcctTypeId` | - | (dropped) | Always `3` (Old Accounts) |
| (none) | + | `source_type` | `'TRANSFER'` |
| (none) | + | `description` | `'Pre-system balance as of Dec 31, 2007'` |

---

### 15. Customer Ledger (Hybrid Construction)

**No direct legacy equivalent** — the ledger is constructed from the migrated data.

**Before Jan 2025 (period_code < `202501`):**

| Source | → | Ledger entry |
|---|---|---|
| Sum of all bills before cutoff | → | 1 opening DEBIT per connection |
| Sum of all charges before cutoff | → | included in opening DEBIT |
| Sum of all payments before cutoff | → | subtracted from opening DEBIT |
| `wat_Old` balances | → | included in opening DEBIT |
| Net result | → | Single `TRANSFER` entry with net debit/credit |

**Jan 2025 onward (period_code >= `202501`):**

| Source | → | Ledger entry |
|---|---|---|
| Each `water_bill_history` | → | Individual `BILL` debit entry |
| Each `CustomerCharge` (penalty/misc) | → | Individual `CHARGE` debit entry |
| Each `Payment` | → | Individual `PAYMENT` credit entry |

---

### 16. Tables NOT Migrated

| Legacy Table | Rows | Reason |
|---|---|---|
| `dbo_Access` | 5 | Access-level security — replaced by RBAC |
| `dbo_Form` | 35 | MS Access form definitions |
| `dbo_Group` | 5 | Legacy user groups — replaced by `roles` |
| `dbo_Permit` | 175 | Legacy form permissions — replaced by `permissions` |
| `dbo_User` | 10 | Legacy users with plaintext passwords — recreate manually |
| `dbo_Zone` | 7 | Sub-purok zones — no equivalent |
| `hrd_Emp` | 17 | Employees — recreate relevant ones as `users` manually |
| `hrd_Posn` | 4 | Job positions — no equivalent |
| `ass_UserLog` | 11 | Legacy audit log — replaced by `activity_log` |
| `wat_Bill_Old` | 6,052 | Archived bill backup — redundant with `wat_Bill` |
| `wat_ConnStat` | 5,314 | Connection status history — current only stores current status |
| `wat_Change` | 2,016 | Used to build MeterAssignments, not migrated directly |
| `wat_Collection` | 6,900 | Older collection format — superseded by `wat_CollBill`/etc. |
| `wat_Production` | 32 | Water source production — no equivalent |
| `wat_Power` | 1 | Power consumption — no equivalent |
| `wat_Invoice/Issuance` | 5/5 | Barely used invoice system |
| `wat_Adjust` | 2 | Production adjustments — no equivalent |
| `wat_qORDup` | 49 | Temp query table |
| `wat_qRcptColl_Temp` | 6,528 | Temp query table |
| `wat_rMoSum_temp` | 0 | Temp query table |
| `wat_ReadRem/Rem/Remarks` | 0 | Empty tables |
| `wat_Rate` | 1 | Rate schedule name — only "Original" |
| `wat_Range` | 8 | **Tiered rates incompatible with current flat-rate model.** Legacy tiers (base+increment per tier) cannot map to current single `rate_val` per class. Historical bills carry pre-calculated amounts, so rates are not needed for migration |
| `wat_Source` | 3 | Water production sources |
| `wat_Schedule` | 3 | Billing schedule types (migrated as area metadata) |

---

## Data Volume Summary

| Entity | Legacy Table | Rows | Target Table |
|--------|-------------|------|-------------|
| Persons → Customers | `dbo_Person` + `wat_Consumer` | 3,002 | `customer` |
| Addresses | area mapping + `wat_Location` | 3,002 | `consumer_address` |
| Service Connections | `wat_Consumer` | 3,002 | `ServiceConnection` |
| Meters | `wat_Meter` | 622 | `meter` |
| Areas | `wat_Area` | 22 | `area` |
| Periods | `wat_Month` | 219 | `period` |
| Charge Items | `wat_MiscItem` | 11 | `ChargeItem` |
| Meter Assignments (synthetic) | `wat_Consumer` + `wat_Change` | ~5,000 est. | `MeterAssignment` |
| Readings | `wat_Read` | 275,531 | `MeterReading` |
| Bills | `wat_Bill` | 271,975 | `water_bill_history` |
| Penalties | `wat_Pen` | 27,482 | `CustomerCharge` |
| Misc Charges | `wat_Misc` | 7,131 | `CustomerCharge` |
| Old Balances | `wat_Old` | 867 | `CustomerLedger` (opening) |
| Receipts | `wat_Rcpt` | 144,048 | `Payment` |
| Bill Collections | `wat_CollBill` | 286,072 | `PaymentAllocation` |
| Penalty Collections | `wat_CollPen` | 15,127 | `PaymentAllocation` |
| Misc Collections | `wat_CollMisc` | 7,385 | `PaymentAllocation` |
| Old Collections | `wat_CollOld` | 1,504 | `PaymentAllocation` or `CustomerLedger` |
| Ledger (recent detail) | Derived | ~50K est. | `CustomerLedger` |
| **Total** | | **~1.1M** | |

---

## Pre-Migration Requirements

### Requirement 1: Area-to-Barangay Mapping File

The user must provide a CSV file at `database/migration-data/area-mapping.csv`:

```csv
legacy_area_id,legacy_area_code,legacy_area_desc,target_barangay_id,target_purok_id
1,"1","Near the sea along the river",,
2,"A","Around the market",,
3,"B","Poblacion left side",,
...
```

**Legacy areas (22) — fill in the barangay/purok columns:**

| AreaId | Code | Description | Schedule |
|--------|------|-------------|----------|
| 1 | 1 | Near the sea along the river | Schedule30 |
| 2 | A | Around the market | Schedule30 |
| 3 | B | Poblacion left side | Schedule30 |
| 4 | C | East Avenue | Schedule30 |
| 5 | D | (none) | Schedule30 |
| 6 | E | (none) | Schedule30 |
| 7 | F | (none) | Schedule30 |
| 8 | G | (none) | Schedule30 |
| 9 | H | (none) | Schedule30 |
| 10 | I | (none) | Schedule30 |
| 11 | J | (none) | Schedule15 |
| 12 | K | (none) | Schedule15 |
| 13 | L | (none) | Schedule15 |
| 14 | M | (none) | Schedule15 |
| 15 | N | (none) | Schedule15 |
| 16 | O | (none) | Schedule15 |
| 17 | P | (none) | Schedule30 |
| 18 | Q | (none) | Schedule30 |
| 19 | R | (none) | Schedule30 |
| 20 | S | (none) | Schedule15 |
| 21 | T | (none) | Schedule15 |
| 22 | U | Brgy. Jampason | schedule20 |

**Current barangays (target options):**

| b_id | Name | | b_id | Name |
|------|------|-|------|------|
| 1 | Aluna | | 9 | Oguis |
| 2 | Andales | | 10 | Pagahan |
| 3 | Apas | | 11 | Poblacion |
| 4 | Calacapan | | 12 | Pontacon |
| 5 | Gimangpang | | 13 | San Pedro |
| 6 | Jampason | | 14 | Sinalac |
| 7 | Kamelon | | 15 | Tawantawan |
| 8 | Kanitoan | | 16 | Tubigan |

### Requirement 2: Export Legacy CSV Files

```bash
mkdir -p database/migration-data/csv
MDB="Database - 02162026/MeedoInitao2.mdb"

mdb-export "$MDB" "dbo_Person" > database/migration-data/csv/dbo_Person.csv
mdb-export "$MDB" "wat_Consumer" > database/migration-data/csv/wat_Consumer.csv
mdb-export "$MDB" "wat_Meter" > database/migration-data/csv/wat_Meter.csv
mdb-export "$MDB" "wat_Brand" > database/migration-data/csv/wat_Brand.csv
mdb-export "$MDB" "wat_Area" > database/migration-data/csv/wat_Area.csv
mdb-export "$MDB" "wat_Month" > database/migration-data/csv/wat_Month.csv
mdb-export "$MDB" "wat_Range" > database/migration-data/csv/wat_Range.csv
mdb-export "$MDB" "wat_Location" > database/migration-data/csv/wat_Location.csv
mdb-export "$MDB" "wat_Read" > database/migration-data/csv/wat_Read.csv
mdb-export "$MDB" "wat_Bill" > database/migration-data/csv/wat_Bill.csv
mdb-export "$MDB" "wat_Pen" > database/migration-data/csv/wat_Pen.csv
mdb-export "$MDB" "wat_Misc" > database/migration-data/csv/wat_Misc.csv
mdb-export "$MDB" "wat_MiscItem" > database/migration-data/csv/wat_MiscItem.csv
mdb-export "$MDB" "wat_Old" > database/migration-data/csv/wat_Old.csv
mdb-export "$MDB" "wat_Rcpt" > database/migration-data/csv/wat_Rcpt.csv
mdb-export "$MDB" "wat_CollBill" > database/migration-data/csv/wat_CollBill.csv
mdb-export "$MDB" "wat_CollPen" > database/migration-data/csv/wat_CollPen.csv
mdb-export "$MDB" "wat_CollMisc" > database/migration-data/csv/wat_CollMisc.csv
mdb-export "$MDB" "wat_CollOld" > database/migration-data/csv/wat_CollOld.csv
mdb-export "$MDB" "wat_CollDiff" > database/migration-data/csv/wat_CollDiff.csv
mdb-export "$MDB" "wat_Change" > database/migration-data/csv/wat_Change.csv
mdb-export "$MDB" "wat_ConnStat" > database/migration-data/csv/wat_ConnStat.csv
mdb-export "$MDB" "wat_Class" > database/migration-data/csv/wat_Class.csv
mdb-export "$MDB" "wat_SubClass" > database/migration-data/csv/wat_SubClass.csv
```

---

## Phase 1: Infrastructure & Reference Data

### Task 1: Create Migration Infrastructure

**Files:**
- Create: `app/Services/Migration/LegacyCsvReader.php`
- Create: `app/Services/Migration/MigrationLogger.php`
- Create: `database/migrations/2026_02_17_000001_create_legacy_id_maps_table.php`

**Step 1:** Create the `legacy_id_maps` tracking table migration. This table tracks legacy-to-new ID mappings for every migrated entity, enabling idempotent re-runs. Columns: `id`, `entity_type` (varchar 50), `legacy_id` (bigint), `new_id` (bigint), `metadata` (json nullable), `created_at`. Unique index on `[entity_type, legacy_id]`.

**Step 2:** Run `php artisan migrate`. Expected: table created.

**Step 3:** Create `LegacyCsvReader` service — Generator-based CSV reader that yields associative arrays row by row (memory-efficient for 275K+ rows). Methods: `read()` (generator), `count()`, `readAll()` (small tables only). Handles BOM stripping.

**Step 4:** Create `MigrationLogger` service — tracks created/skipped/errors per entity, prints progress every 1,000 rows, outputs summary table at end. Methods: `setTotal()`, `created()`, `skipped()`, `error()`, `getCreatedCount()`, `summary()`.

**Step 5: Commit**

```bash
git add database/migrations/*legacy_id_maps* app/Services/Migration/
git commit -m "feat(migration): add migration infrastructure - CSV reader, logger, ID mapping table"
```

---

### Task 2: Migrate Reference Data (Areas, Periods, Meters) + Seed Penalty Config

**Files:**
- Create: `app/Console/Commands/Migration/MigrateAreasCommand.php`
- Create: `app/Console/Commands/Migration/MigratePeriodsCommand.php`
- Create: `app/Console/Commands/Migration/MigrateMetersCommand.php`
- Create: `app/Console/Commands/Migration/SeedPenaltyConfigCommand.php`

Each command follows the pattern: read CSV → check `legacy_id_maps` for dedup → create record → insert mapping.

**Key transforms per schema sections 2, 4, 5:**
- **Areas:** `wat_Area.AreaDesc` → `area.a_desc`. Empty descriptions → `"Area {AreaCode}"`.
- **Periods:** Parse `"Dec 2007"` → `per_code: "200712"`, dates. All historical → `is_closed = true`.
- **Meters:** `MeterSN` → `mtr_serial`. Resolve `BrandId` → brand name string. Empty serials → `"LEGACY-{MeterId}"`.
- **Penalty Config:** Seed a `PenaltyConfiguration` record with `rate_percentage=10.00`, `effective_date='2007-12-01'`, `is_active=true`. This matches the legacy 10% penalty rate that was used across all 219 billing periods. Skip if an active config already exists.

**Note: Water rates (`wat_Range`) are NOT migrated.** The legacy tiered rate model (base + increment per tier) is incompatible with the current flat-rate model (`rate_val` per cu.m.). Historical bills carry pre-calculated amounts, so rates are not needed. See Schema Section 6 for details.

**Step 1:** Create all four commands
**Step 2:** Run and verify: 22 areas, 219 periods, 622 meters, 1 penalty config
**Step 3: Commit**

---

### Task 3: Migrate MiscItem → ChargeItem Mapping

**Files:**
- Create: `app/Console/Commands/Migration/MigrateMiscItemsCommand.php`

Per schema section 11 mapping table. Hardcode known mappings (MiscItemId 1→APP_PROC, 2→RECONN_FEE, etc.). Create new ChargeItem records with `LEGACY_` prefix codes for unmapped items.

**Step 1:** Create command
**Step 2:** Run — 11 items processed
**Step 3: Commit**

---

## Phase 2: Customer & Connection Data

### Task 4: Migrate Customers (dbo_Person + wat_Consumer)

**Files:**
- Create: `app/Console/Commands/Migration/MigrateCustomersCommand.php`

Per schema sections 1, 2, 3. **Creates 3 records per consumer:**
1. `consumer_address` — from area mapping CSV (or default Poblacion)
2. `customer` — from `dbo_Person` (name) + `wat_Consumer` (metadata)
3. `ServiceConnection` — from `wat_Consumer` (account data)

**Key transforms:**
- Name parsing: Handle malformed records where LastName=FirstName=MidName
- `ClassId` → `c_type`, `ConsuNo` → zero-padded `account_no`
- `Active` → status mapping, `LocaId` → landmark text
- Generate `resolution_no`, parse `MM/DD/YY` dates
- `users.signature_path` and `DocumentSignatory` table exist but are unrelated — no impact

**Step 1:** Create command
**Step 2:** Run — 3,002 customers + connections
**Step 3: Commit**

---

## Phase 3: Meter Assignments & Readings

### Task 5: Build Synthetic Meter Assignments

**Files:**
- Create: `app/Console/Commands/Migration/MigrateMeterAssignmentsCommand.php`

Per schema section 7. Reconstruct from `wat_Consumer` + `wat_Change`:
- No changes → 1 assignment
- N meter changes (negative RdngDiff) → N+1 assignments
- Skip positive RdngDiff (corrections, not swaps)

**Step 1:** Create command
**Step 2:** Run — ~5K assignments
**Step 3: Commit**

---

### Task 6: Migrate Meter Readings (275K rows)

**Files:**
- Create: `app/Console/Commands/Migration/MigrateReadingsCommand.php`

Per schema section 8. Key structural change: resolve `ConsuId` → correct `MeterAssignment` by date range. Pre-load all mappings into memory. Chunked commits every 2,000 rows.

**Step 1:** Create command with `--chunk=2000`
**Step 2:** Run — ~275K readings
**Step 3: Commit**

---

## Phase 4: Bills & Charges

### Task 7: Migrate Water Bills (272K rows)

**Files:**
- Create: `app/Console/Commands/Migration/MigrateBillsCommand.php`

Per schema section 9. Map `ReadId` as `legacy_id` (referenced by collections). Track `prev_reading_id` during iteration. Derive `due_date` from `BillDate + 30 days`.

**Step 1:** Create command
**Step 2:** Run — ~272K bills
**Step 3: Commit**

---

### Task 8: Migrate Penalties & Misc Charges

**Files:**
- Create: `app/Console/Commands/Migration/MigratePenaltiesCommand.php`
- Create: `app/Console/Commands/Migration/MigrateMiscChargesCommand.php`

Per schema sections 10, 11. Penalties → CustomerCharge with LATE_PENALTY. Store ReadId in metadata. Misc → CustomerCharge via ChargeItem mapping.

**Note:** The `PenaltyConfiguration` table (seeded in Task 2) provides the penalty rate. The `LATE_PENALTY` ChargeItem (seeded, `default_amount=0.00`) is the reference item — actual penalty amounts come from the legacy `PenAmt` field, not computed from the config during migration.

**Step 1:** Create both commands
**Step 2:** Run — ~27K penalties, ~7K misc
**Step 3: Commit**

---

## Phase 5: Payments & Allocations

### Task 9: Migrate Receipts → Payments (144K rows)

**Files:**
- Create: `app/Console/Commands/Migration/MigratePaymentsCommand.php`

Per schema section 12. Chain: `PayorId` → Person → Consumer → Customer. Handle cancelled receipts.

**Step 1:** Create command
**Step 2:** Run — ~144K payments
**Step 3: Commit**

---

### Task 10: Migrate Collections → PaymentAllocation (~310K rows)

**Files:**
- Create: `app/Console/Commands/Migration/MigrateAllocationsCommand.php`

Per schema section 13. Three sub-migrations:
1. `wat_CollBill` (286K) → BILL allocations
2. `wat_CollPen` (15K) → CHARGE allocations (penalty)
3. `wat_CollMisc` (7K) → CHARGE allocations (misc)

Skip `wat_CollOld` and `wat_CollDiff`.

**Step 1:** Create command with three internal methods
**Step 2:** Run — ~308K allocations
**Step 3: Commit**

---

## Phase 6: Ledger Construction

### Task 11: Build Customer Ledger (Hybrid Approach)

**Files:**
- Create: `app/Console/Commands/Migration/BuildCustomerLedgerCommand.php`

Per schema sections 14, 15. Two phases:
- Phase A: Opening balance per connection (before Jan 2025)
- Phase B: Detailed entries from Jan 2025 onward

**Step 1:** Create command with `--cutoff-period-code=202501`
**Step 2:** Run — ~3K balances + ~50K detail entries
**Step 3: Commit**

---

## Phase 7: Orchestration & Verification

### Task 12: Create Master Orchestrator Command

**Files:**
- Create: `app/Console/Commands/Migration/RunFullMigrationCommand.php`

Runs all phases in order. Supports `--phase=N` and `--dry-run`. Stops on failure with phase resume instructions.

**Step 1:** Create command
**Step 2:** Test with `--dry-run`
**Step 3: Commit**

---

### Task 13: Create Verification Command

**Files:**
- Create: `app/Console/Commands/Migration/VerifyMigrationCommand.php`

Checks: row counts (95%+ = PASS), balance spot checks, orphan detection.

**Step 1:** Create command
**Step 2: Commit**

---

### Task 14: Add .gitignore for Migration Data

Add: `database/migration-data/csv/`, `Database - */`, `*.mdb`, `*.ldb`, `*.rar`

**Step 1:** Update `.gitignore`
**Step 2: Commit**

---

## Execution Summary

```
php artisan migrate:legacy-full              # Run everything
php artisan migrate:legacy-full --phase=1    # Just reference data
php artisan migrate:legacy-full --dry-run    # Preview
php artisan migrate:verify-legacy            # Validate results
```

**Estimated total time:** 15-30 minutes for full migration (~1.1M rows)

---

## Known Risks & Mitigations

| Risk | Mitigation |
|------|-----------|
| Duplicate receipt numbers | Unique constraint. Append "-DUP-N" suffix for duplicates |
| Malformed person names | `parseFullName()` handles "Last, First Mid". Fallback to "Unknown" |
| Missing meter for consumer | Log error and skip. Review after migration |
| Penalty lookup performance | Pre-load penalty map in memory |
| Memory on 275K+ rows | Generator-based CSV reader + chunked commits |
| Area mapping not provided | Default all to Poblacion barangay, flag for review |
| Date parsing edge cases | 2-digit year via PHP `DateTime::createFromFormat('m/d/y')` |
| `wat_Collection` (6,900 rows) | Older format superseded by `wat_CollBill`/etc. — skip to avoid double-counting |

---

## Post-Migration Checklist

- [ ] Verify all counts with `php artisan migrate:verify-legacy`
- [ ] Spot-check 10 customer balances manually against legacy system
- [ ] Verify active consumer count matches (~1,779 active)
- [ ] Check that recent bills (Jan 2025+) have correct prev/curr readings
- [ ] Verify payment allocation totals match receipt amounts
- [ ] Test billing generation for a current period on migrated data
- [ ] Test payment processing on a migrated customer
- [ ] Review error logs from each migration phase
- [ ] Confirm ledger balances make sense (debits - credits = expected AR)
