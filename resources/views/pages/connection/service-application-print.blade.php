<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Application - {{ $application->application_number }}</title>
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

        .application {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #ddd;
        }

        /* Header */
        .header {
            text-align: center;
            padding: 15px;
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

        /* Document Title */
        .doc-title {
            text-align: center;
            padding: 10px 15px;
            background: #1e3a5f;
            color: #fff;
        }

        .doc-title h2 {
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .doc-title .app-no {
            font-family: 'Courier New', monospace;
            font-size: 14px;
            font-weight: 700;
            margin-top: 4px;
        }

        /* Content */
        .content {
            padding: 15px;
        }

        /* Section */
        .section {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .section:last-child {
            border-bottom: none;
        }

        .section-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #1e3a5f;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #1e3a5f;
        }

        /* Info Row */
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
            font-size: 11px;
        }

        .info-row .label {
            color: #666;
            flex: 0 0 40%;
        }

        .info-row .value {
            font-weight: 600;
            color: #333;
            text-align: right;
            flex: 0 0 55%;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        .items-table th {
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            color: #666;
            text-align: left;
            padding: 6px 0;
            border-bottom: 1px solid #ddd;
        }

        .items-table th:last-child {
            text-align: right;
        }

        .items-table td {
            font-size: 11px;
            padding: 8px 0;
            border-bottom: 1px dotted #eee;
        }

        .items-table td:last-child {
            text-align: right;
            font-family: 'Courier New', monospace;
        }

        .items-table tfoot td {
            font-weight: 700;
            border-top: 2px solid #1e3a5f;
            padding-top: 10px;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .status-pending { background: #fef3c7; color: #92400e; }
        .status-verified { background: #dbeafe; color: #1e40af; }
        .status-paid { background: #d1fae5; color: #065f46; }
        .status-scheduled { background: #e0e7ff; color: #3730a3; }
        .status-connected { background: #d1fae5; color: #065f46; }
        .status-rejected { background: #fee2e2; color: #991b1b; }
        .status-cancelled { background: #f3f4f6; color: #374151; }

        /* Payment Status */
        .payment-status {
            margin-top: 10px;
            padding: 8px;
            border-radius: 4px;
            text-align: center;
        }

        .payment-status.paid {
            background: #d1fae5;
            color: #065f46;
        }

        .payment-status.pending {
            background: #fef3c7;
            color: #92400e;
        }

        /* Signature Section */
        .signatures {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            gap: 30px;
        }

        .signature-box {
            flex: 1;
            text-align: center;
        }

        .signature-box .line {
            border-top: 1px solid #333;
            margin-bottom: 5px;
            margin-top: 40px;
        }

        .signature-box .label {
            font-size: 9px;
            color: #666;
            text-transform: uppercase;
        }

        /* Footer */
        .footer {
            margin-top: 20px;
            padding: 10px 15px;
            background: #f8f9fa;
            border-top: 1px solid #ddd;
            text-align: center;
        }

        .footer p {
            font-size: 9px;
            color: #888;
            margin-bottom: 3px;
        }

        .footer .doc-id {
            font-family: 'Courier New', monospace;
            font-size: 8px;
            color: #aaa;
        }

        /* Print Actions */
        .print-actions {
            max-width: 600px;
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

        .btn-primary:hover {
            background: #2d4a6f;
        }

        .btn-secondary {
            background: #e5e7eb;
            color: #374151;
        }

        .btn-secondary:hover {
            background: #d1d5db;
        }

        /* Print Styles */
        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .application {
                border: none;
                max-width: none;
                width: 100%;
            }

            .print-actions {
                display: none !important;
            }

            .header, .doc-title, .footer, .status-badge, .payment-status {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        @page {
            size: Letter portrait;
            margin: 15mm;
        }
    </style>
</head>
<body>
    @php
        $customerName = $application->customer
            ? trim(($application->customer->cust_first_name ?? '') . ' ' .
                   ($application->customer->cust_middle_name ? $application->customer->cust_middle_name[0] . '. ' : '') .
                   ($application->customer->cust_last_name ?? ''))
            : '-';
        $fullAddress = $application->address
            ? trim(($application->address->purok?->p_desc ?? '') . ', ' .
                   ($application->address->barangay?->b_desc ?? '') . ', Initao, Misamis Oriental')
            : '-';
        $statusDesc = strtolower($application->status?->stat_desc ?? 'pending');
        $statusClass = 'status-' . $statusDesc;
    @endphp

    <div class="application">
        <!-- Header -->
        <div class="header">
            <h1>Initao Water District</h1>
            <div class="subtitle">Municipal Water Utility</div>
            <div class="address">Municipal Hall Compound, Poblacion, Initao, Misamis Oriental 9022</div>
        </div>

        <!-- Document Title -->
        <div class="doc-title">
            <h2>Service Application Form</h2>
            <div class="app-no">{{ $application->application_number }}</div>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Application Info -->
            <div class="section">
                <div class="section-title">Application Information</div>
                <div class="info-row">
                    <span class="label">Application Number:</span>
                    <span class="value">{{ $application->application_number }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Date Submitted:</span>
                    <span class="value">{{ $application->submitted_at ? $application->submitted_at->format('F d, Y h:i A') : '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Status:</span>
                    <span class="value">
                        <span class="status-badge {{ $statusClass }}">
                            {{ $application->status?->stat_desc ?? 'PENDING' }}
                        </span>
                    </span>
                </div>
                @if($application->scheduled_connection_date)
                <div class="info-row">
                    <span class="label">Scheduled Connection:</span>
                    <span class="value">{{ $application->scheduled_connection_date->format('F d, Y') }}</span>
                </div>
                @endif
                @if($application->connected_at)
                <div class="info-row">
                    <span class="label">Connected On:</span>
                    <span class="value">{{ $application->connected_at->format('F d, Y h:i A') }}</span>
                </div>
                @endif
            </div>

            <!-- Customer Information -->
            <div class="section">
                <div class="section-title">Customer Information</div>
                <div class="info-row">
                    <span class="label">Full Name:</span>
                    <span class="value">{{ $customerName }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Resolution Number:</span>
                    <span class="value">{{ $application->customer?->resolution_no ?? '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Contact Number:</span>
                    <span class="value">{{ $application->customer?->contact_number ?? '-' }}</span>
                </div>
                @if($application->customer?->id_type)
                <div class="info-row">
                    <span class="label">ID Type:</span>
                    <span class="value">{{ $application->customer->id_type }}</span>
                </div>
                <div class="info-row">
                    <span class="label">ID Number:</span>
                    <span class="value">{{ $application->customer->id_number ?? '-' }}</span>
                </div>
                @endif
            </div>

            <!-- Service Location -->
            <div class="section">
                <div class="section-title">Service Location</div>
                <div class="info-row">
                    <span class="label">Purok:</span>
                    <span class="value">{{ $application->address?->purok?->p_desc ?? '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Barangay:</span>
                    <span class="value">{{ $application->address?->barangay?->b_desc ?? '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Municipality:</span>
                    <span class="value">Initao, Misamis Oriental</span>
                </div>
                <div class="info-row">
                    <span class="label">Full Address:</span>
                    <span class="value">{{ $fullAddress }}</span>
                </div>
            </div>

            <!-- Application Charges -->
            @if($chargesData['charges']->count() > 0)
            <div class="section">
                <div class="section-title">Application Charges</div>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($chargesData['charges'] as $charge)
                        <tr>
                            <td>{{ $charge->description }}</td>
                            <td>PHP {{ number_format($charge->total_amount, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>Total Amount</td>
                            <td>PHP {{ number_format($chargesData['total_amount'], 2) }}</td>
                        </tr>
                    </tfoot>
                </table>

                @if($chargesData['is_fully_paid'])
                <div class="payment-status paid">
                    <strong>FULLY PAID</strong>
                    @if($application->payment)
                    <span style="margin-left: 10px; font-size: 10px;">
                        Receipt: {{ $application->payment->receipt_no }}
                    </span>
                    @endif
                </div>
                @else
                <div class="payment-status pending">
                    <strong>PAYMENT PENDING</strong>
                    <span style="margin-left: 10px; font-size: 10px;">
                        Balance: PHP {{ number_format($chargesData['remaining_amount'], 2) }}
                    </span>
                </div>
                @endif
            </div>
            @endif

            <!-- Remarks -->
            @if($application->remarks)
            <div class="section">
                <div class="section-title">Remarks</div>
                <p style="font-size: 11px; color: #333;">{{ $application->remarks }}</p>
            </div>
            @endif

            <!-- Signatures -->
            <div class="signatures">
                <div class="signature-box">
                    <div class="line"></div>
                    <div class="label">Applicant's Signature</div>
                </div>
                <div class="signature-box">
                    <div class="line"></div>
                    <div class="label">Receiving Officer</div>
                </div>
                <div class="signature-box">
                    <div class="line"></div>
                    <div class="label">Approving Authority</div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>This is an official document of Initao Water District</p>
            <p>For inquiries: (088) 123-4567 | Mon-Fri 8AM-5PM</p>
            <div class="doc-id">{{ strtoupper(substr(md5($application->application_number . $application->application_id), 0, 12)) }} | Generated: {{ now()->format('m/d/Y H:i') }}</div>
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
        <a href="{{ url()->previous() }}" class="btn btn-secondary">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back
        </a>
    </div>
</body>
</html>
