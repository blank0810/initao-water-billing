<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Collection Summary</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            font-size: 10px;
            line-height: 1.5;
            color: #111827;
            background: #f9fafb;
            padding: 20px;
        }

        .document {
            width: 100%;
            max-width: 950px;
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

        /* Summary Cards */
        /* Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        .data-table th {
            background: #f9fafb;
            padding: 10px 8px;
            text-align: center;
            font-weight: 600;
            color: #374151;
            border: 1px solid #e5e7eb;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .data-table td {
            padding: 8px;
            border: 1px solid #e5e7eb;
            font-size: 10px;
        }

        .data-table tbody tr:hover {
            background: #f9fafb;
        }

        .text-right {
            text-align: right !important;
            font-family: 'SF Mono', 'Consolas', monospace;
        }

        .text-center {
            text-align: center !important;
        }

        .text-left {
            text-align: left !important;
        }

        /* Totals Row */
        .totals-row {
            background: #f9fafb;
            font-weight: 700;
        }

        .totals-row td {
            padding: 10px 8px;
            font-size: 11px;
        }

        /* Signatures */
        .signatures {
            margin-top: 48px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 32px;
        }

        .signature-block {
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #374151;
            padding-top: 6px;
            margin-top: 40px;
        }

        .signature-name {
            font-weight: 600;
            font-size: 11px;
            color: #111827;
        }

        .signature-title {
            font-size: 10px;
            color: #6b7280;
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
            max-width: 950px;
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

            .print-actions {
                display: none !important;
            }

        }

        @page {
            size: A4 landscape;
            margin: 15mm;
        }

        /* Light Theme Filter Bar */
        .filter-bar {
            max-width: 950px;
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

        @media print {
            .filter-bar,
            .back-link {
                display: none !important;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        <span class="filter-label"><i class="fas fa-filter" style="margin-right: 8px;"></i>Filter Report</span>
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
            @php
                $month = request('month', now()->month);
                $year = request('year', now()->year);
                $periodDate = \Carbon\Carbon::create($year, $month, 1);
            @endphp
            <h2>Monthly Collection Summary</h2>
            <div class="date">For the Month of {{ $periodDate->format('F Y') }}</div>
        </div>

        <!-- Content -->
        <div class="content">
            @php
                $payments = \App\Models\Payment::with(['payer'])
                    ->whereMonth('payment_date', $month)
                    ->whereYear('payment_date', $year)
                    ->orderBy('payment_date')
                    ->orderBy('receipt_no')
                    ->get();
                
                $totalWaterBill = 0;
                $totalPenalty = 0;
                $totalOldAccount = 0;
                $totalOthers = 0;
                $grandTotal = 0;
            @endphp

            <!-- Collection Table -->
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 10%;">Receipt No</th>
                        <th style="width: 12%;">Date</th>
                        <th style="width: 23%;">Consumer Name</th>
                        <th style="width: 12%;">Water Bill</th>
                        <th style="width: 10%;">Penalty</th>
                        <th style="width: 10%;">Old Acct</th>
                        <th style="width: 8%;">Others</th>
                        <th style="width: 10%;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $index => $payment)
                        @php
                            $waterBillAmount = $payment->waterBill?->water_bill_amount ?? $payment->amount_received;
                            $penaltyAmount = $payment->waterBill?->penalty ?? 0;
                            $oldAccountAmount = $payment->waterBill?->old_account ?? 0;
                            $othersAmount = $payment->waterBill?->others ?? 0;
                            $rowTotal = $payment->amount_received;
                            
                            $totalWaterBill += $waterBillAmount;
                            $totalPenalty += $penaltyAmount;
                            $totalOldAccount += $oldAccountAmount;
                            $totalOthers += $othersAmount;
                            $grandTotal += $rowTotal;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center">{{ $payment->receipt_no ?? 'N/A' }}</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($payment->payment_date)->format('m/d/Y') }}</td>
                            <td class="text-left">{{ $payment->payer ? trim($payment->payer->cust_last_name . ', ' . $payment->payer->cust_first_name) : 'N/A' }}</td>
                            <td class="text-right">₱ {{ number_format($waterBillAmount, 2) }}</td>
                            <td class="text-right">{{ $penaltyAmount > 0 ? '₱ ' . number_format($penaltyAmount, 2) : '-' }}</td>
                            <td class="text-right">{{ $oldAccountAmount > 0 ? '₱ ' . number_format($oldAccountAmount, 2) : '-' }}</td>
                            <td class="text-right">{{ $othersAmount > 0 ? '₱ ' . number_format($othersAmount, 2) : '-' }}</td>
                            <td class="text-right">₱ {{ number_format($rowTotal, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center" style="padding: 40px; color: #6b7280;">
                                No collection records found for the selected period.
                            </td>
                        </tr>
                    @endforelse

                    @if($grandTotal > 0)
                        <tr class="totals-row">
                            <td colspan="4" class="text-right">GRAND TOTALS:</td>
                            <td class="text-right">₱ {{ number_format($totalWaterBill, 2) }}</td>
                            <td class="text-right">₱ {{ number_format($totalPenalty, 2) }}</td>
                            <td class="text-right">₱ {{ number_format($totalOldAccount, 2) }}</td>
                            <td class="text-right">₱ {{ number_format($totalOthers, 2) }}</td>
                            <td class="text-right">₱ {{ number_format($grandTotal, 2) }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>

            <!-- Signatures -->
            <div class="signatures">
                <div class="signature-block">
                    <div class="signature-line">
                        <div class="signature-name">_________________________</div>
                        <div class="signature-title">Prepared by: Cashier</div>
                    </div>
                </div>
                <div class="signature-block">
                    <div class="signature-line">
                        <div class="signature-name">_________________________</div>
                        <div class="signature-title">Verified by: Accountant</div>
                    </div>
                </div>
                <div class="signature-block">
                    <div class="signature-line">
                        <div class="signature-name">_________________________</div>
                        <div class="signature-title">Noted by: Municipal Treasurer</div>
                    </div>
                </div>
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
            'exportFilename' => 'monthly-collection',
            'exportSelector' => '.document'
        ])
    </div>

    <script>
        function applyFilter() {
            const month = document.getElementById('filterMonth').value;
            const year = document.getElementById('filterYear').value;
            window.location.href = `{{ route('reports.monthly-collection') }}?month=${month}&year=${year}`;
        }
    </script>
</body>
</html>
