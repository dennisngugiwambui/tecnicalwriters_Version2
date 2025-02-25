@extends('writers.app')

@section('content')

<!-- Order Header with Bid Button -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
    <div class="p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4">
            <div class="flex items-center space-x-4">
                <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center">
                    <i class="fas fa-file-alt text-green-500 text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-semibold text-gray-800">Order #614973494</h1>
                    <div class="flex items-center text-sm text-gray-500">
                        <div class="w-2 h-2 rounded-full bg-green-500 mr-2"></div>
                        Available
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-6 mt-4 md:mt-0">
                <span class="text-2xl font-semibold text-gray-800">$65</span>
                <div class="flex items-center border-l pl-6">
                    <div class="flex flex-col items-end">
                        <span class="text-sm font-medium text-gray-600">Customer</span>
                        <span class="text-sm text-gray-500">08:44 PM</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Bid Section -->
        <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                <p class="text-gray-700 mb-3 md:mb-0">Place your bid, if you are ready to execute this order for <span class="font-semibold">$65</span>.</p>
                <button id="placeBidBtn" 
                        class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors duration-200">
                    Place Bid
                </button>
            </div>
            <div class="mt-3 text-sm text-gray-500">
                Number of bids placed for this order is 17 • Take Order option is disabled by Support Team.
            </div>
        </div>
    </div>
</div>

<!-- Tab Navigation with Slider -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
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
                <span class="ml-2 bg-green-500 text-white text-xs px-2 py-0.5 rounded-full">11</span>
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
                    <!-- Price Info -->
                    <div class="p-4 bg-gradient-to-r from-gray-50 to-white rounded-lg border border-gray-100">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Price</span>
                            <span class="text-gray-800 font-medium">$65</span>
                        </div>
                    </div>

                    <!-- Deadline Info -->
                    <div class="p-4 bg-gradient-to-r from-gray-50 to-white rounded-lg border border-gray-100">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Deadline</span>
                            <div>
                                <span class="text-gray-800">2 Mar, 05:17 AM</span>
                                <span class="text-green-500 ml-2">(4d 19h)</span>
                            </div>
                        </div>
                    </div>

                    <!-- Task Size -->
                    <div class="p-4 bg-gradient-to-r from-gray-50 to-white rounded-lg border border-gray-100">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Task size</span>
                            <div class="flex items-center">
                                <span class="text-gray-800">Large</span>
                                <div class="ml-2 text-gray-400 cursor-help">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Type of Service -->
                    <div class="p-4 bg-gradient-to-r from-gray-50 to-white rounded-lg border border-gray-100">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Type of service</span>
                            <span class="text-gray-800">Calculations</span>
                        </div>
                    </div>

                    <!-- Discipline -->
                    <div class="p-4 bg-gradient-to-r from-gray-50 to-white rounded-lg border border-gray-100">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Discipline</span>
                            <span class="text-gray-800">Project Planning and Control</span>
                        </div>
                    </div>

                    <!-- Software -->
                    <div class="p-4 bg-gradient-to-r from-gray-50 to-white rounded-lg border border-gray-100">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Software</span>
                            <span class="text-gray-800">AstaPowerProject</span>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Instructions -->
                <div class="bg-white rounded-xl">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Paper instructions</h3>
                        <div class="flex space-x-3">
                            <button class="text-gray-400 hover:text-gray-600 transition-colors duration-200 relative" 
                                   onclick="copyInstructions()">
                                <i class="fas fa-copy"></i>
                            </button>
                            <button class="text-gray-400 hover:text-gray-600 transition-colors duration-200" 
                                   onclick="expandInstructions()">
                                <i class="fas fa-expand-alt"></i>
                            </button>
                        </div>
                    </div>

                    <div class="prose max-w-none text-gray-700">
                        <p>
                            The site layout plan should be in diagrammatic/drawn format utilising methods shown in tutorials (PowerPoint), and also the word count is 2000 words excluding the references and charts and the software and the powerpoint, it should be structured professionally with a cover page and a summary and an Introduction to both task1 and task2 with conclusion and references, please do not repeat the same reference twice and also make sure they are reliable please. 7 to 10 references or as much as needed.
                        </p>
                        <p>Thank you.</p>
                    </div>
                    
                    <!-- Customer Files -->
                    <div class="mt-8">
                        <div class="flex justify-between items-center">
                            <h4 class="font-medium text-gray-800">Customer files</h4>
                            <span class="text-green-500 bg-green-50 px-2 py-1 rounded text-xs">11</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Files Panel -->
        <div id="files-panel" class="hidden" role="tabpanel">
            <div class="space-y-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">All files</h3>
                    <button class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors duration-200">
                        Report instructions problems
                    </button>
                </div>
                
                <!-- File list would go here -->
                <p class="text-gray-600">All files for this order will appear here.</p>
            </div>
        </div>

        <!-- Messages Panel -->
        <div id="messages-panel" class="hidden" role="tabpanel">
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-800">Messages</h3>
                <p class="text-gray-600">Communication with the customer will appear here.</p>
            </div>
        </div>
    </div>
