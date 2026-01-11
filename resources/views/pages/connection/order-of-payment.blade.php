<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order of Payment - {{ $application->application_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            background: #fff;
        }

        .page {
            width: 8.5in;
            min-height: 5.5in;
            padding: 0.5in;
            margin: 0 auto;
            background: #fff;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #1e3a5f;
            padding-bottom: 15px;
        }

        .header h1 {
            font-size: 18px;
            font-weight: bold;
            color: #1e3a5f;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header h2 {
            font-size: 14px;
            color: #555;
            margin-top: 5px;
        }

        .header p {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
        }

        .document-title {
            text-align: center;
            margin: 20px 0;
        }

        .document-title h3 {
            font-size: 16px;
            font-weight: bold;
            color: #1e3a5f;
            text-transform: uppercase;
            border: 2px solid #1e3a5f;
            display: inline-block;
            padding: 8px 30px;
        }

        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .info-box {
            width: 48%;
        }

        .info-row {
            display: flex;
            margin-bottom: 8px;
        }

        .info-label {
            font-weight: bold;
            width: 120px;
            color: #555;
        }

        .info-value {
            flex: 1;
            color: #333;
        }

        .charges-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .charges-table th {
            background: #1e3a5f;
            color: #fff;
            padding: 10px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
        }

        .charges-table th:last-child {
            text-align: right;
        }

        .charges-table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .charges-table td:last-child {
            text-align: right;
            font-weight: bold;
        }

        .charges-table tbody tr:hover {
            background: #f8f9fa;
        }

        .charges-table tfoot tr {
            background: #f0f0f0;
        }

        .charges-table tfoot td {
            padding: 12px 10px;
            font-weight: bold;
            font-size: 14px;
            border-top: 2px solid #1e3a5f;
        }

        .total-row td:last-child {
            color: #1e3a5f;
            font-size: 16px;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }

        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }

        .signature-box {
            width: 45%;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 50px;
            padding-top: 5px;
            font-size: 11px;
        }

        .notice {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 10px 15px;
            margin-top: 20px;
            font-size: 11px;
            border-radius: 4px;
        }

        .notice strong {
            color: #856404;
        }

        .print-date {
            text-align: right;
            font-size: 10px;
            color: #999;
            margin-top: 20px;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .page {
                padding: 0.3in;
                margin: 0;
            }

            .no-print {
                display: none !important;
            }
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: #1e3a5f;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .print-button:hover {
            background: #2d4a6f;
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">
        Print Order of Payment
    </button>

    <div class="page">
        <!-- Header -->
        <div class="header">
            <h1>Initao Water District</h1>
            <h2>Municipality of Initao, Misamis Oriental</h2>
            <p>Provincial Road, Poblacion, Initao, Misamis Oriental</p>
        </div>

        <!-- Document Title -->
        <div class="document-title">
            <h3>Order of Payment</h3>
        </div>

        <!-- Application Info -->
        @php
            $firstName = data_get($application, 'customer.cust_first_name', '');
            $middleName = data_get($application, 'customer.cust_middle_name', '');
            $lastName = data_get($application, 'customer.cust_last_name', '');
            $middleInitial = $middleName ? substr($middleName, 0, 1) . '.' : '';
            $fullName = trim(implode(' ', array_filter([$firstName, $middleInitial, $lastName])));

            $purokName = data_get($application, 'address.purok.p_desc', '');
            $barangayName = data_get($application, 'address.barangay.b_desc', '');
            $addressParts = array_filter([$purokName, $barangayName, 'Initao', 'Misamis Oriental']);
            $fullAddress = implode(', ', $addressParts);
        @endphp
        <div class="info-section">
            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">Application No:</span>
                    <span class="info-value"><strong>{{ $application->application_number }}</strong></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Date:</span>
                    <span class="info-value">{{ now()->format('F d, Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span class="info-value">{{ data_get($application, 'status.stat_desc', 'N/A') }}</span>
                </div>
            </div>
            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">Customer Name:</span>
                    <span class="info-value">
                        <strong>{{ $fullName ?: 'N/A' }}</strong>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Service Address:</span>
                    <span class="info-value">{{ $fullAddress }}</span>
                </div>
            </div>
        </div>

        <!-- Charges Table -->
        <table class="charges-table">
            <thead>
                <tr>
                    <th style="width: 60%">Description</th>
                    <th style="width: 20%">Qty</th>
                    <th style="width: 20%">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse(($chargesData['charges'] ?? []) as $charge)
                <tr>
                    <td>{{ $charge->description ?? 'N/A' }}</td>
                    <td>{{ number_format((float) ($charge->quantity ?? 0), 0) }}</td>
                    <td>{{ number_format((float) ($charge->total_amount ?? 0), 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="text-align: center; color: #999;">No charges found</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="2">TOTAL AMOUNT DUE</td>
                    <td>PHP {{ number_format((float) ($chargesData['total_amount'] ?? 0), 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Notice -->
        <div class="notice">
            <strong>Important:</strong> Please present this Order of Payment to the Cashier for payment processing.
            Full payment is required before the service connection can be scheduled.
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line">
                    Prepared By
                </div>
            </div>
            <div class="signature-box">
                <div class="signature-line">
                    Customer's Signature
                </div>
            </div>
        </div>

        <!-- Print Date -->
        <div class="print-date">
            Printed on: {{ now()->format('F d, Y h:i A') }}
        </div>
    </div>

    <script>
        // Auto-print when loaded (optional - can be removed)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
