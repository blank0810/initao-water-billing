<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Statement - {{ $customer['name'] }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #1a1a2e;
            background: #f0f2f5;
            padding: 20px;
        }

        .statement {
            width: 100%;
            max-width: 850px;
            margin: 0 auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        /* Header - Fintech gradient style */
        .header {
            background: linear-gradient(135deg, #0f4c81 0%, #1a365d 50%, #0d3a5c 100%);
            color: #fff;
            padding: 25px 30px;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 50%;
        }

        .header::after {
            content: '';
            position: absolute;
            bottom: -60%;
            left: -10%;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 50%;
        }

        .header-content {
            position: relative;
            z-index: 1;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .brand {
            flex: 1;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .brand-logo {
            width: 65px;
            height: 65px;
            border-radius: 50%;
            background: #fff;
            padding: 4px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            flex-shrink: 0;
        }

        .brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 50%;
        }

        .brand-text h1 {
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .brand-text .tagline {
            font-size: 10px;
            opacity: 0.8;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .brand-text .contact {
            font-size: 9px;
            opacity: 0.7;
            margin-top: 8px;
        }

        .statement-badge {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            padding: 12px 20px;
            border-radius: 8px;
            text-align: right;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .statement-badge .label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            opacity: 0.8;
        }

        .statement-badge .title {
            font-size: 16px;
            font-weight: 700;
            margin-top: 2px;
        }

        /* Customer Info Section */
        .customer-section {
            display: flex;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        .customer-info {
            flex: 1;
            padding: 20px 30px;
            border-right: 1px solid #e2e8f0;
        }

        .customer-info:last-child {
            border-right: none;
        }

        .info-label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            margin-bottom: 4px;
        }

        .info-value {
            font-size: 13px;
            font-weight: 600;
            color: #1e293b;
        }

        .info-value.mono {
            font-family: 'Courier New', monospace;
        }

        .info-sub {
            font-size: 10px;
            color: #64748b;
            margin-top: 2px;
        }

        /* Statement Period Bar */
        .period-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            background: #fff;
            border-bottom: 2px solid #0f4c81;
        }

        .period-info {
            display: flex;
            gap: 30px;
        }

        .period-item {
            text-align: center;
        }

        .period-item .label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
        }

        .period-item .value {
            font-size: 12px;
            font-weight: 600;
            color: #1e293b;
            margin-top: 2px;
        }

        /* Balance Summary Cards */
        .balance-summary {
            display: flex;
            padding: 20px 30px;
            gap: 15px;
            background: #fff;
        }

        .balance-card {
            flex: 1;
            padding: 18px 20px;
            border-radius: 10px;
            position: relative;
            overflow: hidden;
        }

        .balance-card.debit {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border: 1px solid #fecaca;
        }

        .balance-card.credit {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border: 1px solid #bbf7d0;
        }

        .balance-card.net {
            background: linear-gradient(135deg, #0f4c81 0%, #1e40af 100%);
            color: #fff;
        }

        .balance-card .icon {
            position: absolute;
            top: 12px;
            right: 12px;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .balance-card.debit .icon {
            background: rgba(239, 68, 68, 0.15);
            color: #dc2626;
        }

        .balance-card.credit .icon {
            background: rgba(34, 197, 94, 0.15);
            color: #16a34a;
        }

        .balance-card.net .icon {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        .balance-card .label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.8;
        }

        .balance-card .amount {
            font-size: 22px;
            font-weight: 700;
            margin-top: 6px;
            font-family: 'Courier New', monospace;
        }

        .balance-card.debit .amount {
            color: #dc2626;
        }

        .balance-card.credit .amount {
            color: #16a34a;
        }

        .balance-card .count {
            font-size: 10px;
            margin-top: 4px;
            opacity: 0.7;
        }

        /* Transactions Section */
        .transactions-section {
            padding: 20px 30px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #0f4c81;
        }

        .section-title {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #0f4c81;
        }

        .entry-count {
            font-size: 10px;
            color: #64748b;
            background: #f1f5f9;
            padding: 4px 10px;
            border-radius: 12px;
        }

        /* Transactions Table */
        .ledger-table {
            width: 100%;
            border-collapse: collapse;
        }

        .ledger-table thead th {
            background: #f8fafc;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #475569;
            padding: 12px 10px;
            text-align: left;
            border-bottom: 2px solid #e2e8f0;
        }

        .ledger-table thead th.right {
            text-align: right;
        }

        .ledger-table thead th.center {
            text-align: center;
        }

        /* Date Group Header */
        .date-group-header td {
            background: #f1f5f9;
            padding: 10px;
            font-weight: 600;
            font-size: 11px;
            color: #334155;
            border-bottom: 1px solid #e2e8f0;
        }

        .date-group-header .date-icon {
            display: inline-block;
            width: 18px;
            height: 18px;
            background: #0f4c81;
            color: #fff;
            border-radius: 4px;
            text-align: center;
            line-height: 18px;
            font-size: 10px;
            margin-right: 8px;
        }

        .ledger-table tbody td {
            padding: 12px 10px;
            font-size: 10px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .ledger-table tbody tr:hover {
            background: #fafbfc;
        }

        .ledger-table .time {
            font-size: 9px;
            color: #94a3b8;
        }

        .ledger-table .type-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .type-badge.bill {
            background: #fef3c7;
            color: #b45309;
        }

        .type-badge.charge {
            background: #fee2e2;
            color: #dc2626;
        }

        .type-badge.payment {
            background: #d1fae5;
            color: #059669;
        }

        .ledger-table .description {
            max-width: 200px;
        }

        .ledger-table .amount {
            font-family: 'Courier New', monospace;
            font-weight: 600;
            text-align: right;
        }

        .ledger-table .amount.debit {
            color: #dc2626;
        }

        .ledger-table .amount.credit {
            color: #16a34a;
        }

        .ledger-table .amount.muted {
            color: #cbd5e1;
        }

        .ledger-table .balance {
            font-family: 'Courier New', monospace;
            font-weight: 700;
            text-align: right;
            font-size: 11px;
        }

        .ledger-table .balance.positive {
            color: #dc2626;
        }

        .ledger-table .balance.zero {
            color: #16a34a;
        }

        .ledger-table .balance.negative {
            color: #16a34a;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: #64748b;
        }

        .empty-state .icon {
            font-size: 40px;
            margin-bottom: 15px;
            opacity: 0.3;
        }

        /* Footer */
        .statement-footer {
            background: #f8fafc;
            padding: 20px 30px;
            border-top: 1px solid #e2e8f0;
        }

        .footer-grid {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 30px;
        }

        .footer-section {
            flex: 1;
        }

        .footer-title {
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            margin-bottom: 8px;
        }

        .footer-text {
            font-size: 9px;
            color: #64748b;
            line-height: 1.6;
        }

        .footer-disclaimer {
            text-align: center;
            padding-top: 15px;
            margin-top: 15px;
            border-top: 1px dashed #e2e8f0;
        }

        .footer-disclaimer p {
            font-size: 8px;
            color: #94a3b8;
        }

        .doc-id {
            font-family: 'Courier New', monospace;
            font-size: 9px;
            color: #94a3b8;
            margin-top: 8px;
        }

        /* QR Code placeholder */
        .qr-section {
            text-align: center;
        }

        .qr-placeholder {
            width: 60px;
            height: 60px;
            background: #e2e8f0;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 8px;
            color: #64748b;
        }

        /* Print Actions */
        .print-actions {
            max-width: 850px;
            margin: 20px auto 0;
            display: flex;
            gap: 12px;
            justify-content: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #0f4c81 0%, #1e40af 100%);
            color: #fff;
            box-shadow: 0 4px 12px rgba(15, 76, 129, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(15, 76, 129, 0.4);
        }

        .btn-secondary {
            background: #fff;
            color: #475569;
            border: 1px solid #e2e8f0;
        }

        .btn-secondary:hover {
            background: #f8fafc;
        }

        /* Print Styles */
        @media print {
            body {
                background: #fff;
                padding: 0;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .statement {
                box-shadow: none;
                border-radius: 0;
                max-width: none;
            }

            .print-actions {
                display: none !important;
            }

            .header,
            .balance-card,
            .balance-card.net,
            .type-badge,
            .date-group-header td {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        @page {
            size: A4 portrait;
            margin: 8mm;
        }
    </style>
</head>
<body>
    <div class="statement">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <div class="brand">
                    <div class="brand-logo">
                        <img src="{{ asset('images/logo.png') }}" alt="LGU Initao Logo">
                    </div>
                    <div class="brand-text">
                        <h1>Initao Water District</h1>
                        <div class="tagline">Municipal Water Utility Services</div>
                        <div class="contact">Municipal Hall Compound, Poblacion, Initao, Misamis Oriental 9022</div>
                    </div>
                </div>
                <div class="statement-badge">
                    <div class="label">Official Document</div>
                    <div class="title">Account Statement</div>
                </div>
            </div>
        </div>

        <!-- Customer Section -->
        <div class="customer-section">
            <div class="customer-info">
                <div class="info-label">Account Holder</div>
                <div class="info-value">{{ $customer['name'] }}</div>
                <div class="info-sub">{{ $customer['address'] }}</div>
            </div>
            <div class="customer-info">
                <div class="info-label">Customer ID</div>
                <div class="info-value mono">{{ $customer['customer_code'] }}</div>
            </div>
            <div class="customer-info">
                <div class="info-label">Statement Date</div>
                <div class="info-value">{{ now()->format('F d, Y') }}</div>
                <div class="info-sub">Generated at {{ now()->format('h:i A') }}</div>
            </div>
        </div>

        <!-- Period Bar -->
        <div class="period-bar">
            <div class="period-info">
                <div class="period-item">
                    <div class="label">Statement Period</div>
                    <div class="value">
                        @if($period['from'] && $period['to'])
                            {{ $period['from'] }} - {{ $period['to'] }}
                        @else
                            All Transactions
                        @endif
                    </div>
                </div>
                <div class="period-item">
                    <div class="label">Entries</div>
                    <div class="value">{{ $summary['entry_count'] }}</div>
                </div>
            </div>
        </div>

        <!-- Balance Summary -->
        <div class="balance-summary">
            <div class="balance-card debit">
                <div class="icon">↑</div>
                <div class="label">Total Charges</div>
                <div class="amount">₱{{ number_format($summary['total_debit'], 2) }}</div>
                <div class="count">{{ $summary['debit_count'] }} transaction(s)</div>
            </div>
            <div class="balance-card credit">
                <div class="icon">↓</div>
                <div class="label">Total Payments</div>
                <div class="amount">₱{{ number_format($summary['total_credit'], 2) }}</div>
                <div class="count">{{ $summary['credit_count'] }} transaction(s)</div>
            </div>
            <div class="balance-card net">
                <div class="icon">≡</div>
                <div class="label">Current Balance</div>
                <div class="amount">₱{{ number_format(abs($summary['net_balance']), 2) }}</div>
                <div class="count">{{ $summary['net_balance'] > 0 ? 'Amount Due' : ($summary['net_balance'] < 0 ? 'Credit Balance' : 'Fully Paid') }}</div>
            </div>
        </div>

        <!-- Transactions Section -->
        <div class="transactions-section">
            <div class="section-header">
                <div class="section-title">Transaction History</div>
                <div class="entry-count">{{ $summary['entry_count'] }} entries</div>
            </div>

            @if(count($entries) > 0)
            <table class="ledger-table">
                <thead>
                    <tr>
                        <th style="width: 70px;">Time</th>
                        <th style="width: 80px;">Type</th>
                        <th>Description</th>
                        <th class="right" style="width: 100px;">Debit</th>
                        <th class="right" style="width: 100px;">Credit</th>
                        <th class="right" style="width: 110px;">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @php $currentDate = null; @endphp
                    @foreach($entries as $entry)
                        @if($entry['txn_date'] !== $currentDate)
                            @php $currentDate = $entry['txn_date']; @endphp
                            <tr class="date-group-header">
                                <td colspan="6">
                                    <span class="date-icon">📅</span>
                                    {{ \Carbon\Carbon::parse($entry['txn_date'])->format('F d, Y') }}
                                    <span style="font-weight: normal; color: #64748b; margin-left: 8px;">
                                        ({{ \Carbon\Carbon::parse($entry['txn_date'])->diffForHumans() }})
                                    </span>
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td class="time">{{ $entry['time'] }}</td>
                            <td>
                                <span class="type-badge {{ strtolower($entry['source_type']) }}">
                                    {{ $entry['source_type_label'] }}
                                </span>
                            </td>
                            <td class="description">{{ $entry['description'] }}</td>
                            <td class="amount {{ $entry['debit'] > 0 ? 'debit' : 'muted' }}">
                                {{ $entry['debit'] > 0 ? '₱' . number_format($entry['debit'], 2) : '-' }}
                            </td>
                            <td class="amount {{ $entry['credit'] > 0 ? 'credit' : 'muted' }}">
                                {{ $entry['credit'] > 0 ? '₱' . number_format($entry['credit'], 2) : '-' }}
                            </td>
                            <td class="balance {{ $entry['running_balance'] > 0 ? 'positive' : ($entry['running_balance'] == 0 ? 'zero' : 'negative') }}">
                                ₱{{ number_format($entry['running_balance'], 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="empty-state">
                <div class="icon">📋</div>
                <p>No transactions found for this period.</p>
            </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="statement-footer">
            <div class="footer-grid">
                <div class="footer-section">
                    <div class="footer-title">Payment Instructions</div>
                    <div class="footer-text">
                        Please pay at the Initao Water District office during business hours (Mon-Fri, 8AM-5PM).
                        Bring this statement for reference.
                    </div>
                </div>
                <div class="footer-section">
                    <div class="footer-title">Questions?</div>
                    <div class="footer-text">
                        Contact us at the Municipal Hall Compound, Poblacion, Initao or visit our office during business hours.
                    </div>
                </div>
                <div class="footer-section qr-section">
                    <div class="qr-placeholder">
                        <span>IWD</span>
                    </div>
                </div>
            </div>
            <div class="footer-disclaimer">
                <p>This is a computer-generated statement. Please verify all transactions against your records.</p>
                <p>For discrepancies, please contact our office within 30 days of statement date.</p>
                <div class="doc-id">
                    Document ID: {{ strtoupper(substr(md5($customer['customer_code'] . now()->timestamp), 0, 16)) }} | Generated: {{ now()->format('m/d/Y H:i:s') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Print Actions -->
    <div class="print-actions">
        <button onclick="window.print()" class="btn btn-primary">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            Print Statement
        </button>
        <button onclick="window.history.back()" class="btn btn-secondary">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back
        </button>
    </div>
</body>
</html>
