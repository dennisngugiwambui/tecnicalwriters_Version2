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
<div id="uploadModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>

    <!-- Modal Container -->
    <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
        <!-- Modal Content -->
        <div class="relative bg-white rounded-lg max-w-xl w-full mx-auto shadow-xl transform transition-all">
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
                    <button onclick="submitFiles()" 
                            class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors duration-200">
                        Continue
                    </button>
                </div>
            </div>
        </div>
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
// Global variables
let selectedFiles = [];
let isDragging = false;

// Tab Switching
function switchTab(tabName) {
    const tabs = document.querySelectorAll('[role="tab"]');
    const panels = document.querySelectorAll('[role="tabpanel"]');
    const slider = document.getElementById('tab-slider');

    tabs.forEach(tab => {
        const isSelected = tab.id === `${tabName}-tab`;
        tab.setAttribute('aria-selected', isSelected);
        tab.classList.toggle('text-green-600', isSelected);
        tab.classList.toggle('text-gray-500', !isSelected);

        if (isSelected) {
            const width = tab.dataset.width;
            slider.style.width = `${width}px`;
            slider.style.left = `${tab.offsetLeft}px`;
        }
    });

    panels.forEach(panel => {
        panel.classList.toggle('hidden', panel.id !== `${tabName}-panel`);
    });
}

