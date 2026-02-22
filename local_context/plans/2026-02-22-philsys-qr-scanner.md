# PhilSys QR Code Scanner Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Add a webcam-based Philippine National ID QR code scanner to the Service Application form (Form B) that auto-fills customer name and ID fields.

**Architecture:** Browser-based QR scanning using `html5-qrcode` library. A "Scan National ID" button on Step 2 opens a modal with live webcam feed. On successful scan, raw QR data is parsed to extract name fields (firstName, middleName, lastName, suffix) and the PhilSys Card Number (PCN), which are injected into the existing Alpine.js `customerForm` object. No backend changes required.

**Tech Stack:** html5-qrcode (npm), Alpine.js (existing), Tailwind CSS + Flowbite (existing)

---

### Task 1: Install html5-qrcode dependency

**Files:**
- Modify: `package.json`

**Step 1: Install the npm package**

Run:
```bash
npm install html5-qrcode
```

Expected: `html5-qrcode` added to `dependencies` in `package.json`

**Step 2: Commit**

```bash
git add package.json package-lock.json
git commit -m "chore: add html5-qrcode dependency for QR scanning"
```

---

### Task 2: Create the PhilSys QR parser module

**Files:**
- Create: `resources/js/parsers/philsys-parser.js`

**Step 1: Create the parser module**

This module takes raw QR string output and extracts customer fields. Since the exact PhilSys QR format is unknown until tested with a real ID, this parser attempts multiple strategies (JSON, delimiter-based) and logs raw data for debugging.

