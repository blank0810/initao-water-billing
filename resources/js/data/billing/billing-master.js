/**
 * ========================================
 * BILLING MASTER MODULE
 * Integrates Rate Master, Ledger, and UI
 * ========================================
 */

// ========================================
// RATE MASTER FILE
// ========================================
const RATE_MASTER = {
    periods: [{
        id: 'BP-2024-01',
        period: '2024-01',
        status: 'ACTIVE',
        structures: {
            RESIDENTIAL: {
                tiers: [
                    { tier: 1, min: 0, max: 10, rate: 15.00, label: 'Lifeline' },
                    { tier: 2, min: 11, max: 20, rate: 25.00, label: 'Normal' },
                    { tier: 3, min: 21, max: 30, rate: 35.00, label: 'Moderate' },
                    { tier: 4, min: 31, max: 999, rate: 50.00, label: 'High' }
                ],
                fixed: [
                    { code: 'BSF', name: 'Basic Service Fee', amount: 50.00 },
                    { code: 'MM', name: 'Meter Maintenance', amount: 25.00 },
                    { code: 'EF', name: 'Environmental Fee', amount: 10.00 }
                ]
            },
            COMMERCIAL: {
                tiers: [{ tier: 1, min: 0, max: 999, rate: 45.00, label: 'Flat' }],
                fixed: [
                    { code: 'BSF', name: 'Basic Service Fee', amount: 100.00 },
                    { code: 'MM', name: 'Meter Maintenance', amount: 50.00 },
                    { code: 'EF', name: 'Environmental Fee', amount: 20.00 },
                    { code: 'SDF', name: 'System Development', amount: 30.00 }
                ]
            },
            INDUSTRIAL: {
                tiers: [{ tier: 1, min: 0, max: 999, rate: 60.00, label: 'Flat' }],
                fixed: [
                    { code: 'BSF', name: 'Basic Service Fee', amount: 150.00 },
                    { code: 'MM', name: 'Meter Maintenance', amount: 75.00 },
                    { code: 'EF', name: 'Environmental Fee', amount: 30.00 },
                    { code: 'SDF', name: 'System Development', amount: 50.00 }
                ]
            }
        },
        adjustments: {
            VAT: 0.12,
            SENIOR_DISCOUNT: 0.05,
            PWD_DISCOUNT: 0.05
        }
    }],

    getStructure(period, type) {
        const p = this.periods.find(x => x.period === period && x.status === 'ACTIVE');
        return p?.structures[type] || null;
    },

    compute(consumption, type, period, discounts = []) {
        const s = this.getStructure(period, type);
        if (!s) return null;

        let consumptionCharge = 0;
        let remaining = consumption;
        const tierBreakdown = [];

        s.tiers.forEach(t => {
            if (remaining > 0) {
                const used = Math.min(remaining, t.max - t.min + 1);
                const charge = used * t.rate;
                consumptionCharge += charge;
                tierBreakdown.push({ tier: t.tier, label: t.label, consumption: used, rate: t.rate, charge });
                remaining -= used;
            }
        });

        const fixedTotal = s.fixed.reduce((sum, f) => sum + f.amount, 0);
        const subtotal = consumptionCharge + fixedTotal;

        let discountAmount = 0;
        const p = this.periods.find(x => x.period === period);
        discounts.forEach(d => {
            if (p.adjustments[d]) discountAmount += subtotal * p.adjustments[d];
        });

        const vat = (subtotal - discountAmount) * p.adjustments.VAT;
        const total = subtotal - discountAmount + vat;

        return { consumption, consumptionCharge, tierBreakdown, fixedCharges: s.fixed, fixedTotal, subtotal, discountAmount, vat, total };
    }
};

