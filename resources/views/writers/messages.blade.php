@extends('writers.app')

@section('content')

<main class="flex-1 px-4 lg:px-8 pb-8 lg:ml-72 transition-all duration-300 pt-24">
    <!-- Header with New Message button, Search bar, and dropdowns -->
    <div class="flex justify-between items-center mb-8">
        <div class="flex items-center gap-4">
            <h1 class="text-xl font-semibold">Messages</h1>
            <button 
                onclick="openNewMessageModal()"
                class="px-4 py-2 bg-green-500 text-white rounded-md text-sm hover:bg-green-600"
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
                    <option value="writers">Writer's Department</option>
                    <option value="manager">Manager</option>
                    <option value="editors">Editors</option>
                    <option value="dissertation">Dissertation Dept</option>
                </select>
                <span class="absolute inset-y-0 left-0 flex items-center pl-2">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </span>
            </div>
            <input 
                type="text" 
                id="search-order"
                placeholder="Order"
                class="px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-green-500"
            />
            <button id="search-button" class="px-4 py-2 bg-green-500 text-white text-sm rounded-md hover:bg-green-600">
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

    <!-- Display Message Thread if we're viewing a specific thread -->
    @if(isset($messages) && isset($type))
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="flex justify-between items-center p-4 border-b border-gray-100">
            <div>
                <h2 class="text-lg font-semibold">
                    @if($type === 'order')
                        Order #{{ $order->id }}: {{ Str::limit($order->title, 40) }}
                    @else
                        {{ $title }}
                    @endif
                </h2>
                <p class="text-sm text-gray-500">
                    Conversation with 
                    @if(isset($otherUser))
                        {{ $otherUser->name }} ({{ ucfirst($otherUser->usertype) }})
                    @else
                        Support
                    @endif
                </p>
            </div>
            
            @if($type === 'order')
                @php
                    $isAssignedToAnotherWriter = $order->writer_id && $order->writer_id != Auth::id();
                @endphp
                
                @if(!$isAssignedToAnotherWriter)
                    <button 
                        onclick="openAnswerModal('{{ $type === 'order' ? 'order_' . $order->id : 'general_' . $title . '_' . $otherUser->id }}', '{{ isset($otherUser) ? $otherUser->name : 'Support' }}', '{{ $type === 'order' ? 'Order #' . $order->id . ': ' . $order->title : $title }}', '{{ $messages->count() > 0 ? Str::limit($messages->last()->message, 200) : '' }}')"
                        class="px-4 py-1.5 text-green-500 border border-green-500 rounded-md text-sm hover:bg-green-50"
                    >
                        Answer
                    </button>
                @endif
            @else
                <button 
                    onclick="openAnswerModal('{{ $type === 'order' ? 'order_' . $order->id : 'general_' . $title . '_' . $otherUser->id }}', '{{ $otherUser->name }}', '{{ $type === 'order' ? 'Order #' . $order->id . ': ' . $order->title : $title }}', '{{ $messages->count() > 0 ? Str::limit($messages->last()->message, 200) : '' }}')"
                    class="px-4 py-1.5 text-green-500 border border-green-500 rounded-md text-sm hover:bg-green-50"
                >
                    Answer
                </button>
            @endif
        </div>
        
        <div class="p-4 divide-y divide-gray-100 max-h-96 overflow-y-auto">
            @if($messages->count() > 0)
                @foreach($messages as $message)
                    <div class="py-4 @if($loop->first) pt-2 @endif @if($loop->last) pb-2 @endif">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                                <span class="text-gray-600 font-semibold">{{ substr($message->user->name, 0, 1) }}</span>
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3">
                                    <span class="font-semibold">
                                        @if($message->user_id == Auth::id())
                                            Me
                                        @else
                                            {{ $message->user->name }}
                                        @endif
                                    </span>
                                    <span class="text-sm text-gray-400">{{ $message->created_at->format('M d, Y h:i A') }}</span>
                                </div>
                                
                                <div class="mt-2 text-gray-700 whitespace-pre-line">
                                    {{ $message->message }}
                                </div>
                                
                                @if($message->files->count() > 0)
                                    <div class="mt-3">
                                        <div class="text-sm font-semibold text-gray-500 mb-2">Attachments:</div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            @foreach($message->files as $file)
                                                <div class="flex items-center p-2 border border-gray-200 rounded-md bg-gray-50">
                                                    <i class="fas fa-file text-gray-400 mr-2"></i>
                                                    <div class="flex-1 min-w-0">
                                                        <div class="text-sm font-medium truncate">{{ $file->name }}</div>
                                                        <div class="text-xs text-gray-500">{{ round($file->size / 1024, 2) }} KB</div>
                                                    </div>
                                                    <a href="{{ route('writer.file.download', ['file_id' => $file->id]) }}"
                                                       class="text-green-500 text-sm font-medium hover:text-green-600">
                                                        Download
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="py-8 text-center">
                    <p class="text-gray-500">No messages in this thread yet.</p>
                </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Messages List -->
    <div class="divide-y divide-gray-100">
        @if(isset($messageThreads) && $messageThreads->count() > 0)
            @foreach($messageThreads as $thread)
            <div class="border-b border-gray-100 last:border-b-0 message-thread" 
                data-order-id="{{ $thread->order_id ?? '' }}" 
                data-message-type="{{ $thread->message_type ?? '' }}">
                
                @php
                    // Determine thread ID based on message type
                    $threadId = $thread->is_general 
                        ? 'general_' . $thread->title . '_' . ($thread->user_id == Auth::id() ? $thread->receiver_id : $thread->user_id)
                        : 'order_' . $thread->order_id;
                    
                    // Check if the order is assigned to another writer
                    $isAssignedToAnotherWriter = false;
                    if (!$thread->is_general && isset($thread->order) && $thread->order) {
                        $isAssignedToAnotherWriter = $thread->order->writer_id && $thread->order->writer_id != Auth::id();
                    }
                @endphp

                <div 
                    class="flex items-start gap-4 py-2 px-4 cursor-pointer hover:bg-gray-50"
                    onclick="toggleMessage('message-{{$thread->id}}', this)"
                >
                    <div>
                        <!-- FontAwesome Message Icon -->
                        <i class="fas fa-envelope {{ $thread->read_at ? 'text-gray-400' : 'text-green-500' }}"></i>
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 text-sm">
                            <span class="font-semibold {{ $thread->read_at ? 'text-normal' : 'text-bold' }}" id="message-{{$thread->id}}-label">
                                {{ $thread->user_id == Auth::id() ? 'Me' : ($thread->message_type == 'client' ? 'Customer' : 'Support') }}
                            </span>
                            <span class="text-gray-400">▸</span>
                            <span>{{ $thread->user_id == Auth::id() ? ($thread->message_type == 'client' ? 'Customer' : 'Support') : 'Me' }}</span>
                        </div>
                        <div class="text-sm text-gray-600 font-semibold">
                            Order #{{ $thread->order_id ?? '0' }}: {{ isset($thread->order) ? Str::limit($thread->order->title, 40) : 'New message' }}
                        </div>
                        <div class="text-sm text-gray-400 truncate" id="message-{{$thread->id}}-preview">
                            {{ Str::limit($thread->message, 70) }}
                        </div>
                    </div>

                    <div class="flex flex-col items-end text-sm text-gray-400 whitespace-nowrap">
                        <div class="font-semibold">#{{ $thread->order_id ?? '0' }}</div>
                        <div class="font-semibold">{{ $thread->created_at->format('d M, h:i A') }}</div>
                        @if($thread->files && $thread->files->count() > 0)
                        <div class="flex items-center mt-1">
                            <i class="fas fa-paperclip text-gray-400 mr-1"></i>
                            <span>{{ $thread->files->count() }}</span>
                        </div>
                        @endif
                    </div>

                    <div class="text-gray-400">
                        <svg class="w-4 h-4 transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>

                <div id="message-{{$thread->id}}" class="px-16 py-3 bg-white border-t border-gray-100 hidden">
                    <div class="text-sm text-gray-600">
                        {{ $thread->message }}
                    </div>
                    @if($thread->files && $thread->files->count() > 0)
                    <div class="mt-3">
                        <div class="text-sm font-semibold text-gray-500 mb-2">Attachments:</div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($thread->files as $file)
                                <div class="flex items-center p-2 border border-gray-200 rounded-md bg-gray-50">
                                    <i class="fas fa-file text-gray-400 mr-2"></i>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-medium truncate">{{ $file->name }}</div>
                                        <div class="text-xs text-gray-500">{{ round($file->size / 1024, 2) }} KB</div>
                                    </div>
                                    <a href="{{ route('writer.file.download', ['file_id' => $file->id]) }}"
                                       class="text-green-500 text-sm font-medium hover:text-green-600">
                                        Download
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    <div class="mt-3">
                        @if(!$isAssignedToAnotherWriter)
                        <button 
                            onclick="openAnswerModal('{{ $threadId }}', '{{ $thread->user_id == Auth::id() ? ($thread->message_type == 'client' ? 'Customer' : 'Support') : $thread->user->name }}', '{{ isset($thread->order) ? 'Order #' . $thread->order_id . ': ' . Str::limit($thread->order->title, 40) : $thread->title }}', '{{ Str::limit($thread->message, 200) }}')"
                            class="px-4 py-1.5 text-green-500 border border-green-500 rounded-md text-sm hover:bg-green-50"
                        >
                            Answer
                        </button>
                        @endif
                        <a href="{{ route('writer.message.thread', $threadId) }}" class="ml-2 px-4 py-1.5 text-blue-500 border border-blue-500 rounded-md text-sm hover:bg-blue-50">
                            View Thread
                        </a>
                    </div>
                </div>
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
        
        <div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Order</label>
                <select id="selectOrder" class="w-full p-2 border rounded-md text-sm">
                    <option value="">Select an order...</option>
                    @if(isset($userOrders))
                        @foreach($userOrders as $order)
                        <option value="{{ $order->id }}">Order #{{ $order->id }}: {{ Str::limit($order->title, 40) }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Recipient</label>
                <select id="selectRecipient" class="w-full p-2 border rounded-md text-sm">
                    <option value="">Select recipient...</option>
                    <option value="client">Customer</option>
                    <option value="support">Support</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Message</label>
                <textarea id="messageContent" class="w-full h-32 p-2 border rounded-md text-sm" placeholder="Type your message here..." onkeyup="checkForbiddenWords(this)"></textarea>
                <div id="forbiddenWordsWarning" class="hidden mt-2 p-2 bg-yellow-100 text-yellow-800 text-xs rounded-lg border border-yellow-200">
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
                        id="fileAttachments"
                        class="hidden"
                        onchange="handleFileAttach(event)"
                        multiple
                    />
                    <span class="inline-block px-4 py-2 text-green-500 border border-green-500 rounded-md text-sm hover:bg-green-50">
                        Attach file
                    </span>
                </label>

                <div class="flex justify-end gap-2">
                    <button 
                        onclick="closeNewMessageModal()"
                        class="px-4 py-2 text-gray-600 text-sm hover:bg-gray-50 rounded-md"
                    >
                        Cancel
                    </button>
                    <button type="button" id="sendMessageBtn" onclick="submitMessage()" class="px-4 py-2 bg-green-500 text-white text-sm rounded-md hover:bg-green-600">
                        Send
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Answer Message Modal -->
<div id="answerMessageModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center hidden z-50">
    <div class="bg-white p-8 rounded-md w-full max-w-md">
        <h2 class="text-xl mb-4">Answer Message</h2>

        <!-- Replied Message -->
        <div class="bg-green-50 p-4 rounded-md mb-4">
            <div class="text-sm text-green-600 font-semibold">
                Original message:
            </div>
            <div class="text-sm text-green-600" id="originalMessagePreview">
                Does your excel sheet look like the picture I uploaded?...
            </div>
        </div>

        <form id="answerMessageForm" method="POST" action="{{ route('writer.message.reply') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="threadId" name="thread_id" value="">

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Reply</label>
                <textarea name="message" id="answerContent" class="w-full h-32 p-2 border rounded-md text-sm" placeholder="Type your reply here..." onkeyup="checkForbiddenWords(this)"></textarea>
                <div id="answerForbiddenWordsWarning" class="hidden mt-2 p-2 bg-yellow-100 text-yellow-800 text-xs rounded-lg border border-yellow-200">
                    Warning: Your message contains prohibited keywords. Please avoid payment-related discussions.
                </div>
            </div>

            <!-- Attach File Section for Answer -->
            <div id="answerAttachments" class="mb-4 space-y-2">
                <!-- Attachment rows will be added here -->
            </div>

            <div class="flex justify-between gap-2">
                <label class="cursor-pointer">
                    <input
                        type="file"
                        name="attachments[]"
                        id="answerFileInput"
                        class="hidden"
                        onchange="handleAnswerFileAttach(event)"
                        multiple
                    />
                    <span class="inline-block px-4 py-2 text-green-500 border border-green-500 rounded-md text-sm hover:bg-green-50">
                        Attach file
                    </span>
                </label>

                <div class="flex justify-end gap-2">
                    <button 
                        type="button"
                        onclick="closeAnswerModal()"
                        class="px-4 py-2 text-gray-600 text-sm hover:bg-gray-50 rounded-md"
                    >
                        Cancel
                    </button>
                    <button type="submit" id="sendAnswerBtn" class="px-4 py-2 bg-green-500 text-white text-sm rounded-md hover:bg-green-600">
                        Send
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Toggle the message detail visibility
    function toggleMessage(messageId, button) {
        const messageDetail = document.getElementById(messageId);
        const truncatedMessage = document.getElementById(messageId + '-preview');
        
        messageDetail.classList.toggle('hidden');
        
        // Change the font weight based on visibility
        const label = document.getElementById(messageId + '-label');
        if (messageDetail.classList.contains('hidden')) {
            label.classList.remove('font-bold');
            label.classList.add('font-normal');
        } else {
            label.classList.add('font-bold');
            label.classList.remove('font-normal');
        }
    }

    // Check for forbidden words in the message
    function checkForbiddenWords(inputElement) {
        const forbiddenKeywords = ['dollar', 'money', 'pay', 'shillings', 'cash', 'price', 'payment'];
        const messageText = inputElement.value.toLowerCase();
        const isAnswer = inputElement.id === 'answerContent';
        const warningElement = document.getElementById(isAnswer ? 'answerForbiddenWordsWarning' : 'forbiddenWordsWarning');
        const sendButton = document.getElementById(isAnswer ? 'sendAnswerBtn' : 'sendMessageBtn');
        
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
        document.getElementById('selectOrder').value = '';
        document.getElementById('selectRecipient').value = '';
        document.getElementById('messageContent').value = '';
        document.getElementById('fileAttachments').value = '';
        
        // Clear attachments
        document.getElementById('attachments').innerHTML = '';
        
        // Clear warning
        document.getElementById('forbiddenWordsWarning').classList.add('hidden');
    }

    // Submit message using the correct route
    function submitMessage() {
        const orderId = document.getElementById('selectOrder').value;
        const messageType = document.getElementById('selectRecipient').value;
        const message = document.getElementById('messageContent').value;
        
        if (!orderId || !messageType || !message.trim()) {
            alert('Please fill in all required fields');
            return;
        }
        
        // Create form data
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('message', message);
        formData.append('message_type', messageType);
        
        // Add any attachments
        const fileInput = document.getElementById('fileAttachments');
        if (fileInput.files.length > 0) {
            for (let i = 0; i < fileInput.files.length; i++) {
                formData.append('attachment', fileInput.files[i]);
            }
        }
        
        // Get attachment descriptions
        const descriptionInputs = document.querySelectorAll('input[name="attachment_description"]');
        descriptionInputs.forEach((input, index) => {
            formData.append(`attachment_descriptions[${index}]`, input.value);
        });
        
        // Submit to the correct route
        fetch(`/writer/order/${orderId}/message`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeNewMessageModal();
                // Refresh the page to show new message
                window.location.reload();
            } else {
                alert(data.message || 'Failed to send message');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to send message');
        });
    }

    // Open Answer Modal and display the message
    function openAnswerModal(threadId, receiver, subject, message) {
        document.getElementById('threadId').value = threadId;
        document.getElementById('originalMessagePreview').textContent = message;
        
        // Clear previous inputs
        document.getElementById('answerContent').value = '';
        document.getElementById('answerFileInput').value = '';
        document.getElementById('answerAttachments').innerHTML = '';
        document.getElementById('answerForbiddenWordsWarning').classList.add('hidden');
        
        // Enable the send button (in case it was disabled from a previous attempt)
        const sendButton = document.getElementById('sendAnswerBtn');
        sendButton.disabled = false;
        sendButton.classList.remove('bg-gray-400');
        sendButton.classList.add('bg-green-500', 'hover:bg-green-600');
        
        document.getElementById('answerMessageModal').classList.remove('hidden');
    }

    // Close Answer Modal
    function closeAnswerModal() {
        document.getElementById('answerMessageModal').classList.add('hidden');
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
                    <input type="text" name="attachment_description" placeholder="Description" class="flex-1 p-1 text-sm border rounded focus:outline-none focus:ring-1 focus:ring-green-500" />
                    <button type="button" onclick="removeAttachment(this)" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                `;
                attachmentContainer.appendChild(newAttachment);
            }
        }
    }
    
    // Handle file attachment for answer modal
    function handleAnswerFileAttach(event) {
        const files = event.target.files;
        if (files.length > 0) {
            const attachmentContainer = document.getElementById('answerAttachments');
            
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const newAttachment = document.createElement('div');
                newAttachment.classList.add('flex', 'items-center', 'gap-4', 'p-2', 'bg-gray-50', 'rounded-md');
                newAttachment.innerHTML = `
                    <span class="text-sm text-gray-600 truncate" style="max-width: 180px;">${file.name}</span>
                    <input type="text" name="attachment_descriptions[${i}]" placeholder="Description" class="flex-1 p-1 text-sm border rounded focus:outline-none focus:ring-1 focus:ring-green-500" />
                    <button type="button" onclick="removeAttachment(this)" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-trash-alt"></i>
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
                if (searchTerm && orderId && !orderId.includes(searchTerm)) {
                    visible = false;
                }
                
                // Filter by message type if not "all"
                if (messageType !== 'all' && threadType && threadType !== messageType) {
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