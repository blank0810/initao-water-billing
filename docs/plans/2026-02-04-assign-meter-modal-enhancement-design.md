# Assign Meter Modal Enhancement Design

**Date:** 2026-02-04
**Feature:** Enhanced Assign Meter Modal with Add New Meter capability and Auto-Replace

## Overview

Enhanced the Assign Meter modal on the Service Connection detail page (`/customer/service-connection/{id}`) to support:
1. Selecting an existing meter from inventory OR registering a new meter
2. Automatically handling meter replacement when a meter already exists

## UI Design

### Approach: Toggle Tabs with Conditional Replacement Section

The modal has two tabs:
1. **Select Existing** - Shows dropdown of available meters
2. **Add New** - Shows form to register a new meter with Serial Number and Brand fields

When a meter already exists on the connection, a **Replacing Existing Meter** section appears showing:
- Current meter info (serial + brand)
- Old Meter Final Reading field (required)

### Layout - Fresh Assignment (No Existing Meter)

```
┌─────────────────────────────────────────────────┐
│ [Icon] Assign Meter                         [X] │
│         Assign a meter to this connection       │
├─────────────────────────────────────────────────┤
│  ┌──────────────────┬──────────────────┐        │
│  │ Select Existing  │    Add New       │        │
│  └──────────────────┴──────────────────┘        │
│                                                 │
│  [Tab Content - Dropdown OR Serial+Brand]       │
│  ───────────────────────────────────────        │
│  Initial Reading: [_______________]             │
├─────────────────────────────────────────────────┤
│                    [Cancel] [Assign Meter]      │
└─────────────────────────────────────────────────┘
```

### Layout - Replacement (Existing Meter)

```
┌─────────────────────────────────────────────────┐
│ [Icon] Replace Meter                        [X] │
│         Replace the current meter               │
├─────────────────────────────────────────────────┤
│  ┌─────────────────────────────────────────┐    │
│  │ ⚠ REPLACING EXISTING METER              │    │
│  │ Current: MTR-2024-001 (Brand X)         │    │
│  │                                         │    │
│  │ Old Meter Final Reading: [_______]      │    │
│  └─────────────────────────────────────────┘    │
│                                                 │
│  ┌──────────────────┬──────────────────┐        │
│  │ Select Existing  │    Add New       │        │
│  └──────────────────┴──────────────────┘        │
│                                                 │
│  [Tab Content]                                  │
│  ───────────────────────────────────────        │
│  New Meter Initial Reading: [_______________]   │
├─────────────────────────────────────────────────┤
│                    [Cancel] [Replace Meter]     │
└─────────────────────────────────────────────────┘
```

## Database Operations

### Scenario 1: Fresh Assignment

| Table | Operation | Fields |
|-------|-----------|--------|
| `Meter` | UPDATE | `stat_id` → INACTIVE |
| `MeterAssignment` | INSERT | connection_id, meter_id, installed_at, install_read |
| `MeterReading` | INSERT | assignment_id, reading_date, reading_value |

### Scenario 2: Replacement

| Table | Operation | Fields |
|-------|-----------|--------|
| `MeterAssignment` (old) | UPDATE | removed_at, removal_read |
| `Meter` (old) | UPDATE | `stat_id` → ACTIVE |
| `Meter` (new) | UPDATE | `stat_id` → INACTIVE |
| `MeterAssignment` (new) | INSERT | connection_id, meter_id, installed_at, install_read |
| `MeterReading` | INSERT | assignment_id, reading_date, reading_value |

## Implementation Details

### Files Modified

1. **`app/Http/Controllers/ServiceConnection/ServiceConnectionController.php`**
   - Modified `assignMeter()` to detect existing meter and call `replaceMeter()` when needed
   - Added validation for `removal_read` parameter

2. **`resources/views/pages/connection/service-connection-detail.blade.php`**
   - Added `data-current-meter` attribute with meter JSON
   - Added "Replacing Existing Meter" conditional section
   - Added `initializeAssignMeterModal()` function
   - Added `updateMeterButtonText()` helper
   - Modified `submitAssignMeter()` to include `removal_read`
   - Modified `closeAssignMeterModal()` to reset all fields

### API Endpoint

**POST** `/customer/service-connection/{id}/assign-meter`

**Payload:**
```json
{
    "meter_id": 123,
    "install_read": 0.000,
    "removal_read": 150.500  // Only required when replacing
}
```

### Button Text States

| State | Select Existing | Add New |
|-------|-----------------|---------|
| Fresh | "Assign Meter" | "Register & Assign" |
| Replace | "Replace Meter" | "Register & Replace" |

## Validation

- `removal_read`: Required when replacing, must be >= current meter's install_read
- `install_read`: Required, must be >= 0
- Serial Number (Add New): Required, max 50 chars, unique
- Brand (Add New): Required, max 100 chars

## Error Handling

| Scenario | Message |
|----------|---------|
| Missing final reading | "Please enter the old meter's final reading" |
| Final reading too low | "Final reading cannot be less than the install reading (X.XXX)" |
| Duplicate serial | "Meter serial number already exists" |
| Meter already assigned | "Meter is already assigned to another connection" |
