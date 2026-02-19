<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consumer Master List</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            font-size: 9px;
            line-height: 1.5;
            color: #111827;
            background: #f9fafb;
            padding: 20px;
        }

        .document {
            width: 100%;
            max-width: 1100px;
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

        /* Summary Stats */
        /* Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th {
            background: #f9fafb;
            padding: 8px 6px;
            text-align: center;
            font-weight: 600;
            color: #374151;
            border: 1px solid #e5e7eb;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .data-table td {
            padding: 6px;
            border: 1px solid #e5e7eb;
            font-size: 9px;
        }

        .data-table tbody tr:hover {
            background: #f9fafb;
        }

        .text-right {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }

        .text-left {
            text-align: left !important;
        }

        /* Status badges */
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: 600;
        }

        .status-active {
            background: #dcfce7;
            color: #166534;
        }

        .status-inactive {
            background: #f3f4f6;
            color: #6b7280;
        }

        .status-disconnected {
            background: #fef3c7;
            color: #92400e;
        }

        /* Footer */
        .footer {
            padding: 12px 24px;
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer p {
            font-size: 9px;
            color: #9ca3af;
        }

        /* Print Actions */
        .print-actions {
            max-width: 1100px;
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
                font-size: 8px;
            }

            .document {
                border: none;
                border-radius: 0;
                max-width: none;
                width: 100%;
            }

            .print-actions,
            .filter-bar,
            .back-link {
                display: none !important;
            }

            .data-table th,
            .data-table td {
                padding: 4px;
            }
        }

        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        /* Light Theme Filter Bar */
        .filter-bar {
            max-width: 1100px;
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

        .filter-bar select {
            padding: 8px 32px 8px 12px;
            background: #ffffff;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            color: #111827;
            font-size: 13px;
            cursor: pointer;
            appearance: none;
            background-image: url('data:image/svg+xml;charset=utf-8,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 fill=%22none%22 viewBox=%220 0 20 20%22%3E%3Cpath stroke=%22%23374151%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22 stroke-width=%221.5%22 d=%22m6 8 4 4 4-4%22/%3E%3C/svg%3E');
            background-position: right 8px center;
            background-repeat: no-repeat;
            background-size: 1.25em 1.25em;
        }

        .filter-bar select:focus {
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
    </style>
    @vite(['resources/css/app.css'])
</head>
<body>
    <!-- Back Link (Top Left) -->
    <div class="back-link" style="position: absolute; top: 20px; left: 20px; z-index: 10;">
        <a href="{{ route('reports') }}" style="text-decoration: none; color: #374151; font-size: 14px; display: flex; align-items: center; gap: 6px;">
            <i class="fas fa-chevron-left"></i> Back to Reports
        </a>
    </div>

    <!-- Dark Theme Filter Bar -->
    <div class="filter-bar" style="margin-top: 60px;">
        <span class="filter-label"><i class="fas fa-filter" style="margin-right: 8px;"></i>Filter Report</span>
        <div class="filter-controls">
            <select id="filterStatus">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="disconnected" {{ request('status') == 'disconnected' ? 'selected' : '' }}>Disconnected</option>
            </select>
            <select id="filterArea">
                <option value="">All Areas</option>
                @foreach(\App\Models\Area::orderBy('a_desc')->get() as $area)
                    <option value="{{ $area->a_id }}" {{ request('area') == $area->a_id ? 'selected' : '' }}>{{ $area->a_desc }}</option>
                @endforeach
            </select>
            <button class="btn-apply" onclick="applyFilter()">
                <i class="fas fa-sync-alt" style="margin-right: 6px;"></i>Apply
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
            <h2>Consumer Master List</h2>
            <div class="date">As of {{ now()->format('F d, Y') }}</div>
        </div>

        <!-- Content -->
        <div class="content">
            @php
                // Get all service connections with their customers
                $activeStatusId = \App\Models\Status::getIdByDescription('ACTIVE');
                $connections = \App\Models\ServiceConnection::with(['customer', 'accountType', 'address.barangay', 'area', 'meterAssignments.meter', 'status'])
                    ->whereHas('customer')
                    ->orderBy('account_no')
                    ->get();
                
                $totalActive = $connections->where('stat_id', $activeStatusId)->count();
                $totalInactive = $connections->count() - $totalActive;
            @endphp

            <!-- Consumer Table -->
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 8%;">Account No</th>
                        <th style="width: 20%;">Consumer Name</th>
                        <th style="width: 22%;">Address</th>
                        <th style="width: 10%;">Classification</th>
                        <th style="width: 10%;">Area</th>
                        <th style="width: 8%;">Meter No</th>
                        <th style="width: 9%;">Date Enrolled</th>
                        <th style="width: 8%;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($connections as $index => $connection)
                        @php
                            $customer = $connection->customer;
                            $status = $connection->status;
                            $statusName = $status?->stat_desc ?? 'Unknown';
                            $statusClass = 'status-inactive';
                            if ($connection->stat_id == $activeStatusId) {
                                $statusClass = 'status-active';
                            } elseif (strtolower($statusName) == 'disconnected') {
                                $statusClass = 'status-disconnected';
                            }
                            $fullName = $customer ? trim($customer->cust_last_name . ', ' . $customer->cust_first_name . ' ' . ($customer->cust_middle_name ?? '')) : 'N/A';
                            $address = $connection->address ? ($connection->address->barangay?->b_desc ?? 'N/A') : 'N/A';
                            $meterSerial = $connection->meterAssignments->whereNull('removed_at')->first()?->meter?->mtr_serial ?? '-';
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center">{{ $connection->account_no }}</td>
                            <td class="text-left">{{ $fullName }}</td>
                            <td class="text-left">{{ $address }}</td>
                            <td class="text-center">{{ $connection->accountType?->at_desc ?? 'N/A' }}</td>
                            <td class="text-center">{{ $connection->area?->name ?? 'N/A' }}</td>
                            <td class="text-center">{{ $meterSerial }}</td>
                            <td class="text-center">{{ $connection->started_at ? \Carbon\Carbon::parse($connection->started_at)->format('m/d/Y') : '-' }}</td>
                            <td class="text-center">
                                <span class="status-badge {{ $statusClass }}">{{ $statusName }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center" style="padding: 40px; color: #6b7280;">
                                No consumers found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Total Records: {{ number_format($connections->count()) }}</p>
            <p>Generated on {{ now()->format('F d, Y h:i A') }} • Initao Municipal Water System</p>
        </div>
    </div>

    <!-- Print Actions (Bottom) -->
    <div class="print-actions" style="margin-top: 20px; display: flex; gap: 10px; justify-content: center;">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> Print Document
        </button>
        @include('components.export-dropdown-print', [
            'exportFilename' => 'consumer-master-list',
            'exportSelector' => '.document'
        ])
    </div>

    <script>
        function applyFilter() {
            const status = document.getElementById('filterStatus').value;
            const area = document.getElementById('filterArea').value;
            let url = `{{ route('reports.consumer-master-list') }}?`;
            if (status) url += `status=${status}&`;
            if (area) url += `area=${area}`;
            window.location.href = url;
        }
    </script>
</body>
</html>