```js
/**
 * PhilSys National ID QR Code Parser
 *
 * Attempts to parse the raw QR string from a Philippine National ID.
 * Tries JSON first, then delimiter-based formats.
 *
 * Returns: { firstName, middleName, lastName, suffix, idNumber } or null
 */

const KNOWN_SUFFIXES = ['JR', 'JR.', 'SR', 'SR.', 'II', 'III', 'IV', 'V'];

/**
 * Parse a full name string into parts: firstName, middleName, lastName, suffix
 * Assumes format: "LAST NAME, FIRST NAME MIDDLE NAME SUFFIX"
 * or: "FIRST NAME MIDDLE NAME LAST NAME SUFFIX"
 */
function parseFullName(fullName) {
    if (!fullName || typeof fullName !== 'string') return null;

    const cleaned = fullName.trim().toUpperCase();

    // Format 1: "LAST, FIRST MIDDLE" (common in PH government IDs)
    if (cleaned.includes(',')) {
        const [lastPart, ...restParts] = cleaned.split(',').map(s => s.trim());
        const rest = restParts.join(' ').trim().split(/\s+/);

        let suffix = '';
        const lastWord = rest[rest.length - 1];
        if (rest.length > 1 && KNOWN_SUFFIXES.includes(lastWord)) {
            suffix = lastWord;
            rest.pop();
        }

        return {
            firstName: rest[0] || '',
            middleName: rest.slice(1).join(' '),
            lastName: lastPart,
            suffix: suffix
        };
    }

    // Format 2: "FIRST MIDDLE LAST SUFFIX" (space separated)
    const parts = cleaned.split(/\s+/);
    let suffix = '';
    if (parts.length > 2 && KNOWN_SUFFIXES.includes(parts[parts.length - 1])) {
        suffix = parts.pop();
    }

    if (parts.length === 1) {
        return { firstName: parts[0], middleName: '', lastName: '', suffix };
    }
    if (parts.length === 2) {
        return { firstName: parts[0], middleName: '', lastName: parts[1], suffix };
    }

    return {
        firstName: parts[0],
        middleName: parts.slice(1, -1).join(' '),
        lastName: parts[parts.length - 1],
        suffix
    };
}

/**
 * Attempt to parse as JSON
 */
function tryParseJSON(raw) {
    try {
        const data = JSON.parse(raw);
        if (typeof data !== 'object' || data === null) return null;

        // Try common field names (case-insensitive search)
        const keys = Object.keys(data);
        const find = (patterns) => {
            for (const pattern of patterns) {
                const key = keys.find(k => k.toLowerCase().includes(pattern));
                if (key) return data[key];
            }
            return '';
        };

        // Check if name is a single field or separate fields
        const fullName = find(['full_name', 'fullname', 'name']);
        const firstName = find(['first_name', 'firstname', 'given']);
        const lastName = find(['last_name', 'lastname', 'surname', 'family']);
        const middleName = find(['middle_name', 'middlename', 'middle']);
        const suffix = find(['suffix', 'ext', 'extension']);
        const pcn = find(['pcn', 'card_number', 'id_number', 'idnumber', 'number', 'phil_sys', 'philsys']);

        if (firstName || lastName) {
            return {
                firstName: String(firstName).toUpperCase(),
                middleName: String(middleName).toUpperCase(),
                lastName: String(lastName).toUpperCase(),
                suffix: String(suffix).toUpperCase(),
                idNumber: String(pcn)
            };
        }

        if (fullName) {
            const parsed = parseFullName(String(fullName));
            return parsed ? { ...parsed, idNumber: String(pcn) } : null;
        }

        return null;
    } catch {
        return null;
    }
}

/**
 * Attempt to parse as delimited string (pipe, newline, tab)
 */
function tryParseDelimited(raw) {
    const delimiters = ['|', '\n', '\t'];

    for (const delim of delimiters) {
        const parts = raw.split(delim).map(s => s.trim()).filter(Boolean);
        if (parts.length >= 2) {
            // Heuristic: look for a part that looks like a PCN (alphanumeric, 12+ chars)
            const pcnIndex = parts.findIndex(p => /^[A-Z0-9-]{8,}$/i.test(p));
            const namePart = pcnIndex === 0 ? parts[1] : parts[0];
            const pcn = pcnIndex >= 0 ? parts[pcnIndex] : '';

            const parsed = parseFullName(namePart);
            if (parsed && (parsed.firstName || parsed.lastName)) {
                return { ...parsed, idNumber: pcn };
            }
        }
    }

    return null;
}

/**
 * Main parse function. Tries all strategies.
 * Always logs raw data for debugging.
 *
 * @param {string} rawData - The raw string from QR code scan
 * @returns {{ firstName: string, middleName: string, lastName: string, suffix: string, idNumber: string } | null}
 */
export function parsePhilSysQR(rawData) {
    if (!rawData || typeof rawData !== 'string') return null;

    const raw = rawData.trim();
    console.log('[PhilSys QR] Raw scan data:', raw);

    // Strategy 1: JSON
    const jsonResult = tryParseJSON(raw);
    if (jsonResult) {
        console.log('[PhilSys QR] Parsed as JSON:', jsonResult);
        return jsonResult;
    }

    // Strategy 2: Delimited
    const delimitedResult = tryParseDelimited(raw);
    if (delimitedResult) {
        console.log('[PhilSys QR] Parsed as delimited:', delimitedResult);
        return delimitedResult;
    }

    // Strategy 3: Treat entire string as a name (last resort, no PCN)
    if (raw.length > 2 && raw.length < 100 && !/^[{[]/.test(raw)) {
        const nameResult = parseFullName(raw);
        if (nameResult && (nameResult.firstName || nameResult.lastName)) {
            console.log('[PhilSys QR] Parsed as plain name:', nameResult);
            return { ...nameResult, idNumber: '' };
        }
    }

    console.warn('[PhilSys QR] Could not parse QR data:', raw);
    return null;
}
```

**Step 2: Commit**

```bash
git add resources/js/parsers/philsys-parser.js
git commit -m "feat(qr): add PhilSys QR code parser module"
```

---

### Task 3: Create the QR scanner Alpine.js component

**Files:**
- Create: `resources/js/components/qr-scanner.js`
- Modify: `resources/js/app.js` (add import)

**Step 1: Create the scanner component**

This registers an Alpine.js `qrScanner` component that manages the modal lifecycle, webcam access, scanning loop, and field population via a callback.

