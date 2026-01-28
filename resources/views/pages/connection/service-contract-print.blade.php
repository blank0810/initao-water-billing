<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Water Service Contract - {{ $application->application_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #111827;
            background: #f9fafb;
            padding: 20px;
        }

        .contract {
            width: 100%;
            max-width: 750px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
        }

        /* Header */
        .header {
            text-align: center;
            padding: 20px 24px 16px;
            border-bottom: 1px solid #e5e7eb;
        }

        .header-logo {
            margin-bottom: 12px;
        }

        .header-logo img {
            height: 60px;
            width: auto;
        }

        .header .republic {
            font-size: 10px;
            color: #6b7280;
            margin-bottom: 2px;
        }

        .header h1 {
            font-size: 14px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 2px;
        }

        .header .subtitle {
            font-size: 10px;
            color: #6b7280;
        }

        /* Document Title */
        .doc-title {
            text-align: center;
            padding: 16px 24px;
            border-bottom: 1px solid #e5e7eb;
        }

        .doc-title h2 {
            font-size: 14px;
            font-weight: 600;
            color: #111827;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Content */
        .content {
            padding: 24px;
        }

        /* Concessioner Info */
        .concessioner-info {
            margin-bottom: 20px;
        }

        .info-row {
            display: flex;
            margin-bottom: 8px;
            align-items: baseline;
        }

        .info-row .label {
            color: #6b7280;
            font-size: 10px;
            width: 100px;
            flex-shrink: 0;
        }

        .info-row .value {
            flex: 1;
            border-bottom: 1px solid #d1d5db;
            padding-bottom: 2px;
            font-weight: 500;
            min-height: 16px;
        }

        /* Classification */
        .classification {
            margin-bottom: 20px;
            display: flex;
            gap: 24px;
            flex-wrap: wrap;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .checkbox {
            width: 14px;
            height: 14px;
            border: 1px solid #6b7280;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
        }

        .checkbox.checked::after {
            content: "✓";
        }

        .checkbox-label {
            font-size: 11px;
            color: #374151;
        }

        /* Meter and Service Info */
        .service-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 20px;
        }

        .service-info .info-row {
            margin-bottom: 0;
        }

        /* OR Fields */
        .or-fields {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 16px;
            margin-bottom: 24px;
            padding: 12px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
        }

        /* Clauses */
        .clauses {
            margin-bottom: 24px;
        }

        .clause {
            margin-bottom: 12px;
            text-align: justify;
        }

        .clause-number {
            font-weight: 600;
            color: #374151;
        }

        .clause-text {
            color: #374151;
        }

        /* Rate Schedule */
        .rate-schedule {
            margin-bottom: 24px;
        }

        .rate-schedule h3 {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #374151;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e5e7eb;
        }

        .rate-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #e5e7eb;
            font-size: 10px;
        }

        .rate-table th {
            background: #f9fafb;
            padding: 8px 10px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border: 1px solid #e5e7eb;
        }

        .rate-table td {
            padding: 6px 10px;
            border: 1px solid #e5e7eb;
        }

        .rate-table .rate-category {
            font-weight: 600;
            background: #f9fafb;
        }

        /* Signatures */
        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            margin-top: 40px;
        }

        .signature-box {
            text-align: center;
        }

        .signature-box .line {
            border-top: 1px solid #374151;
            margin-bottom: 6px;
            margin-top: 40px;
        }

        .signature-box .name {
            font-weight: 600;
            font-size: 11px;
            color: #111827;
            min-height: 16px;
        }

        .signature-box .title {
            font-size: 9px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        /* Addendum */
        .addendum {
            margin-top: 32px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }

        .addendum h3 {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #374151;
            margin-bottom: 12px;
        }

        .addendum p {
            text-align: justify;
            color: #374151;
            margin-bottom: 12px;
        }

        .addendum .signature-box {
            max-width: 250px;
            margin-top: 30px;
        }

        /* Footer */
        .footer {
            margin-top: 24px;
            padding: 12px 24px;
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
            text-align: center;
        }

        .footer p {
            font-size: 9px;
            color: #9ca3af;
            margin-bottom: 2px;
        }

        .footer .doc-id {
            font-family: 'SF Mono', 'Consolas', 'Liberation Mono', Menlo, monospace;
            font-size: 8px;
            color: #d1d5db;
        }

        /* Print Actions */
        .print-actions {
            max-width: 750px;
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
                font-size: 10px;
            }

            .contract {
                border: none;
                border-radius: 0;
                max-width: none;
                width: 100%;
            }

            .print-actions {
                display: none !important;
            }

            .checkbox.checked::after {
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
            ? strtoupper(trim(($application->customer->cust_first_name ?? '') . ' ' .
                   ($application->customer->cust_middle_name ?? '') . ' ' .
                   ($application->customer->cust_last_name ?? '')))
            : '';
        $fullAddress = $application->address
            ? trim(($application->address->purok?->p_desc ?? '') . ', ' .
                   ($application->address->barangay?->b_desc ?? '') . ', Initao, Misamis Oriental')
            : '';
        $accountType = $application->serviceConnection?->accountType?->at_desc ?? '';
        $meterSerial = $meterAssignment?->meter?->mtr_serial ?? '';
    @endphp

    <div class="contract">
        <!-- Header -->
        <div class="header">
            <div class="header-logo">
                <img src="{{ asset('images/logo.png') }}" alt="IMWS Logo">
            </div>
            <div class="republic">Republic of the Philippines</div>
            <h1>Municipality of Initao, Misamis Oriental</h1>
            <div class="subtitle">Initao Municipal Water System (IMWS)</div>
        </div>

        <!-- Document Title -->
        <div class="doc-title">
            <h2>Water Service Contract</h2>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Concessioner Information -->
            <div class="concessioner-info">
                <div class="info-row">
                    <span class="label">Concessioner:</span>
                    <span class="value">{{ $customerName }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Address:</span>
                    <span class="value">{{ $fullAddress }}</span>
                </div>
            </div>

            <!-- Classification -->
            <div class="classification">
                <div class="checkbox-item">
                    <div class="checkbox {{ strtolower($accountType) === 'residential' ? 'checked' : '' }}"></div>
                    <span class="checkbox-label">Residential</span>
                </div>
                <div class="checkbox-item">
                    <div class="checkbox {{ strtolower($accountType) === 'commercial' ? 'checked' : '' }}"></div>
                    <span class="checkbox-label">Commercial</span>
                </div>
                <div class="checkbox-item">
                    <div class="checkbox {{ strtolower($accountType) === 'industrial' ? 'checked' : '' }}"></div>
                    <span class="checkbox-label">Industrial</span>
                </div>
            </div>

            <!-- Meter and Service Info -->
            <div class="service-info">
                <div class="info-row">
                    <span class="label">Meter Serial No:</span>
                    <span class="value">{{ $meterAssignment?->meter?->mtr_serial ?? '' }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Meter Brand:</span>
                    <span class="value">{{ $meterAssignment?->meter?->mtr_brand ?? '' }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Kind of Service:</span>
                    <span class="value">New Connection</span>
                </div>
            </div>

            <!-- Payment/OR Fields -->
            @if($application->payment)
            <div class="or-fields" style="background: #ecfdf5; border-color: #a7f3d0;">
                <div class="info-row">
                    <span class="label">O.R. No:</span>
                    <span class="value">{{ $application->payment->receipt_no }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Date:</span>
                    <span class="value">{{ $application->payment->payment_date?->format('F d, Y') ?? $application->payment->created_at?->format('F d, Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Amount:</span>
                    <span class="value">₱{{ number_format($application->payment->amount, 2) }}</span>
                </div>
            </div>
            @else
            <div class="or-fields">
                <div class="info-row">
                    <span class="label">O.R. No:</span>
                    <span class="value"></span>
                </div>
                <div class="info-row">
                    <span class="label">Date:</span>
                    <span class="value"></span>
                </div>
                <div class="info-row">
                    <span class="label">Amount:</span>
                    <span class="value"></span>
                </div>
            </div>
            @endif

            <!-- Contract Clauses -->
            <div class="clauses">
                <div class="clause">
                    <span class="clause-number">1.</span>
                    <span class="clause-text">The concessioner agrees to pay for the installation of the water service connection from the main line to the premises, including the cost of materials and labor as determined by IMWS.</span>
                </div>
                <div class="clause">
                    <span class="clause-number">2.</span>
                    <span class="clause-text">The water meter and all appurtenances installed by IMWS remain the property of the Municipality of Initao. The concessioner shall be responsible for the safekeeping and protection of the meter and its accessories.</span>
                </div>
                <div class="clause">
                    <span class="clause-number">3.</span>
                    <span class="clause-text">The concessioner shall pay all water bills on or before the due date indicated in the billing statement. Failure to pay within the prescribed period shall subject the account to disconnection.</span>
                </div>
                <div class="clause">
                    <span class="clause-number">4.</span>
                    <span class="clause-text">Reconnection after disconnection due to non-payment shall require payment of all outstanding bills plus the reconnection fee as prescribed by IMWS.</span>
                </div>
                <div class="clause">
                    <span class="clause-number">5.</span>
                    <span class="clause-text">The concessioner shall not tamper with, bypass, or illegally connect to the water system. Any violation shall result in immediate disconnection and appropriate legal action.</span>
                </div>
                <div class="clause">
                    <span class="clause-number">6.</span>
                    <span class="clause-text">The concessioner shall allow authorized IMWS personnel to read the meter and inspect the water service connection at reasonable hours.</span>
                </div>
                <div class="clause">
                    <span class="clause-number">7.</span>
                    <span class="clause-text">Any extension of the service line within the premises shall require prior approval from IMWS and shall be at the expense of the concessioner.</span>
                </div>
                <div class="clause">
                    <span class="clause-number">8.</span>
                    <span class="clause-text">The concessioner shall immediately report any leakage, damage, or irregularity in the water service connection to IMWS.</span>
                </div>
                <div class="clause">
                    <span class="clause-number">9.</span>
                    <span class="clause-text">IMWS reserves the right to interrupt water service for repairs, maintenance, or emergency purposes without prior notice.</span>
                </div>
                <div class="clause">
                    <span class="clause-number">10.</span>
                    <span class="clause-text">The concessioner shall not resell water or allow unauthorized persons to use the water service connection.</span>
                </div>
                <div class="clause">
                    <span class="clause-number">11.</span>
                    <span class="clause-text">In case of change of ownership or tenancy, the new owner or tenant must apply for transfer of the water service account.</span>
                </div>
                <div class="clause">
                    <span class="clause-number">12.</span>
                    <span class="clause-text">The concessioner agrees to abide by all existing rules and regulations of IMWS and any amendments thereto.</span>
                </div>
                <div class="clause">
                    <span class="clause-number">13.</span>
                    <span class="clause-text">IMWS shall not be liable for any damage caused by water service interruption due to force majeure or circumstances beyond its control.</span>
                </div>
                <div class="clause">
                    <span class="clause-number">14.</span>
                    <span class="clause-text">This contract shall remain in effect until terminated by either party with proper notice and settlement of all outstanding obligations.</span>
                </div>
                <div class="clause">
                    <span class="clause-number">15.</span>
                    <span class="clause-text">Any dispute arising from this contract shall be settled through amicable means. If no settlement is reached, the matter shall be referred to the proper authorities for resolution.</span>
                </div>
            </div>

            <!-- Rate Schedule -->
            <div class="rate-schedule">
                <h3>Schedule of Rates - {{ $accountTypeName }}</h3>
                <table class="rate-table">
                    <thead>
                        <tr>
                            <th>Consumption (cu.m.)</th>
                            <th>Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rates as $rate)
                        <tr>
                            <td>{{ $rate->range_min }} - {{ $rate->range_max >= 999999 ? 'above' : $rate->range_max }}</td>
                            <td>
                                @if($rate->range_id == 1)
                                    ₱{{ number_format($rate->rate_val, 2) }} (Minimum)
                                @else
                                    ₱{{ number_format($rate->rate_val, 2) }} / cu.m.
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" style="text-align: center; color: #6b7280; font-style: italic;">
                                No rates configured for this account type
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <p style="font-size: 9px; color: #6b7280; margin-top: 8px; font-style: italic;">
                    * Rates are subject to change based on Municipal Ordinance
                </p>
            </div>

            <!-- Signatures -->
            <div class="signatures">
                <div class="signature-box">
                    <div class="line"></div>
                    <div class="name">{{ $customerName }}</div>
                    <div class="title">Concessioner</div>
                </div>
                <div class="signature-box">
                    <div class="line"></div>
                    <div class="name">{{ $application->processedBy?->name ?? '' }}</div>
                    <div class="title">MEEDO Representative</div>
                </div>
                <div class="signature-box">
                    <div class="line"></div>
                    <div class="name"></div>
                    <div class="title">Property Owner</div>
                </div>
            </div>

            <!-- Addendum -->
            <div class="addendum">
                <h3>Addendum: Property Owner's Guarantee</h3>
                <p>
                    I, the undersigned property owner, hereby guarantee the payment of all water bills and other charges incurred by the above-named concessioner in connection with the water service installed in my property. In case of default by the concessioner, I agree to assume full responsibility for the settlement of all outstanding obligations.
                </p>
                <div class="signature-box">
                    <div class="line"></div>
                    <div class="name"></div>
                    <div class="title">Property Owner's Signature Over Printed Name</div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Initao Municipal Water System | Municipal Hall Compound, Poblacion, Initao, Misamis Oriental</p>
            <div class="doc-id">Contract Ref: {{ $application->application_number }} | Generated: {{ now()->format('M d, Y H:i') }}</div>
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