// ========================================
// LEDGER DATA
// ========================================
const LEDGER = {
    entries: [
        { id: 'L-001', accountNo: 'ACC-2024-001', date: '2024-01-05', type: 'BILLING', description: 'Water Bill - Jan 2024', debit: 2450.00, credit: 0, balance: 2450.00 },
        { id: 'L-002', accountNo: 'ACC-2024-001', date: '2024-01-15', type: 'PAYMENT', description: 'Payment Received', debit: 0, credit: 2450.00, balance: 0 },
        { id: 'L-003', accountNo: 'ACC-2024-001', date: '2024-02-05', type: 'BILLING', description: 'Water Bill - Feb 2024', debit: 2380.00, credit: 0, balance: 2380.00 },
        { id: 'L-004', accountNo: 'ACC-2024-002', date: '2024-01-05', type: 'BILLING', description: 'Water Bill - Jan 2024', debit: 3200.00, credit: 0, balance: 3200.00 },
        { id: 'L-005', accountNo: 'ACC-2024-002', date: '2024-01-20', type: 'ADJUSTMENT', description: 'Meter Correction', debit: 0, credit: 150.00, balance: 3050.00 },
        { id: 'L-006', accountNo: 'ACC-2024-003', date: '2024-01-05', type: 'BILLING', description: 'Water Bill - Jan 2024', debit: 1850.00, credit: 0, balance: 1850.00 },
        { id: 'L-007', accountNo: 'ACC-2024-003', date: '2024-01-12', type: 'PAYMENT', description: 'Partial Payment', debit: 0, credit: 1000.00, balance: 850.00 },
        { id: 'L-008', accountNo: 'ACC-2024-003', date: '2024-02-05', type: 'BILLING', description: 'Water Bill - Feb 2024', debit: 1920.00, credit: 0, balance: 2770.00 },
        { id: 'L-009', accountNo: 'ACC-2024-003', date: '2024-02-10', type: 'PENALTY', description: 'Late Payment', debit: 55.40, credit: 0, balance: 2825.40 }
    ],

    getByAccount(accountNo) {
        return this.entries.filter(e => e.accountNo === accountNo);
    },

    getBalance(accountNo) {
        const entries = this.getByAccount(accountNo);
        return entries.length > 0 ? entries[entries.length - 1].balance : 0;
    }
};

// ========================================
// BILLING DATA
// ========================================
const BILLING_DATA = [
    { id: 1, name: 'Juan Dela Cruz', accountNo: 'ACC-2024-001', period: '2024-01', amount: 2450.00, status: 'PAID', type: 'RESIDENTIAL', consumption: 25 },
    { id: 2, name: 'Maria Santos', accountNo: 'ACC-2024-002', period: '2024-01', amount: 3200.00, status: 'UNPAID', type: 'COMMERCIAL', consumption: 45 },
    { id: 3, name: 'Pedro Garcia', accountNo: 'ACC-2024-003', period: '2024-01', amount: 1850.00, status: 'PARTIAL', type: 'RESIDENTIAL', consumption: 18 }
];