// Extension Dialog Functions
function showExtensionDialog() {
    const dialog = document.getElementById('extensionDialog');
    dialog.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function hideExtensionDialog() {
    const dialog = document.getElementById('extensionDialog');
    dialog.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function submitExtension() {
    const time = document.getElementById('extensionTime').value;
    const reason = document.getElementById('extensionReason').value;

    if (!time || !reason.trim()) {
        alert('Please fill in all fields');
        return;
    }

    // Here you would typically make an API call
    console.log('Extension submitted:', { time, reason });
    hideExtensionDialog();
}

// Copy Instructions Function
async function copyInstructions() {
    const instructions = document.querySelector('.prose p')?.textContent;
    const customerComments = document.querySelector('.bg-cyan-100 p')?.textContent;
    
    if (!instructions) return;

    const textToCopy = `Instructions:\n${instructions.trim()}\n\nCustomer Comments:\n${customerComments?.trim() || ''}`;

    try {
        await navigator.clipboard.writeText(textToCopy);
        showCopyTooltip('Copied!');
    } catch (err) {
        console.error('Failed to copy:', err);
        showCopyTooltip('Failed to copy');
    }
}

function showCopyTooltip(message) {
    const copyButton = document.querySelector('.fa-copy').parentElement;
    const tooltip = document.createElement('div');
    tooltip.className = 'copy-tooltip';
    tooltip.textContent = message;
    
    copyButton.appendChild(tooltip);
    setTimeout(() => tooltip.classList.add('show'), 10);
    
    setTimeout(() => {
        tooltip.classList.remove('show');
        setTimeout(() => tooltip.remove(), 200);
    }, 2000);
}

// File Upload Functions
function showUploadModal() {
    const modal = document.getElementById('uploadModal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function hideUploadModal() {
    const modal = document.getElementById('uploadModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
    resetUploadForm();
}

function resetUploadForm() {
    selectedFiles = [];
    const fileList = document.getElementById('fileList');
    fileList.innerHTML = '';
    document.getElementById('fileInput').value = '';
}

function handleFileSelect(files) {
    const fileList = document.getElementById('fileList');
    
    Array.from(files).forEach(file => {
        const fileId = Math.random().toString(36).substr(2, 9);
        selectedFiles.push({ id: fileId, file, description: '' });
        
        const fileRow = createFileRow(file, fileId);
        fileList.appendChild(fileRow);
    });
}

function createFileRow(file, fileId) {
    const row = document.createElement('div');
    row.className = 'flex items-center space-x-4 p-4 border rounded-lg mb-2';
    row.innerHTML = `
        <div class="flex-grow">
            <p class="text-sm font-medium text-gray-700">${file.name}</p>
            <p class="text-xs text-gray-500">${formatFileSize(file.size)}</p>
        </div>
        <div class="relative">
            <input type="text"
                   class="px-3 py-2 border rounded-lg text-sm"
                   placeholder="Description"
                   onFocus="showDescriptionDropdown(this)"
                   data-file-id="${fileId}">
            <div class="description-dropdown">
                <div class="py-1">
                    <button class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100" onclick="selectDescription(this, 'completed')">completed</button>
                    <button class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100" onclick="selectDescription(this, 'sources')">sources</button>
                    <button class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100" onclick="selectDescription(this, 'file with corrections')">file with corrections</button>
                    <button class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100" onclick="selectDescription(this, 'preview')">preview</button>
                </div>
            </div>
        </div>
        <button onclick="removeFile('${fileId}')" class="text-gray-400 hover:text-gray-600">
            <i class="fas fa-trash"></i>
        </button>
    `;
    return row;
}

function showDescriptionDropdown(input) {
    const dropdown = input.nextElementSibling;
    dropdown.classList.add('show');

    // Close when clicking outside
    document.addEventListener('click', function closeDropdown(e) {
        if (!dropdown.contains(e.target) && e.target !== input) {
            dropdown.classList.remove('show');
            document.removeEventListener('click', closeDropdown);
        }
    });
}

function selectDescription(button, description) {
    const input = button.closest('.relative').querySelector('input');
    const fileId = input.dataset.fileId;
    input.value = description;
    
    const fileIndex = selectedFiles.findIndex(f => f.id === fileId);
    if (fileIndex !== -1) {
        selectedFiles[fileIndex].description = description;
    }
    
    button.closest('.description-dropdown').classList.remove('show');
}

function removeFile(fileId) {
    selectedFiles = selectedFiles.filter(f => f.id !== fileId);
    const fileRow = document.querySelector(`[data-file-id="${fileId}"]`).closest('.flex');
    fileRow.remove();
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function handleDrop(e) {
    e.preventDefault();
    const dropZone = document.getElementById('dropZone');
    dropZone.classList.remove('drag-over');
    handleFileSelect(e.dataTransfer.files);
}

function handleDragOver(e) {
    e.preventDefault();
    const dropZone = document.getElementById('dropZone');
    dropZone.classList.add('drag-over');
}

function handleDragLeave(e) {
    e.preventDefault();
    const dropZone = document.getElementById('dropZone');
    dropZone.classList.remove('drag-over');
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    // Set initial tab
    switchTab('instructions');
    
    // Initialize extension dialog close on outside click
    const extensionDialog = document.getElementById('extensionDialog');
    extensionDialog?.addEventListener('click', (e) => {
        if (e.target === extensionDialog) {
            hideExtensionDialog();
        }
    });

    // Initialize upload modal close on outside click
    const uploadModal = document.getElementById('uploadModal');
    uploadModal?.addEventListener('click', (e) => {
        if (e.target === uploadModal) {
            hideUploadModal();
        }
    });

    // Handle escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            hideExtensionDialog();
            hideUploadModal();
        }
    });
});

// Global variables
let uploadedFiles = new Map();

// File Upload Functions
function showUploadModal() {
    document.getElementById('uploadModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeUploadModal() {
    document.getElementById('uploadModal').classList.add('hidden');
    document.body.style.overflow = '';
    resetUpload();
}

function resetUpload() {
    uploadedFiles.clear();
    document.getElementById('uploadedFiles').innerHTML = '';
    document.getElementById('fileInput').value = '';
}

function handleFileSelect(event) {
    const files = event.target.files;
    addFiles(files);
}

function handleFileDrop(event) {
    event.preventDefault();
    const dropZone = document.getElementById('dropZone');
    dropZone.classList.remove('border-green-500');
    const files = event.dataTransfer.files;
    addFiles(files);
}

function handleDragOver(event) {
    event.preventDefault();
    document.getElementById('dropZone').classList.add('border-green-500');
}

function handleDragLeave(event) {
    event.preventDefault();
    document.getElementById('dropZone').classList.remove('border-green-500');
}

function addFiles(files) {
    const uploadedFilesContainer = document.getElementById('uploadedFiles');
    
    Array.from(files).forEach(file => {
        const fileId = Math.random().toString(36).substr(2, 9);
        uploadedFiles.set(fileId, { file, description: '' });
        
        const fileElement = createFileElement(file, fileId);
        uploadedFilesContainer.appendChild(fileElement);
    });
}

function createFileElement(file, fileId) {
    const div = document.createElement('div');
    div.className = 'flex items-center space-x-4 border rounded-lg p-4';
    div.innerHTML = `
        <input type="text" class="flex-grow text-gray-700 bg-transparent outline-none" 
               value="${file.name}" readonly>
        <div class="relative inline-block">
            <input type="text" 
                   class="px-3 py-2 border rounded-lg text-sm w-40"
                   placeholder="Description"
                   onclick="toggleDescriptionDropdown('${fileId}')"
                   data-file-id="${fileId}">
            <div id="dropdown-${fileId}" class="hidden absolute z-10 mt-1 w-full bg-white border rounded-lg shadow-lg">
                <div class="py-1">
                    <button class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" 
                            onclick="selectDescription('${fileId}', 'completed')">completed</button>
                    <button class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                            onclick="selectDescription('${fileId}', 'sources')">sources</button>
                    <button class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                            onclick="selectDescription('${fileId}', 'file with corrections')">file with corrections</button>
                    <button class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                            onclick="selectDescription('${fileId}', 'preview')">preview</button>
                </div>
            </div>
        </div>
        <button onclick="removeFile('${fileId}')" class="text-gray-400 hover:text-gray-600">
            <i class="fas fa-trash"></i>
        </button>
    `;
    return div;
}

function toggleDescriptionDropdown(fileId) {
    const dropdown = document.getElementById(`dropdown-${fileId}`);
    const allDropdowns = document.querySelectorAll('[id^="dropdown-"]');
    
    // Close all other dropdowns
    allDropdowns.forEach(d => {
        if (d.id !== `dropdown-${fileId}`) {
            d.classList.add('hidden');
        }
    });
    
    dropdown.classList.toggle('hidden');

    // Close when clicking outside
    document.addEventListener('click', function closeDropdown(e) {
        const isDropdownClick = e.target.closest(`#dropdown-${fileId}`);
        const isInputClick = e.target.dataset.fileId === fileId;
        
        if (!isDropdownClick && !isInputClick) {
            dropdown.classList.add('hidden');
            document.removeEventListener('click', closeDropdown);
        }
    });
}

function selectDescription(fileId, description) {
    const input = document.querySelector(`input[data-file-id="${fileId}"]`);
    input.value = description;
    document.getElementById(`dropdown-${fileId}`).classList.add('hidden');
    
    const fileData = uploadedFiles.get(fileId);
    if (fileData) {
        fileData.description = description;
        uploadedFiles.set(fileId, fileData);
    }
}

function removeFile(fileId) {
    uploadedFiles.delete(fileId);
    const fileElement = document.querySelector(`input[data-file-id="${fileId}"]`).closest('.flex');
    fileElement.remove();
}

function submitFiles() {
    if (uploadedFiles.size === 0) {
        alert('Please select at least one file');
        return;
    }

    const files = Array.from(uploadedFiles.entries()).map(([id, data]) => ({
        file: data.file,
        description: data.description
    }));

    // Here you would typically send the files to your server
    console.log('Uploading files:', files);
    closeUploadModal();
}

// Initialize file selection behaviors
document.addEventListener('DOMContentLoaded', () => {
    const selectAllCheckbox = document.getElementById('selectAllFiles');
    const fileCheckboxes = document.querySelectorAll('.form-checkbox:not(#selectAllFiles)');

    selectAllCheckbox?.addEventListener('change', (e) => {
        fileCheckboxes.forEach(checkbox => {
            checkbox.checked = e.target.checked;
        });
    });

    fileCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            selectAllCheckbox.checked = 
                Array.from(fileCheckboxes).every(cb => cb.checked);
        });
    });
});

