<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing Statement</title>
    <style>
        @page { size: A4; margin: 10mm; }
        @media print {
            body { margin: 0; padding: 0; }
            .no-print { display: none; }
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #111827;
            background: #f9fafb;
            padding: 20px;
        }

        .document {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
        }

        /* Header */
        .header {
            text-align: center;
            padding: 24px 24px 20px;
            border-bottom: 1px solid #e5e7eb;
        }

        .header-logo {
            margin-bottom: 12px;
        }

        .header-logo img {
            height: 60px;
            width: auto;
        }

        .header h1 {
            font-size: 14px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 4px;
        }

        .header .subtitle {
            font-size: 10px;
            color: #6b7280;
            margin-bottom: 2px;
        }

        .header .address {
            font-size: 9px;
            color: #9ca3af;
        }

        /* Document Title */
        .doc-title {
            padding: 12px 24px;
            border-bottom: 1px solid #e5e7eb;
            text-align: center;
            background: #f9fafb;
        }

        .doc-title h2 {
            font-size: 13px;
            font-weight: 600;
            color: #111827;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .doc-title .date {
            font-size: 10px;
            color: #6b7280;
            margin-top: 4px;
        }

        /* Content */
        .content {
            padding: 20px 24px;
        }

        /* Consumer Info */
        .consumer-info {
            margin-bottom: 16px;
            padding: 12px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
        }

        .consumer-info .row {
            display: flex;
            margin-bottom: 6px;
        }

        .consumer-info .row:last-child {
            margin-bottom: 0;
        }

        .consumer-info .label {
            width: 120px;
            font-size: 10px;
            color: #6b7280;
            font-weight: 500;
        }

        .consumer-info .value {
            flex: 1;
            font-size: 11px;
            font-weight: 500;
            color: #111827;
        }

        /* Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 8px;
            margin-bottom: 16px;
        }

        .info-box {
            border: 1px solid #e5e7eb;
            padding: 10px;
            text-align: center;
            border-radius: 4px;
        }

        .info-box .label {
            font-size: 8px;
            font-weight: 600;
            color: #6b7280;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .info-box .value {
            font-size: 11px;
            font-weight: 600;
            color: #111827;
        }

        /* Billing Table */
        .billing-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        .billing-table td {
            padding: 8px 12px;
            border: 1px solid #e5e7eb;
            font-size: 11px;
        }

        .billing-table td:last-child {
            text-align: right;
            font-family: 'SF Mono', 'Consolas', monospace;
            font-size: 10px;
        }

        .billing-table tr.total {
            background: #f9fafb;
            font-weight: 700;
        }

        .billing-table tr.total td {
            padding: 10px 12px;
            font-size: 12px;
        }

        .billing-table tr.total td:last-child {
            color: #3D90D7;
            font-size: 12px;
        }

        /* Notice */
        .notice {
            padding: 12px;
            background: #fef3c7;
            border: 1px solid #fcd34d;
            border-radius: 4px;
            margin-bottom: 16px;
        }

        .notice p {
            font-size: 10px;
            color: #92400e;
            text-align: center;
            line-height: 1.5;
        }

        /* Footer */
        .footer {
            padding: 12px 24px;
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
            text-align: center;
        }

        .footer p {
            font-size: 9px;
            color: #9ca3af;
        }

        /* Print Actions */
        .print-actions {
            max-width: 600px;
            margin: 20px auto 0;
            display: flex;
            gap: 12px;
            justify-content: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.15s ease;
        }

        .btn-primary {
            background: #3D90D7;
            color: #fff;
        }

        .btn-primary:hover {
            background: #3580c0;
        }

        .btn-secondary {
            background: #fff;
            color: #374151;
            border: 1px solid #e5e7eb;
        }

        .btn-secondary:hover {
            background: #f9fafb;
        }

        /* Print Styles */
        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .document {
                border: none;
                border-radius: 0;
                max-width: none;
                width: 100%;
            }

            .print-actions,
            .filter-bar,
            .back-link {
                display: none !important;
            }
        }

        @page {
            size: A4 portrait;
            margin: 15mm;
        }

        /* Light Theme Filter Bar */
        .filter-bar {
            max-width: 600px;
            margin: 0 auto 16px;
            padding: 12px 16px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .filter-bar .filter-label {
            color: #374151;
            font-size: 13px;
            font-weight: 500;
        }

        .filter-bar .filter-controls {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .filter-bar select {
            padding: 8px 32px 8px 12px;
            background: #ffffff;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            color: #111827;
            font-size: 13px;
            cursor: pointer;
            appearance: none;
            background-image: url('data:image/svg+xml;charset=utf-8,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 fill=%22none%22 viewBox=%220 0 20 20%22%3E%3Cpath stroke=%22%23374151%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22 stroke-width=%221.5%22 d=%22m6 8 4 4 4-4%22/%3E%3C/svg%3E');
            background-position: right 8px center;
            background-repeat: no-repeat;
            background-size: 1.25em 1.25em;
        }

        .filter-bar select:focus {
            outline: none;
            border-color: #3D90D7;
        }

        .filter-bar .btn-apply {
            padding: 8px 16px;
            background: #3D90D7;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.15s;
        }

        .filter-bar .btn-apply:hover {
            background: #3580c0;
        }
    </style>
    @vite(['resources/css/app.css'])
</head>
<body>
    <!-- Back Link (Top Left) -->
    <div class="back-link" style="position: absolute; top: 20px; left: 20px; z-index: 10;">
        <a href="{{ route('reports') }}" style="text-decoration: none; color: #374151; font-size: 14px; display: flex; align-items: center; gap: 6px;">
            <i class="fas fa-chevron-left"></i> Back to Reports
        </a>
    </div>

    <!-- Dark Theme Filter Bar -->
    <div class="filter-bar" style="margin-top: 60px;">
        <span class="filter-label"><i class="fas fa-filter" style="margin-right: 8px;"></i>Billing Period</span>
        <div class="filter-controls">
            <select id="filterMonth">
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" {{ (request('month', now()->month) == $m) ? 'selected' : '' }}>
                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                    </option>
                @endfor
            </select>
            <select id="filterYear">
                @for($y = now()->year; $y >= now()->year - 5; $y--)
                    <option value="{{ $y }}" {{ (request('year', now()->year) == $y) ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <button class="btn-apply" onclick="applyFilter()">
                <i class="fas fa-sync-alt" style="margin-right: 6px;"></i>Apply
            </button>
        </div>
    </div>

    <div class="document">
        <!-- Header -->
        <div class="header">
            <div class="header-logo">
                <img src="{{ asset('images/logo.png') }}" alt="IMWS Logo">
            </div>
            <h1>Initao Municipal Water System</h1>
            <div class="subtitle">Municipal Government of Initao</div>
            <div class="address">Municipal Hall Compound, Poblacion, Initao, Misamis Oriental 9022</div>
        </div>

        <!-- Document Title -->
        <div class="doc-title">
            <h2>Billing Statement</h2>
            <div class="date">As of {{ now()->format('F d, Y') }}</div>
        </div>

        <!-- Content -->
        <div class="content">
            @php
                $bill = isset($waterBill) ? $waterBill : null;
                $customer = $bill?->customer ?? (isset($customer) ? $customer : null);
                $consumer = $customer?->consumer ?? null;
            @endphp

            <!-- Consumer Info -->
            <div class="consumer-info">
                <div class="row">
                    <span class="label">Consumer Name:</span>
                    <span class="value">{{ $consumer?->full_name ?? 'Sample Consumer Name' }}</span>
                </div>
                <div class="row">
                    <span class="label">Consumer Address:</span>
                    <span class="value">{{ $consumer?->address ?? 'Sample Address, Initao, Misamis Oriental' }}</span>
                </div>
                <div class="row">
                    <span class="label">Account Number:</span>
                    <span class="value">{{ $customer?->account_no ?? '00001' }}</span>
                </div>
            </div>

            <!-- Info Grid -->
            <div class="info-grid">
                <div class="info-box">
                    <div class="label">Area</div>
                    <div class="value">{{ $customer?->area?->name ?? 'Area 1' }}</div>
                </div>
                <div class="info-box">
                    <div class="label">Schedule</div>
                    <div class="value">{{ $customer?->schedule ?? 'A' }}</div>
                </div>
                <div class="info-box">
                    <div class="label">Class</div>
                    <div class="value">{{ $customer?->accountType?->name ?? 'Res.' }}</div>
                </div>
                <div class="info-box">
                    <div class="label">Reading</div>
                    <div class="value">{{ $bill?->billing_date ? \Carbon\Carbon::parse($bill->billing_date)->format('m/d/y') : now()->format('m/d/y') }}</div>
                </div>
                <div class="info-box">
                    <div class="label">Due Date</div>
                    <div class="value">{{ $bill?->due_date ? \Carbon\Carbon::parse($bill->due_date)->format('m/d/y') : now()->addDays(15)->format('m/d/y') }}</div>
                </div>
            </div>

            <!-- Billing Table -->
            <table class="billing-table">
                <tr>
                    <td>Meter Reading This Month</td>
                    <td>{{ number_format($bill?->current_reading ?? 1250) }}</td>
                </tr>
                <tr>
                    <td>Previous Reading</td>
                    <td>{{ number_format($bill?->previous_reading ?? 1230) }}</td>
                </tr>
                <tr>
                    <td>Consumption This Month</td>
                    <td>{{ number_format($bill?->consumption ?? 20) }} cu.m.</td>
                </tr>
                <tr>
                    <td>Water Bill This Month</td>
                    <td>₱ {{ number_format($bill?->water_bill_amount ?? 350.00, 2) }}</td>
                </tr>
                <tr>
                    <td>Arrears</td>
                    <td>₱ {{ number_format($bill?->arrears ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <td>Penalty (10%)</td>
                    <td>₱ {{ number_format($bill?->penalty ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <td>Old Account Balance</td>
                    <td>₱ {{ number_format($bill?->old_account ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <td>Other Charges</td>
                    <td>₱ {{ number_format($bill?->others ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <td>Less: Unapplied/Advance</td>
                    <td>(₱ {{ number_format($bill?->unapplied_amount ?? 0, 2) }})</td>
                </tr>
                <tr class="total">
                    <td>TOTAL AMOUNT DUE</td>
                    <td>₱ {{ number_format($bill?->total_amount ?? 350.00, 2) }}</td>
                </tr>
            </table>

            <!-- Notice -->
            <div class="notice">
                <p><strong>NOTICE:</strong> This serves as official notice. Please settle your account on or before the due date. A penalty of 10% of the principal amount will be charged for late payments. Failure to pay may result in disconnection of water service.</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Generated on {{ now()->format('F d, Y h:i A') }} • Initao Municipal Water System</p>
        </div>
    </div>

    <!-- Print Actions (Bottom) -->
    <div class="print-actions" style="margin-top: 20px; display: flex; gap: 10px; justify-content: center;">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> Print Document
        </button>
        @include('components.export-dropdown-print', [
            'exportFilename' => 'billing-statement',
            'exportSelector' => '.document'
        ])
    </div>

    <script>
        function applyFilter() {
            const month = document.getElementById('filterMonth').value;
            const year = document.getElementById('filterYear').value;
            window.location.href = `{{ route('reports.billing-statement') }}?month=${month}&year=${year}`;
        }
    </script>
</body>
</html>
