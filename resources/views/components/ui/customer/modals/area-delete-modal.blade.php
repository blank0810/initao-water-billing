<!-- Area Delete Modal -->
<div id="areaDeleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Delete Record</h3>
        </div>
        <div class="p-6 space-y-2">
            <p class="text-sm text-gray-700 dark:text-gray-300">Are you sure you want to delete this area assignment?</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">This action cannot be undone.</p>
            <input type="hidden" id="ad_connection_id">
        </div>
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
            <button onclick="document.getElementById('areaDeleteModal').classList.add('hidden')" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg">Cancel</button>
            <button onclick="(function(){ const id=document.getElementById('ad_connection_id').value; window.areaAssignmentAPI?.delete(id); document.getElementById('areaDeleteModal').classList.add('hidden'); })()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg">Delete</button>
        </div>
    </div>
</div>
<script>
    window.showAreaDeleteModal = function(rowEl) {
        document.getElementById('areaDeleteModal').classList.remove('hidden');
        const id = rowEl.querySelectorAll('td')[1]?.textContent?.trim() || '';
        document.getElementById('ad_connection_id').value = id;
    };
</script>
