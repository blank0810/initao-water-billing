<div id="editCustomerModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full mx-4">
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Edit Customer</h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div class="p-6">
            <form id="editCustomerForm" class="space-y-4">
                <input type="hidden" id="edit_customer_id">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Customer Name</label>
                    <input type="text" id="edit_customer_name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Customer Code</label>
                    <input type="text" id="edit_customer_code" readonly class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-600 text-gray-900 dark:text-white">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Address</label>
                    <input type="text" id="edit_customer_address" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type</label>
                    <select id="edit_customer_type" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="Residential">Residential</option>
                        <option value="Commercial">Commercial</option>
                        <option value="Industrial">Industrial</option>
                    </select>
                </div>
            </form>
        </div>
        
        <div class="flex gap-3 p-6 border-t border-gray-200 dark:border-gray-700">
            <button onclick="closeEditModal()" class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                Cancel
            </button>
            <button onclick="saveCustomer()" class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                Save Changes
            </button>
        </div>
    </div>
</div>

<script>
function openEditModal(id, name, code, address, type) {
    document.getElementById('edit_customer_id').value = id;
    document.getElementById('edit_customer_name').value = name;
    document.getElementById('edit_customer_code').value = code;
    document.getElementById('edit_customer_address').value = address;
    document.getElementById('edit_customer_type').value = type;
    document.getElementById('editCustomerModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editCustomerModal').classList.add('hidden');
}

function saveCustomer() {
    alert('Customer updated successfully!');
    closeEditModal();
}
</script>
