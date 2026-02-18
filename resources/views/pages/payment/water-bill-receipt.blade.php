<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Receipt - {{ $payment->receipt_no }}</title>
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
            padding: 10px;
        }

        .receipt {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #ddd;
        }

        /* Header */
        .header {
            text-align: center;
            padding: 12px 15px;
            border-bottom: 2px solid #1e3a5f;
            background: #f8f9fa;
        }

        .header h1 {
            font-size: 14px;
            font-weight: 700;
            color: #1e3a5f;
            margin-bottom: 2px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header .subtitle {
            font-size: 9px;
            color: #666;
            margin-bottom: 4px;
        }

        .header .address {
            font-size: 9px;
            color: #666;
        }

        /* Receipt Title */
        .receipt-title {
            text-align: center;
            padding: 8px 15px;
            background: #1e3a5f;
            color: #fff;
        }

        .receipt-title h2 {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .receipt-title .receipt-no {
            font-family: 'Courier New', monospace;
            font-size: 13px;
            font-weight: 700;
            margin-top: 2px;
        }

        /* Content */
        .content {
            padding: 12px 15px;
        }

        /* Meta Row */
        .meta-row {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
            font-size: 10px;
            border-bottom: 1px dotted #ddd;
        }

        .meta-row:last-child {
            border-bottom: none;
        }

        .meta-row .label {
            color: #666;
        }

        .meta-row .value {
            font-weight: 600;
            color: #333;
        }

        /* Section */
        .section {
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px solid #eee;
        }

        .section-title {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #1e3a5f;
            margin-bottom: 6px;
        }

        /* Customer Info */
        .customer-name {
            font-size: 12px;
            font-weight: 700;
            color: #333;
        }

        .customer-details {
            font-size: 10px;
            color: #666;
            margin-top: 2px;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }

        .items-table th {
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            color: #666;
            text-align: left;
            padding: 4px 0;
            border-bottom: 1px solid #ddd;
        }

        .items-table th:last-child {
            text-align: right;
        }

        .items-table td {
            font-size: 10px;
            padding: 6px 0;
            border-bottom: 1px dotted #eee;
            vertical-align: top;
        }

        .items-table td:last-child {
            text-align: right;
            font-family: 'Courier New', monospace;
            font-weight: 500;
        }

        .items-table tr:last-child td {
            border-bottom: none;
        }

        /* Totals */
        .totals {
            margin-top: 8px;
            padding-top: 8px;
            border-top: 2px solid #1e3a5f;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
            font-size: 10px;
        }

        .total-row .label {
            color: #666;
        }

        .total-row .value {
            font-family: 'Courier New', monospace;
            font-weight: 600;
        }

        .total-row.grand {
            margin-top: 4px;
            padding-top: 6px;
            border-top: 1px solid #ddd;
            font-size: 12px;
        }

        .total-row.grand .label {
            font-weight: 700;
            color: #1e3a5f;
        }

        .total-row.grand .value {
            font-size: 14px;
            font-weight: 700;
            color: #1e3a5f;
        }

        .total-row.change .value {
            color: #059669;
        }

        /* Paid Badge */
        .paid-badge {
            text-align: center;
            margin-top: 10px;
            padding: 6px;
            background: #ecfdf5;
            border: 1px solid #10b981;
            border-radius: 4px;
        }

        .paid-badge span {
            font-size: 10px;
            font-weight: 700;
            color: #059669;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        /* Cancelled Badge */
        .cancelled-badge {
            text-align: center;
            margin-top: 10px;
            padding: 6px;
            background: #fef2f2;
            border: 2px solid #ef4444;
            border-radius: 4px;
        }

        .cancelled-badge span {
            font-size: 10px;
            font-weight: 700;
            color: #dc2626;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        /* Footer */
        .footer {
            margin-top: 10px;
            padding: 10px 15px;
            background: #f8f9fa;
            border-top: 1px solid #ddd;
            text-align: center;
        }

        .footer p {
            font-size: 8px;
            color: #888;
            margin-bottom: 4px;
        }

        .footer .doc-id {
            font-family: 'Courier New', monospace;
            font-size: 8px;
            color: #aaa;
        }

        /* Signature Line */
        .signature {
            margin-top: 15px;
            padding-top: 8px;
        }

        .signature-line {
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }

        .signature-box {
            flex: 1;
            text-align: center;
        }

        .signature-box .line {
            border-top: 1px solid #333;
            margin-bottom: 4px;
        }

        .signature-box .label {
            font-size: 8px;
            color: #666;
            text-transform: uppercase;
        }

        /* Print Actions */
        .print-actions {
            max-width: 400px;
            margin: 15px auto 0;
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
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

            .receipt {
                border: none;
                max-width: none;
                width: 100%;
            }

            .print-actions {
                display: none !important;
            }

            .header, .receipt-title, .paid-badge, .cancelled-badge, .footer {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        @page {
            size: Legal portrait;
            margin: 10mm;
        }
    </style>
</head>
<body>
    @php
        $customer = $payment->payer;
        $customerName = $customer
            ? trim(($customer->cust_first_name ?? '') . ' ' . ($customer->cust_middle_name ? $customer->cust_middle_name[0] . '. ' : '') . ($customer->cust_last_name ?? ''))
            : '-';
        $fullAddress = $customer?->address
            ? trim(($customer->address->purok?->p_desc ?? '') . ', ' . ($customer->address->barangay?->b_desc ?? '') . ', Initao, Misamis Oriental')
            : '-';
        $change = $payment->amount_received - $totalDue;
    @endphp

    <div class="receipt">
        <!-- Header -->
        <div class="header">
            <h1>Initao Water District</h1>
            <div class="subtitle">Municipal Water Utility</div>
            <div class="address">Jampason, Initao, Misamis Oriental</div>
        </div>

        <!-- Receipt Title -->
        <div class="receipt-title">
            <h2>Official Receipt</h2>
            <div class="receipt-no">{{ $payment->receipt_no }}</div>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Transaction Meta -->
            <div class="meta-row">
                <span class="label">Date:</span>
                <span class="value">{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</span>
            </div>
            <div class="meta-row">
                <span class="label">Time:</span>
                <span class="value">{{ \Carbon\Carbon::parse($payment->created_at)->format('h:i A') }}</span>
            </div>
            <div class="meta-row">
                <span class="label">Cashier:</span>
                <span class="value">{{ $payment->user?->name ?? 'System' }}</span>
            </div>

            <!-- Customer Section -->
            <div class="section">
                <div class="section-title">Received From</div>
                <div class="customer-name">{{ $customerName }}</div>
                <div class="customer-details">{{ $customer?->resolution_no ?? '-' }}</div>
                <div class="customer-details">{{ $fullAddress }}</div>
            </div>

            <!-- Items Section -->
            <div class="section">
                <div class="section-title">Payment For</div>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lineItems as $item)
                        <tr>
                            <td>{{ $item['description'] }}</td>
                            <td>{{ number_format($item['amount'], 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Totals -->
            <div class="totals">
                <div class="total-row">
                    <span class="label">Subtotal:</span>
                    <span class="value">PHP {{ number_format($totalDue, 2) }}</span>
                </div>
                <div class="total-row">
                    <span class="label">Amount Tendered:</span>
                    <span class="value">PHP {{ number_format($payment->amount_received, 2) }}</span>
                </div>
                @if($change > 0)
                <div class="total-row change">
                    <span class="label">Change:</span>
                    <span class="value">PHP {{ number_format($change, 2) }}</span>
                </div>
                @endif
                <div class="total-row grand">
                    <span class="label">TOTAL PAID:</span>
                    <span class="value">PHP {{ number_format($totalDue, 2) }}</span>
                </div>
            </div>

            <!-- Status Badge -->
            @if ($payment->status?->stat_desc === 'CANCELLED')
            <div class="cancelled-badge" role="status" aria-label="Payment Cancelled">
                <span>&#10007; Payment Cancelled</span>
            </div>
            @else
            <div class="paid-badge" role="status" aria-label="Payment Confirmed">
                <span>&#10003; Payment Confirmed</span>
            </div>
            @endif

            <!-- Signature -->
            <div class="signature">
                <div class="signature-line">
                    <x-document-signature position-key="CUSTOMER" :name="$customerName" label="Customer Signature" :show-image="false" style="receipt" />
                    <x-document-signature position-key="CASHIER" :name="$payment->user?->name" :signature-url="$payment->user?->signature_url" label="Cashier" style="receipt" />
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>This is an official receipt. Please keep for your records.</p>
            <p>For inquiries: (088) 123-4567 | Mon-Fri 8AM-5PM</p>
            <div class="doc-id">{{ strtoupper(substr(md5($payment->receipt_no), 0, 12)) }} | {{ now()->format('m/d/Y H:i') }}</div>
        </div>
    </div>

    <!-- Print Actions -->
    <div class="print-actions">
        <button onclick="window.print()" class="btn btn-primary">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            Print
        </button>
        <a href="{{ route('payment.management') }}" class="btn btn-secondary">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back
        </a>
    </div>
</body>
</html>
