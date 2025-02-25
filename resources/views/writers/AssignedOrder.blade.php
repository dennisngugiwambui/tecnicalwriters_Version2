@extends('writers.app')
@section('content')

<style>
    /* Center modal styles */
    .modal-container {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 50;
        padding: 1rem;
        background: rgba(0, 0, 0, 0.5); /* Add a background overlay */
    }

    .modal-content {
        width: 100%;
        max-width: 42rem; /* xl */
        max-height: 90vh;
        overflow-y: auto;
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        animation: fadeIn 0.3s ease-out;
    }

    /* Loading spinner */
    .spinner {
        border: 3px solid #f3f3f3;
        border-top: 3px solid #22C55E;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .tab-slider {
        position: absolute;
        bottom: -1px;
        height: 2px;
        background-color: #22C55E;
        transition: all 0.3s ease;
    }

    .description-dropdown {
        max-height: 200px;
        overflow-y: auto;
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        z-index: 50;
    }

    .description-dropdown.show {
        display: block;
    }

    .file-upload-zone {
        border: 2px dashed #e5e7eb;
        transition: all 0.3s ease;
    }

    .file-upload-zone.drag-over {
        border-color: #22C55E;
        background-color: rgba(34, 197, 94, 0.05);
    }

    .copy-tooltip {
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        padding: 0.25rem 0.5rem;
        background-color: #1F2937;
        color: white;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        opacity: 0;
        transition: opacity 0.2s ease;
        pointer-events: none;
    }

    .copy-tooltip.show {
        opacity: 1;
    }

    /* Extension Dialog Styles */
    .extension-dialog {
        backdrop-filter: blur(4px);
    }

    .extension-dialog-content {
        animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
        from {
            transform: translateY(-10px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    /* Upload Modal Styles */
    .upload-modal {
        backdrop-filter: blur(4px);
    }

    .upload-modal-content {
        animation: fadeIn 0.3s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }
</style>


<div class="flex h-full pt-20 px-6 lg:px-8">
    <main class="flex-1 px-4 lg:px-8 pb-8 lg:ml-72 transition-all duration-300 pt-16">
        <!-- Order
        <!-- Order Header -->
        <div class="bg-gradient-to-r from-white to-gray-50 rounded-xl shadow-sm border border-gray-100 mb-6">
            <div class="p-6">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-4">
                        <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center">
                            <i class="fas fa-file-alt text-green-500 text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-semibold text-gray-800">Order #615487328</h1>
                            <div class="flex items-center text-sm text-gray-500">
                                <div class="w-2 h-2 rounded-full bg-green-500 mr-2"></div>
                                Writer Assigned
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-6">
                        <span class="text-2xl font-semibold text-gray-800">$80</span>
                        <div class="flex items-center border-l pl-6">
                            <div class="flex flex-col items-end">
                                <span class="text-sm font-medium text-gray-600">Customer</span>
                                <span class="text-sm text-gray-500">11:35 AM</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <button class="inline-flex items-center text-blue-600 hover:text-blue-700 text-sm font-medium group" onclick="reassignOrder()">
                        <i class="fas fa-sync-alt mr-2 transform group-hover:rotate-180 transition-transform duration-300"></i>
                        Reassign this order
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Content Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <!-- Tab Navigation -->
            <div class="border-b border-gray-100 relative">
                <nav class="flex" role="tablist">
                    <div id="tab-slider" class="tab-slider"></div>
                    
                    <button id="instructions-tab" 
                            class="relative px-6 py-4 text-gray-500 hover:text-gray-700 focus:outline-none" 
                            onclick="switchTab('instructions')" 
                            role="tab"
                            data-width="120">
                        Instructions
                    </button>
                    <button id="files-tab" 
                            class="relative px-6 py-4 text-gray-500 hover:text-gray-700 focus:outline-none flex items-center" 
                            onclick="switchTab('files')" 
                            role="tab"
                            data-width="100">
                        All files
                        <span class="ml-2 bg-green-500 text-white text-xs px-2 py-0.5 rounded-full">3</span>
                    </button>
                    <button id="messages-tab" 
                            class="relative px-6 py-4 text-gray-500 hover:text-gray-700 focus:outline-none" 
                            onclick="switchTab('messages')" 
                            role="tab"
                            data-width="110">
                        Messages
                    </button>
                </nav>
            </div>

            <!-- Content Area -->
            <div class="p-6">
                <!-- Instructions Panel -->
                <div id="instructions-panel" role="tabpanel">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Left Column - Order Information -->
                        <div class="space-y-4">
                            <!-- Deadline Info -->
                            <!-- Add this inside the Deadline Info section -->
                        <div class="p-4 bg-gradient-to-r from-gray-50 to-white rounded-lg border border-gray-100">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Deadline</span>
                                <div class="flex items-center space-x-2">
                                    <span class="text-gray-800">25 Feb, 08:26 AM</span>
                                    <span class="text-green-500">(30 h)</span>
                                    <button 
                                        class="px-3 py-1 bg-green-500 text-white text-sm rounded hover:bg-green-600 transition-colors duration-200"
                                        onclick="showExtensionDialog()">
                                        Extend
                                    </button>
                                </div>
                            </div>
                        </div>

                <!-- Extension Dialog -->
                <div id="extensionDialog" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-800 font-medium">Deadline</span>
                                <span class="text-gray-800">25 Feb, 08:26 AM (30 h)</span>
                            </div>
                            
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Extension time:</label>
                                <div class="relative">
                                    <select class="w-full px-3 py-2 border border-gray-200 rounded-lg appearance-none focus:outline-none focus:border-green-500">
                                        <option value="">--:--</option>
                                        <option value="1">1 hour</option>
                                        <option value="2">2 hours</option>
                                        <option value="3">3 hours</option>
                                        <option value="4">4 hours</option>
                                        <option value="6">6 hours</option>
                                        <option value="12">12 hours</option>
                                        <option value="24">24 hours</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                        <i class="fas fa-chevron-down text-gray-400"></i>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Extension reason comment</label>
                                <textarea 
                                    class="w-full px-3 py-2 border border-gray-200 rounded-lg resize-none focus:outline-none focus:border-green-500"
                                    rows="4"
                                    placeholder="Enter your reason for extension..."></textarea>
                            </div>

                            <div class="text-sm text-gray-600 bg-blue-50 p-3 rounded-lg">
                                Please, update the customer about the progress and specify the reason for the extension request
                            </div>

                            <div class="flex justify-end space-x-3 mt-6">
                                <button 
                                    class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors duration-200"
                                    onclick="hideExtensionDialog()">
                                    Cancel
                                </button>
                                <button 
                                    class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors duration-200">
                                    Extend
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                            <!-- Screening Deadline -->
                            <div class="p-4 bg-gradient-to-r from-gray-50 to-white rounded-lg border border-gray-100">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Screening deadline</span>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-gray-800">22 Feb, 03:40 PM</span>
                                        <span class="text-orange-500">(-32h)</span>
                                        <div class="group relative">
                                            <i class="fas fa-info-circle text-gray-400 cursor-help"></i>
                                            <div class="absolute bottom-full right-0 mb-2 w-48 p-2 bg-gray-800 text-white text-sm rounded-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                                                Time until screening expires
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Task Size -->
                            <div class="p-4 bg-gradient-to-r from-gray-50 to-white rounded-lg border border-gray-100">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Task size</span>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-gray-800">Large</span>
                                        <div class="group relative">
                                            <i class="fas fa-info-circle text-gray-400 cursor-help"></i>
                                            <div class="absolute bottom-full right-0 mb-2 w-48 p-2 bg-gray-800 text-white text-sm rounded-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                                                Large tasks require more time
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Type of Service -->
                            <div class="p-4 bg-gradient-to-r from-gray-50 to-white rounded-lg border border-gray-100">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Type of service</span>
                                    <span class="text-gray-800">Programming</span>
                                </div>
                            </div>

                            <!-- Discipline -->
                            <div class="p-4 bg-gradient-to-r from-gray-50 to-white rounded-lg border border-gray-100">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Discipline</span>
                                    <span class="text-gray-800">Computer science</span>
                                </div>
                            </div>

                            <!-- Programming Language -->
                            <div class="p-4 bg-gradient-to-r from-gray-50 to-white rounded-lg border border-gray-100">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Programming language</span>
                                    <span class="text-gray-800">network design</span>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column - Instructions -->
                        <div class="bg-gray-50 rounded-xl p-6">
                            <div class="flex justify-between items-start mb-6">
                                <h3 class="text-lg font-semibold text-gray-800">Paper instructions</h3>
                                <div class="flex space-x-3">
                                    <button 
                                        class="text-gray-400 hover:text-gray-600 transition-colors duration-200 relative" 
                                        onclick="copyInstructions()">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                    <button class="text-gray-400 hover:text-gray-600 transition-colors duration-200" onclick="expandInstructions()">
                                        <i class="fas fa-expand-alt"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="prose max-w-none">
                                <p class="text-gray-700">
                                    I will attach instructions and physical and logical network design. please add some referencing using Harvard style. I tried to order in the paper section and the support told me to do it in the programming section
                                </p>
                            </div>

                            <!-- Customer Comments -->
                            <div class="mt-6">
                                <div class="bg-cyan-50 rounded-lg p-4 border border-cyan-100">
                                    <h4 class="font-medium text-gray-800 mb-2">Comments from Customer</h4>
                                    <p class="text-gray-700">
                                        This task should be completed with the original code/solutions. Please make sure you do not use any open source solutions.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Files Panel -->
                            <!-- Files Panel -->
                            <!-- Files Panel -->
                <div id="files-panel" class="hidden" role="tabpanel">
                    <!-- Files List -->
                    <div class="space-y-3 bg-white rounded-lg">
                        <!-- Header with Select All and Upload Button -->
                        <div class="flex justify-between items-center mb-6">
                            <div class="flex items-center">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" class="form-checkbox h-4 w-4 text-green-500 rounded border-gray-300" id="selectAllFiles">
                                    <span class="ml-2 text-gray-700">All files</span>
                                </label>
                                <button 
                                    class="ml-4 text-gray-400 hover:text-gray-600 transition-colors bulk-download-btn"
                                    onclick="downloadSelectedFiles()"
                                >
                                    <i class="fas fa-download"></i>
                                </button>
                            </div>
                            
                        </div>

                        <!-- File Items -->
                        <div class="space-y-3">
                            <!-- File 1 -->
                            <div class="flex items-center space-x-4 border border-gray-100 p-4 rounded-lg hover:shadow-md transition duration-200">
                                <input type="checkbox" class="form-checkbox h-4 w-4 text-green-500 rounded border-gray-300">
                                <i class="fas fa-file-pdf text-gray-400"></i>
                                <div class="flex-grow">
                                    <p class="text-sm font-medium text-gray-700">615487328_Introduction_580948953120760.pdf</p>
                                    <p class="text-xs text-gray-500">Instructions / Guidelines</p>
                                    <p class="text-xs text-gray-500">22 Feb, 07:54 AM • 569 KB</p>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <span class="text-sm text-gray-600">Customer</span>
                                    <button class="text-gray-400 hover:text-gray-600 transition-colors">
                                        <i class="fas fa-download"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Upload Button (Mobile) -->
                        <div class="lg:hidden mt-6">
                            <button onclick="showUploadModal()" class="w-full bg-green-500 text-white rounded-lg px-4 py-2 hover:bg-green-600 transition duration-200 flex items-center justify-center space-x-2">
                                <i class="fas fa-upload"></i>
                                <span>Upload files</span>
                            </button>
                        </div>

                        <!-- Upload Button (Desktop) -->
                        <div class="hidden lg:block">
                            <button onclick="showUploadModal()" class="bg-green-500 text-white rounded-lg px-4 py-2 hover:bg-green-600 transition duration-200 flex items-center space-x-2">
                                <i class="fas fa-upload"></i>
                                <span>Upload files</span>
                            </button>
                        </div>

                        <!-- File Conversion Notice -->
                        <div class="mt-6 p-4 bg-yellow-50 rounded-lg text-sm text-gray-600">
                            <p>If you can't open a file (.pages, .numbers, etc.), download it anyway and use one of these links to convert it to .doc, .docx, .xlsx, etc.:</p>
                            <div class="mt-2">
                                <a href="https://cloudconvert.com" class="text-blue-600 hover:underline" target="_blank">cloudconvert.com</a>
                                <span class="mx-2">and</span>
                                <a href="https://online-convert.com" class="text-blue-600 hover:underline" target="_blank">online-convert.com</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upload Modal -->
                <!-- Upload Modal -->
                <!-- Upload Modal with Multi-step Workflow -->
                <div id="uploadModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center hidden z-50 upload-modal">
                    <!-- Backdrop -->
                    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>

                    <!-- Modal Container -->
                    <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
                        <!-- Modal Content -->
                        <div id="uploadModalContent" class="relative bg-white rounded-lg max-w-xl w-full mx-auto shadow-xl transform transition-all">
                            <!-- Step 1: File Selection -->
                            <div id="uploadStep1" class="block">
                                <!-- Modal Header -->
                                <div class="flex justify-between items-center p-6 border-b">
                                    <h3 class="text-lg font-medium text-gray-900">Upload files</h3>
                                    <button onclick="closeUploadModal()" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>

                                <!-- Modal Body -->
                                <div class="p-6">
                                    <p class="text-sm text-gray-600 mb-6">
                                        Please make sure to upload a video preview together with the completed order. Select description "Preview" to your video file. Maximum file size allowed: 99 MB.
                                    </p>

                                    <!-- File List -->
                                    <div id="uploadedFiles" class="space-y-3 mb-4"></div>

                                    <!-- Upload Zone -->
                                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center cursor-pointer hover:border-green-500 transition-colors duration-200"
                                        id="dropZone"
                                        ondrop="handleFileDrop(event)"
                                        ondragover="handleDragOver(event)"
                                        ondragleave="handleDragLeave(event)"
                                        onclick="document.getElementById('fileInput').click()">
                                        <input type="file" id="fileInput" class="hidden" multiple onchange="handleFileSelect(event)">
                                        <button type="button" class="text-green-500 font-medium">Choose file</button>
                                        <span class="text-gray-500 ml-2">or drag file</span>
                                    </div>

                                    <!-- Modal Footer -->
                                    <div class="mt-6 flex justify-end space-x-3">
                                        <button onclick="closeUploadModal()" 
                                                class="px-4 py-2 text-gray-700 hover:text-gray-900 transition-colors duration-200 rounded-md">
                                            Cancel
                                        </button>
                                        <button onclick="gotoVerificationStep()" 
                                                class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors duration-200">
                                            Continue
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 2: Verification Checklist -->
                            <div id="uploadStep2" class="hidden">
                                <!-- Modal Header -->
                                <div class="flex justify-between items-center p-6 border-b">
                                    <h3 class="text-lg font-medium text-gray-900">Upload files</h3>
                                    <button onclick="closeUploadModal()" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>

                                <!-- Modal Body -->
                                <div class="p-6">
                                    <h4 class="text-base font-medium text-gray-700 mb-3">Paper details</h4>
                                    
                                    <div class="space-y-4">
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600">Paper format</span>
                                            <span class="text-gray-800">Not applicable</span>
                                        </div>
                                        
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600">Pages</span>
                                            <div>
                                                <span class="text-gray-800">0 pages</span>
                                                <span class="text-gray-500 text-sm ml-1">(~0 words)</span>
                                                <div class="text-xs text-gray-500">Double spaced</div>
                                            </div>
                                        </div>
                                        
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600">Sources to be cited</span>
                                            <span class="text-gray-800">0</span>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-6">
                                        <h4 class="text-base font-medium text-gray-700 mb-3">To be on the safe side, please, double-check whether:</h4>
                                        <div class="space-y-3">
                                            <label class="flex items-start">
                                                <input type="checkbox" class="form-checkbox h-5 w-5 text-green-500 rounded border-gray-300 mt-0.5">
                                                <span class="ml-2 text-gray-700">All order files are checked</span>
                                            </label>
                                            <label class="flex items-start">
                                                <input type="checkbox" class="form-checkbox h-5 w-5 text-green-500 rounded border-gray-300 mt-0.5">
                                                <span class="ml-2 text-gray-700">All order messages are thoroughly read</span>
                                            </label>
                                            <label class="flex items-start">
                                                <input type="checkbox" class="form-checkbox h-5 w-5 text-green-500 rounded border-gray-300 mt-0.5">
                                                <span class="ml-2 text-gray-700">All paper instructions are followed</span>
                                            </label>
                                            <label class="flex items-start">
                                                <input type="checkbox" class="form-checkbox h-5 w-5 text-green-500 rounded border-gray-300 mt-0.5">
                                                <span class="ml-2 text-gray-700">Number of sources is as requested</span>
                                            </label>
                                            <label class="flex items-start">
                                                <input type="checkbox" class="form-checkbox h-5 w-5 text-green-500 rounded border-gray-300 mt-0.5">
                                                <span class="ml-2 text-gray-700">Required formatting style is applied</span>
                                            </label>
                                        </div>
                                        
                                        <div class="mt-4 p-3 bg-gray-50 text-sm text-gray-600 rounded-lg">
                                            Plagiarism report will be available within 5-10 minutes in the Files section.
                                        </div>
                                    </div>

                                    <!-- Modal Footer -->
                                    <div class="mt-6 flex justify-end space-x-3">
                                        <button onclick="cancelVerification()" 
                                                class="px-4 py-2 text-gray-700 hover:text-gray-900 transition-colors duration-200 rounded-md">
                                            Cancel
                                        </button>
                                        <button onclick="startUpload()" 
                                                class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors duration-200">
                                            Submit
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Processing Modal (Step 3) -->
                <div id="processingModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">
                    <div class="bg-white rounded-lg p-8 max-w-md w-full">
                        <h3 class="text-lg font-medium text-gray-700 text-center mb-6">Processing...</h3>
                        <div class="w-full bg-gray-200 rounded-full h-2.5 mb-4">
                            <div id="uploadProgressBar" class="bg-green-500 h-2.5 rounded-full" style="width: 0%"></div>
                        </div>
                    </div>
                </div>

                <!-- Success Modal (Step 4) -->
                <div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">
                    <div class="bg-white rounded-lg p-8 max-w-md w-full text-center">
                        <div class="w-16 h-16 mx-auto mb-4 bg-green-50 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-medium text-gray-700">Success</h3>
                    </div>
                </div>

                <!-- Toaster Notification -->
                <div id="toaster" class="fixed top-4 right-4 z-50 transform translate-x-full transition-transform duration-300 ease-in-out">
                    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded shadow-lg flex items-start max-w-sm">
                        <div class="text-green-500 mr-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-green-800">Success!</p>
                            <p class="text-sm text-green-700 mt-1">Your files have been uploaded successfully.</p>
                        </div>
                        <button onclick="hideToaster()" class="ml-auto text-green-500 hover:text-green-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <!-- Messages Panel -->
                <!-- Messages Panel -->
                <div id="messages-panel" class="hidden" role="tabpanel">
                    <div class="flex flex-col h-[600px]">
                        <div class="flex-1 overflow-y-auto mb-4 space-y-4">
                            <!-- First Message -->
                            <div class="flex justify-end">
                                <div class="max-w-lg rounded-lg p-4 bg-blue-50">
                                    <p class="text-gray-700">Dear client, I have received the assignment and I am currently going through the instructions. I will update you in case of anything. Thank yo</p>
                                    <div class="mt-2 flex justify-between items-center">
                                        <span class="text-xs text-gray-500">Feb 22 at 8:32 AM</span>
                                        <span class="text-xs text-green-500">Seen</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Second Message -->
                            <div class="flex justify-end">
                                <div class="max-w-lg rounded-lg p-4 bg-blue-50">
                                    <p class="text-gray-700">Dear client, Wanted to update you that everything is clear to me and I have started working on the assignment. I will update you in case of any inquiries</p>
                                    <div class="mt-2 flex justify-between items-center">
                                        <span class="text-xs text-gray-500">Feb 22 at 3:24 PM</span>
                                        <span class="text-xs text-green-500">Seen</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Third Message -->
                            <div class="flex justify-end">
                                <div class="max-w-lg rounded-lg p-4 bg-blue-50">
                                    <p class="text-gray-700">However I would like to inquire if the report have a specific length?</p>
                                    <div class="mt-2 flex justify-between items-center">
                                        <span class="text-xs text-gray-500">Feb 22 at 3:24 PM</span>
                                        <span class="text-xs text-green-500">Seen</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Client Response -->
                            <div class="flex justify-start">
                                <div class="max-w-lg rounded-lg p-4 bg-gray-100">
                                    <div class="flex items-center mb-2">
                                        <span class="font-medium text-gray-800">Client</span>
                                        <span class="text-xs text-gray-500 ml-2">Yesterday at 8:21 AM</span>
                                    </div>
                                    <p class="text-gray-700">No but it should answer the questions</p>
                                </div>
                            </div>
                        </div>

                        <!-- Message Input Section -->
                        <div class="border-t pt-4">
                            <div class="flex items-center space-x-4">
                                <div class="flex-1 relative">
                                    <input type="text" 
                                           class="w-full px-4 py-2 pr-10 rounded-lg border border-gray-200 focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 transition-all duration-200"
                                           placeholder="Type your message...">
                                    <button class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors duration-200">
                                        <i class="fas fa-paperclip"></i>
                                    </button>
                                </div>
                                <button class="px-6 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors duration-200 flex items-center space-x-2">
                                    <i class="fas fa-paper-plane"></i>
                                    <span>Send</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</main>
</div>

<script>
    // Enhanced File Upload Workflow Functions

// Show the upload modal (Step 1)
function showUploadModal() {
    document.getElementById('uploadModal').classList.remove('hidden');
    document.getElementById('uploadStep1').classList.remove('hidden');
    document.getElementById('uploadStep2').classList.add('hidden');
    document.getElementById('processingModal').classList.add('hidden');
    document.getElementById('successModal').classList.add('hidden');
    document.body.style.overflow = 'hidden';
}

// Close all modals
function closeUploadModal() {
    document.getElementById('uploadModal').classList.add('hidden');
    document.getElementById('processingModal').classList.add('hidden');
    document.getElementById('successModal').classList.add('hidden');
    document.body.style.overflow = '';
    resetUpload();
}

// Reset the upload state
function resetUpload() {
    uploadedFiles.clear();
    document.getElementById('uploadedFiles').innerHTML = '';
    if (document.getElementById('fileInput')) {
        document.getElementById('fileInput').value = '';
    }
}

// Go to the verification step (Step 2)
function gotoVerificationStep() {
    if (uploadedFiles.size === 0) {
        alert('Please select at least one file to upload');
        return;
    }
    
    document.getElementById('uploadStep1').classList.add('hidden');
    document.getElementById('uploadStep2').classList.remove('hidden');
}

// Cancel verification and go back to upload (Step 1)
function cancelVerification() {
    document.getElementById('uploadStep2').classList.add('hidden');
    document.getElementById('uploadStep1').classList.remove('hidden');
}

// Start the upload process (Step 3 - Processing)
function startUpload() {
    document.getElementById('uploadModal').classList.add('hidden');
    document.getElementById('processingModal').classList.remove('hidden');
    
    // Reset progress bar to 0%
    const progressBar = document.getElementById('uploadProgressBar');
    progressBar.style.width = '0%';
    
    // Simulate upload progress
    simulateUploadProgress();
}

// Simulates the progress of file upload
function simulateUploadProgress() {
    const progressBar = document.getElementById('uploadProgressBar');
    let progress = 0;
    
    const interval = setInterval(() => {
        progress += Math.random() * 10;
        
        if (progress >= 100) {
            progress = 100;
            clearInterval(interval);
            
            // Show success after completion
            setTimeout(() => {
                showUploadSuccess();
            }, 500);
        }
        
        progressBar.style.width = `${progress}%`;
    }, 300);
}

// Show upload success (Step 4)
function showUploadSuccess() {
    document.getElementById('processingModal').classList.add('hidden');
    document.getElementById('successModal').classList.remove('hidden');
    
    // Auto close after success and show toaster
    setTimeout(() => {
        document.getElementById('successModal').classList.add('hidden');
        showToaster();
    }, 1500);
}

// Show toaster notification
function showToaster() {
    const toaster = document.getElementById('toaster');
    toaster.classList.remove('translate-x-full');
    
    // Auto hide after 5 seconds
    setTimeout(() => {
        hideToaster();
    }, 5000);
}

// Hide toaster notification
function hideToaster() {
    const toaster = document.getElementById('toaster');
    toaster.classList.add('translate-x-full');
}

// Override submitFiles to use the new workflow
function submitFiles() {
    gotoVerificationStep();
}

// Initialize event listeners
document.addEventListener('DOMContentLoaded', () => {
    // Modal close on outside click for all modals
    const modals = [
        document.getElementById('uploadModal'),
        document.getElementById('processingModal'),
        document.getElementById('successModal')
    ];
    
    modals.forEach(modal => {
        if (modal) {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    closeUploadModal();
                }
            });
        }
    });

    // Handle escape key to close modals
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeUploadModal();
        }
    });
});
</script>
<script src="{{ ('../../js/instructions.js') }}"></script>
@endsection