```js
import { Html5Qrcode } from 'html5-qrcode';
import { parsePhilSysQR } from '../parsers/philsys-parser.js';

/**
 * Alpine.js QR Scanner Component
 *
 * Usage in Blade:
 *   <div x-data="qrScanner(@entangle('...'))" ...>
 *
 * Dispatches 'qr-scanned' event with parsed data on success.
 */
document.addEventListener('alpine:init', () => {
    Alpine.data('qrScanner', () => ({
        isOpen: false,
        isScanning: false,
        scanner: null,
        error: '',

        openScanner() {
            this.isOpen = true;
            this.error = '';

            // Wait for modal DOM to render, then start camera
            this.$nextTick(() => {
                this.startScanning();
            });
        },

        async startScanning() {
            const readerId = 'qr-reader';
            this.scanner = new Html5Qrcode(readerId);
            this.isScanning = true;

            try {
                await this.scanner.start(
                    { facingMode: 'environment' },
                    {
                        fps: 10,
                        qrbox: { width: 250, height: 250 },
                    },
                    (decodedText) => this.onScanSuccess(decodedText),
                    () => {} // Ignore scan errors (no QR found yet)
                );
            } catch (err) {
                console.error('[QR Scanner] Failed to start camera:', err);
                this.error = 'Could not access camera. Please check browser permissions.';
                this.isScanning = false;
            }
        },

        onScanSuccess(decodedText) {
            const parsed = parsePhilSysQR(decodedText);

            if (parsed) {
                // Dispatch event with parsed data for the parent form to consume
                this.$dispatch('qr-scanned', parsed);
                this.closeScanner();
            } else {
                this.error = 'QR code detected but could not read ID data. Please try again.';
            }
        },

        async closeScanner() {
            if (this.scanner) {
                try {
                    await this.scanner.stop();
                    this.scanner.clear();
                } catch (err) {
                    console.warn('[QR Scanner] Error stopping scanner:', err);
                }
                this.scanner = null;
            }
            this.isScanning = false;
            this.isOpen = false;
            this.error = '';
        }
    }));
});
```

**Step 2: Add import to app.js**

In `resources/js/app.js`, add after the existing shared component imports (after the `signature-pad.js` import, around line 25):

```js
import './components/qr-scanner.js';
```

**Step 3: Commit**

```bash
git add resources/js/components/qr-scanner.js resources/js/app.js
git commit -m "feat(qr): add Alpine.js QR scanner component"
```

---

### Task 4: Add scanner button and modal to the Service Application form

**Files:**
- Modify: `resources/views/pages/application/service-application.blade.php`

**Step 1: Add the "Scan National ID" button**

In the Step 2a section (New Customer Registration), add a scan button in the section header area. Find the existing header div (around line 140-148):

```html
<div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
    <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
        <i class="fas fa-user text-blue-600 dark:text-blue-400"></i>
    </div>
    <div>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">New Customer Registration</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">Enter customer's personal information</p>
    </div>
</div>
```

Replace with (adds the scan button on the right side of the header):

```html
<div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
            <i class="fas fa-user text-blue-600 dark:text-blue-400"></i>
        </div>
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">New Customer Registration</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Enter customer's personal information</p>
        </div>
    </div>
    <!-- QR Scanner Button -->
    <div x-data="qrScanner" @qr-scanned.window="handleQrScanned($event.detail)">
        <button @click="openScanner()" type="button"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
            <i class="fas fa-qrcode"></i>
            <span class="hidden sm:inline">Scan National ID</span>
        </button>

        <!-- QR Scanner Modal -->
        <template x-teleport="body">
            <div x-show="isOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
                 @keydown.escape.window="closeScanner()">
                <div @click.outside="closeScanner()"
                     class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-lg w-full">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                                <i class="fas fa-qrcode text-purple-600 dark:text-purple-400"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Scan National ID QR Code</h3>
                        </div>
                        <button @click="closeScanner()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    <!-- Scanner Body -->
                    <div class="p-4">
                        <div id="qr-reader" class="w-full rounded-lg overflow-hidden"></div>

                        <!-- Error Message -->
                        <div x-show="error" x-transition
                             class="mt-3 p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-exclamation-triangle text-amber-500"></i>
                                <p class="text-sm text-amber-700 dark:text-amber-300" x-text="error"></p>
                            </div>
                        </div>

                        <!-- Instructions -->
                        <p class="mt-3 text-sm text-gray-500 dark:text-gray-400 text-center">
                            <i class="fas fa-info-circle mr-1"></i>
                            Hold the National ID QR code in front of the camera
                        </p>
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex justify-end p-4 border-t border-gray-200 dark:border-gray-700">
                        <button @click="closeScanner()" type="button"
                                class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-colors">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>
```

**Step 2: Add the `handleQrScanned` method to the `serviceApplicationWizard`**

In the `<script>` tag, inside the `serviceApplicationWizard()` return object, add this method (after the `resetForm()` method, around line 988):

