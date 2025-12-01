<!-- Add Reader Modal -->
<div id="addReaderModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Add Meter Reader</h3>
            <button onclick="closeAddReaderModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Full Name *</label>
                <input type="text" id="readerName" required placeholder="Enter full name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Contact Number *</label>
                <input type="tel" id="readerContact" required placeholder="+63 XXX XXX XXXX" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                <input type="email" id="readerEmail" placeholder="email@example.com" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Assigned Area *</label>
                <select id="readerArea" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="">Select area...</option>
                    <option value="Brgy. 1-3">Brgy. 1-3</option>
                    <option value="Brgy. 4-6">Brgy. 4-6</option>
                    <option value="Brgy. 7-9">Brgy. 7-9</option>
                    <option value="Brgy. 10-12">Brgy. 10-12</option>
                    <option value="Commercial District">Commercial District</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Employment Date</label>
                <input type="date" id="employmentDate" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
            
            <div>
                <label class="flex items-center">
                    <input type="checkbox" id="readerActive" checked class="mr-2">
                    <span class="text-sm text-gray-700 dark:text-gray-300">Active Status</span>
                </label>
            </div>
        </div>
        
        <div class="flex justify-end gap-3 mt-6">
            <button onclick="closeAddReaderModal()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                Cancel
            </button>
            <button onclick="submitAddReader()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                <i class="fas fa-check mr-2"></i>Add Reader
            </button>
        </div>
    </div>
</div>

<script>
function openAddReaderModal() {
    document.getElementById('addReaderModal').classList.remove('hidden');
    document.getElementById('employmentDate').valueAsDate = new Date();
}

function closeAddReaderModal() {
    document.getElementById('addReaderModal').classList.add('hidden');
    document.getElementById('readerName').value = '';
    document.getElementById('readerContact').value = '';
    document.getElementById('readerEmail').value = '';
    document.getElementById('readerArea').value = '';
    document.getElementById('readerActive').checked = true;
}

function submitAddReader() {
    const name = document.getElementById('readerName').value;
    const contact = document.getElementById('readerContact').value;
    const area = document.getElementById('readerArea').value;
    
    if (!name || !contact || !area) {
        showAlert('Please fill all required fields', 'error');
        return;
    }
    
    showAlert('Meter reader added successfully!', 'success');
    closeAddReaderModal();
}

window.openAddReaderModal = openAddReaderModal;
window.closeAddReaderModal = closeAddReaderModal;
window.submitAddReader = submitAddReader;
</script>
