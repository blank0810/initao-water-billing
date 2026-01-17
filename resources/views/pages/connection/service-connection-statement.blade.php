<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Statement - {{ $connection->account_no }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #111827;
            background: white;
            padding: 0.5in;
        }

        .header {
            display: flex;
            align-items: center;
            gap: 16px;
            padding-bottom: 16px;
            border-bottom: 2px solid #111827;
            margin-bottom: 24px;
        }

        .logo {
            width: 64px;
            height: 64px;
        }

        .header-text {
            flex: 1;
        }

        .org-name {
            font-size: 14px;
            font-weight: 700;
            color: #111827;
        }

        .org-sub {
            font-size: 11px;
            color: #4b5563;
        }

        .doc-title {
            text-align: center;
            font-size: 16px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 24px;
        }

        .info-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 24px;
        }

        .info-box {
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            padding: 12px;
        }

        .info-box-title {
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 8px;
            letter-spacing: 0.05em;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
        }

        .info-label {
            color: #6b7280;
        }

        .info-value {
            font-weight: 500;
        }

        .summary-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        .summary-card {
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            padding: 16px;
            text-align: center;
        }

        .summary-card-label {
            font-size: 10px;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .summary-card-value {
            font-size: 18px;
            font-weight: 700;
        }

        .summary-card-value.positive {
            color: #059669;
        }

        .summary-card-value.negative {
            color: #dc2626;
        }

        .transactions-section {
            margin-bottom: 24px;
        }

        .section-title {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            color: #374151;
            margin-bottom: 12px;
            letter-spacing: 0.05em;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f9fafb;
            padding: 8px 12px;
            text-align: left;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            color: #6b7280;
            border-bottom: 1px solid #e5e7eb;
        }

        td {
            padding: 8px 12px;
            border-bottom: 1px solid #f3f4f6;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .debit {
            color: #dc2626;
        }

        .credit {
            color: #059669;
        }

        .type-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .type-bill {
            background: #fef3c7;
            color: #92400e;
        }

        .type-payment {
            background: #d1fae5;
            color: #065f46;
        }

        .type-charge {
            background: #fee2e2;
            color: #991b1b;
        }

        .type-adjustment {
            background: #e0e7ff;
            color: #3730a3;
        }

        .footer {
            margin-top: 32px;
            padding-top: 16px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
        }

        .no-data {
            text-align: center;
            padding: 24px;
            color: #6b7280;
            font-style: italic;
        }

        @media print {
            body {
                padding: 0.25in;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    @php
        $customer = $connection->customer;
        $customerName = trim(implode(' ', array_filter([
            $customer?->cust_first_name,
            $customer?->cust_middle_name ? substr($customer->cust_middle_name, 0, 1) . '.' : '',
            $customer?->cust_last_name
        ]))) ?: '-';

        $purokDesc = $connection->address?->purok?->p_desc ?? '';
        $barangayDesc = $connection->address?->barangay?->b_desc ?? '';
        $fullAddress = trim(implode(', ', array_filter([$purokDesc, $barangayDesc, 'Initao, Misamis Oriental'])));
    @endphp

    <!-- Header -->
    <div class="header">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo">
        <div class="header-text">
            <div class="org-name">Municipal Economic Enterprise and Development Office</div>
            <div class="org-sub">Municipality of Initao, Misamis Oriental</div>
            <div class="org-sub">Water Utility Services</div>
        </div>
    </div>

    <!-- Document Title -->
    <div class="doc-title">Account Statement</div>

    <!-- Account & Customer Info -->
    <div class="info-section">
        <div class="info-box">
            <div class="info-box-title">Account Information</div>
            <div class="info-row">
                <span class="info-label">Account No:</span>
                <span class="info-value">{{ $connection->account_no }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Account Type:</span>
                <span class="info-value">{{ $connection->accountType?->at_desc ?? 'Residential' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Status:</span>
                <span class="info-value">{{ $connection->status?->stat_desc ?? 'ACTIVE' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Service Since:</span>
                <span class="info-value">{{ $connection->started_at?->format('M d, Y') ?? '-' }}</span>
            </div>
        </div>
        <div class="info-box">
            <div class="info-box-title">Customer Information</div>
            <div class="info-row">
                <span class="info-label">Name:</span>
                <span class="info-value">{{ $customerName }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Address:</span>
                <span class="info-value">{{ $fullAddress }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Statement Date:</span>
                <span class="info-value">{{ now()->format('F d, Y') }}</span>
            </div>
        </div>
    </div>

    <!-- Balance Summary -->
    <div class="summary-cards">
        <div class="summary-card">
            <div class="summary-card-label">Total Billed</div>
            <div class="summary-card-value">{{ number_format($balance['total_billed'] ?? 0, 2) }}</div>
        </div>
        <div class="summary-card">
            <div class="summary-card-label">Total Paid</div>
            <div class="summary-card-value positive">{{ number_format($balance['total_paid'] ?? 0, 2) }}</div>
        </div>
        <div class="summary-card">
            <div class="summary-card-label">Outstanding Balance</div>
            <div class="summary-card-value {{ ($balance['balance'] ?? 0) > 0 ? 'negative' : 'positive' }}">
                {{ number_format($balance['balance'] ?? 0, 2) }}
            </div>
        </div>
    </div>

    <!-- Transaction History -->
    <div class="transactions-section">
        <div class="section-title">Transaction History</div>
        @if($ledgerEntries->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th class="text-right">Debit</th>
                    <th class="text-right">Credit</th>
                    <th class="text-right">Balance</th>
                </tr>
            </thead>
            <tbody>
                @php $runningBalance = 0; @endphp
                @foreach($ledgerEntries->reverse() as $entry)
                    @php
                        $debit = $entry->debit ?? 0;
                        $credit = $entry->credit ?? 0;
                        $runningBalance += $debit - $credit;

                        $typeClass = match(strtoupper($entry->source_type ?? '')) {
                            'BILL', 'APP\MODELS\WATERBILLHISTORY' => 'type-bill',
                            'PAYMENT', 'APP\MODELS\PAYMENT' => 'type-payment',
                            'CHARGE', 'APP\MODELS\CUSTOMERCHARGE' => 'type-charge',
                            'ADJUSTMENT' => 'type-adjustment',
                            default => ''
                        };

                        $typeLabel = match(true) {
                            str_contains(strtoupper($entry->source_type ?? ''), 'BILL') => 'BILL',
                            str_contains(strtoupper($entry->source_type ?? ''), 'PAYMENT') => 'PAYMENT',
                            str_contains(strtoupper($entry->source_type ?? ''), 'CHARGE') => 'CHARGE',
                            default => $entry->source_type ?? '-'
                        };
                    @endphp
                    <tr>
                        <td>{{ $entry->txn_date?->format('M d, Y') ?? '-' }}</td>
                        <td>
                            <span class="type-badge {{ $typeClass }}">{{ $typeLabel }}</span>
                        </td>
                        <td>{{ $entry->description ?? '-' }}</td>
                        <td class="text-right debit">
                            {{ $debit > 0 ? number_format($debit, 2) : '' }}
                        </td>
                        <td class="text-right credit">
                            {{ $credit > 0 ? number_format($credit, 2) : '' }}
                        </td>
                        <td class="text-right" style="font-weight: 500;">
                            {{ number_format($runningBalance, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="no-data">No transactions found for this account.</div>
        @endif
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>This is a computer-generated statement. No signature required.</p>
        <p>Generated on {{ now()->format('F d, Y h:i A') }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
