<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aging of Accounts Receivable</title>
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
            max-width: 1000px;
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
            max-width: 1000px;
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
            .back-link,
            .filter-bar {
                display: none !important;
            }
        }

        @page {
            size: A4 landscape;
            margin: 15mm;
        }

        /* Light Theme Filter Bar */
        .filter-bar {
            max-width: 1000px;
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
        <span class="filter-label"><i class="fas fa-filter" style="margin-right: 8px;"></i>Filter Report</span>
        <div class="filter-controls">
            <select id="filterMonth">
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" {{ now()->month == $m ? 'selected' : '' }}>
                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                    </option>
                @endfor
            </select>
            <select id="filterYear">
                @for($y = now()->year; $y >= now()->year - 5; $y--)
                    <option value="{{ $y }}" {{ now()->year == $y ? 'selected' : '' }}>{{ $y }}</option>
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
            <h2>Aging of Accounts Receivable</h2>
            <div class="date">As of {{ now()->format('F d, Y') }}</div>
        </div>

        <!-- Content -->
        <div class="content">
            @php
                // Calculate aging data
                $today = now();
                $paidStatusId = \App\Models\Status::getIdByDescription('PAID');
                
                // Get unpaid bills through WaterBillHistory
                $waterBills = \App\Models\WaterBillHistory::with(['serviceConnection.customer', 'status'])
                    ->where('stat_id', '!=', $paidStatusId)
                    ->whereNotNull('due_date')
                    ->get();
                
                // Group by customer
                $customerBills = $waterBills->groupBy(function($bill) {
                    return $bill->serviceConnection?->customer?->cust_id;
                })->filter(fn($bills, $custId) => !is_null($custId));

                $total30 = 0;
                $total60 = 0;
                $total90 = 0;
                $totalOver90 = 0;
                $totalCurrent = 0;
            @endphp

            <!-- Aging Table -->
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 20%;">Consumer Name</th>
                        <th style="width: 15%;">Account No</th>
                        <th style="width: 12%;">Current</th>
                        <th style="width: 12%;">1-30 Days</th>
                        <th style="width: 12%;">31-60 Days</th>
                        <th style="width: 12%;">61-90 Days</th>
                        <th style="width: 12%;">Over 90 Days</th>
                    </tr>
                </thead>
                <tbody>
                    @php $rowNum = 0; @endphp
                    @forelse($customerBills as $custId => $bills)
                        @php
                            $customer = $bills->first()?->serviceConnection?->customer;
                            if (!$customer) continue;
                            
                            $rowNum++;
                            $current = 0;
                            $within30 = 0;
                            $days31to60 = 0;
                            $days61to90 = 0;
                            $over90 = 0;

                            foreach($bills as $bill) {
                                $dueDate = \Carbon\Carbon::parse($bill->due_date);
                                $daysOverdue = $today->diffInDays($dueDate, false);
                                $amount = $bill->total_amount;

                                if ($daysOverdue >= 0) {
                                    $current += $amount;
                                } elseif ($daysOverdue >= -30) {
                                    $within30 += $amount;
                                } elseif ($daysOverdue >= -60) {
                                    $days31to60 += $amount;
                                } elseif ($daysOverdue >= -90) {
                                    $days61to90 += $amount;
                                } else {
                                    $over90 += $amount;
                                }
                            }

                            $totalCurrent += $current;
                            $total30 += $within30;
                            $total60 += $days31to60;
                            $total90 += $days61to90;
                            $totalOver90 += $over90;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $rowNum }}</td>
                            <td class="text-left">{{ trim($customer->cust_last_name . ', ' . $customer->cust_first_name) }}</td>
                            <td class="text-center">{{ $bills->first()?->serviceConnection?->account_no ?? '-' }}</td>
                            <td class="text-right">{{ $current > 0 ? '₱ ' . number_format($current, 2) : '-' }}</td>
                            <td class="text-right">{{ $within30 > 0 ? '₱ ' . number_format($within30, 2) : '-' }}</td>
                            <td class="text-right">{{ $days31to60 > 0 ? '₱ ' . number_format($days31to60, 2) : '-' }}</td>
                            <td class="text-right">{{ $days61to90 > 0 ? '₱ ' . number_format($days61to90, 2) : '-' }}</td>
                            <td class="text-right">{{ $over90 > 0 ? '₱ ' . number_format($over90, 2) : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center" style="padding: 40px; color: #6b7280;">
                                No accounts receivable found.
                            </td>
                        </tr>
                    @endforelse

                    <!-- Grand Totals -->
                    <tr class="totals-row">
                        <td colspan="3" class="text-right">GRAND TOTALS:</td>
                        <td class="text-right">₱ {{ number_format($totalCurrent, 2) }}</td>
                        <td class="text-right">₱ {{ number_format($total30, 2) }}</td>
                        <td class="text-right">₱ {{ number_format($total60, 2) }}</td>
                        <td class="text-right">₱ {{ number_format($total90, 2) }}</td>
                        <td class="text-right">₱ {{ number_format($totalOver90, 2) }}</td>
                    </tr>
                </tbody>
            </table>

            <!-- Total Summary -->
            <div style="text-align: right; padding: 12px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 4px;">
                <span style="font-size: 11px; color: #6b7280;">Total Accounts Receivable:</span>
                <span style="font-size: 16px; font-weight: 700; color: #3D90D7; margin-left: 12px;">
                    ₱ {{ number_format($totalCurrent + $total30 + $total60 + $total90 + $totalOver90, 2) }}
                </span>
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
            'exportFilename' => 'aging-of-accounts',
            'exportSelector' => '.document'
        ])
    </div>

    <script>
        function applyFilter() {
            const month = document.getElementById('filterMonth').value;
            const year = document.getElementById('filterYear').value;
            window.location.href = `{{ route('reports.aging-accounts') }}?month=${month}&year=${year}`;
        }
    </script>
</body>
</html>
