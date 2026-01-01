<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div @click="paymentType = 'application'; window.paymentManager.setPaymentType('application')"
        :class="{'bg-green-50 border-green-500 ring-2 ring-green-500/50': paymentType === 'application', 'bg-white border-gray-200 hover:border-green-300': paymentType !== 'application'}"
        class="p-4 border-2 rounded-xl flex items-center gap-4 cursor-pointer transition-all duration-200 dark:bg-gray-800 dark:border-gray-700">
        <div :class="{'bg-green-500 text-white shadow-lg shadow-green-500/30': paymentType === 'application', 'bg-gray-100 text-gray-500 dark:bg-gray-700': paymentType !== 'application'}" 
             class="w-12 h-12 rounded-xl flex items-center justify-center transition-all duration-200">
            <i class="fas fa-money-bill-wave text-lg"></i>
        </div>
        <div>
            <h3 :class="{'text-green-800 dark:text-green-400': paymentType === 'application', 'text-gray-900 dark:text-white': paymentType !== 'application'}" class="font-bold text-lg">Application Payment</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Process fees and charges</p>
        </div>
        <div x-show="paymentType === 'application'" class="ml-auto text-green-600">
            <i class="fas fa-check-circle text-xl"></i>
        </div>
    </div>

    <div @click="paymentType = 'document'; window.paymentManager.setPaymentType('document')"
        :class="{'bg-blue-50 border-blue-500 ring-2 ring-blue-500/50': paymentType === 'document', 'bg-white border-gray-200 hover:border-blue-300': paymentType !== 'document'}"
        class="p-4 border-2 rounded-xl flex items-center gap-4 cursor-pointer transition-all duration-200 dark:bg-gray-800 dark:border-gray-700">
        <div :class="{'bg-blue-500 text-white shadow-lg shadow-blue-500/30': paymentType === 'document', 'bg-gray-100 text-gray-500 dark:bg-gray-700': paymentType !== 'document'}" 
             class="w-12 h-12 rounded-xl flex items-center justify-center transition-all duration-200">
            <i class="fas fa-file-alt text-lg"></i>
        </div>
        <div>
            <h3 :class="{'text-blue-800 dark:text-blue-400': paymentType === 'document', 'text-gray-900 dark:text-white': paymentType !== 'document'}" class="font-bold text-lg">Document Processing</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Validate documents only</p>
        </div>
        <div x-show="paymentType === 'document'" class="ml-auto text-blue-600">
            <i class="fas fa-check-circle text-xl"></i>
        </div>
    </div>
</div>

