<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Water Bill History</title>
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
            max-width: 900px;
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
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-bottom: 20px;
            padding: 12px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
        }

        .info-item {
            display: flex;
            gap: 8px;
        }

        .info-label {
            color: #6b7280;
            font-size: 10px;
            font-weight: 500;
        }

        .info-value {
            font-weight: 600;
            font-size: 10px;
            color: #111827;
        }

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

        /* Total Row */
        .total-row {
            background: #3D90D7;
            color: white;
            font-weight: 700;
        }

        .total-row td {
            padding: 12px 8px;
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
            max-width: 900px;
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
            max-width: 900px;
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

        .filter-bar input[type="text"] {
            padding: 8px 12px;
            background: #ffffff;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            color: #111827;
            font-size: 13px;
            min-width: 200px;
        }

        .filter-bar input[type="text"]:focus {
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

    <!-- Light Theme Filter Bar -->
    <div class="filter-bar" style="margin-top: 60px;">
        <span class="filter-label"><i class="fas fa-search" style="margin-right: 8px;"></i>Search Consumer</span>
        <div class="filter-controls">
            <input type="text" id="consumerSearch" placeholder="Enter account number or consumer name..." value="{{ request('search', '') }}">
            <button class="btn-apply" onclick="applyFilter()">
                <i class="fas fa-sync-alt" style="margin-right: 6px;"></i>Search
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
            <h2>Water Bill History</h2>
            <div class="date">{{ request('search') ? 'Search Results for: ' . request('search') : 'All Bill Records' }}</div>
        </div>

        <!-- Content -->
        <div class="content">
            @php
                $search = request('search', '');
                $billsQuery = \App\Models\WaterBillHistory::with(['serviceConnection.customer', 'serviceConnection.area', 'period', 'status'])
                    ->orderBy('created_at', 'desc');
                
                if ($search) {
                    $billsQuery->whereHas('serviceConnection', function($q) use ($search) {
                        $q->where('account_no', 'like', "%{$search}%")
                          ->orWhereHas('customer', function($q2) use ($search) {
                              $q2->whereRaw("CONCAT(cust_first_name, ' ', cust_last_name) LIKE ?", ["%{$search}%"]);
                          });
                    });
                }
                
                $bills = $billsQuery->limit(100)->get();
                $totalAmount = $bills->sum('total_amount');
            @endphp

            @if($bills->count() > 0)
                <!-- Bill History Table -->
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th style="width: 12%;">Bill No.</th>
                            <th style="width: 12%;">Account No.</th>
                            <th style="width: 20%;">Consumer Name</th>
                            <th style="width: 12%;">Billing Period</th>
                            <th style="width: 12%;">Due Date</th>
                            <th style="width: 10%;">Volume (m³)</th>
                            <th style="width: 12%;">Amount</th>
                            <th style="width: 10%;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bills as $index => $bill)
                            @php
                                $customer = $bill->serviceConnection?->customer;
                                $customerName = $customer ? trim($customer->cust_first_name . ' ' . $customer->cust_last_name) : 'N/A';
                                $paidStatusId = \App\Models\Status::getIdByDescription('PAID');
                                $isPaid = $bill->stat_id == $paidStatusId;
                            @endphp
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="text-center">{{ str_pad($bill->bill_id, 6, '0', STR_PAD_LEFT) }}</td>
                                <td class="text-center">{{ $bill->serviceConnection?->account_no ?? 'N/A' }}</td>
                                <td class="text-left">{{ $customerName }}</td>
                                <td class="text-center">{{ $bill->period?->per_name ?? '-' }}</td>
                                <td class="text-center">{{ $bill->due_date ? \Carbon\Carbon::parse($bill->due_date)->format('m/d/Y') : '-' }}</td>
                                <td class="text-right">{{ number_format($bill->consumption ?? 0, 2) }}</td>
                                <td class="text-right">₱ {{ number_format($bill->total_amount ?? 0, 2) }}</td>
                                <td class="text-center">
                                    @if($isPaid)
                                        <span style="color: #16a34a; font-weight: 600;">Paid</span>
                                    @else
                                        <span style="color: #dc2626; font-weight: 600;">Unpaid</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        <tr class="total-row">
                            <td colspan="7" class="text-right">TOTAL:</td>
                            <td class="text-right">₱ {{ number_format($totalAmount, 2) }}</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            @else
                <div style="text-align: center; padding: 60px; color: #6b7280;">
                    <i class="fas fa-file-invoice fa-3x" style="margin-bottom: 16px; opacity: 0.5;"></i>
                    <p>No bill records found{{ $search ? ' for "' . $search . '"' : '' }}.</p>
                </div>
            @endif
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
            'exportFilename' => 'water-bill-history',
            'exportSelector' => '.document'
        ])
    </div>

    <script>
        function applyFilter() {
            const search = document.getElementById('consumerSearch').value;
            window.location.href = `{{ route('reports.water-bill-history') }}?search=${encodeURIComponent(search)}`;
        }
    </script>
</body>
</html>
