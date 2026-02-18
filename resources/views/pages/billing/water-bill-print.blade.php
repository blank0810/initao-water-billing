<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing Statement - {{ $data['connection']['account_no'] }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #1a1a1a;
            background: #f0f0f0;
            padding: 20px;
        }

        .statement {
            width: 100%;
            max-width: 650px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #d0d0d0;
        }

        /* ===== HEADER ===== */
        .header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 20px;
            border-bottom: 3px solid #2563eb;
            background: linear-gradient(135deg, #f8faff 0%, #eef2ff 100%);
        }

        .header-logo {
            width: 56px;
            height: 56px;
            flex-shrink: 0;
        }

        .header-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .header-info {
            flex: 1;
        }

        .header-info .org-name {
            font-size: 14px;
            font-weight: 700;
            color: #1e3a5f;
            letter-spacing: 0.3px;
        }

        .header-info .org-sub {
            font-size: 10px;
            color: #4b5563;
        }

        .header-right {
            text-align: right;
        }

        .header-right .doc-label {
            font-size: 13px;
            font-weight: 700;
            color: #2563eb;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header-right .bill-no {
            font-size: 9px;
            color: #6b7280;
            margin-top: 2px;
        }

        /* ===== CUSTOMER INFO BAR ===== */
        .customer-bar {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            padding: 14px 20px;
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
        }

        .customer-bar .field {
            display: flex;
            gap: 6px;
        }

        .customer-bar .label {
            font-size: 9px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            white-space: nowrap;
        }

        .customer-bar .value {
            font-size: 11px;
            font-weight: 600;
            color: #111827;
        }

        /* ===== INFO GRID (Bill at a Glance) ===== */
        .info-strip {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            border-bottom: 1px solid #e5e7eb;
        }

        .info-strip .cell {
            padding: 10px 8px;
            text-align: center;
            border-right: 1px solid #e5e7eb;
        }

        .info-strip .cell:last-child {
            border-right: none;
        }

        .info-strip .cell-label {
            font-size: 8px;
            font-weight: 600;
            text-transform: uppercase;
            color: #6b7280;
            letter-spacing: 0.3px;
            margin-bottom: 4px;
        }

        .info-strip .cell-value {
            font-size: 11px;
            font-weight: 600;
            color: #111827;
        }

        /* ===== CONTENT AREA ===== */
        .content {
            padding: 16px 20px;
        }

        .section-title {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            color: #2563eb;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 1px solid #e5e7eb;
        }

        /* ===== BILL TABLE ===== */
        .bill-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        .bill-table td {
            padding: 7px 10px;
            font-size: 11px;
            border-bottom: 1px solid #f3f4f6;
        }

        .bill-table td:last-child {
            text-align: right;
            font-family: 'SF Mono', 'Consolas', 'Courier New', monospace;
            font-size: 10px;
            white-space: nowrap;
        }

        .bill-table tr.sub-item td {
            padding-left: 24px;
            color: #6b7280;
            font-size: 10px;
        }

        .bill-table tr.separator td {
            border-bottom: 1px solid #d1d5db;
        }

        .bill-table tr.subtotal td {
            font-weight: 600;
            border-top: 1px solid #d1d5db;
            background: #f9fafb;
        }

        .bill-table tr.total td {
            font-weight: 700;
            font-size: 12px;
            border-top: 2px solid #2563eb;
            background: #eef2ff;
            padding: 10px;
        }

        .bill-table tr.total td:last-child {
            color: #2563eb;
            font-size: 13px;
        }

        /* ===== METER CHANGE BANNER ===== */
        .meter-change-banner {
            margin-bottom: 12px;
            padding: 10px 12px;
            background: #fff7ed;
            border: 1px solid #fed7aa;
            border-radius: 4px;
        }

        .meter-change-banner .banner-title {
            font-size: 10px;
            font-weight: 700;
            color: #9a3412;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .meter-change-banner .banner-detail {
            font-size: 10px;
            color: #78350f;
        }

        .meter-change-banner .banner-detail span {
            font-weight: 600;
            font-family: 'SF Mono', 'Consolas', monospace;
        }

        /* ===== CONSUMPTION HISTORY ===== */
        .history-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
            gap: 6px;
            margin-bottom: 16px;
        }

        .history-bar {
            text-align: center;
            padding: 8px 4px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
        }

        .history-bar .period-label {
            font-size: 8px;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .history-bar .consumption-value {
            font-size: 11px;
            font-weight: 700;
            color: #111827;
        }

        .history-bar .amount-value {
            font-size: 9px;
            color: #6b7280;
            margin-top: 2px;
        }

        .history-bar.current {
            background: #eef2ff;
            border-color: #2563eb;
        }

        /* ===== NOTICE ===== */
        .notice {
            padding: 10px 12px;
            background: #fefce8;
            border: 1px solid #fde047;
            border-radius: 4px;
            margin-bottom: 12px;
        }

        .notice p {
            font-size: 9px;
            color: #713f12;
            text-align: center;
            line-height: 1.5;
        }

        /* ===== FOOTER ===== */
        .footer {
            padding: 10px 20px;
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
            text-align: center;
        }

        .footer p {
            font-size: 8px;
            color: #9ca3af;
        }

        /* ===== TEAR LINE ===== */
        .tear-line {
            border-top: 2px dashed #9ca3af;
            margin: 0;
            position: relative;
        }

        .tear-line::before {
            content: '✂';
            position: absolute;
            top: -8px;
            left: 10px;
            font-size: 12px;
            color: #9ca3af;
            background: white;
            padding: 0 4px;
        }

        /* ===== PAYMENT STUB ===== */
        .payment-stub {
            padding: 12px 20px;
        }

        .stub-title {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            color: #6b7280;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .stub-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 12px;
        }

        .stub-field .stub-label {
            font-size: 8px;
            color: #6b7280;
            text-transform: uppercase;
        }

        .stub-field .stub-value {
            font-size: 11px;
            font-weight: 600;
            color: #111827;
        }

        .stub-field .stub-value.amount {
            font-size: 14px;
            font-weight: 700;
            color: #2563eb;
        }

        /* ===== PRINT ACTIONS ===== */
        .print-actions {
            max-width: 650px;
            margin: 16px auto 0;
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.15s;
        }

        .btn-primary {
            background: #2563eb;
            color: #fff;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        .btn-secondary {
            background: #fff;
            color: #374151;
            border: 1px solid #d1d5db;
        }

        .btn-secondary:hover {
            background: #f9fafb;
        }

        /* ===== PRINT STYLES ===== */
        @media print {
            body {
                background: #fff;
                padding: 0;
                margin: 0;
            }

            .statement {
                border: none;
                max-width: none;
                width: 100%;
            }

            .header {
                background: #f8faff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .info-strip .cell,
            .bill-table tr.subtotal td,
            .bill-table tr.total td,
            .customer-bar,
            .history-bar,
            .history-bar.current,
            .meter-change-banner,
            .notice,
            .footer,
            .payment-stub {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .print-actions,
            .back-link {
                display: none !important;
            }
        }
    </style>
    @vite(['resources/css/app.css'])
</head>
<body>
    @php
        $bill = $data['bill'];
        $readings = $data['readings'];
        $conn = $data['connection'];
        $cust = $data['customer'];
        $period = $data['period'];
        $oldMeter = $data['old_meter'];
        $arrears = $data['arrears'];
        $adjustments = $data['adjustments'];
        $history = $data['consumption_history'];
        $totalDue = $data['total_amount_due'];
    @endphp

    <!-- Back Link -->
    <div class="back-link no-print" style="max-width: 650px; margin: 0 auto 12px;">
        <a href="javascript:history.back()" style="text-decoration: none; color: #374151; font-size: 13px; display: flex; align-items: center; gap: 6px;">
            <i class="fas fa-chevron-left"></i> Back
        </a>
    </div>

    <div class="statement">
        <!-- ===== HEADER ===== -->
        <div class="header">
            <div class="header-logo">
                <img src="{{ asset('images/logo.png') }}" alt="Logo">
            </div>
            <div class="header-info">
                <div class="org-name">Initao Municipal Water System</div>
                <div class="org-sub">Municipal Economic Enterprise and Development Office</div>
                <div class="org-sub">Municipality of Initao, Misamis Oriental</div>
            </div>
            <div class="header-right">
                <div class="doc-label">Billing Statement</div>
                <div class="bill-no">Bill No. {{ str_pad($bill['bill_id'], 6, '0', STR_PAD_LEFT) }}</div>
            </div>
        </div>

        <!-- ===== CUSTOMER INFO BAR ===== -->
        <div class="customer-bar">
            <div>
                <div class="field">
                    <span class="label">Name:</span>
                    <span class="value">{{ $cust['name'] }}</span>
                </div>
                <div class="field" style="margin-top: 4px;">
                    <span class="label">Address:</span>
                    <span class="value">{{ $cust['address'] }}</span>
                </div>
            </div>
            <div style="text-align: right;">
                <div class="field" style="justify-content: flex-end;">
                    <span class="label">Account No:</span>
                    <span class="value">{{ $conn['account_no'] }}</span>
                </div>
                <div class="field" style="margin-top: 4px; justify-content: flex-end;">
                    <span class="label">Meter No:</span>
                    <span class="value">{{ $conn['meter_serial'] }}</span>
                </div>
            </div>
        </div>

        <!-- ===== INFO STRIP (Bill at a Glance) ===== -->
        <div class="info-strip">
            <div class="cell">
                <div class="cell-label">Account Type</div>
                <div class="cell-value">{{ $conn['account_type'] }}</div>
            </div>
            <div class="cell">
                <div class="cell-label">Billing Period</div>
                <div class="cell-value">{{ $period['name'] }}</div>
            </div>
            <div class="cell">
                <div class="cell-label">Reading Date</div>
                <div class="cell-value">{{ $readings['reading_date'] ? \Carbon\Carbon::parse($readings['reading_date'])->format('m/d/Y') : 'N/A' }}</div>
            </div>
            <div class="cell">
                <div class="cell-label">Due Date</div>
                <div class="cell-value">{{ $bill['due_date'] ? \Carbon\Carbon::parse($bill['due_date'])->format('m/d/Y') : 'N/A' }}</div>
            </div>
            <div class="cell">
                <div class="cell-label">Area</div>
                <div class="cell-value">{{ $conn['area'] }}</div>
            </div>
        </div>

        <!-- ===== CONTENT ===== -->
        <div class="content">

            {{-- Meter Change Banner --}}
            @if($bill['is_meter_change'] && $oldMeter)
                <div class="meter-change-banner">
                    <div class="banner-title"><i class="fas fa-exchange-alt" style="margin-right: 4px;"></i> Meter Replacement Notice</div>
                    <div class="banner-detail">
                        Old meter (<span>{{ $oldMeter['serial'] }}</span>) removed at reading <span>{{ number_format($oldMeter['removal_read'], 3) }}</span> m&sup3;
                        &mdash; Old meter consumption: <span>{{ number_format($bill['old_meter_consumption'], 3) }}</span> m&sup3;
                        | New meter consumption: <span>{{ number_format($bill['new_meter_consumption'], 3) }}</span> m&sup3;
                    </div>
                </div>
            @endif

            <!-- BILL DETAILS -->
            <div class="section-title">Bill Details</div>
            <table class="bill-table">
                <tr>
                    <td>Present Meter Reading</td>
                    <td>{{ number_format($readings['current'], 3) }}</td>
                </tr>
                <tr>
                    <td>Previous Meter Reading</td>
                    <td>{{ number_format($readings['previous'], 3) }}</td>
                </tr>
                @if($bill['is_meter_change'])
                    <tr class="sub-item">
                        <td>New Meter Consumption</td>
                        <td>{{ number_format($bill['new_meter_consumption'], 3) }} cu.m.</td>
                    </tr>
                    <tr class="sub-item">
                        <td>Old Meter Consumption</td>
                        <td>{{ number_format($bill['old_meter_consumption'], 3) }} cu.m.</td>
                    </tr>
                @endif
                <tr class="separator">
                    <td>Total Consumption</td>
                    <td><strong>{{ number_format($bill['consumption'], 3) }} cu.m.</strong></td>
                </tr>
                <tr>
                    <td>Water Bill This Month</td>
                    <td>&#8369; {{ number_format($bill['water_amount'], 2) }}</td>
                </tr>
                @if(count($adjustments) > 0)
                    @foreach($adjustments as $adj)
                        <tr class="sub-item">
                            <td>{{ $adj['type'] }}</td>
                            <td>{{ $adj['amount'] >= 0 ? '' : '(' }}&#8369; {{ number_format(abs($adj['amount']), 2) }}{{ $adj['amount'] >= 0 ? '' : ')' }}</td>
                        </tr>
                    @endforeach
                @endif
                @if($bill['adjustment_total'] != 0)
                    <tr>
                        <td>Adjustments</td>
                        <td>{{ $bill['adjustment_total'] >= 0 ? '' : '(' }}&#8369; {{ number_format(abs($bill['adjustment_total']), 2) }}{{ $bill['adjustment_total'] >= 0 ? '' : ')' }}</td>
                    </tr>
                @endif
                <tr class="subtotal">
                    <td>Current Bill Amount</td>
                    <td>&#8369; {{ number_format($bill['total_amount'], 2) }}</td>
                </tr>
                @if($arrears > 0)
                    <tr>
                        <td>Arrears (Previous Unpaid Balance)</td>
                        <td>&#8369; {{ number_format($arrears, 2) }}</td>
                    </tr>
                @endif
                <tr class="total">
                    <td>TOTAL AMOUNT DUE</td>
                    <td>&#8369; {{ number_format($totalDue, 2) }}</td>
                </tr>
            </table>

            {{-- CONSUMPTION HISTORY --}}
            @if(count($history) > 1)
                <div class="section-title">Consumption History</div>
                <div class="history-grid">
                    @foreach($history as $index => $h)
                        <div class="history-bar {{ $index === count($history) - 1 ? 'current' : '' }}">
                            <div class="period-label">{{ $h['period'] }}</div>
                            <div class="consumption-value">{{ number_format($h['consumption'], 1) }}</div>
                            <div class="amount-value">&#8369;{{ number_format($h['amount'], 2) }}</div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- NOTICE -->
            <div class="notice">
                <p><strong>NOTICE:</strong> Please settle your account on or before the due date. A penalty of 10% of the principal amount will be charged for late payments. Failure to pay may result in disconnection of water service without further notice.</p>
            </div>
        </div>

        <!-- ===== FOOTER ===== -->
        <div class="footer">
            <p>This is a system-generated billing statement. No signature required.</p>
            <p>Generated on {{ now()->format('F d, Y h:i A') }} &bull; Initao Municipal Water System</p>
        </div>

        <!-- ===== TEAR LINE ===== -->
        <div class="tear-line"></div>

        <!-- ===== PAYMENT STUB ===== -->
        <div class="payment-stub">
            <div class="stub-title">Payment Stub &mdash; Present this when paying</div>
            <div class="stub-grid">
                <div class="stub-field">
                    <div class="stub-label">Account No</div>
                    <div class="stub-value">{{ $conn['account_no'] }}</div>
                </div>
                <div class="stub-field">
                    <div class="stub-label">Name</div>
                    <div class="stub-value">{{ $cust['name'] }}</div>
                </div>
                <div class="stub-field">
                    <div class="stub-label">Billing Period</div>
                    <div class="stub-value">{{ $period['name'] }}</div>
                </div>
                <div class="stub-field">
                    <div class="stub-label">Due Date</div>
                    <div class="stub-value">{{ $bill['due_date'] ? \Carbon\Carbon::parse($bill['due_date'])->format('M d, Y') : 'N/A' }}</div>
                </div>
                <div class="stub-field">
                    <div class="stub-label">Current Bill</div>
                    <div class="stub-value">&#8369; {{ number_format($bill['total_amount'], 2) }}</div>
                </div>
                <div class="stub-field">
                    <div class="stub-label">Total Amount Due</div>
                    <div class="stub-value amount">&#8369; {{ number_format($totalDue, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== PRINT ACTIONS ===== -->
    <div class="print-actions no-print">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> Print Statement
        </button>
        <a href="javascript:history.back()" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Go Back
        </a>
    </div>

    <script>
        // Auto-print when opened
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
