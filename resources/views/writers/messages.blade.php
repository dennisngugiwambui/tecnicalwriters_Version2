@extends('writers.app')

@section('content')

<main class="flex-1 px-4 lg:px-8 pb-8 lg:ml-72 transition-all duration-300 pt-24">
    <!-- Header with New Message button, Search bar, and dropdowns -->
    <div class="flex justify-between items-center mb-8">
        <div class="flex items-center gap-4">
            <h1 class="text-xl font-semibold">Messages</h1>
            <button 
                onclick="openNewMessageModal()"
                class="px-4 py-2 bg-green-500 text-white rounded-md text-sm hover:bg-green-600 transition-colors duration-200"
            >
                New message
            </button>
        </div>

        <!-- Search and Filter Bar -->
        <div class="flex gap-4 items-center">
            <div class="relative">
                <select id="message-type-filter" class="px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="all">All departments</option>
                    <option value="support">Support</option>
                    <option value="client">Client</option>
                </select>
            </div>
            <input 
                type="text" 
                id="search-order"
                placeholder="Search by order #"
                class="px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-green-500"
            />
            <button id="search-button" class="px-4 py-2 bg-green-500 text-white text-sm rounded-md hover:bg-green-600 transition-colors duration-200">
                Search
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-md">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-green-700">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-md">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-red-700">{{ session('error') }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Messages List -->
    <div class="divide-y divide-gray-100">
        @if(count($messageThreads) > 0)
            @foreach($messageThreads as $thread)
            <div class="border-b border-gray-100 last:border-b-0 message-thread" 
                data-order-id="{{ $thread->order_id }}" 
                data-message-type="{{ $thread->message_type }}">
                <a href="{{ route('writer.message.thread', $thread->order_id) }}" 
                    class="flex items-start gap-4 py-2 px-4 cursor-pointer hover:bg-gray-50 transition-colors duration-200">
                    <div>
                        <!-- FontAwesome Message Icon -->
                        <i class="fas fa-envelope {{ $thread->read_at ? 'text-gray-400' : 'text-green-500' }}"></i>
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 text-sm">
                            <span class="font-semibold {{ $thread->read_at ? 'text-normal' : 'text-bold' }}">
                                {{ $thread->user_id == Auth::id() ? 'Me' : ($thread->message_type == 'client' ? 'Client' : 'Support') }}
                            </span>
                            <span class="text-gray-400">▸</span>
                            <span>{{ $thread->user_id == Auth::id() ? ($thread->message_type == 'client' ? 'Client' : 'Support') : 'Me' }}</span>
                        </div>
                        <div class="text-sm text-gray-600 font-semibold">
                            Order #{{ $thread->order_id }}: {{ $thread->order->title ?? 'Order' }}
                        </div>
                        <div class="text-sm text-gray-400 truncate">
                            {{ Str::limit($thread->message, 70) }}
                        </div>
                    </div>

                    <div class="flex flex-col items-end text-sm text-gray-400 whitespace-nowrap">
                        <div class="font-semibold">#{{ $thread->order_id }}</div>
                        <div class="font-semibold">{{ $thread->created_at->format('d M, h:i A') }}</div>
                        @if($thread->files->count() > 0)
                        <div class="flex items-center mt-1">
                            <i class="fas fa-paperclip text-gray-400 mr-1"></i>
                            <span>{{ $thread->files->count() }}</span>
                        </div>
                        @endif
                    </div>

                    <div class="text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </a>
            </div>
            @endforeach
        @else
            <div class="py-8 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <h3 class="text-lg font-medium text-gray-600 mb-1">No messages yet</h3>
                <p class="text-gray-500">Start a new conversation with support or clients</p>
            </div>
        @endif
    </div>
</main>

<!-- New Message Modal -->
<div id="newMessageModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center hidden z-50">
    <div class="bg-white p-8 rounded-md w-full max-w-md">
        <h2 class="text-xl mb-4">New Message</h2>
        
        <form action="{{ route('writer.message.send') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Title</label>
                <select name="order_id" class="w-full p-2 border rounded-md text-sm" required>
                    <option value="">Select an order...</option>
                    @foreach($userOrders as $order)
                    <option value="{{ $order->id }}">Order #{{ $order->id }}: {{ Str::limit($order->title, 40) }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Recipient</label>
                <select name="message_type" class="w-full p-2 border rounded-md text-sm" required>
                    <option value="">Select recipient...</option>
                    <option value="client">Client</option>
                    <option value="support">Support Team</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Message</label>
                <textarea name="message" class="w-full h-32 p-2 border rounded-md text-sm" placeholder="Type your message here..." required onkeyup="checkForbiddenWords(this)"></textarea>
                <div id="forbiddenWordsWarning" class="hidden mt-2 p-2 bg-yellow-100 text-yellow-800 text-xs rounded-lg border border-yellow-200 w-full">
                    Warning: Your message contains prohibited keywords. Please avoid payment-related discussions.
                </div>
            </div>

            <!-- Attach File Section -->
            <div id="attachments" class="mb-4 space-y-2">
                <!-- Attachment rows will be added here -->
            </div>

            <div class="flex justify-between gap-2">
                <label class="cursor-pointer">
                    <input
                        type="file"
                        name="attachments[]"
                        class="hidden"
                        onchange="handleFileAttach(event)"
                        multiple
                    />
                    <span class="inline-block px-4 py-2 text-green-500 border border-green-500 rounded-md text-sm hover:bg-green-50 transition-colors duration-200">
                        Attach file
                    </span>
                </label>

                <div class="flex justify-end gap-2">
                    <button 
                        type="button"
                        onclick="closeNewMessageModal()"
                        class="px-4 py-2 text-gray-600 text-sm hover:bg-gray-50 rounded-md transition-colors duration-200"
                    >
                        Cancel
                    </button>
                    <button type="submit" id="sendMessageBtn" class="px-4 py-2 bg-green-500 text-white text-sm rounded-md hover:bg-green-600 transition-colors duration-200">
                        Send
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Check for forbidden words in the message
    function checkForbiddenWords(inputElement) {
        const forbiddenKeywords = ['dollar', 'money', 'pay', 'shillings', 'cash', 'price', 'payment'];
        const messageText = inputElement.value.toLowerCase();
        const warningElement = document.getElementById('forbiddenWordsWarning');
        const sendButton = document.getElementById('sendMessageBtn');
        
        let containsForbiddenWord = false;
        
        forbiddenKeywords.forEach(keyword => {
            if (messageText.includes(keyword)) {
                containsForbiddenWord = true;
            }
        });
        
        warningElement.classList.toggle('hidden', !containsForbiddenWord);
        
        // Optionally disable the send button if forbidden words are found
        if (sendButton) {
            sendButton.disabled = containsForbiddenWord;
            if (containsForbiddenWord) {
                sendButton.classList.add('bg-gray-400');
                sendButton.classList.remove('bg-green-500', 'hover:bg-green-600');
            } else {
                sendButton.classList.remove('bg-gray-400');
                sendButton.classList.add('bg-green-500', 'hover:bg-green-600');
            }
        }
    }

    // Open New Message Modal
    function openNewMessageModal() {
        document.getElementById('newMessageModal').classList.remove('hidden');
    }

    // Close New Message Modal
    function closeNewMessageModal() {
        document.getElementById('newMessageModal').classList.add('hidden');
        
        // Clear form fields
        const form = document.querySelector('#newMessageModal form');
        if (form) form.reset();
        
        // Clear attachments
        document.getElementById('attachments').innerHTML = '';
        
        // Clear warning
        document.getElementById('forbiddenWordsWarning').classList.add('hidden');
    }

    // Handle file attachment
    function handleFileAttach(event) {
        const files = event.target.files;
        if (files.length > 0) {
            const attachmentContainer = document.getElementById('attachments');
            
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const newAttachment = document.createElement('div');
                newAttachment.classList.add('flex', 'items-center', 'gap-4', 'p-2', 'bg-gray-50', 'rounded-md');
                newAttachment.innerHTML = `
                    <span class="text-sm text-gray-600 truncate" style="max-width: 180px;">${file.name}</span>
                    <input type="text" name="attachment_descriptions[]" placeholder="Description" class="flex-1 p-1 text-sm border rounded focus:outline-none focus:ring-1 focus:ring-green-500" />
                    <button type="button" onclick="removeAttachment(this)" class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                attachmentContainer.appendChild(newAttachment);
            }
        }
    }

    // Remove attachment
    function removeAttachment(button) {
        button.closest('div').remove();
    }
    
    // Filter messages
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search-order');
        const searchButton = document.getElementById('search-button');
        const typeFilter = document.getElementById('message-type-filter');
        const messageThreads = document.querySelectorAll('.message-thread');
        
        function filterMessages() {
            const searchTerm = searchInput.value.toLowerCase();
            const messageType = typeFilter.value;
            
            messageThreads.forEach(thread => {
                const orderId = thread.dataset.orderId;
                const threadType = thread.dataset.messageType;
                
                let visible = true;
                
                // Filter by order ID if search term exists
                if (searchTerm && !orderId.includes(searchTerm)) {
                    visible = false;
                }
                
                // Filter by message type if not "all"
                if (messageType !== 'all' && threadType !== messageType) {
                    visible = false;
                }
                
                thread.style.display = visible ? 'block' : 'none';
            });
        }
        
        if (searchButton) {
            searchButton.addEventListener('click', filterMessages);
        }
        
        if (typeFilter) {
            typeFilter.addEventListener('change', filterMessages);
        }
        
        if (searchInput) {
            searchInput.addEventListener('keyup', function(e) {
                if (e.key === 'Enter') {
                    filterMessages();
                }
            });
        }
    });
</script>

@endsection