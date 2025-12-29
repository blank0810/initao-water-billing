<!-- Area Assign Modal -->
<div id="areaAssignModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-lg w-full">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Assign Area</h3>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1 uppercase">Connection ID</label>
                <input type="text" id="aa_connection_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1 uppercase">Area</label>
                <input type="text" id="aa_area" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1 uppercase">Assigned Date</label>
                <input type="date" id="aa_assigned_date" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
            <button onclick="document.getElementById('areaAssignModal').classList.add('hidden')" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg">Cancel</button>
            <button onclick="(function(){ const payload={customer_name:'',customer_code:'',connection_id:document.getElementById('aa_connection_id').value,address:'',area:document.getElementById('aa_area').value,assigned_date:document.getElementById('aa_assigned_date').value,status:'SCHEDULED'}; window.areaAssignmentAPI?.assign(payload); document.getElementById('areaAssignModal').classList.add('hidden'); })()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg">Assign</button>
        </div>
    </div>
</div>
<script>
    window.showAreaAssignModal = function(conn) {
        document.getElementById('areaAssignModal').classList.remove('hidden');
        document.getElementById('aa_assigned_date').value = new Date().toISOString().split('T')[0];
        document.getElementById('aa_connection_id').value = (conn && conn.connection_id) || '';
    };
</script>
