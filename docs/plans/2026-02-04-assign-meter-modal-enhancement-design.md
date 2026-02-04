# Assign Meter Modal Enhancement Design

**Date:** 2026-02-04
**Feature:** Enhanced Assign Meter Modal with Add New Meter capability

## Overview

Enhanced the Assign Meter modal on the Service Connection detail page (`/customer/service-connection/{id}`) to support both selecting an existing meter from inventory AND registering a new meter directly from the modal.

## UI Design

### Approach: Toggle Tabs

The modal now has two tabs:
1. **Select Existing** - Shows dropdown of available meters (original behavior)
2. **Add New** - Shows form to register a new meter with Serial Number and Brand fields

Both tabs share the **Initial Reading** field which appears below the tab content.

### Layout

```
┌─────────────────────────────────────────────────┐
│ [Icon] Assign Meter                         [X] │
│         Assign a meter to this connection       │
├─────────────────────────────────────────────────┤
│  ┌──────────────────┬──────────────────┐        │
│  │ Select Existing  │    Add New       │        │
│  │    (active)      │                  │        │
│  └──────────────────┴──────────────────┘        │
│                                                 │
│  [Tab Content - Dropdown OR Serial+Brand]       │
│  ───────────────────────────────────────        │
│  Initial Reading: [_______________]             │
│                                                 │
├─────────────────────────────────────────────────┤
│                    [Cancel] [Assign Meter]      │
└─────────────────────────────────────────────────┘
```

## Implementation Details

### Files Modified

- `resources/views/pages/connection/service-connection-detail.blade.php`
  - Added tab navigation UI (lines 451-463)
  - Added "Select Existing" tab content (lines 465-476)
  - Added "Add New" tab content with Serial Number and Brand fields (lines 478-494)
  - Moved Initial Reading to shared section below tabs (lines 496-503)
  - Added `switchMeterTab()` JavaScript function for tab switching
  - Modified `submitAssignMeter()` to handle both modes
  - Modified `closeAssignMeterModal()` to reset form state

### API Endpoints Used

1. **Create Meter**: `POST /meters`
   - Payload: `{ mtr_serial: string, mtr_brand: string }`
   - Returns: `{ success: boolean, data: { mtr_id: number, ... } }`

2. **Assign Meter**: `POST /customer/service-connection/{id}/assign-meter`
   - Payload: `{ meter_id: number, install_read: number }`
   - Returns: `{ success: boolean, message: string }`

### Flow for "Add New" Mode

1. User enters Serial Number and Brand
2. User enters Initial Reading
3. On submit:
   - First, create meter via `POST /meters`
   - If successful, assign meter via `POST /customer/service-connection/{id}/assign-meter`
   - Show success toast: "Meter registered and assigned successfully!"
   - Reload page

### Button Text Changes

- **Select Existing tab**: "Assign Meter"
- **Add New tab**: "Register & Assign"

## Validation

- Serial Number: Required, max 50 characters
- Brand: Required, max 100 characters
- Initial Reading: Required, must be >= 0

## Error Handling

- Duplicate serial number: Shows error from API ("Meter serial number already exists")
- Network errors: Shows generic error alert
- Button resets to appropriate text based on current tab
