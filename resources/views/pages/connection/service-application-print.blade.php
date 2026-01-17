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
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #111827;
            background: #f9fafb;
            padding: 20px;
        }

        .application {
            width: 100%;
            max-width: 650px;
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
            font-size: 16px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }

        .header .subtitle {
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 2px;
        }

        .header .address {
            font-size: 10px;
            color: #9ca3af;
        }

        /* Document Title */
        .doc-title {
            padding: 16px 24px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .doc-title h2 {
            font-size: 14px;
            font-weight: 600;
            color: #111827;
        }

        .doc-title .app-no {
            font-family: 'SF Mono', 'Consolas', 'Liberation Mono', Menlo, monospace;
            font-size: 12px;
            font-weight: 500;
            color: #6b7280;
        }

        /* Content */
        .content {
            padding: 24px;
        }

        /* Section */
        .section {
            margin-bottom: 24px;
        }

        .section:last-child {
            margin-bottom: 0;
        }

        .section-title {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #374151;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e5e7eb;
        }

        /* Info Table */
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 8px 0;
            vertical-align: top;
            border-bottom: 1px solid #f3f4f6;
        }

        .info-table tr:last-child td {
            border-bottom: none;
        }

        .info-table .label {
            color: #6b7280;
            width: 40%;
            font-size: 11px;
        }

        .info-table .value {
            font-weight: 500;
            color: #111827;
            text-align: right;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .status-pending { background: #fef3c7; color: #92400e; }
        .status-verified { background: #dbeafe; color: #1e40af; }
        .status-paid { background: #d1fae5; color: #065f46; }
        .status-scheduled { background: #e0e7ff; color: #3730a3; }
        .status-connected { background: #d1fae5; color: #065f46; }
        .status-rejected { background: #fee2e2; color: #991b1b; }
        .status-cancelled { background: #f3f4f6; color: #374151; }

        /* Charges Table */
        .charges-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
        }

        .charges-table th {
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            color: #6b7280;
            text-align: left;
            padding: 10px 12px;
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
        }

        .charges-table th:last-child {
            text-align: right;
        }

        .charges-table td {
            font-size: 12px;
            padding: 12px;
            border-bottom: 1px solid #f3f4f6;
        }

        .charges-table tbody tr:last-child td {
            border-bottom: none;
        }

        .charges-table td:last-child {
            text-align: right;
            font-family: 'SF Mono', 'Consolas', 'Liberation Mono', Menlo, monospace;
            font-size: 11px;
        }

        .charges-table tfoot td {
            font-weight: 600;
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
            padding: 12px;
        }

        /* Payment Status */
        .payment-status {
            margin-top: 16px;
            padding: 12px 16px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .payment-status.paid {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
        }

        .payment-status.paid .status-label {
            color: #065f46;
            font-weight: 600;
        }

        .payment-status.paid .status-detail {
            color: #047857;
            font-size: 11px;
        }

        .payment-status.pending {
            background: #fffbeb;
            border: 1px solid #fde68a;
        }

        .payment-status.pending .status-label {
            color: #92400e;
            font-weight: 600;
        }

        .payment-status.pending .status-detail {
            color: #b45309;
            font-size: 11px;
        }

        /* Signatures */
        .signatures {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            gap: 24px;
        }

        .signature-box {
            flex: 1;
            text-align: center;
        }

        .signature-box .line {
            border-top: 1px solid #374151;
            margin-bottom: 8px;
            margin-top: 48px;
        }

        .signature-box .label {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        /* Footer */
        .footer {
            margin-top: 32px;
            padding: 16px 24px;
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
            text-align: center;
        }

        .footer p {
            font-size: 10px;
            color: #9ca3af;
            margin-bottom: 4px;
        }

        .footer .doc-id {
            font-family: 'SF Mono', 'Consolas', 'Liberation Mono', Menlo, monospace;
            font-size: 9px;
            color: #d1d5db;
        }

        /* Print Actions */
        .print-actions {
            max-width: 650px;
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
            background: #111827;
            color: #fff;
        }

        .btn-primary:hover {
            background: #374151;
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

            .application {
                border: none;
                border-radius: 0;
                max-width: none;
                width: 100%;
            }

            .print-actions {
                display: none !important;
            }

            .status-badge, .payment-status {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        @page {
            size: Letter portrait;
            margin: 20mm;
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
            <div class="header-logo">
                <img src="{{ asset('images/logo.png') }}" alt="IMWS Logo">
            </div>
            <h1>Initao Municipal Water System</h1>
            <div class="subtitle">Municipal Government of Initao</div>
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
                <table class="info-table">
                    <tr>
                        <td class="label">Application Number</td>
                        <td class="value">{{ $application->application_number }}</td>
                    </tr>
                    <tr>
                        <td class="label">Date Submitted</td>
                        <td class="value">{{ $application->submitted_at ? $application->submitted_at->format('F d, Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Status</td>
                        <td class="value">
                            <span class="status-badge {{ $statusClass }}">
                                {{ $application->status?->stat_desc ?? 'PENDING' }}
                            </span>
                        </td>
                    </tr>
                    @if($application->scheduled_connection_date)
                    <tr>
                        <td class="label">Scheduled Connection</td>
                        <td class="value">{{ $application->scheduled_connection_date->format('F d, Y') }}</td>
                    </tr>
                    @endif
                    @if($application->connected_at)
                    <tr>
                        <td class="label">Connected On</td>
                        <td class="value">{{ $application->connected_at->format('F d, Y') }}</td>
                    </tr>
                    @endif
                </table>
            </div>

            <!-- Customer Information -->
            <div class="section">
                <div class="section-title">Customer Information</div>
                <table class="info-table">
                    <tr>
                        <td class="label">Full Name</td>
                        <td class="value">{{ $customerName }}</td>
                    </tr>
                    <tr>
                        <td class="label">Resolution Number</td>
                        <td class="value">{{ $application->customer?->resolution_no ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Contact Number</td>
                        <td class="value">{{ $application->customer?->contact_number ?? '-' }}</td>
                    </tr>
                    @if($application->customer?->id_type)
                    <tr>
                        <td class="label">ID Type</td>
                        <td class="value">{{ $application->customer->id_type }}</td>
                    </tr>
                    <tr>
                        <td class="label">ID Number</td>
                        <td class="value">{{ $application->customer->id_number ?? '-' }}</td>
                    </tr>
                    @endif
                </table>
            </div>

            <!-- Service Location -->
            <div class="section">
                <div class="section-title">Service Location</div>
                <table class="info-table">
                    <tr>
                        <td class="label">Purok</td>
                        <td class="value">{{ $application->address?->purok?->p_desc ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Barangay</td>
                        <td class="value">{{ $application->address?->barangay?->b_desc ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Municipality</td>
                        <td class="value">Initao, Misamis Oriental</td>
                    </tr>
                    <tr>
                        <td class="label">Full Address</td>
                        <td class="value">{{ $fullAddress }}</td>
                    </tr>
                </table>
            </div>

            <!-- Application Charges -->
            @if($chargesData['charges']->count() > 0)
            <div class="section">
                <div class="section-title">Application Charges</div>
                <table class="charges-table">
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
                            <td>₱{{ number_format($charge->total_amount, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>Total Amount</td>
                            <td>₱{{ number_format($chargesData['total_amount'], 2) }}</td>
                        </tr>
                    </tfoot>
                </table>

                @if($chargesData['is_fully_paid'])
                <div class="payment-status paid">
                    <span class="status-label">FULLY PAID</span>
                    @if($application->payment)
                    <span class="status-detail">Receipt: {{ $application->payment->receipt_no }}</span>
                    @endif
                </div>
                @else
                <div class="payment-status pending">
                    <span class="status-label">PAYMENT PENDING</span>
                    <span class="status-detail">Balance: ₱{{ number_format($chargesData['remaining_amount'], 2) }}</span>
                </div>
                @endif
            </div>
            @endif

            <!-- Remarks -->
            @if($application->remarks)
            <div class="section">
                <div class="section-title">Remarks</div>
                <p style="color: #374151;">{{ $application->remarks }}</p>
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
                    @if($application->processedBy)
                    <div class="name" style="font-weight: 500; margin-bottom: 4px;">{{ $application->processedBy->name }}</div>
                    @endif
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
            <p>This is an official document of Initao Municipal Water System</p>
            <p>For inquiries: (088) 123-4567 | Mon-Fri 8:00 AM - 5:00 PM</p>
            <div class="doc-id">{{ strtoupper(substr(md5($application->application_number . $application->application_id), 0, 12)) }} | Generated: {{ now()->format('M d, Y H:i') }}</div>
        </div>
    </div>

    <!-- Print Actions -->
    <div class="print-actions">
        <button onclick="window.print()" class="btn btn-primary">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            Print
        </button>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back
        </a>
    </div>
</body>
</html>
