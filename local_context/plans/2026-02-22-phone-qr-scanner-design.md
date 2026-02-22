# Phone-Assisted QR Scanner — Design Document

## Problem

PhilSys National ID QR codes are extremely dense (Version 40, 177x177 modules) with an embedded logo and watermark text. PC webcams lack the resolution and autofocus to reliably decode them. Phone cameras handle this effortlessly.

## Solution

Instead of fighting webcam limitations, leverage the operator's phone as a scanning device. The PC generates a one-time link (displayed as a QR code on screen), the operator opens it on their phone, scans the PhilSys ID with the phone camera, and the result is pushed back to the PC in real-time via Laravel Reverb (WebSockets).

## Architecture

```
PC: Create session → Display QR code → Listen on Reverb channel "scan-session.{token}"
                                              ↑
Phone: Open link → Scan PhilSys QR → POST /api/scan/{token}
                                              ↓
Server: Save result → Broadcast ScanCompleted event → PC receives instantly
```

## Decisions Made

| Decision | Choice | Reasoning |
|----------|--------|-----------|
| Data flow | Real-time push via Reverb | Instant feedback, no polling overhead |
| Link delivery | QR code on PC screen | Fast, no typing, natural UX |
| Push method | Laravel Reverb (built-in WebSocket) | First-party, zero external deps, ~5 min setup, reusable for future features |
| Phone auth | One-time token only (no login) | Minimal friction, token expires in 5 min |

---

## Detailed Specs

### 1. Backend Components

**ScanSession Model + Migration:**
- `id` (primary key)
- `token` (string, unique, indexed) — 64-char cryptographically random hex
- `status` (enum: pending, completed, expired)
- `scanned_data` (JSON, nullable) — raw QR code content + format
- `expires_at` (datetime) — created_at + 5 minutes
- `created_by` (foreign key → users.id)
- `completed_at` (datetime, nullable)

**Routes:**
- `POST /api/scan-sessions` — authenticated, creates a new scan session, returns token
- `GET /scan/{token}` — public (no auth), phone scanning page
- `POST /api/scan/{token}` — public (token-validated), phone submits scanned data

**ScanSessionController:**
- `store()` — creates session with random token, 5-min expiry, returns token
- `show($token)` — validates token (not expired, not used), renders phone scanning page
- `submit($token)` — validates token, saves scanned data, marks completed, broadcasts event

**ScanCompleted Event:**
- Implements `ShouldBroadcast`
- Broadcasts on private channel `scan-session.{token}`
- Payload: `{ raw_data, format }`

**Channel authorization (routes/channels.php):**
- `scan-session.{token}` — authorized if the authenticated user created the session

### 2. Phone Scanning Page (`/scan/{token}`)

**Layout:** Standalone guest layout — no app navigation, no sidebar, no login required.

**Flow:**
1. Token validation — if expired/used/invalid → "This link has expired" message
2. Camera opens automatically — rear camera, full viewport
3. Uses `html5-qrcode` library (already installed) — works great on phones
4. On successful scan → shows green checkmark briefly → auto-submits POST to server
5. Success screen: "Scanned successfully! You can return to your computer."
6. If no camera access → fallback file upload on phone

**What the phone sends:**
```json
{
    "raw_data": "...QR code content...",
    "format": "qr_code"
}
```

**Key UX details:**
- Mobile-optimized (full viewport, large touch targets)
- Auto-retries POST if network fails
- Token consumed after first successful submission

### 3. PC-Side Modal

**"Scan National ID" button** opens modal with **two tabs:**

**Tab 1: "Scan with Phone" (default)**
- Creates scan session on open → receives token
- Displays QR code rendered client-side (npm `qrcode` library) from URL `{APP_URL}/scan/{token}`
- Status indicator:
  - "Waiting for phone..." (pulsing dot)
  - "QR code scanned!" (green flash when Reverb event arrives)
- Auto-expiry countdown: "Link expires in 4:32"
- Regenerate button if token expires
- Laravel Echo listens on private channel `scan-session.{token}`

**Tab 2: "Upload Image"**
- Existing file upload flow (jsQR + binarization + native BarcodeDetector)
- Fallback if operator doesn't have phone handy

**When Reverb event arrives:**
1. Green "Captured!" flash animation
2. Raw data displayed briefly
3. Parse PhilSys data (existing parser)
4. Auto-fill form fields (firstName, middleName, lastName, suffix, idType, idNumber)
5. Modal auto-closes after 2 seconds

### 4. Laravel Reverb Setup

```bash
php artisan install:broadcasting    # Installs Reverb + Echo + channels.php
# Update .env with generated Reverb settings
php artisan reverb:start            # Run WebSocket server (add to dev command)
```

Frontend: Laravel Echo initialized in `bootstrap.js`, listens on private channels.

### 5. Security

- **Tokens:** 64-char cryptographically random hex (`bin2hex(random_bytes(32))`)
- **Single-use:** status → completed after first submission
- **Time-limited:** 5-minute server-side expiry
- **Scoped channels:** Reverb broadcast on private channel, authorized per user
- **No persistent storage:** sessions are ephemeral, can be pruned by scheduled command

### 6. Edge Cases

- Phone opens expired link → "Link expired" message
- Phone loses network after scan → auto-retry POST
- PC closes modal before phone scans → token still valid but PC not listening; reopening creates fresh session
- Multiple tabs → each creates own session, no interference
- Phone scans wrong QR → raw data sent as-is; PC parser attempts extraction, shows raw if unparseable

### 7. What We're NOT Building

- No persistent scan history
- No phone-side authentication beyond token
- No multi-scan per session
- No data encryption at rest (PhilSys QR data is already encrypted)

---

## Tech Stack

| Component | Technology |
|-----------|-----------|
| WebSocket server | Laravel Reverb (first-party) |
| JS WebSocket client | Laravel Echo |
| QR code display (PC) | `qrcode` npm package |
| QR scanner (phone) | `html5-qrcode` (already installed) |
| QR parser | `philsys-parser.js` (already built) |
| Phone page | Blade guest layout, Alpine.js |

## Files to Create/Modify

**Create:**
- `database/migrations/xxxx_create_scan_sessions_table.php`
- `app/Models/ScanSession.php`
- `app/Http/Controllers/ScanSessionController.php`
- `app/Events/ScanCompleted.php`
- `resources/views/scan/show.blade.php` (phone scanning page)
- `resources/views/scan/expired.blade.php` (expired token page)
- `resources/views/scan/success.blade.php` (phone success page)
- `routes/channels.php`

**Modify:**
- `routes/web.php` — add scan routes
- `routes/api.php` — add scan session API routes
- `resources/js/bootstrap.js` — initialize Laravel Echo
- `resources/js/app.js` — import Echo setup
- `resources/js/components/qr-scanner.js` — replace camera tab with phone QR code display + Echo listener
- `resources/views/pages/application/service-application.blade.php` — update modal tabs
- `.env` — Reverb configuration
- `package.json` — add `qrcode`, `laravel-echo`, `pusher-js` (Reverb uses Pusher protocol)
- `composer.json` — add `laravel/reverb` (via artisan install)