</div>

<!-- Bid Success Toaster -->
<div id="bidToaster" class="fixed bottom-4 left-4 z-50 transform translate-y-full transition-transform duration-300 ease-in-out">
    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded shadow-lg flex items-start max-w-sm">
        <div class="text-green-500 mr-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <div>
            <p class="font-medium text-green-800">Success!</p>
            <p class="text-sm text-green-700 mt-1">Bid clicked successfully.</p>
        </div>
        <button onclick="hideBidToaster()" class="ml-auto text-green-500 hover:text-green-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
</div>


<script>
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

// Copy Instructions Function
async function copyInstructions() {
    const instructions = document.querySelector('.prose p')?.textContent;
    
    if (!instructions) return;

    try {
        await navigator.clipboard.writeText(instructions.trim());
        showCopyTooltip('Copied!');
    } catch (err) {
        console.error('Failed to copy:', err);
        showCopyTooltip('Failed to copy');
    }
}

function showCopyTooltip(message) {
    const copyButton = document.querySelector('.fa-copy').parentElement;
    const tooltip = document.createElement('div');
    tooltip.className = 'copy-tooltip absolute bottom-full mb-2 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 transition-opacity duration-200';
    tooltip.textContent = message;
    
    copyButton.appendChild(tooltip);
    setTimeout(() => tooltip.classList.add('opacity-100'), 10);
    
    setTimeout(() => {
        tooltip.classList.remove('opacity-100');
        setTimeout(() => tooltip.remove(), 200);
    }, 2000);
}

// Expand Instructions Function
function expandInstructions() {
    // Implementation for expanding instructions in a full-screen modal
    console.log('Expand instructions');
    // You could implement a full-screen modal here
}

// Bid Toaster Functions
function showBidToaster() {
    const toaster = document.getElementById('bidToaster');
    toaster.classList.remove('translate-y-full');
    
    // Auto hide after 5 seconds
    setTimeout(() => {
        hideBidToaster();
    }, 5000);
}

function hideBidToaster() {
    const toaster = document.getElementById('bidToaster');
    toaster.classList.add('translate-y-full');
}

// Initialize when the DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    // Set initial tab
    switchTab('instructions');
    
    // Initialize Place Bid button
    const placeBidBtn = document.getElementById('placeBidBtn');
    placeBidBtn?.addEventListener('click', () => {
        console.log('Bid placed for $65');
        showBidToaster();
    });
    
    // Add styles for tab slider if not already in stylesheet
    if (!document.querySelector('style#tab-slider-styles')) {
        const style = document.createElement('style');
        style.id = 'tab-slider-styles';
        style.textContent = `
            .tab-slider {
                position: absolute;
                bottom: -1px;
                height: 2px;
                background-color: #22C55E;
                transition: all 0.3s ease;
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
                margin-bottom: 0.5rem;
                z-index: 50;
            }
        `;
        document.head.appendChild(style);
    }
});
</script>

@endsection