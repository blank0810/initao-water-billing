// Enhanced Ledger Management System
(function(){
    // Source Types
    const sourceTypes = [
        { code: 'BILL', name: 'Bill', color: 'red', icon: 'fa-file-invoice' },
        { code: 'PAYMENT', name: 'Payment', color: 'green', icon: 'fa-money-bill-wave' },
        { code: 'ADJUSTMENT', name: 'Adjustment', color: 'orange', icon: 'fa-adjust' },
        { code: 'CHARGE', name: 'Charge', color: 'purple', icon: 'fa-coins' }
    ];

    // Transaction Status
    const statuses = [
        { id: 1, name: 'Active', color: 'green' },
        { id: 2, name: 'Paid', color: 'blue' },
        { id: 3, name: 'Cancelled', color: 'red' },
        { id: 5, name: 'Adjusted', color: 'yellow' }
    ];

    // Sample Consumers with Ledger History
    const consumers = [
        {
            id: 'C-1001', name: 'Gelogo, Norben', address: 'Brgy. San Roque, Main St',
            meterNo: 'M-1001', status: 'Active', accountType: 'Residential',
            ledgerHistory: []
        },
        {
            id: 'C-1002', name: 'Sayson, Sarah', address: 'Brgy. Poblacion, Oak Ave',
            meterNo: 'M-1002', status: 'Active', accountType: 'Residential',
            ledgerHistory: []
        },
        {
            id: 'C-1003', name: 'Apora, Jose', address: 'Brgy. Riverside, Pine Rd',
            meterNo: 'M-1003', status: 'Active', accountType: 'Residential',
            ledgerHistory: []
        },
        {
            id: 'C-1004', name: 'Cruz, Maria', address: 'Brgy. Centro, Market St',
            meterNo: 'M-1004', status: 'Active', accountType: 'Commercial',
            ledgerHistory: []
        },
        {
            id: 'C-1005', name: 'Santos Manufacturing', address: 'Industrial Zone A',
            meterNo: 'M-1005', status: 'Active', accountType: 'Industrial',
            ledgerHistory: []
        },
        {
            id: 'C-1006', name: 'Reyes, Pedro', address: 'Brgy. Maharlika, Sunset Blvd',
            meterNo: 'M-1006', status: 'Overdue', accountType: 'Residential',
            ledgerHistory: []
        }
    ];

    // Generate ledger entries for each consumer
    consumers.forEach((consumer, idx) => {
        let runningBalance = 0;
        const entries = [];
        
        // Generate 8-12 transactions per consumer
        const numTransactions = 8 + Math.floor(Math.random() * 5);
        
        for(let i = 0; i < numTransactions; i++) {
            const monthsAgo = numTransactions - i;
            const date = new Date();
            date.setMonth(date.getMonth() - monthsAgo);
            const dateStr = date.toISOString().split('T')[0];
            
            // Alternate between bills and payments
            if(i % 3 === 0) {
                // Bill entry
                const amount = 200 + Math.random() * 500;
                runningBalance += amount;
                entries.push({
                    date: dateStr,
                    referenceNo: `BILL-${10000 + idx * 100 + i}`,
                    description: `Water Consumption - ${date.toLocaleDateString('en-US', {month: 'short', year: 'numeric'})}`,
                    sourceType: 'BILL',
                    debit: +amount.toFixed(2),
                    credit: 0,
                    balance: +runningBalance.toFixed(2),
                    status: 'Active'
                });
            } else if(i % 3 === 1) {
                // Payment entry
                const amount = Math.min(runningBalance * 0.8, 200 + Math.random() * 400);
                runningBalance -= amount;
                entries.push({
                    date: dateStr,
                    referenceNo: `RCPT-${20000 + idx * 100 + i}`,
                    description: `Payment Received - Receipt ${20000 + idx * 100 + i}`,
                    sourceType: 'PAYMENT',
                    debit: 0,
                    credit: +amount.toFixed(2),
                    balance: +runningBalance.toFixed(2),
                    status: 'Paid'
                });
            } else {
                // Random adjustment or charge
                const isAdjustment = Math.random() > 0.5;
                if(isAdjustment) {
                    const amount = 10 + Math.random() * 50;
                    const isDiscount = Math.random() > 0.6;
                    if(isDiscount) {
                        runningBalance -= amount;
                        entries.push({
                            date: dateStr,
                            referenceNo: `ADJ-${30000 + idx * 100 + i}`,
                            description: 'Senior Citizen Discount',
                            sourceType: 'ADJUSTMENT',
                            debit: 0,
                            credit: +amount.toFixed(2),
                            balance: +runningBalance.toFixed(2),
                            status: 'Adjusted'
                        });
                    } else {
                        runningBalance += amount;
                        entries.push({
                            date: dateStr,
                            referenceNo: `ADJ-${30000 + idx * 100 + i}`,
                            description: 'Late Payment Penalty',
                            sourceType: 'ADJUSTMENT',
                            debit: +amount.toFixed(2),
                            credit: 0,
                            balance: +runningBalance.toFixed(2),
                            status: 'Active'
                        });
                    }
                } else {
                    const amount = 50 + Math.random() * 150;
                    runningBalance += amount;
                    entries.push({
                        date: dateStr,
                        referenceNo: `CHG-${40000 + idx * 100 + i}`,
                        description: 'Reconnection Fee',
                        sourceType: 'CHARGE',
                        debit: +amount.toFixed(2),
                        credit: 0,
                        balance: +runningBalance.toFixed(2),
                        status: 'Active'
                    });
                }
            }
        }
        
        consumer.ledgerHistory = entries;
    });

    // State
    let filteredConsumers = [...consumers];
    let currentPage = 1;
    const rowsPerPage = 10;
    let selectedSourceType = 'all';

    // DOM refs
    let mainTbody, searchEl, paginationInfo, tableSection, detailsSection;

    function resolveDOM() {
        mainTbody = document.getElementById('ledgerMainTable');
        searchEl = document.getElementById('searchInput');
        paginationInfo = document.getElementById('paginationInfo');
        tableSection = document.getElementById('tableSection');
        detailsSection = document.getElementById('ledgerDetailsSection');
    }

    function computeMainAggregates() {
        const totalConsumers = filteredConsumers.length;
        let totalCharges = 0, totalPayments = 0, totalPenalties = 0, totalEntries = 0;
        
        filteredConsumers.forEach(c => {
            c.ledgerHistory.forEach(e => {
                totalEntries++;
                totalCharges += e.debit;
                totalPayments += e.credit;
                if(e.sourceType === 'ADJUSTMENT' && e.debit > 0) {
                    totalPenalties += e.debit;
                }
            });
        });
        
        const outstandingBalance = totalCharges - totalPayments;
        return { totalConsumers, totalCharges, totalPayments, outstandingBalance, totalPenalties, totalEntries };
    }

    function populateMainCards() {
        const cards = computeMainAggregates();
        document.getElementById('cardTotalConsumersLedger').textContent = cards.totalConsumers;
        document.getElementById('cardTotalChargesLedger').textContent = '₱ ' + cards.totalCharges.toFixed(2);
        document.getElementById('cardTotalPaymentsLedger').textContent = '₱ ' + cards.totalPayments.toFixed(2);
        document.getElementById('cardOutstandingBalanceLedger').textContent = '₱ ' + cards.outstandingBalance.toFixed(2);
    }

    function renderConsumerMainTable() {
        if(!mainTbody) return;
        mainTbody.innerHTML = '';

        const start = (currentPage - 1) * rowsPerPage;
        const pageItems = filteredConsumers.slice(start, start + rowsPerPage);

        if(pageItems.length === 0) {
            mainTbody.innerHTML = '<tr><td colspan="7" class="px-6 py-8 text-center text-gray-500">No consumers found</td></tr>';
        } else {
            pageItems.forEach(c => {
                const totalCharges = c.ledgerHistory.reduce((sum, e) => sum + e.debit, 0);
                const totalPayments = c.ledgerHistory.reduce((sum, e) => sum + e.credit, 0);
                const balance = totalCharges - totalPayments;
                const statusColor = c.status === 'Active' ? 'text-green-600' : 'text-red-600';
                
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-gray-50 dark:hover:bg-gray-700';
                tr.innerHTML = `
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">${c.name}</td>
                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300 font-mono">${c.id}</td>
                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300 font-mono">${c.meterNo}</td>
                    <td class="px-6 py-4 text-sm text-red-600">₱ ${totalCharges.toFixed(2)}</td>
                    <td class="px-6 py-4 text-sm text-green-600">₱ ${totalPayments.toFixed(2)}</td>
                    <td class="px-6 py-4 text-sm font-semibold ${balance > 0 ? 'text-red-600' : 'text-green-600'}">₱ ${Math.abs(balance).toFixed(2)}</td>
                    <td class="px-6 py-4 text-sm"><button class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-xs" onclick="selectConsumer('${c.id}')">View Ledger</button></td>
                `;
                mainTbody.appendChild(tr);
            });
        }

        const totalPages = Math.max(1, Math.ceil(filteredConsumers.length / rowsPerPage));
        if(paginationInfo) paginationInfo.textContent = `Page ${currentPage} of ${totalPages}`;
    }

    function prevConsumerPage() { if(currentPage > 1) { currentPage--; renderConsumerMainTable(); } }
    function nextConsumerPage() { if(currentPage * rowsPerPage < filteredConsumers.length) { currentPage++; renderConsumerMainTable(); } }

    function filterBySourceType(type) {
        selectedSourceType = type;
        if(type === 'all') {
            filteredConsumers = [...consumers];
        } else {
            filteredConsumers = consumers.filter(c => 
                c.ledgerHistory.some(e => e.sourceType === type)
            );
        }
        currentPage = 1;
        populateMainCards();
        renderConsumerMainTable();
    }

    function selectConsumer(consumerId) {
        resolveDOM();
        const consumer = consumers.find(c => c.id === consumerId);
        if(!consumer) return;

        const wrapper = document.getElementById('ledgerSummaryWrapper');
        const searchFilter = document.getElementById('searchFilterSection');
        if(wrapper) wrapper.classList.add('hidden');
        if(searchFilter) searchFilter.classList.add('hidden');
        if(tableSection) tableSection.classList.add('hidden');
        if(detailsSection) detailsSection.classList.remove('hidden');

        // Populate profile
        document.getElementById('ledger_consumer_name').textContent = consumer.name;
        document.getElementById('ledger_consumer_id').textContent = consumer.id;
        document.getElementById('ledger_consumer_address').textContent = consumer.address;
        document.getElementById('ledger_consumer_meter_no').textContent = consumer.meterNo;
        document.getElementById('ledger_account_status').textContent = consumer.status;
        document.getElementById('ledger_account_type').textContent = consumer.accountType;

        // Calculate totals
        const totalCharges = consumer.ledgerHistory.reduce((sum, e) => sum + e.debit, 0);
        const totalPayments = consumer.ledgerHistory.reduce((sum, e) => sum + e.credit, 0);
        const totalPenalties = consumer.ledgerHistory.filter(e => e.sourceType === 'ADJUSTMENT' && e.debit > 0).reduce((sum, e) => sum + e.debit, 0);
        const balance = totalCharges - totalPayments;
        const totalEntries = consumer.ledgerHistory.length;

        document.getElementById('ledger_outstanding_balance').textContent = '₱ ' + Math.abs(balance).toFixed(2);
        document.getElementById('cardTotalCharges').textContent = '₱ ' + totalCharges.toFixed(2);
        document.getElementById('cardTotalPayments').textContent = '₱ ' + totalPayments.toFixed(2);
        document.getElementById('cardOutstandingBalance').textContent = '₱ ' + Math.abs(balance).toFixed(2);
        document.getElementById('cardTotalPenalties').textContent = '₱ ' + totalPenalties.toFixed(2);
        document.getElementById('cardTotalEntries').textContent = totalEntries;

        // Populate ledger table
        renderConsumerLedgerTable(consumer.ledgerHistory);
        renderSourceTypeBreakdown(consumer.ledgerHistory);
        renderLedgerChart(consumer.ledgerHistory);
    }

    function renderConsumerLedgerTable(entries) {
        const t = document.getElementById('consumerLedgerTable');
        if(!t) return;
        t.innerHTML = '';
        
        if(!entries || entries.length === 0) {
            t.innerHTML = '<tr><td colspan="7" class="px-6 py-8 text-center text-gray-500">No ledger entries</td></tr>';
            return;
        }
        
        entries.forEach(e => {
            const sourceType = sourceTypes.find(st => st.code === e.sourceType);
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50 dark:hover:bg-gray-700';
            tr.innerHTML = `
                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">${e.date}</td>
                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300 font-mono">${e.referenceNo}</td>
                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">${e.description}</td>
                <td class="px-4 py-3 text-sm"><span class="px-2 py-1 text-xs rounded bg-${sourceType.color}-100 text-${sourceType.color}-800"><i class="fas ${sourceType.icon} mr-1"></i>${sourceType.name}</span></td>
                <td class="px-4 py-3 text-sm text-red-600">${e.debit > 0 ? '₱ ' + e.debit.toFixed(2) : '-'}</td>
                <td class="px-4 py-3 text-sm text-green-600">${e.credit > 0 ? '₱ ' + e.credit.toFixed(2) : '-'}</td>
                <td class="px-4 py-3 text-sm font-semibold ${e.balance >= 0 ? 'text-red-600' : 'text-green-600'}">₱ ${Math.abs(e.balance).toFixed(2)}</td>
            `;
            t.appendChild(tr);
        });
    }

    function renderSourceTypeBreakdown(entries) {
        const container = document.getElementById('sourceTypeBreakdownTable');
        if(!container) return;
        container.innerHTML = '';
        
        const breakdown = {};
        sourceTypes.forEach(st => {
            breakdown[st.code] = { count: 0, debit: 0, credit: 0 };
        });
        
        entries.forEach(e => {
            if(breakdown[e.sourceType]) {
                breakdown[e.sourceType].count++;
                breakdown[e.sourceType].debit += e.debit;
                breakdown[e.sourceType].credit += e.credit;
            }
        });
        
        sourceTypes.forEach(st => {
            if(breakdown[st.code].count > 0) {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-gray-50 dark:hover:bg-gray-700';
                tr.innerHTML = `
                    <td class="px-4 py-3 text-sm"><span class="px-2 py-1 text-xs rounded bg-${st.color}-100 text-${st.color}-800"><i class="fas ${st.icon} mr-1"></i>${st.name}</span></td>
                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">${breakdown[st.code].count}</td>
                    <td class="px-4 py-3 text-sm text-red-600">₱ ${breakdown[st.code].debit.toFixed(2)}</td>
                    <td class="px-4 py-3 text-sm text-green-600">₱ ${breakdown[st.code].credit.toFixed(2)}</td>
                `;
                container.appendChild(tr);
            }
        });
    }

    function renderLedgerChart(entries) {
        const container = document.getElementById('ledgerChart');
        if(!container) return;
        container.innerHTML = '';
        
        if(!entries || entries.length === 0) {
            container.innerHTML = '<div class="text-sm text-gray-500">No chart data</div>';
            return;
        }

        const width = container.clientWidth || 800;
        const height = 220;
        const padding = 40;
        const svgNS = 'http://www.w3.org/2000/svg';
        const svg = document.createElementNS(svgNS, 'svg');
        svg.setAttribute('width', '100%');
        svg.setAttribute('viewBox', '0 0 ' + width + ' ' + height);

        const balances = entries.map(e => e.balance);
        const maxVal = Math.max(...balances.map(Math.abs), 1);
        const xStep = (width - padding * 2) / Math.max(1, entries.length - 1);

        // Axes
        const axisY = document.createElementNS(svgNS, 'line');
        axisY.setAttribute('x1', padding);
        axisY.setAttribute('y1', padding);
        axisY.setAttribute('x2', padding);
        axisY.setAttribute('y2', height - padding);
        axisY.setAttribute('stroke', '#cbd5e1');
        svg.appendChild(axisY);

        const axisX = document.createElementNS(svgNS, 'line');
        axisX.setAttribute('x1', padding);
        axisX.setAttribute('y1', height - padding);
        axisX.setAttribute('x2', width - padding);
        axisX.setAttribute('y2', height - padding);
        axisX.setAttribute('stroke', '#cbd5e1');
        svg.appendChild(axisX);

        // Line
        const points = balances.map((v, i) => {
            const x = padding + i * xStep;
            const y = (height - padding) - ((v / maxVal) * (height - padding * 2));
            return `${x},${y}`;
        }).join(' ');

        const poly = document.createElementNS(svgNS, 'polyline');
        poly.setAttribute('points', points);
        poly.setAttribute('fill', 'none');
        poly.setAttribute('stroke', '#3b82f6');
        poly.setAttribute('stroke-width', '2');
        svg.appendChild(poly);

        balances.forEach((v, i) => {
            const x = padding + i * xStep;
            const y = (height - padding) - ((v / maxVal) * (height - padding * 2));
            const circle = document.createElementNS(svgNS, 'circle');
            circle.setAttribute('cx', x);
            circle.setAttribute('cy', y);
            circle.setAttribute('r', '3');
            circle.setAttribute('fill', v >= 0 ? '#ef4444' : '#10b981');
            svg.appendChild(circle);
        });

        container.appendChild(svg);
    }

    function setupSearch() {
        if(!searchEl) return;
        searchEl.addEventListener('input', e => {
            const q = (e.target.value || '').toLowerCase().trim();
            filteredConsumers = consumers.filter(c => (
                c.name.toLowerCase().includes(q) || 
                c.id.toLowerCase().includes(q) || 
                (c.meterNo || '').toLowerCase().includes(q)
            ));
            if(selectedSourceType !== 'all') {
                filteredConsumers = filteredConsumers.filter(c => 
                    c.ledgerHistory.some(e => e.sourceType === selectedSourceType)
                );
            }
            currentPage = 1;
            populateMainCards();
            renderConsumerMainTable();
        });
    }

    function populateSourceTypeFilter() {
        const dropdown = document.getElementById('sourceTypeFilterDropdown');
        if(!dropdown) return;
        
        dropdown.innerHTML = '<option value="all">All Transaction Types</option>';
        sourceTypes.forEach(st => {
            const opt = document.createElement('option');
            opt.value = st.code;
            opt.textContent = st.name;
            dropdown.appendChild(opt);
        });
    }

    function init() {
        resolveDOM();
        populateMainCards();
        setupSearch();
        renderConsumerMainTable();
        populateSourceTypeFilter();
    }

    if(document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Expose
    window.renderConsumerMainTable = renderConsumerMainTable;
    window.prevConsumerPage = prevConsumerPage;
    window.nextConsumerPage = nextConsumerPage;
    window.selectConsumer = selectConsumer;
    window.filterBySourceType = filterBySourceType;
    window.showLedgerTable = function() {
        if(detailsSection) detailsSection.classList.add('hidden');
        if(tableSection) tableSection.classList.remove('hidden');
        const wrapper = document.getElementById('ledgerSummaryWrapper');
        const searchFilter = document.getElementById('searchFilterSection');
        if(wrapper) wrapper.classList.remove('hidden');
        if(searchFilter) searchFilter.classList.remove('hidden');
    };
    window._ledgerModule = { sourceTypes, statuses, consumers };
})();

console.log('Enhanced Ledger Management System loaded');
