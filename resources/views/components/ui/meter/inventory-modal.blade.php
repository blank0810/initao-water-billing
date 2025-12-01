<div id="inventoryModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Meter Details</h3>
            <button onclick="closeInventoryModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="inventoryModalContent" class="space-y-3 text-sm">
            <!-- Content populated by JS -->
        </div>
        <div class="flex justify-end gap-3 mt-6">
            <button onclick="closeInventoryModal()" class="px-4 py-2 border rounded-lg">Close</button>
        </div>
    </div>
</div>

<script>
function openInventoryModal(meterId) {
    document.getElementById('inventoryModal').classList.remove('hidden');
}

function closeInventoryModal() {
    document.getElementById('inventoryModal').classList.add('hidden');
}

window.openInventoryModal = openInventoryModal;
window.closeInventoryModal = closeInventoryModal;
</script>
