<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-6 px-4 sm:px-6 lg:px-8" x-data="serviceApplicationWizard()">
        <div class="max-w-4xl mx-auto">
            <!-- Page Header -->
            <x-ui.page-header
                title="New Service Application"
                subtitle="Apply for a new water service connection"
                icon="fas fa-file-alt">
                <x-slot name="actions">
                    <x-ui.button variant="outline" href="{{ route('connection.service-application.index') }}">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Applications
                    </x-ui.button>
                </x-slot>
            </x-ui.page-header>

            <!-- Progress Stepper -->
            <div class="mb-8">
                <div class="flex items-center justify-center">
                    <div class="flex items-center w-full max-w-2xl">
                        <!-- Step 1: Type -->
                        <div class="flex items-center">
                            <div :class="currentStep >= 1 ? 'bg-blue-600 text-white' : 'bg-gray-300 dark:bg-gray-600 text-gray-600 dark:text-gray-400'"
                                 class="w-10 h-10 rounded-full flex items-center justify-center font-semibold transition-colors">
                                <template x-if="currentStep > 1">
                                    <i class="fas fa-check"></i>
                                </template>
                                <template x-if="currentStep <= 1">
                                    <span>1</span>
                                </template>
                            </div>
                            <span class="ml-2 text-sm font-medium" :class="currentStep >= 1 ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400'">Type</span>
                        </div>

                        <!-- Connector -->
                        <div class="flex-1 mx-4 h-1 rounded" :class="currentStep > 1 ? 'bg-blue-600' : 'bg-gray-300 dark:bg-gray-600'"></div>

                        <!-- Step 2: Customer -->
                        <div class="flex items-center">
                            <div :class="currentStep >= 2 ? 'bg-blue-600 text-white' : 'bg-gray-300 dark:bg-gray-600 text-gray-600 dark:text-gray-400'"
                                 class="w-10 h-10 rounded-full flex items-center justify-center font-semibold transition-colors">
                                <template x-if="currentStep > 2">
                                    <i class="fas fa-check"></i>
                                </template>
                                <template x-if="currentStep <= 2">
                                    <span>2</span>
                                </template>
                            </div>
                            <span class="ml-2 text-sm font-medium" :class="currentStep >= 2 ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400'">Customer</span>
                        </div>

                        <!-- Connector -->
                        <div class="flex-1 mx-4 h-1 rounded" :class="currentStep > 2 ? 'bg-blue-600' : 'bg-gray-300 dark:bg-gray-600'"></div>

                        <!-- Step 3: Application -->
                        <div class="flex items-center">
                            <div :class="currentStep >= 3 ? 'bg-blue-600 text-white' : 'bg-gray-300 dark:bg-gray-600 text-gray-600 dark:text-gray-400'"
                                 class="w-10 h-10 rounded-full flex items-center justify-center font-semibold transition-colors">
                                <template x-if="currentStep > 3">
                                    <i class="fas fa-check"></i>
                                </template>
                                <template x-if="currentStep <= 3">
                                    <span>3</span>
                                </template>
                            </div>
                            <span class="ml-2 text-sm font-medium" :class="currentStep >= 3 ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400'">Application</span>
                        </div>

                        <!-- Connector -->
                        <div class="flex-1 mx-4 h-1 rounded" :class="currentStep > 3 ? 'bg-blue-600' : 'bg-gray-300 dark:bg-gray-600'"></div>

                        <!-- Step 4: Review -->
                        <div class="flex items-center">
                            <div :class="currentStep >= 4 ? 'bg-blue-600 text-white' : 'bg-gray-300 dark:bg-gray-600 text-gray-600 dark:text-gray-400'"
                                 class="w-10 h-10 rounded-full flex items-center justify-center font-semibold transition-colors">
                                <span>4</span>
                            </div>
                            <span class="ml-2 text-sm font-medium" :class="currentStep >= 4 ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400'">Review</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 1: Customer Type Toggle -->
            <div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8">
                    <div class="text-center mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Is this a new or existing customer?</h2>
                        <p class="text-gray-500 dark:text-gray-400">Select the appropriate option to continue</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-2xl mx-auto">
                        <!-- New Customer Card -->
                        <div @click="customerType = 'new'"
                             :class="customerType === 'new' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20 ring-2 ring-blue-500' : 'border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-600'"
                             class="cursor-pointer border-2 rounded-xl p-6 transition-all duration-200">
                            <div class="text-center">
                                <div :class="customerType === 'new' ? 'bg-blue-100 dark:bg-blue-800' : 'bg-gray-100 dark:bg-gray-700'"
                                     class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 transition-colors">
                                    <i :class="customerType === 'new' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400'"
                                       class="fas fa-user-plus text-2xl"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">New Customer</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">First time applying for water service</p>
                                <div class="mt-4">
                                    <div :class="customerType === 'new' ? 'border-blue-500 bg-blue-500' : 'border-gray-300 dark:border-gray-600'"
                                         class="w-5 h-5 rounded-full border-2 mx-auto flex items-center justify-center transition-colors">
                                        <div x-show="customerType === 'new'" class="w-2.5 h-2.5 rounded-full bg-white"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Existing Customer Card -->
                        <div @click="customerType = 'existing'"
                             :class="customerType === 'existing' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20 ring-2 ring-blue-500' : 'border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-600'"
                             class="cursor-pointer border-2 rounded-xl p-6 transition-all duration-200">
                            <div class="text-center">
                                <div :class="customerType === 'existing' ? 'bg-blue-100 dark:bg-blue-800' : 'bg-gray-100 dark:bg-gray-700'"
                                     class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 transition-colors">
                                    <i :class="customerType === 'existing' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400'"
                                       class="fas fa-search text-2xl"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Existing Customer</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Already registered, adding new connection</p>
                                <div class="mt-4">
                                    <div :class="customerType === 'existing' ? 'border-blue-500 bg-blue-500' : 'border-gray-300 dark:border-gray-600'"
                                         class="w-5 h-5 rounded-full border-2 mx-auto flex items-center justify-center transition-colors">
                                        <div x-show="customerType === 'existing'" class="w-2.5 h-2.5 rounded-full bg-white"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 2a: New Customer Registration -->
            <div x-show="currentStep === 2 && customerType === 'new'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                <i class="fas fa-user text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">New Customer Registration</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Enter customer's personal information</p>
                            </div>
                        </div>
                        <!-- QR Scanner Button -->
                        <div x-data="qrScanner" @qr-scanned.window="handleQrScanned($event.detail)">
                            <button @click="openScanner()" type="button"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                                <i class="fas fa-qrcode"></i>
                                <span class="hidden sm:inline">Scan National ID</span>
                            </button>

                            <!-- QR Scanner Modal -->
                            <template x-teleport="body">
                                <div x-show="isOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                     x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                     class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
                                     @keydown.escape.window="closeScanner()">
                                    <div @click.outside="closeScanner()"
                                         class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-lg w-full">
                                        <!-- Modal Header -->
                                        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                                                    <i class="fas fa-qrcode text-purple-600 dark:text-purple-400"></i>
                                                </div>
                                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Scan National ID QR Code</h3>
                                            </div>
                                            <button @click="closeScanner()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                                                <i class="fas fa-times text-xl"></i>
                                            </button>
                                        </div>

                                        <!-- Mode Tabs -->
                                        <div class="flex border-b border-gray-200 dark:border-gray-700">
                                            <button @click="switchMode('camera')" type="button"
                                                    :class="mode === 'camera' ? 'border-purple-500 text-purple-600 dark:text-purple-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'"
                                                    class="flex-1 px-4 py-3 text-sm font-medium border-b-2 transition-colors flex items-center justify-center gap-2">
                                                <i class="fas fa-camera"></i> Camera
                                            </button>
                                            <button @click="switchMode('upload')" type="button"
                                                    :class="mode === 'upload' ? 'border-purple-500 text-purple-600 dark:text-purple-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'"
                                                    class="flex-1 px-4 py-3 text-sm font-medium border-b-2 transition-colors flex items-center justify-center gap-2">
                                                <i class="fas fa-upload"></i> Upload Image
                                            </button>
                                        </div>

                                        <!-- Scanner Body -->
                                        <div class="p-4">
                                            <!-- Raw Result Display -->
                                            <div x-show="rawResult" x-transition>
                                                <div class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg mb-3">
                                                    <div class="flex items-center gap-2 mb-2">
                                                        <i class="fas fa-check-circle text-green-500"></i>
                                                        <span class="text-sm font-semibold text-green-700 dark:text-green-300">QR Code Detected</span>
                                                    </div>
                                                    <pre class="text-xs text-gray-800 dark:text-gray-200 bg-white dark:bg-gray-900 rounded p-3 overflow-x-auto whitespace-pre-wrap break-all max-h-48 overflow-y-auto font-mono" x-text="rawResult"></pre>
                                                </div>
                                                <button @click="scanAgain()" type="button"
                                                        class="w-full px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors">
                                                    <i class="fas fa-redo mr-2"></i>Scan Again
                                                </button>
                                            </div>

                                            <!-- Camera Mode -->
                                            <div x-show="mode === 'camera' && !rawResult">
                                                <!-- Scanning Indicator -->
                                                <div x-show="isScanning" class="flex items-center justify-center gap-2 mb-3 py-2 px-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                                                    <span class="relative flex h-3 w-3">
                                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                                        <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-500"></span>
                                                    </span>
                                                    <span class="text-sm text-blue-700 dark:text-blue-300 font-medium">Scanning for QR code...</span>
                                                </div>
                                                <div id="qr-reader" class="w-full rounded-lg overflow-hidden"></div>
                                            </div>

                                            <!-- Upload Mode -->
                                            <div x-show="mode === 'upload' && !rawResult">
                                                <div id="qr-upload-region" class="hidden"></div>
                                                <label class="flex flex-col items-center justify-center w-full h-48 border-2 border-dashed rounded-lg cursor-pointer transition-colors"
                                                       :class="isProcessingFile ? 'border-purple-300 bg-purple-50 dark:bg-purple-900/10' : 'border-gray-300 dark:border-gray-600 hover:border-purple-400 dark:hover:border-purple-500 bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700'">
                                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                        <template x-if="!isProcessingFile">
                                                            <div class="text-center">
                                                                <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 dark:text-gray-500 mb-3"></i>
                                                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">
                                                                    <span class="font-semibold text-purple-600 dark:text-purple-400">Click to upload</span> a photo of the QR code
                                                                </p>
                                                                <p class="text-xs text-gray-400 dark:text-gray-500">PNG, JPG or JPEG</p>
                                                            </div>
                                                        </template>
                                                        <template x-if="isProcessingFile">
                                                            <div class="text-center">
                                                                <i class="fas fa-spinner fa-spin text-3xl text-purple-500 mb-3"></i>
                                                                <p class="text-sm text-purple-600 dark:text-purple-400">Scanning QR code...</p>
                                                            </div>
                                                        </template>
                                                    </div>
                                                    <input type="file" class="hidden" accept="image/*" @change="handleFileUpload($event)" :disabled="isProcessingFile">
                                                </label>
                                            </div>

                                            <!-- Error Message -->
                                            <div x-show="error" x-transition
                                                 class="mt-3 p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
                                                <div class="flex items-center gap-2">
                                                    <i class="fas fa-exclamation-triangle text-amber-500"></i>
                                                    <p class="text-sm text-amber-700 dark:text-amber-300" x-text="error"></p>
                                                </div>
                                            </div>

                                            <!-- Instructions -->
                                            <p x-show="!rawResult" class="mt-3 text-sm text-gray-500 dark:text-gray-400 text-center">
                                                <i class="fas fa-info-circle mr-1"></i>
                                                <span x-show="mode === 'camera'">Hold the National ID QR code in front of the camera</span>
                                                <span x-show="mode === 'upload'">Upload a clear photo of the National ID QR code</span>
                                            </p>
                                        </div>

                                        <!-- Modal Footer -->
                                        <div class="flex justify-end p-4 border-t border-gray-200 dark:border-gray-700">
                                            <button @click="closeScanner()" type="button"
                                                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-colors">
                                                Cancel
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Name Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                First Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" x-model="customerForm.firstName" required placeholder="Juan"
                                   class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Middle Name</label>
                            <input type="text" x-model="customerForm.middleName" placeholder="Santos"
                                   class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Last Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" x-model="customerForm.lastName" required placeholder="Dela Cruz"
                                   class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <!-- Suffix -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Suffix <span class="text-gray-400 text-xs">(Optional)</span>
                            </label>
                            <select x-model="customerForm.suffix"
                                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">None</option>
                                <option value="JR.">Jr.</option>
                                <option value="SR.">Sr.</option>
                                <option value="II">II</option>
                                <option value="III">III</option>
                                <option value="IV">IV</option>
                                <option value="V">V</option>
                                <option value="OTHER">Other</option>
                            </select>
                            <template x-if="customerForm.suffix === 'OTHER'">
                                <input type="text" x-model="customerForm.customSuffix" placeholder="Enter suffix"
                                       class="mt-2 w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white uppercase focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       maxlength="10">
                            </template>
                        </div>
                    </div>

                    <!-- Contact & Type -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Phone Number <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" x-model="customerForm.phone" required placeholder="09XX XXX XXXX" pattern="[0-9]{11}"
                                   class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">For SMS notifications</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Customer Type <span class="text-red-500">*</span>
                            </label>
                            <select x-model="customerForm.customerType" required
                                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select Type</option>
                                <option value="RESIDENTIAL">Residential</option>
                                <option value="COMMERCIAL">Commercial</option>
                                <option value="INDUSTRIAL">Industrial</option>
                            </select>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-gray-200 dark:border-gray-700 my-6"></div>

                    <!-- ID Section Header -->
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                            <i class="fas fa-id-card text-purple-600 dark:text-purple-400 text-sm"></i>
                        </div>
                        <div>
                            <h4 class="text-md font-semibold text-gray-900 dark:text-white">Identification</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Valid government-issued ID</p>
                        </div>
                    </div>

                    <!-- ID Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                ID Type <span class="text-red-500">*</span>
                            </label>
                            <select x-model="customerForm.idType" required
                                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select ID Type</option>
                                <option value="National ID">National ID</option>
                                <option value="Driver's License">Driver's License</option>
                                <option value="Passport">Passport</option>
                                <option value="SSS">SSS ID</option>
                                <option value="PhilHealth">PhilHealth ID</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                ID Number <span class="text-red-500">*</span>
                            </label>
                            <input type="text" x-model="customerForm.idNumber" required placeholder="Enter ID number"
                                   class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-gray-200 dark:border-gray-700 my-6"></div>

                    <!-- Customer Address Section Header -->
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-8 h-8 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                            <i class="fas fa-home text-green-600 dark:text-green-400 text-sm"></i>
                        </div>
                        <div>
                            <h4 class="text-md font-semibold text-gray-900 dark:text-white">Home Address</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Customer's residential/contact address</p>
                        </div>
                    </div>

                    <!-- Customer Address Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Barangay <span class="text-red-500">*</span>
                            </label>
                            <select x-model="customerForm.barangay" @change="onCustomerBarangayChange()" required
                                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select Barangay</option>
                                <template x-for="brgy in barangays" :key="brgy.b_id">
                                    <option :value="brgy.b_id" x-text="brgy.b_desc"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Purok <span class="text-red-500">*</span>
                            </label>
                            <select x-model="customerForm.purok" required :disabled="!customerForm.barangay"
                                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:opacity-50 disabled:cursor-not-allowed">
                                <option value="">Select Purok</option>
                                <template x-for="purok in customerPuroks" :key="purok.p_id">
                                    <option :value="purok.p_id" x-text="purok.p_desc"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Landmark
                            </label>
                            <input type="text" x-model="customerForm.landmark" placeholder="Near church, beside store, etc."
                                   class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>

                    <!-- Same as Service Address Checkbox -->
                    <div class="flex items-center gap-2 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                        <input type="checkbox" x-model="sameAsServiceAddress" id="sameAddress"
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="sameAddress" class="text-sm text-blue-800 dark:text-blue-200">
                            <i class="fas fa-info-circle mr-1"></i>
                            Service location is the same as home address (will auto-fill in next step)
                        </label>
                    </div>
                </div>
            </div>

            <!-- Step 2b: Existing Customer Search -->
            <div x-show="currentStep === 2 && customerType === 'existing'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                            <i class="fas fa-search text-green-600 dark:text-green-400"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Find Existing Customer</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Search by name, phone, or ID number</p>
                        </div>
                    </div>

                    <!-- Search Box -->
                    <div class="mb-6">
                        <div class="relative">
                            <input type="text" x-model="searchQuery" @input.debounce.300ms="searchCustomers()"
                                   placeholder="Type customer name, phone, or ID..."
                                   class="w-full pl-12 pr-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <div x-show="isSearching" class="absolute inset-y-0 right-0 pr-4 flex items-center">
                                <i class="fas fa-spinner fa-spin text-blue-500"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Search Results -->
                    <div x-show="searchResults.length > 0" class="space-y-3">
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                            <span x-text="searchResults.length"></span> customer(s) found
                        </p>
                        <template x-for="customer in searchResults" :key="customer.id">
                            <div @click="selectCustomer(customer)"
                                 :class="selectedCustomer?.id === customer.id ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20 ring-2 ring-blue-500' : 'border-gray-200 dark:border-gray-700 hover:border-blue-300'"
                                 class="cursor-pointer border rounded-lg p-4 transition-all">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center gap-3">
                                        <div :class="selectedCustomer?.id === customer.id ? 'border-blue-500 bg-blue-500' : 'border-gray-300 dark:border-gray-600'"
                                             class="w-5 h-5 rounded-full border-2 flex items-center justify-center flex-shrink-0 transition-colors">
                                            <div x-show="selectedCustomer?.id === customer.id" class="w-2.5 h-2.5 rounded-full bg-white"></div>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-900 dark:text-white" x-text="customer.fullName"></h4>
                                            <div class="flex items-center gap-4 mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                <span><i class="fas fa-phone mr-1"></i> <span x-text="customer.phone"></span></span>
                                                <span class="px-2 py-0.5 rounded-full text-xs font-medium"
                                                      :class="customer.type === 'RESIDENTIAL' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400'"
                                                      x-text="customer.type"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            <i class="fas fa-plug mr-1"></i>
                                            <span x-text="customer.connectionsCount"></span> connection(s)
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- No Results -->
                    <div x-show="searchQuery.length >= 2 && searchResults.length === 0 && !isSearching"
                         class="text-center py-8">
                        <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-user-slash text-gray-400 text-2xl"></i>
                        </div>
                        <p class="text-gray-500 dark:text-gray-400 mb-4">No customers found matching "<span x-text="searchQuery"></span>"</p>
                        <button @click="customerType = 'new'; searchQuery = ''; searchResults = []"
                                class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                            <i class="fas fa-arrow-left mr-1"></i> Go back and select "New Customer"
                        </button>
                    </div>

                    <!-- Initial State -->
                    <div x-show="searchQuery.length < 2 && searchResults.length === 0" class="text-center py-8">
                        <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-search text-gray-400 text-2xl"></i>
                        </div>
                        <p class="text-gray-500 dark:text-gray-400">Start typing to search for customers</p>
                    </div>
                </div>
            </div>

            <!-- Step 3: Service Application -->
            <div x-show="currentStep === 3" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <!-- Customer Banner -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-800 flex items-center justify-center">
                                <i class="fas fa-user text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <div>
                                <p class="text-xs text-blue-600 dark:text-blue-400 font-medium">Applying for:</p>
                                <p class="font-semibold text-blue-900 dark:text-blue-100" x-text="customerDisplayName"></p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                            <i class="fas fa-map-marker-alt text-green-600 dark:text-green-400"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Service Address</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Where the water service will be installed</p>
                        </div>
                    </div>

                    <!-- Address Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Barangay <span class="text-red-500">*</span>
                            </label>
                            <select x-model="applicationForm.barangay" @change="onBarangayChange()" required
                                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select Barangay</option>
                                <template x-for="brgy in barangays" :key="brgy.b_id">
                                    <option :value="brgy.b_id" x-text="brgy.b_desc"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Purok <span class="text-red-500">*</span>
                            </label>
                            <select x-model="applicationForm.purok" required :disabled="!applicationForm.barangay"
                                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:opacity-50 disabled:cursor-not-allowed">
                                <option value="">Select Purok</option>
                                <template x-for="purok in filteredPuroks" :key="purok.p_id">
                                    <option :value="purok.p_id" x-text="purok.p_desc"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Landmark <span class="text-red-500">*</span>
                            </label>
                            <input type="text" x-model="applicationForm.landmark" required placeholder="Near church, beside store, etc."
                                   class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>

                </div>
            </div>

            <!-- Step 4: Review & Submit -->
            <div x-show="currentStep === 4" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                            <i class="fas fa-clipboard-check text-green-600 dark:text-green-400"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Review Application</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Please verify all information before submitting</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <!-- Customer Info Summary -->
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                            <div class="flex items-center gap-2 mb-3">
                                <i class="fas fa-user text-blue-500"></i>
                                <h4 class="font-semibold text-gray-900 dark:text-white">Customer Information</h4>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">Name:</span>
                                    <span class="ml-2 font-medium text-gray-900 dark:text-white" x-text="customerDisplayName"></span>
                                </div>
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">Phone:</span>
                                    <span class="ml-2 font-medium text-gray-900 dark:text-white" x-text="customerType === 'new' ? customerForm.phone : selectedCustomer?.phone"></span>
                                </div>
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">Type:</span>
                                    <span class="ml-2 font-medium text-gray-900 dark:text-white" x-text="customerType === 'new' ? customerForm.customerType : selectedCustomer?.type"></span>
                                </div>
                                <div x-show="customerType === 'new'">
                                    <span class="text-gray-500 dark:text-gray-400">ID:</span>
                                    <span class="ml-2 font-medium text-gray-900 dark:text-white" x-text="customerForm.idType + ' - ' + customerForm.idNumber"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Customer Home Address Summary (for new customers only) -->
                        <div x-show="customerType === 'new'" class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                            <div class="flex items-center gap-2 mb-3">
                                <i class="fas fa-home text-purple-500"></i>
                                <h4 class="font-semibold text-gray-900 dark:text-white">Home Address</h4>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">Address:</span>
                                    <span class="ml-2 font-medium text-gray-900 dark:text-white" x-text="getCustomerAddressText()"></span>
                                </div>
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">Landmark:</span>
                                    <span class="ml-2 font-medium text-gray-900 dark:text-white" x-text="customerForm.landmark || 'N/A'"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Service Location Summary -->
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                            <div class="flex items-center gap-2 mb-3">
                                <i class="fas fa-map-marker-alt text-green-500"></i>
                                <h4 class="font-semibold text-gray-900 dark:text-white">Service Location</h4>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">Address:</span>
                                    <span class="ml-2 font-medium text-gray-900 dark:text-white" x-text="getFullAddressText()"></span>
                                </div>
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">Landmark:</span>
                                    <span class="ml-2 font-medium text-gray-900 dark:text-white" x-text="applicationForm.landmark"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Application Fees Summary -->
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                            <div class="flex items-center gap-2 mb-3">
                                <i class="fas fa-peso-sign text-amber-500"></i>
                                <h4 class="font-semibold text-gray-900 dark:text-white">Application Fees</h4>
                            </div>
                            <div class="space-y-2 text-sm">
                                <!-- Loading state -->
                                <template x-if="isLoadingFees">
                                    <div class="text-center py-2">
                                        <i class="fas fa-spinner fa-spin text-gray-400"></i>
                                        <span class="text-gray-500 dark:text-gray-400 ml-2">Loading fees...</span>
                                    </div>
                                </template>
                                <!-- Fee items -->
                                <template x-if="!isLoadingFees">
                                    <div>
                                        <template x-for="fee in feeTemplates.items" :key="fee.name">
                                            <div class="flex justify-between py-1">
                                                <span class="text-gray-500 dark:text-gray-400" x-text="fee.name + ':'"></span>
                                                <span class="font-medium text-gray-900 dark:text-white" x-text="formatCurrency(fee.amount)"></span>
                                            </div>
                                        </template>
                                        <div class="border-t border-gray-200 dark:border-gray-600 pt-2 mt-2 flex justify-between">
                                            <span class="font-semibold text-gray-900 dark:text-white">Total Due:</span>
                                            <span class="font-bold text-blue-600 dark:text-blue-400" x-text="formatCurrency(feeTemplates.total)"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Buttons -->
            <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        <i class="fas fa-info-circle mr-1"></i>
                        <span x-show="currentStep < 4">Fields marked with <span class="text-red-500">*</span> are required</span>
                        <span x-show="currentStep === 4">Please review all information before submitting</span>
                    </div>
                    <div class="flex gap-3">
                        <button x-show="currentStep > 1" @click="prevStep()" type="button"
                                class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>Back
                        </button>
                        <button x-show="currentStep === 1" @click="window.history.back()" type="button"
                                class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-colors">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </button>
                        <button x-show="currentStep < 4" @click="nextStep()" type="button"
                                :disabled="!canProceed()"
                                :class="canProceed() ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-400 cursor-not-allowed'"
                                class="px-5 py-2.5 text-white rounded-lg font-medium transition-colors">
                            Continue<i class="fas fa-arrow-right ml-2"></i>
                        </button>
                        <button x-show="currentStep === 4" @click="submitApplication()" type="button"
                                :disabled="isSubmitting"
                                class="px-5 py-2.5 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white rounded-lg font-medium transition-colors">
                            <template x-if="!isSubmitting">
                                <span><i class="fas fa-check mr-2"></i>Submit Application</span>
                            </template>
                            <template x-if="isSubmitting">
                                <span><i class="fas fa-spinner fa-spin mr-2"></i>Submitting...</span>
                            </template>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Modal -->
        <div x-show="showSuccessModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full p-8">
                <div class="text-center">
                    <!-- Success Icon -->
                    <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-100 dark:bg-green-900 mb-6">
                        <i class="fas fa-check text-green-600 dark:text-green-400 text-4xl"></i>
                    </div>

                    <!-- Success Message -->
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Application Submitted</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-2">Your service application has been successfully submitted.</p>

                    <!-- Application Number -->
                    <div class="inline-block bg-gray-100 dark:bg-gray-700 rounded-lg px-4 py-2 mb-6">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Application Number</span>
                        <p class="font-mono font-bold text-lg text-gray-900 dark:text-white" x-text="submissionResult.applicationNumber || '-'"></p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button @click="resetForm()"
                                class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                            <i class="fas fa-plus mr-2"></i>Create Another
                        </button>
                        <a href="{{ route('connection.service-application.index') }}"
                           class="flex-1 px-6 py-3 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-colors text-center">
                            <i class="fas fa-list mr-2"></i>View Applications
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div id="toastContainer" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <script>
        function serviceApplicationWizard() {
            return {
                currentStep: 1,
                customerType: null,

                // New customer form (includes home address)
                customerForm: {
                    firstName: '',
                    middleName: '',
                    lastName: '',
                    suffix: '',
                    customSuffix: '',
                    phone: '',
                    customerType: '',
                    idType: '',
                    idNumber: '',
                    // Customer home address
                    barangay: '',
                    purok: '',
                    landmark: ''
                },

                // Customer address puroks (separate from service address puroks)
                customerPuroks: [],

                // Same address checkbox
                sameAsServiceAddress: true,

                // Existing customer search
                searchQuery: '',
                searchResults: [],
                selectedCustomer: null,
                isSearching: false,

                // Service application
                applicationForm: {
                    barangay: '',
                    purok: '',
                    landmark: ''
                },

                // Data
                barangays: [],
                puroks: [],

                // UI state
                isSubmitting: false,
                showSuccessModal: false,
                submissionResult: {},

                // Fee templates from database
                feeTemplates: { items: [], total: 0 },
                isLoadingFees: true,

                async init() {
                    // Load barangays and fee templates in parallel
                    await Promise.all([
                        this.loadBarangays(),
                        this.loadFeeTemplates()
                    ]);
                },

                async loadFeeTemplates() {
                    this.isLoadingFees = true;
                    try {
                        const response = await fetch('/api/service-application/fee-templates');
                        const result = await response.json();
                        if (result.success) {
                            this.feeTemplates = result.data;
                        }
                    } catch (error) {
                        console.error('Failed to load fee templates:', error);
                    } finally {
                        this.isLoadingFees = false;
                    }
                },

                async loadBarangays() {
                    try {
                        const response = await fetch('/api/address/barangays');
                        const data = await response.json();
                        this.barangays = data;
                    } catch (error) {
                        console.error('Failed to load barangays:', error);
                        this.showToast('Failed to load barangays', 'error');
                    }
                },

                async loadPuroksByBarangay(barangayId) {
                    try {
                        const response = await fetch(`/api/address/puroks?barangay_id=${barangayId}`);
                        const data = await response.json();
                        this.puroks = data;
                    } catch (error) {
                        console.error('Failed to load puroks:', error);
                        this.showToast('Failed to load puroks', 'error');
                    }
                },

                async loadCustomerPuroksByBarangay(barangayId) {
                    try {
                        const response = await fetch(`/api/address/puroks?barangay_id=${barangayId}`);
                        const data = await response.json();
                        this.customerPuroks = data;
                    } catch (error) {
                        console.error('Failed to load customer puroks:', error);
                        this.showToast('Failed to load puroks', 'error');
                    }
                },

                async onCustomerBarangayChange() {
                    this.customerForm.purok = '';
                    this.customerPuroks = [];

                    if (this.customerForm.barangay) {
                        await this.loadCustomerPuroksByBarangay(this.customerForm.barangay);
                    }
                },

                get filteredPuroks() {
                    // Puroks are now loaded per barangay, so just return all loaded puroks
                    return this.puroks;
                },

                get customerDisplayName() {
                    if (this.customerType === 'new') {
                        const suffix = this.customerForm.suffix === 'OTHER' ? this.customerForm.customSuffix : this.customerForm.suffix;
                        const name = [
                            this.customerForm.firstName,
                            this.customerForm.middleName,
                            this.customerForm.lastName,
                            suffix
                        ].filter(Boolean).join(' ');
                        return name.toUpperCase() || 'New Customer';
                    }
                    return this.selectedCustomer?.fullName || 'Selected Customer';
                },

                canProceed() {
                    switch (this.currentStep) {
                        case 1:
                            return this.customerType !== null;
                        case 2:
                            if (this.customerType === 'new') {
                                return this.customerForm.firstName &&
                                       this.customerForm.lastName &&
                                       this.customerForm.phone &&
                                       this.customerForm.customerType &&
                                       this.customerForm.idType &&
                                       this.customerForm.idNumber &&
                                       this.customerForm.barangay &&
                                       this.customerForm.purok;
                            } else {
                                return this.selectedCustomer !== null;
                            }
                        case 3:
                            return this.applicationForm.barangay &&
                                   this.applicationForm.purok &&
                                   this.applicationForm.landmark;
                        default:
                            return true;
                    }
                },

                async nextStep() {
                    if (this.canProceed() && this.currentStep < 4) {
                        // If moving from step 2 to step 3 and "same as home address" is checked
                        if (this.currentStep === 2 && this.sameAsServiceAddress && this.customerType === 'new') {
                            // Auto-fill service address from customer address
                            this.applicationForm.barangay = this.customerForm.barangay;
                            this.applicationForm.landmark = this.customerForm.landmark;

                            // Load puroks FIRST (before setting purok value)
                            if (this.applicationForm.barangay) {
                                await this.loadPuroksByBarangay(this.applicationForm.barangay);
                            }

                            // THEN set purok value (after options are loaded)
                            this.applicationForm.purok = this.customerForm.purok;
                        }
                        this.currentStep++;
                    }
                },

                prevStep() {
                    if (this.currentStep > 1) {
                        this.currentStep--;
                    }
                },

                async onBarangayChange() {
                    this.applicationForm.purok = '';
                    this.puroks = [];

                    if (this.applicationForm.barangay) {
                        await this.loadPuroksByBarangay(this.applicationForm.barangay);
                    }
                },

                async searchCustomers() {
                    if (this.searchQuery.length < 2) {
                        this.searchResults = [];
                        return;
                    }

                    this.isSearching = true;

                    try {
                        const response = await fetch(`/api/customers/search?q=${encodeURIComponent(this.searchQuery)}`);
                        const data = await response.json();

                        if (Array.isArray(data)) {
                            this.searchResults = data;
                        } else if (data.success === false) {
                            throw new Error(data.message || 'Search failed');
                        } else {
                            this.searchResults = [];
                        }
                    } catch (error) {
                        console.error('Search failed:', error);
                        this.showToast('Search failed. Please try again.', 'error');
                        this.searchResults = [];
                    } finally {
                        this.isSearching = false;
                    }
                },

                selectCustomer(customer) {
                    this.selectedCustomer = customer;
                },

                getFullAddressText() {
                    const barangay = this.barangays.find(b => b.b_id == this.applicationForm.barangay);
                    const purok = this.puroks.find(p => p.p_id == this.applicationForm.purok);

                    const parts = [];
                    if (purok) parts.push(purok.p_desc);
                    if (barangay) parts.push(barangay.b_desc);
                    parts.push('Initao, Misamis Oriental');

                    return parts.join(', ');
                },

                getCustomerAddressText() {
                    const barangay = this.barangays.find(b => b.b_id == this.customerForm.barangay);
                    const purok = this.customerPuroks.find(p => p.p_id == this.customerForm.purok);

                    const parts = [];
                    if (purok) parts.push(purok.p_desc);
                    if (barangay) parts.push(barangay.b_desc);
                    parts.push('Initao, Misamis Oriental');

                    return parts.join(', ');
                },

                async submitApplication() {
                    this.isSubmitting = true;

                    try {
                        // Resolve suffix (if "OTHER", use custom value)
                        const customerData = this.customerType === 'new'
                            ? { ...this.customerForm, suffix: this.customerForm.suffix === 'OTHER' ? this.customerForm.customSuffix : this.customerForm.suffix }
                            : { id: this.selectedCustomer.id };

                        // Prepare data
                        const data = {
                            customerType: this.customerType,
                            customer: customerData,
                            application: this.applicationForm
                        };

                        const response = await fetch('/connection/service-application', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(data)
                        });

                        const result = await response.json();

                        if (result.success) {
                            this.submissionResult = {
                                applicationNumber: result.data?.applicationNumber || 'APP-' + Date.now(),
                                applicationId: result.data?.application?.application_id,
                                charges: result.data?.charges || [],
                                totalAmount: result.data?.total_amount || 0
                            };
                            this.showSuccessModal = true;
                        } else {
                            throw new Error(result.message || 'Submission failed');
                        }
                    } catch (error) {
                        console.error('Submission failed:', error);
                        this.showToast(error.message || 'Submission failed. Please try again.', 'error');
                    } finally {
                        this.isSubmitting = false;
                    }
                },

                formatCurrency(amount) {
                    return new Intl.NumberFormat('en-PH', {
                        style: 'currency',
                        currency: 'PHP'
                    }).format(amount || 0);
                },

                handleQrScanned(data) {
                    if (!data) return;

                    // Auto-fill name fields
                    if (data.firstName) this.customerForm.firstName = data.firstName.toUpperCase();
                    if (data.middleName) this.customerForm.middleName = data.middleName.toUpperCase();
                    if (data.lastName) this.customerForm.lastName = data.lastName.toUpperCase();

                    // Handle suffix - check if it matches a dropdown value, otherwise use OTHER
                    if (data.suffix) {
                        const normalizedSuffix = data.suffix.toUpperCase().replace('.', '');
                        const validSuffixes = ['JR.', 'SR.', 'II', 'III', 'IV', 'V'];
                        const matchedSuffix = validSuffixes.find(s => s.replace('.', '') === normalizedSuffix);

                        if (matchedSuffix) {
                            this.customerForm.suffix = matchedSuffix;
                            this.customerForm.customSuffix = '';
                        } else {
                            this.customerForm.suffix = 'OTHER';
                            this.customerForm.customSuffix = data.suffix.toUpperCase();
                        }
                    }

                    // Auto-fill ID fields
                    this.customerForm.idType = 'National ID';
                    if (data.idNumber) this.customerForm.idNumber = data.idNumber;

                    // Show success toast
                    this.showToast('National ID scanned successfully!', 'success');

                    // Brief highlight effect on filled fields
                    this.$nextTick(() => {
                        document.querySelectorAll('[x-model^="customerForm.firstName"], [x-model^="customerForm.middleName"], [x-model^="customerForm.lastName"], [x-model^="customerForm.idNumber"], [x-model^="customerForm.idType"]').forEach(el => {
                            el.classList.add('ring-2', 'ring-green-500');
                            setTimeout(() => el.classList.remove('ring-2', 'ring-green-500'), 2000);
                        });
                    });
                },

                resetForm() {
                    this.currentStep = 1;
                    this.customerType = null;
                    this.customerForm = {
                        firstName: '', middleName: '', lastName: '',
                        suffix: '', customSuffix: '',
                        phone: '', customerType: '', idType: '', idNumber: '',
                        barangay: '', purok: '', landmark: ''
                    };
                    this.customerPuroks = [];
                    this.sameAsServiceAddress = false;
                    this.searchQuery = '';
                    this.searchResults = [];
                    this.selectedCustomer = null;
                    this.applicationForm = {
                        barangay: '', purok: '', landmark: ''
                    };
                    this.puroks = [];
                    this.showSuccessModal = false;
                    this.submissionResult = {};
                },

                showToast(message, type = 'info') {
                    const container = document.getElementById('toastContainer');
                    const toast = document.createElement('div');
                    const bgColors = {
                        success: 'bg-green-500',
                        error: 'bg-red-500',
                        info: 'bg-blue-500',
                        warning: 'bg-amber-500'
                    };

                    toast.className = `${bgColors[type]} text-white px-6 py-3 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full`;
                    toast.innerHTML = `<i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'times' : 'info'}-circle mr-2"></i>${message}`;

                    container.appendChild(toast);

                    setTimeout(() => toast.classList.remove('translate-x-full'), 10);
                    setTimeout(() => {
                        toast.classList.add('translate-x-full');
                        setTimeout(() => toast.remove(), 300);
                    }, 3000);
                }
            };
        }
    </script>
</x-app-layout>
