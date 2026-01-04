<!-- Area Edit Modal -->
<div id="areaEditModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-xl w-full">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Edit Area Record</h3>
        </div>
        <div class="p-6 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1 uppercase">Customer Name</label>
                    <input type="text" id="ae_customer_name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1 uppercase">Customer Code</label>
                    <input type="text" id="ae_customer_code" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1 uppercase">Connection ID</label>
                    <input type="text" id="ae_connection_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white" readonly>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1 uppercase">Address</label>
                    <input type="text" id="ae_address" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1 uppercase">Area</label>
                    <input type="text" id="ae_area" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1 uppercase">Assigned Date</label>
                    <input type="date" id="ae_assigned_date" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1 uppercase">Status</label>
                    <select id="ae_status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="PENDING">Pending</option>
                        <option value="SCHEDULED">Scheduled</option>
                        <option value="COMPLETED">Completed</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
            <button onclick="document.getElementById('areaEditModal').classList.add('hidden')" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg">Cancel</button>
            <button onclick="(function(){ const payload={customer_name:document.getElementById('ae_customer_name').value,customer_code:document.getElementById('ae_customer_code').value,connection_id:document.getElementById('ae_connection_id').value,address:document.getElementById('ae_address').value,area:document.getElementById('ae_area').value,assigned_date:document.getElementById('ae_assigned_date').value,status:document.getElementById('ae_status').value}; window.areaAssignmentAPI?.edit(payload); document.getElementById('areaEditModal').classList.add('hidden'); })()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">Save</button>
        </div>
    </div>
</div>
<script>
    window.showAreaEditModal = function(rowEl) {
        document.getElementById('areaEditModal').classList.remove('hidden');
        const cells = rowEl.querySelectorAll('td');
        document.getElementById('ae_customer_name').value = cells[0].querySelector('div.text-sm')?.textContent?.trim() || '';
        document.getElementById('ae_customer_code').value = cells[0].querySelector('div.text-xs')?.textContent?.trim() || '';
        document.getElementById('ae_connection_id').value = cells[1]?.textContent?.trim() || '';
        document.getElementById('ae_address').value = cells[2]?.textContent?.trim() || '';
        document.getElementById('ae_area').value = cells[3]?.textContent?.trim() || '';
        document.getElementById('ae_assigned_date').value = (function(){ const v=cells[4]?.textContent?.trim()||''; const d=new Date(v); if(!isNaN(d)) return d.toISOString().split('T')[0]; return ''; })();
        document.getElementById('ae_status').value = cells[5]?.innerText?.trim() || 'PENDING';
    };
</script>
