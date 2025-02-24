// extension-dialog.js
const extensionHandler = {
    init() {
        this.setupEventListeners();
    },

    setupEventListeners() {
        const extendBtn = document.querySelector('[data-action="extend"]');
        if (extendBtn) {
            extendBtn.addEventListener('click', () => this.showDialog());
        }

        // Close dialog events
        document.addEventListener('click', (e) => {
            if (e.target.matches('.dialog-overlay')) {
                this.hideDialog();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.hideDialog();
            }
        });
    },

    showDialog() {
        // Create dialog if it doesn't exist
        if (!document.getElementById('extensionDialog')) {
            const dialog = `
                <div id="extensionDialog" class="fixed inset-0 z-50 hidden">
                    <div class="absolute inset-0 bg-black bg-opacity-50 dialog-overlay"></div>
                    <div class="relative min-h-screen flex items-center justify-center p-4">
                        <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
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
                                        onclick="extensionHandler.hideDialog()">
                                        Cancel
                                    </button>
                                    <button 
                                        class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors duration-200"
                                        onclick="extensionHandler.submitExtension()">
                                        Extend
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', dialog);
        }

        // Show dialog
        const dialogEl = document.getElementById('extensionDialog');
        dialogEl.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    },

    hideDialog() {
        const dialogEl = document.getElementById('extensionDialog');
        if (dialogEl) {
            dialogEl.classList.add('hidden');
            document.body.style.overflow = '';
        }
    },

    submitExtension() {
        const time = document.querySelector('#extensionDialog select').value;
        const reason = document.querySelector('#extensionDialog textarea').value;

        if (!time) {
            alert('Please select an extension time');
            return;
        }
        if (!reason.trim()) {
            alert('Please provide a reason for the extension');
            return;
        }

        // Here you would typically send the data to your backend
        console.log('Extension requested:', { time, reason });
        this.hideDialog();
    }
};

// copy-instructions.js
const copyHandler = {
    init() {
        this.setupCopyButton();
    },

    setupCopyButton() {
        const copyBtn = document.querySelector('[onclick="copyInstructions()"]');
        if (copyBtn) {
            copyBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.copyInstructions();
            });
        }
    },

    async copyInstructions() {
        try {
            // Get all instruction content
            const instructionsContent = document.querySelector('.prose p')?.textContent || '';
            const customerComments = document.querySelector('.bg-cyan-100 p')?.textContent || '';
            
            // Format the content
            const textToCopy = `
Instructions:
${instructionsContent.trim()}

Customer Comments:
${customerComments.trim()}
            `.trim();

            // Copy to clipboard
            await navigator.clipboard.writeText(textToCopy);

            // Show success feedback
            const copyBtn = document.querySelector('.fa-copy');
            const originalColor = copyBtn.parentElement.className;
            copyBtn.parentElement.classList.add('text-green-500');
            
            // Show tooltip
            const tooltip = document.createElement('div');
            tooltip.className = 'absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded';
            tooltip.textContent = 'Copied!';
            copyBtn.parentElement.appendChild(tooltip);

            // Reset after animation
            setTimeout(() => {
                copyBtn.parentElement.classList.remove('text-green-500');
                tooltip.remove();
            }, 2000);

        } catch (err) {
            console.error('Failed to copy:', err);
            alert('Failed to copy instructions');
        }
    }
};

// Initialize both handlers when the document is ready
document.addEventListener('DOMContentLoaded', () => {
    extensionHandler.init();
    copyHandler.init();
});