<div class="space-y-6">
    <!-- Account Type Selector -->
    <x-ui.card title="Select Account Type">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <button onclick="loadRateStructure(1)" class="p-4 border-2 border-gray-300 dark:border-gray-600 rounded-lg hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900 transition text-left">
                <div class="flex items-center">
                    <i class="fas fa-home text-blue-500 text-2xl mr-3"></i>
                    <div>
                        <div class="font-semibold text-gray-900 dark:text-white">Residential</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Standard</div>
                    </div>
                </div>
            </button>
            <button onclick="loadRateStructure(2)" class="p-4 border-2 border-gray-300 dark:border-gray-600 rounded-lg hover:border-green-500 hover:bg-green-50 dark:hover:bg-green-900 transition text-left">
                <div class="flex items-center">
                    <i class="fas fa-hand-holding-heart text-green-500 text-2xl mr-3"></i>
                    <div>
                        <div class="font-semibold text-gray-900 dark:text-white">Low-Income</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Subsidized</div>
                    </div>
                </div>
            </button>
            <button onclick="loadRateStructure(3)" class="p-4 border-2 border-gray-300 dark:border-gray-600 rounded-lg hover:border-purple-500 hover:bg-purple-50 dark:hover:bg-purple-900 transition text-left">
                <div class="flex items-center">
                    <i class="fas fa-user-clock text-purple-500 text-2xl mr-3"></i>
                    <div>
                        <div class="font-semibold text-gray-900 dark:text-white">Senior Citizen</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">With Discount</div>
                    </div>
                </div>
            </button>
            <button onclick="loadRateStructure(4)" class="p-4 border-2 border-gray-300 dark:border-gray-600 rounded-lg hover:border-orange-500 hover:bg-orange-50 dark:hover:bg-orange-900 transition text-left">
                <div class="flex items-center">
                    <i class="fas fa-store text-orange-500 text-2xl mr-3"></i>
                    <div>
                        <div class="font-semibold text-gray-900 dark:text-white">Commercial</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Small Business</div>
                    </div>
                </div>
            </button>
            <button onclick="loadRateStructure(5)" class="p-4 border-2 border-gray-300 dark:border-gray-600 rounded-lg hover:border-red-500 hover:bg-red-50 dark:hover:bg-red-900 transition text-left">
                <div class="flex items-center">
                    <i class="fas fa-building text-red-500 text-2xl mr-3"></i>
                    <div>
                        <div class="font-semibold text-gray-900 dark:text-white">Commercial</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Large</div>
                    </div>
                </div>
            </button>
            <button onclick="loadRateStructure(6)" class="p-4 border-2 border-gray-300 dark:border-gray-600 rounded-lg hover:border-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900 transition text-left">
                <div class="flex items-center">
                    <i class="fas fa-industry text-indigo-500 text-2xl mr-3"></i>
                    <div>
                        <div class="font-semibold text-gray-900 dark:text-white">Industrial</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Light</div>
                    </div>
                </div>
            </button>
            <button onclick="loadRateStructure(7)" class="p-4 border-2 border-gray-300 dark:border-gray-600 rounded-lg hover:border-gray-500 hover:bg-gray-50 dark:hover:bg-gray-700 transition text-left">
                <div class="flex items-center">
                    <i class="fas fa-industry text-gray-500 text-2xl mr-3"></i>
                    <div>
                        <div class="font-semibold text-gray-900 dark:text-white">Industrial</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Heavy</div>
                    </div>
                </div>
            </button>
            <button onclick="loadRateStructure(8)" class="p-4 border-2 border-gray-300 dark:border-gray-600 rounded-lg hover:border-teal-500 hover:bg-teal-50 dark:hover:bg-teal-900 transition text-left">
                <div class="flex items-center">
                    <i class="fas fa-landmark text-teal-500 text-2xl mr-3"></i>
                    <div>
                        <div class="font-semibold text-gray-900 dark:text-white">Government</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Public</div>
                    </div>
                </div>
            </button>
        </div>
    </x-ui.card>

    <!-- Rate Structure Details -->
    <div id="rateStructureDetails" class="hidden space-y-6">
        <x-ui.card>
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 id="structureAccountType" class="text-lg font-semibold text-gray-900 dark:text-white">Account Type</h3>
                    <p id="structureAccountCode" class="text-sm text-gray-500 dark:text-gray-400">Code</p>
                </div>
                <button onclick="hideRateStructure()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="space-y-6">
                <!-- Tiered Rates -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                        <i class="fas fa-layer-group text-blue-500 mr-2"></i>
                        Tiered Rate Structure
                    </h4>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900 dark:to-indigo-900">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">Tier</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">Range (m³)</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">Rate per m³</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">Description</th>
                                </tr>
                            </thead>
                            <tbody id="structureTierTable" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Fixed Charges -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                        <i class="fas fa-coins text-orange-500 mr-2"></i>
                        Fixed Monthly Charges
                    </h4>
                    <div id="structureFixedCharges" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    </div>
                </div>

                <!-- Applicable Adjustments -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                        <i class="fas fa-adjust text-purple-500 mr-2"></i>
                        Applicable Adjustments
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="p-3 bg-red-50 dark:bg-red-900 rounded-lg border border-red-200 dark:border-red-700">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Late Payment Penalty</span>
                                <span class="text-sm font-semibold text-red-600 dark:text-red-400">+2%</span>
                            </div>
                        </div>
                        <div class="p-3 bg-green-50 dark:bg-green-900 rounded-lg border border-green-200 dark:border-green-700">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Early Payment Discount</span>
                                <span class="text-sm font-semibold text-green-600 dark:text-green-400">-3%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </x-ui.card>
    </div>
</div>

<script>
function loadRateStructure(accountTypeId) {
    if(!window._rateModule) return;
    
    const accountType = window._rateModule.accountTypes.find(at => at.id === accountTypeId);
    if(!accountType) return;
    
    document.getElementById('structureAccountType').textContent = accountType.name;
    document.getElementById('structureAccountCode').textContent = accountType.code;
    
    const tiers = window._rateModule.rateTiers.filter(rt => rt.accountTypeId === accountTypeId);
    const tierTable = document.getElementById('structureTierTable');
    tierTable.innerHTML = '';
    
    tiers.forEach(tier => {
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-gray-50 dark:hover:bg-gray-700';
        tr.innerHTML = `
            <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white">Tier ${tier.tier}</td>
            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">${tier.minCubic} - ${tier.maxCubic === 999 ? '∞' : tier.maxCubic}</td>
            <td class="px-4 py-3 text-sm font-semibold text-blue-600 dark:text-blue-400">₱ ${tier.ratePerCubic.toFixed(5)}</td>
            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">${tier.description}</td>
        `;
        tierTable.appendChild(tr);
    });
    
    const fixedChargesDiv = document.getElementById('structureFixedCharges');
    fixedChargesDiv.innerHTML = '';
    window._rateModule.fixedCharges.forEach(fc => {
        const div = document.createElement('div');
        div.className = 'p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600';
        div.innerHTML = `
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-700 dark:text-gray-300">${fc.name}</span>
                <span class="text-sm font-semibold text-gray-900 dark:text-white">₱ ${fc.amount.toFixed(2)}</span>
            </div>
        `;
        fixedChargesDiv.appendChild(div);
    });
    
    document.getElementById('rateStructureDetails').classList.remove('hidden');
}

function hideRateStructure() {
    document.getElementById('rateStructureDetails').classList.add('hidden');
}

window.loadRateStructure = loadRateStructure;
window.hideRateStructure = hideRateStructure;
</script>
