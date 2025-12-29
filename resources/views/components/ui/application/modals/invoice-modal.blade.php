<div id="invoiceViewModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4 no-print">
                <h3 class="text-xl font-semibold text-gray-900">Invoice Details</h3>
                <button onclick="closeInvoiceViewModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div id="invoiceContent" class="bg-white">
                <div class="text-center mb-6">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="mx-auto h-20 mb-4">
                    <div class="text-sm text-gray-700">Republic of the Philippines</div>
                    <div class="text-sm text-gray-700">Initao, Misamis Oriental</div>
                    <div class="text-xs text-gray-600 mt-1">Date: <span id="invoicePrintDate"></span></div>
                    <div class="text-lg font-bold text-gray-900 mt-3">INITAO MUNICIPALITY WATERWORKS SYSTEM</div>
                    <div class="text-md font-bold text-gray-900 mt-1">SERVICE INVOICE</div>
                </div>

                <hr class="border-gray-300 mb-4">

                <div class="mb-4">
                    <div class="text-sm"><span class="font-semibold">Customer Name:</span> <span id="invoiceCustomerName"></span></div>
                    <div class="text-sm mt-1"><span class="font-semibold">Customer Code:</span> <span id="invoiceCustomerCode"></span></div>
                    <div class="text-sm mt-1"><span class="font-semibold">Invoice No:</span> <span id="invoiceNumber"></span></div>
                </div>

                <div class="grid grid-cols-3 gap-2 mb-4">
                    <div class="border border-gray-300 p-3 text-center">
                        <div class="text-xs font-semibold text-gray-600 mb-1">Invoice Date</div>
                        <div class="text-sm" id="invoiceDate"></div>
                    </div>
                    <div class="border border-gray-300 p-3 text-center">
                        <div class="text-xs font-semibold text-gray-600 mb-1">Due Date</div>
                        <div class="text-sm" id="invoiceDueDate"></div>
                    </div>
                    <div class="border border-gray-300 p-3 text-center">
                        <div class="text-xs font-semibold text-gray-600 mb-1">Status</div>
                        <div class="text-sm" id="invoiceStatus"></div>
                    </div>
                </div>

                <div class="border border-gray-300 p-4 mb-4">
                    <div class="space-y-2 text-sm" id="invoiceItemsList">
                        <!-- Items will be injected here -->
                    </div>
                    
                    <div class="flex justify-between pt-3 border-t border-gray-300 mt-4">
                        <span class="font-bold text-base uppercase">Total Payable Amount:</span>
                        <span>₱<span id="invoiceTotalAmount" class="font-bold text-base text-blue-600"></span></span>
                    </div>
                </div>

                <div class="border border-gray-300 p-3 bg-gray-50">
                    <p class="text-xs text-gray-700 text-center">
                        This serves as an official invoice for water service connection and related charges.
                        Please pay before the due date to avoid delays.
                    </p>
                </div>

                <hr class="border-gray-300 my-4 no-print">
                
                <div class="flex justify-end no-print">
                    <button onclick="printInvoice()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm">
                        <i class="fas fa-print mr-2"></i>Print Invoice
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
}
</style>