// ========================================
// BILLING MODULE
// ========================================
window.BillingModule = {
    switchTab(tab) {
        document.querySelectorAll('.billing-tab-content').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.billing-tab-btn').forEach(el => {
            el.classList.remove('active', 'border-blue-500', 'text-blue-600', 'dark:text-blue-400');
            el.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
        });

        document.getElementById(`tab-content-${tab}`).classList.remove('hidden');
        const btn = document.getElementById(`tab-btn-${tab}`);
        btn.classList.add('active', 'border-blue-500', 'text-blue-600', 'dark:text-blue-400');
        btn.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
    },

    initOverview() {
        const total = BILLING_DATA.length;
        const billed = BILLING_DATA.reduce((s, b) => s + b.amount, 0);
        const collected = BILLING_DATA.filter(b => b.status === 'PAID').reduce((s, b) => s + b.amount, 0);
        const outstanding = billed - collected;

        document.getElementById('overview-total-consumers').textContent = total;
        document.getElementById('overview-total-billed').textContent = `₱${billed.toFixed(2)}`;
        document.getElementById('overview-collected').textContent = `₱${collected.toFixed(2)}`;
        document.getElementById('overview-outstanding').textContent = `₱${outstanding.toFixed(2)}`;

        this.renderOverviewTable(BILLING_DATA);

        document.getElementById('overview-search')?.addEventListener('input', (e) => {
            const q = e.target.value.toLowerCase();
            const filtered = BILLING_DATA.filter(b => b.name.toLowerCase().includes(q) || b.accountNo.toLowerCase().includes(q));
            this.renderOverviewTable(filtered);
        });
    },

    renderOverviewTable(data) {
        const tbody = document.getElementById('overview-table-body');
        if (!tbody) return;

        tbody.innerHTML = '';
        data.forEach(b => {
            const colors = { PAID: 'bg-green-100 text-green-800', UNPAID: 'bg-red-100 text-red-800', PARTIAL: 'bg-yellow-100 text-yellow-800' };
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50 dark:hover:bg-gray-700';
            tr.innerHTML = `
                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">${b.name}</td>
                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">${b.accountNo}</td>
                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">${b.period}</td>
                <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-white">₱${b.amount.toFixed(2)}</td>
                <td class="px-6 py-4"><span class="px-2 py-1 text-xs font-semibold rounded-full ${colors[b.status]}">${b.status}</span></td>
                <td class="px-6 py-4"><button class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-xs">View</button></td>
            `;
            tbody.appendChild(tr);
        });
    },

    initRate() {
        document.getElementById('rate-account-type')?.addEventListener('change', () => this.updateRateDisplay());
        this.updateRateDisplay();
    },

    updateRateDisplay() {
        const type = document.getElementById('rate-account-type')?.value || 'RESIDENTIAL';
        const s = RATE_MASTER.getStructure('2024-01', type);
        if (!s) return;

        const tierBody = document.getElementById('rate-tier-body');
        if (tierBody) {
            tierBody.innerHTML = '';
            s.tiers.forEach(t => {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-gray-50 dark:hover:bg-gray-700';
                tr.innerHTML = `
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">Tier ${t.tier}</td>
                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">${t.min} - ${t.max === 999 ? '∞' : t.max}</td>
                    <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white">₱${t.rate.toFixed(2)}</td>
                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">${t.label}</td>
                `;
                tierBody.appendChild(tr);
            });
        }

        const fixedBody = document.getElementById('rate-fixed-body');
        if (fixedBody) {
            fixedBody.innerHTML = '';
            s.fixed.forEach(f => {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-gray-50 dark:hover:bg-gray-700';
                tr.innerHTML = `
                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">${f.code}</td>
                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">${f.name}</td>
                    <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white">₱${f.amount.toFixed(2)}</td>
                `;
                fixedBody.appendChild(tr);
            });
        }
    },

    calculateRate() {
        const consumption = parseFloat(document.getElementById('rate-calc-consumption')?.value || 0);
        const discount = document.getElementById('rate-calc-discount')?.value;
        const type = document.getElementById('rate-account-type')?.value || 'RESIDENTIAL';
        
        const discounts = discount ? [discount] : [];
        const result = RATE_MASTER.compute(consumption, type, '2024-01', discounts);
        
        if (!result) return;

        document.getElementById('rate-calc-consumption-charge').textContent = `₱${result.consumptionCharge.toFixed(2)}`;
        document.getElementById('rate-calc-fixed').textContent = `₱${result.fixedTotal.toFixed(2)}`;
        document.getElementById('rate-calc-subtotal').textContent = `₱${result.subtotal.toFixed(2)}`;
        document.getElementById('rate-calc-discount-amt').textContent = `-₱${result.discountAmount.toFixed(2)}`;
        document.getElementById('rate-calc-vat').textContent = `₱${result.vat.toFixed(2)}`;
        document.getElementById('rate-calc-total').textContent = `₱${result.total.toFixed(2)}`;

        const tierDiv = document.getElementById('rate-calc-tiers');
        if (tierDiv) {
            tierDiv.innerHTML = '<h5 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Tier Breakdown:</h5>';
            result.tierBreakdown.forEach(tb => {
                const div = document.createElement('div');
                div.className = 'text-xs text-gray-700 dark:text-gray-300 flex justify-between';
                div.innerHTML = `<span>Tier ${tb.tier} (${tb.label}): ${tb.consumption}m³ × ₱${tb.rate.toFixed(2)}</span><span class="font-semibold">₱${tb.charge.toFixed(2)}</span>`;
                tierDiv.appendChild(div);
            });
        }

        document.getElementById('rate-calc-result')?.classList.remove('hidden');
    },

    initLedger() {
        document.getElementById('ledger-account')?.addEventListener('change', () => this.updateLedger());
    },

    updateLedger() {
        const accountNo = document.getElementById('ledger-account')?.value;
        if (!accountNo) {
            document.getElementById('ledger-balance').textContent = '₱0.00';
            document.getElementById('ledger-table-body').innerHTML = '<tr><td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">Select account</td></tr>';
            return;
        }

        const balance = LEDGER.getBalance(accountNo);
        document.getElementById('ledger-balance').textContent = `₱${balance.toFixed(2)}`;

        const entries = LEDGER.getByAccount(accountNo);
        const tbody = document.getElementById('ledger-table-body');
        if (!tbody) return;

        tbody.innerHTML = '';
        entries.forEach(e => {
            const colors = { BILLING: 'text-blue-600', PAYMENT: 'text-green-600', ADJUSTMENT: 'text-yellow-600', PENALTY: 'text-red-600' };
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50 dark:hover:bg-gray-700';
            tr.innerHTML = `
                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">${e.date}</td>
                <td class="px-6 py-4 text-sm font-semibold ${colors[e.type]}">${e.type}</td>
                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">${e.description}</td>
                <td class="px-6 py-4 text-sm text-right text-red-600">${e.debit > 0 ? '₱' + e.debit.toFixed(2) : '-'}</td>
                <td class="px-6 py-4 text-sm text-right text-green-600">${e.credit > 0 ? '₱' + e.credit.toFixed(2) : '-'}</td>
                <td class="px-6 py-4 text-sm text-right font-semibold text-gray-900 dark:text-white">₱${e.balance.toFixed(2)}</td>
            `;
            tbody.appendChild(tr);
        });
    }
};

// ========================================
// INIT
// ========================================
document.addEventListener('DOMContentLoaded', () => {
    window.BillingModule.initOverview();
    window.BillingModule.initRate();
    window.BillingModule.initLedger();
});
