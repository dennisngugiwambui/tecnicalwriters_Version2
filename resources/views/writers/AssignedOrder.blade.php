@extends('writers.app')

@section('content')
<style>
    .tab-slider {
        position: absolute;
        bottom: -1px;
        height: 2px;
        background-color: #22C55E;
        transition: all 0.3s ease;
    }
</style>

<div class="flex h-full pt-20 px-6 lg:px-8">
    <main class="flex-1 px-4 lg:px-8 pb-8 lg:ml-72 transition-all duration-300 pt-16">
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
                            <div class="p-4 bg-gradient-to-r from-gray-50 to-white rounded-lg border border-gray-100">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Deadline</span>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-gray-800">25 Feb, 08:26 AM</span>
                                        <span class="text-green-500">(31 h)</span>
                                        <button class="px-3 py-1 bg-green-500 text-white text-sm rounded hover:bg-green-600 transition-colors duration-200">
                                            Extend
                                        </button>
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
                                    <button class="text-gray-400 hover:text-gray-600 transition-colors duration-200" onclick="copyInstructions()">
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
                <div id="files-panel" class="hidden" role="tabpanel">
                    <div class="bg-white rounded-lg">
                        <div class="flex justify-between items-center mb-6">
                            <h4 class="font-medium text-gray-800">All Files (3)</h4>
                            <button class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors duration-200 flex items-center space-x-2">
                                <i class="fas fa-upload"></i>
                                <span>Upload files</span>
                            </button>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-all duration-200">
                                <div class="flex items-center space-x-4">
                                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                                        <i class="fas fa-file-pdf text-gray-400 text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">615487328_Introduction_580948953120760.pdf</p>
                                        <p class="text-xs text-gray-500">Instructions / Guidelines</p>
                                        <p class="text-xs text-gray-500">22 Feb, 07:54 AM • 569 KB</p>
                                    </div>
                                </div>
                                <button class="p-2 text-gray-400 hover:text-gray-600 transition-colors duration-200 rounded-lg hover:bg-white">
                                    <i class="fas fa-download"></i>
                                </button>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-all duration-200">
                                <div class="flex items-center space-x-4">
                                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                                        <i class="fas fa-file-pdf text-gray-400 text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">615487328_GreenZone_7135081579577641.pdf</p>
                                        <p class="text-xs text-gray-500">network design</p>
                                        <p class="text-xs text-gray-500">22 Feb, 07:53 AM • 77 KB</p>
                                    </div>
                                </div>
                                <button class="p-2 text-gray-400 hover:text-gray-600 transition-colors duration-200 rounded-lg hover:bg-white">
                                    <i class="fas fa-download"></i>
                                </button>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-all duration-200">
                                <div class="flex items-center space-x-4">
                                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                                        <i class="fas fa-file-pdf text-gray-400 text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">615487328_GreenZone_1762882483610118.pdf</p>
                                        <p class="text-xs text-gray-500">network design</p>
                                        <p class="text-xs text-gray-500">22 Feb, 07:53 AM • 72 KB</p>
                                    </div>
                                </div>
                                <button class="p-2 text-gray-400 hover:text-gray-600 transition-colors duration-200 rounded-lg hover:bg-white">
                                    <i class="fas fa-download"></i>
                                </button>
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
// Updated tab switching functionality with green indicator
function switchTab(tabName) {
const tabs = document.querySelectorAll('[role="tab"]');
const panels = document.querySelectorAll('[role="tabpanel"]');
const slider = document.getElementById('tab-slider');

// Update tabs and slider
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

// Update panels
panels.forEach(panel => {
    panel.classList.toggle('hidden', panel.id !== `${tabName}-panel`);
});
}

// Initialize the page
document.addEventListener('DOMContentLoaded', () => {
switchTab('instructions');

// Message input handling
const messageInput = document.querySelector('input[type="text"]');
const sendButton = document.querySelector('button:has(.fa-paper-plane)');

messageInput?.addEventListener('keypress', (e) => {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendButton.click();
    }
});

sendButton?.addEventListener('click', () => {
    const message = messageInput.value.trim();
    if (message) {
        // Here you would normally send the message
        console.log('Sending message:', message);
        messageInput.value = '';
    }
});
});
</script>
@endsection