// File download helpers
async function downloadFile(fileId, fileName) {
    try {
        // Show loading state
        const downloadBtn = document.querySelector(`button[data-file-id="${fileId}"]`);
        const originalContent = downloadBtn.innerHTML;
        downloadBtn.innerHTML = '<div class="spinner"></div>';
        downloadBtn.disabled = true;

        // Simulate file download (replace with your actual file download logic)
        const response = await fetch(`/api/files/${fileId}`);
        const blob = await response.blob();

        // Create download link
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = fileName;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);

        // Restore button state
        downloadBtn.innerHTML = originalContent;
        downloadBtn.disabled = false;
    } catch (error) {
        console.error('Download failed:', error);
        alert('Failed to download file. Please try again.');
    }
}

// Download multiple files as zip
async function downloadSelectedFiles() {
    const selectedFiles = document.querySelectorAll('input[type="checkbox"]:checked:not(#selectAllFiles)');
    
    if (selectedFiles.length === 0) {
        alert('Please select at least one file to download');
        return;
    }

    // Show loading state
    const downloadBtn = document.querySelector('.bulk-download-btn');
    const originalContent = downloadBtn.innerHTML;
    downloadBtn.innerHTML = '<div class="spinner"></div>';
    downloadBtn.disabled = true;

    try {
        const zip = new JSZip();

        // Add files to zip
        const filePromises = Array.from(selectedFiles).map(async checkbox => {
            const fileId = checkbox.closest('.file-item').dataset.fileId;
            const fileName = checkbox.closest('.file-item').dataset.fileName;
            
            // Replace with your actual file download logic
            const response = await fetch(`/api/files/${fileId}`);
            const blob = await response.blob();
            
            zip.file(fileName, blob);
        });

        await Promise.all(filePromises);

        // Generate and download zip
        const content = await zip.generateAsync({ type: 'blob' });
        const url = window.URL.createObjectURL(content);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'files.zip';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
    } catch (error) {
        console.error('Failed to download files:', error);
        alert('Failed to download files. Please try again.');
    }

    // Restore button state
    downloadBtn.innerHTML = originalContent;
    downloadBtn.disabled = false;
}


</script>
<script src="{{ ('../../js/instructions.js') }}"></script>
@endsection