<div id="receiptModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4 no-print">
                <h3 class="text-xl font-semibold text-gray-900">Water Bill Receipt</h3>
                <button onclick="closeReceiptModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div id="receiptContent" class="bg-white">
                <div class="text-center mb-6">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="mx-auto h-20 mb-4">
                    <div class="text-sm text-gray-700">Republic of the Philippines</div>
                    <div class="text-sm text-gray-700">Initao, Misamis Oriental</div>
                    <div class="text-xs text-gray-600 mt-1">As of <span id="receiptDate"></span></div>
                    <div class="text-lg font-bold text-gray-900 mt-3">INITAO MUNICIPALITY WATERWORKS SYSTEM</div>
                </div>

                <hr class="border-gray-300 mb-4">

                <div class="mb-4">
                    <div class="text-sm"><span class="font-semibold">Consumer Name:</span> <span id="consumerName"></span></div>
                    <div class="text-sm mt-1"><span class="font-semibold">Consumer Address:</span> <span id="consumerAddress"></span></div>
                </div>

                <div class="grid grid-cols-5 gap-2 mb-4">
                    <div class="border border-gray-300 p-3 text-center">
                        <div class="text-xs font-semibold text-gray-600 mb-1">Area</div>
                        <div class="text-sm" id="area"></div>
                    </div>
                    <div class="border border-gray-300 p-3 text-center">
                        <div class="text-xs font-semibold text-gray-600 mb-1">Number</div>
                        <div class="text-sm" id="number"></div>
                    </div>
                    <div class="border border-gray-300 p-3 text-center">
                        <div class="text-xs font-semibold text-gray-600 mb-1">Classification</div>
                        <div class="text-sm" id="classification"></div>
                    </div>
                    <div class="border border-gray-300 p-3 text-center">
                        <div class="text-xs font-semibold text-gray-600 mb-1">Reading Date</div>
                        <div class="text-sm" id="readingDate"></div>
                    </div>
                    <div class="border border-gray-300 p-3 text-center">
                        <div class="text-xs font-semibold text-gray-600 mb-1">Due Date</div>
                        <div class="text-sm" id="dueDate"></div>
                    </div>
                </div>

                <div class="border border-gray-300 p-4 mb-4">
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span>Meter Reading This Month:</span>
                            <span id="currentReading" class="font-medium"></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Last Month:</span>
                            <span id="previousReading" class="font-medium"></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Consumption This Month:</span>
                            <span id="consumption" class="font-medium"></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Water Bill This Month:</span>
                            <span>₱<span id="waterBill" class="font-medium"></span></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Arrears:</span>
                            <span>₱<span id="arrears" class="font-medium"></span></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Penalty:</span>
                            <span>₱<span id="penalty" class="font-medium"></span></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Old Account:</span>
                            <span>₱<span id="oldAccount" class="font-medium"></span></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Others:</span>
                            <span>₱<span id="others" class="font-medium"></span></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Unapplied Account:</span>
                            <span>₱<span id="unappliedAccount" class="font-medium"></span></span>
                        </div>
                        <div class="flex justify-between pt-3 border-t border-gray-300 mt-2">
                            <span class="font-bold text-base uppercase">Total Payable Amount:</span>
                            <span>₱<span id="totalAmount" class="font-bold text-base text-blue-600"></span></span>
                        </div>
                    </div>
                </div>

                <div class="border border-gray-300 p-3 bg-gray-50">
                    <p class="text-xs text-gray-700 text-center">
                        This serves as notice of disconnection whenever the above bill is not paid within the specified due date. 
                        A penalty of 10% of the principal amount will be charged.
                    </p>
                </div>

                <hr class="border-gray-300 my-4 no-print">
                
                <div class="flex justify-end no-print">
                    <button onclick="printReceipt()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm">
                        <i class="fas fa-print mr-2"></i>Print Receipt
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    @page {
        margin: 0;
        size: A4;
    }
    
    html, body {
        margin: 0 !important;
        padding: 0 !important;
        height: 100%;
        overflow: hidden;
    }
    
    body * {
        visibility: hidden;
    }
    
    .no-print {
        display: none !important;
    }
    
    #receiptContent, #receiptContent * {
        visibility: visible;
    }
    
    #receiptContent {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        padding: 20mm;
        margin: 0;
        background: white;
        color: black;
    }
    
    #receiptContent .border {
        border: 1.5px solid #000 !important;
    }
    
    #receiptContent .border-gray-300 {
        border-color: #000 !important;
    }
    
    #receiptContent hr {
        border: 1px solid #000 !important;
        margin: 10px 0 !important;
    }
    
    #receiptContent .grid {
        display: grid !important;
    }
    
    #receiptContent .grid-cols-2 {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }
    
    #receiptContent .grid-cols-5 {
        grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
    }
    
    #receiptContent .gap-2 {
        gap: 0.5rem !important;
    }
    
    #receiptContent .gap-4 {
        gap: 1rem !important;
    }
    
    #receiptContent .space-y-2 > * + * {
        margin-top: 0.5rem !important;
    }
    
    #receiptContent .bg-gray-50 {
        background-color: #f9f9f9 !important;
    }
    
    #receiptContent .text-blue-600 {
        color: #000 !important;
    }
}
</style>

<script>
function openReceiptModal(billData) {
    const modal = document.getElementById('receiptModal');
    
    document.getElementById('receiptDate').textContent = new Date().toLocaleDateString();
    document.getElementById('consumerName').textContent = billData.consumerName || 'N/A';
    document.getElementById('consumerAddress').textContent = billData.consumerAddress || 'N/A';
    document.getElementById('area').textContent = billData.area || 'N/A';
    document.getElementById('number').textContent = billData.number || 'N/A';
    document.getElementById('classification').textContent = billData.classification || 'N/A';
    document.getElementById('readingDate').textContent = billData.readingDate || 'N/A';
    document.getElementById('dueDate').textContent = billData.dueDate || 'N/A';
    document.getElementById('currentReading').textContent = billData.currentReading || '0.00';
    document.getElementById('previousReading').textContent = billData.previousReading || '0.00';
    document.getElementById('consumption').textContent = billData.consumption || '0.00 m³';
    document.getElementById('waterBill').textContent = billData.waterBill || '0.00';
    document.getElementById('arrears').textContent = billData.arrears || '0.00';
    document.getElementById('penalty').textContent = billData.penalty || '0.00';
    document.getElementById('oldAccount').textContent = billData.oldAccount || '0.00';
    document.getElementById('others').textContent = billData.others || '0.00';
    document.getElementById('unappliedAccount').textContent = billData.unappliedAccount || '0.00';
    document.getElementById('totalAmount').textContent = billData.totalAmount || '0.00';
    
    modal.classList.remove('hidden');
}

function closeReceiptModal() {
    document.getElementById('receiptModal').classList.add('hidden');
}

function printReceipt() {
    window.print();
}

window.openReceiptModal = openReceiptModal;
window.closeReceiptModal = closeReceiptModal;
window.printReceipt = printReceipt;
</script>
