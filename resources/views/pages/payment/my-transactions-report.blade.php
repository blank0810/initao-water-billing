<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Collection Report - {{ $data['date_display'] }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            background: #f5f5f5;
            padding: 15px;
        }

        .report {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #ddd;
        }

        /* Header */
        .header {
            text-align: center;
            padding: 15px 20px;
            border-bottom: 2px solid #1e3a5f;
            background: #f8f9fa;
        }

        .header h1 {
            font-size: 16px;
            font-weight: 700;
            color: #1e3a5f;
            margin-bottom: 2px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header .subtitle {
            font-size: 10px;
            color: #666;
            margin-bottom: 4px;
        }

        .header .address {
            font-size: 9px;
            color: #666;
        }

        /* Report Title */
        .report-title {
            text-align: center;
            padding: 12px 20px;
            background: #1e3a5f;
            color: #fff;
        }

        .report-title h2 {
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .report-title .date {
            font-size: 12px;
            margin-top: 4px;
            opacity: 0.9;
        }

        /* Content */
        .content {
            padding: 20px;
        }

        /* Cashier Info */
        .cashier-info {
            display: flex;
            justify-content: space-between;
            padding: 10px 15px;
            background: #f8f9fa;
            border: 1px solid #eee;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .cashier-info .item {
            text-align: center;
        }

        .cashier-info .label {
            font-size: 9px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .cashier-info .value {
            font-size: 12px;
            font-weight: 600;
            color: #333;
            margin-top: 2px;
        }

        /* Summary Cards */
        .summary {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .summary-card {
            flex: 1;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-align: center;
        }

        .summary-card.primary {
            background: #1e3a5f;
            border-color: #1e3a5f;
            color: #fff;
        }

        .summary-card .label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            opacity: 0.8;
        }

        .summary-card .value {
            font-size: 18px;
            font-weight: 700;
            margin-top: 4px;
        }

        .summary-card.primary .value {
            color: #fff;
        }

        /* Type Breakdown */
        .breakdown {
            margin-bottom: 20px;
        }

        .breakdown-title {
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #666;
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 1px solid #eee;
        }

        .breakdown-item {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            font-size: 11px;
            border-bottom: 1px dotted #eee;
        }

        .breakdown-item:last-child {
            border-bottom: none;
        }

        .breakdown-item .type {
            color: #666;
        }

        .breakdown-item .amount {
            font-weight: 600;
            font-family: 'Courier New', monospace;
        }

        /* Transactions Table */
        .section-title {
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #1e3a5f;
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 2px solid #1e3a5f;
        }

        .transactions-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        .transactions-table th {
            background: #f8f9fa;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 0.5px;
            color: #666;
            padding: 8px 10px;
            text-align: left;
            border-bottom: 2px solid #ddd;
        }

        .transactions-table th:last-child {
            text-align: right;
        }

        .transactions-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }

        .transactions-table td:last-child {
            text-align: right;
            font-family: 'Courier New', monospace;
            font-weight: 600;
        }

        .transactions-table tr:hover {
            background: #f8f9fa;
        }

        .transactions-table .receipt-no {
            font-family: 'Courier New', monospace;
            font-weight: 500;
        }

        .transactions-table .type-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .transactions-table .type-badge.application {
            background: #dbeafe;
            color: #1e40af;
        }

        .transactions-table .type-badge.other {
            background: #f3e8ff;
            color: #7c3aed;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #666;
        }

        .empty-state p {
            font-size: 12px;
        }

        /* Table Footer */
        .table-footer {
            display: flex;
            justify-content: space-between;
            padding: 12px 10px;
            background: #f8f9fa;
            border-top: 2px solid #1e3a5f;
            font-size: 11px;
        }

        .table-footer .total-label {
            font-weight: 600;
            color: #1e3a5f;
        }

        .table-footer .total-value {
            font-size: 14px;
            font-weight: 700;
            font-family: 'Courier New', monospace;
            color: #1e3a5f;
        }

        /* Footer */
        .footer {
            margin-top: 30px;
            padding: 15px 20px;
            background: #f8f9fa;
            border-top: 1px solid #ddd;
        }

        .signature-section {
            display: flex;
            justify-content: space-between;
            gap: 40px;
            margin-bottom: 20px;
        }

        .signature-box {
            flex: 1;
            text-align: center;
        }

        .signature-box .line {
            border-top: 1px solid #333;
            margin-bottom: 4px;
            margin-top: 40px;
        }

        .signature-box .label {
            font-size: 9px;
            color: #666;
            text-transform: uppercase;
        }

        .footer-note {
            text-align: center;
            font-size: 8px;
            color: #888;
            margin-top: 15px;
        }

        .footer-note .doc-id {
            font-family: 'Courier New', monospace;
            margin-top: 4px;
        }

        /* Print Actions */
        .print-actions {
            max-width: 800px;
            margin: 15px auto 0;
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 20px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background: #1e3a5f;
            color: #fff;
        }

        .btn-secondary {
            background: #e5e7eb;
            color: #374151;
        }

        /* Print Styles */
        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .report {
                border: none;
                max-width: none;
            }

            .print-actions {
                display: none !important;
            }

            .header, .report-title, .summary-card.primary {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        @page {
            size: A4 portrait;
            margin: 10mm;
        }
    </style>
</head>
<body>
    <div class="report">
        <!-- Header -->
        <div class="header">
            <h1>Initao Water District</h1>
            <div class="subtitle">Municipal Water Utility</div>
            <div class="address">Municipal Hall Compound, Poblacion, Initao, Misamis Oriental 9022</div>
        </div>

        <!-- Report Title -->
        <div class="report-title">
            <h2>Daily Collection Report</h2>
            <div class="date">{{ $data['date_display'] }}</div>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Cashier Info -->
            <div class="cashier-info">
                <div class="item">
                    <div class="label">Cashier</div>
                    <div class="value">{{ $cashierName }}</div>
                </div>
                <div class="item">
                    <div class="label">Report Date</div>
                    <div class="value">{{ $data['date_display'] }}</div>
                </div>
                <div class="item">
                    <div class="label">Generated</div>
                    <div class="value">{{ now()->format('M d, Y h:i A') }}</div>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="summary">
                <div class="summary-card primary">
                    <div class="label">Total Collected</div>
                    <div class="value">{{ $data['summary']['total_collected_formatted'] }}</div>
                </div>
                <div class="summary-card">
                    <div class="label">Transactions</div>
                    <div class="value">{{ $data['summary']['transaction_count'] }}</div>
                </div>
            </div>

            <!-- Type Breakdown -->
            @if(count($data['summary']['by_type']) > 0)
            <div class="breakdown">
                <div class="breakdown-title">Collection by Type</div>
                @foreach($data['summary']['by_type'] as $type)
                <div class="breakdown-item">
                    <span class="type">{{ $type['type'] }}</span>
                    <span class="amount">{{ $type['amount_formatted'] }}</span>
                </div>
                @endforeach
            </div>
            @endif

            <!-- Transactions Table -->
            <div class="section-title">Transaction Details</div>

            @if(count($data['transactions']) > 0)
            <table class="transactions-table">
                <thead>
                    <tr>
                        <th>Receipt #</th>
                        <th>Customer</th>
                        <th>Type</th>
                        <th>Time</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['transactions'] as $tx)
                    <tr>
                        <td class="receipt-no">{{ $tx['receipt_no'] }}</td>
                        <td>{{ $tx['customer_name'] }}</td>
                        <td>
                            <span class="type-badge {{ $tx['payment_type'] === 'APPLICATION_FEE' ? 'application' : 'other' }}">
                                {{ $tx['payment_type_label'] }}
                            </span>
                        </td>
                        <td>{{ $tx['time'] }}</td>
                        <td>{{ $tx['amount_formatted'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="table-footer">
                <span class="total-label">TOTAL COLLECTION:</span>
                <span class="total-value">{{ $data['summary']['total_collected_formatted'] }}</span>
            </div>
            @else
            <div class="empty-state">
                <p>No transactions recorded for this date.</p>
            </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="signature-section">
                <div class="signature-box">
                    <div class="line"></div>
                    <div class="label">Prepared By (Cashier)</div>
                </div>
                <div class="signature-box">
                    <div class="line"></div>
                    <div class="label">Verified By (Supervisor)</div>
                </div>
            </div>

            <div class="footer-note">
                <p>This is a system-generated report. Please verify all amounts before signing.</p>
                <div class="doc-id">{{ strtoupper(substr(md5($data['date'] . auth()->id()), 0, 12)) }} | {{ now()->format('m/d/Y H:i') }}</div>
            </div>
        </div>
    </div>

    <!-- Print Actions -->
    <div class="print-actions">
        <button onclick="window.print()" class="btn btn-primary">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            Print Report
        </button>
        <a href="{{ route('payment.management') }}?tab=my-transactions" class="btn btn-secondary">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back
        </a>
    </div>
</body>
</html>