```js
handleQrScanned(data) {
    if (!data) return;

    // Auto-fill name fields
    if (data.firstName) this.customerForm.firstName = data.firstName.toUpperCase();
    if (data.middleName) this.customerForm.middleName = data.middleName.toUpperCase();
    if (data.lastName) this.customerForm.lastName = data.lastName.toUpperCase();

    // Handle suffix - check if it matches a dropdown value, otherwise use OTHER
    if (data.suffix) {
        const normalizedSuffix = data.suffix.toUpperCase().replace('.', '');
        const validSuffixes = ['JR.', 'SR.', 'II', 'III', 'IV', 'V'];
        const matchedSuffix = validSuffixes.find(s => s.replace('.', '') === normalizedSuffix);

        if (matchedSuffix) {
            this.customerForm.suffix = matchedSuffix;
            this.customerForm.customSuffix = '';
        } else {
            this.customerForm.suffix = 'OTHER';
            this.customerForm.customSuffix = data.suffix.toUpperCase();
        }
    }

    // Auto-fill ID fields
    this.customerForm.idType = 'National ID';
    if (data.idNumber) this.customerForm.idNumber = data.idNumber;

    // Show success toast
    this.showToast('National ID scanned successfully!', 'success');

    // Brief highlight effect on filled fields
    this.$nextTick(() => {
        document.querySelectorAll('[x-model^="customerForm.firstName"], [x-model^="customerForm.middleName"], [x-model^="customerForm.lastName"], [x-model^="customerForm.idNumber"], [x-model^="customerForm.idType"]').forEach(el => {
            el.classList.add('ring-2', 'ring-green-500');
            setTimeout(() => el.classList.remove('ring-2', 'ring-green-500'), 2000);
        });
    });
},
```

**Step 3: Commit**

```bash
git add resources/views/pages/application/service-application.blade.php
git commit -m "feat(qr): add QR scanner button and modal to service application form"
```

---

### Task 5: Test with a real PhilSys ID and adjust parser

**Files:**
- Possibly modify: `resources/js/parsers/philsys-parser.js`

**Step 1: Build assets and start dev server**

Run:
```bash
npm run dev
```

**Step 2: Open the Service Application form in browser**

Navigate to the Service Application page, select "New Customer", and click the "Scan National ID" button.

**Step 3: Scan a real PhilSys ID**

- Open browser DevTools console (F12 → Console tab)
- Hold the PhilSys ID QR code in front of the webcam
- Watch the console for: `[PhilSys QR] Raw scan data: ...`
- Note the exact raw format

**Step 4: Adjust parser if needed**

Based on the actual QR data format observed in the console:
- If the format doesn't match JSON or delimiter patterns, update `philsys-parser.js` with the correct parsing logic
- Test again until fields auto-fill correctly

**Step 5: Verify auto-filled fields**

Confirm that:
- `firstName`, `middleName`, `lastName` are filled and UPPERCASE
- `suffix` matches a dropdown option or populates the "OTHER" custom input
- `idType` is set to "National ID"
- `idNumber` contains the PCN
- Green highlight appears briefly on filled fields
- Success toast appears
- All fields are still editable after auto-fill
- Form can be submitted normally with auto-filled data

**Step 6: Commit**

```bash
git add resources/js/parsers/philsys-parser.js
git commit -m "fix(qr): adjust parser for actual PhilSys QR format"
```

---

### Task 6: Build production assets and final commit

**Files:**
- Modify: built assets (auto-generated by Vite)

**Step 1: Build production assets**

Run:
```bash
npm run build
```

Expected: Build succeeds with no errors.

**Step 2: Final commit**

```bash
git add -A
git commit -m "chore: build production assets with QR scanner feature"
```

---

## Summary of all changes

| File | Action | Description |
|------|--------|-------------|
| `package.json` | Modify | Add `html5-qrcode` dependency |
| `resources/js/parsers/philsys-parser.js` | Create | PhilSys QR data parser (JSON, delimited, plain name strategies) |
| `resources/js/components/qr-scanner.js` | Create | Alpine.js component for webcam scanning modal |
| `resources/js/app.js` | Modify | Add import for `qr-scanner.js` |
| `resources/views/pages/application/service-application.blade.php` | Modify | Add scan button + modal to Step 2a header, add `handleQrScanned()` method |

**No backend changes.** The form submission flow remains identical — the scanner just populates existing form fields.
