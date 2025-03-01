@extends('writers.app')

@section('content')

<main class="flex-1 px-4 lg:px-8 pb-8 lg:ml-72 transition-all duration-300 pt-24">
    <!-- Header with New Message button, Search bar, and dropdowns -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div class="flex items-center gap-4">
            <h1 class="text-xl font-semibold">Messages</h1>
            <button 
                onclick="openNewMessageModal()"
                class="px-4 py-2 bg-green-500 text-white rounded-md text-sm hover:bg-green-600 transition-colors duration-200 flex items-center gap-2"
            >
                <i class="fas fa-plus-circle"></i> New message
            </button>
        </div>

        <!-- Search and Filter Bar -->
        <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center w-full md:w-auto">
            <div class="relative w-full sm:w-48">
                <select id="message-type-filter" class="select2-departments w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="all">All departments</option>
                    <option value="support">Support</option>
                    <option value="client">Client</option>
                    <option value="writers">Writer's Department</option>
                    <option value="manager">Manager</option>
                    <option value="editors">Editors</option>
                    <option value="dissertation">Dissertation Dept</option>
                </select>
            </div>
            <div class="flex w-full sm:w-auto">
                <input 
                    type="text" 
                    id="search-order"
                    placeholder="Search by order #"
                    class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-l-md text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-green-500"
                />
                <button id="search-button" class="px-4 py-2 bg-green-500 text-white text-sm rounded-r-md hover:bg-green-600 transition-colors duration-200">
                    <i class="fas fa-search"></i>
                </button>
            </div>
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
    <div class="bg-white rounded-lg shadow mb-8 border border-gray-200">
        <div class="flex justify-between items-center p-4 border-b border-gray-200 bg-gray-50">
            <div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('writer.messages') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h2 class="text-lg font-semibold">
                        @if($type === 'order')
                            Order #{{ $order->id }}: {{ Str::limit($order->title, 40) }}
                        @else
                            {{ $title }}
                        @endif
                    </h2>
                </div>
                <p class="text-sm text-gray-500 mt-1">
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
                        class="px-4 py-2 bg-green-500 text-white rounded-md text-sm hover:bg-green-600 transition-colors duration-200 flex items-center gap-2"
                    >
                        <i class="fas fa-reply"></i> Reply
                    </button>
                @endif
            @else
                <button 
                    onclick="openAnswerModal('{{ $type === 'order' ? 'order_' . $order->id : 'general_' . $title . '_' . $otherUser->id }}', '{{ $otherUser->name }}', '{{ $type === 'order' ? 'Order #' . $order->id . ': ' . $order->title : $title }}', '{{ $messages->count() > 0 ? Str::limit($messages->last()->message, 200) : '' }}')"
                    class="px-4 py-2 bg-green-500 text-white rounded-md text-sm hover:bg-green-600 transition-colors duration-200 flex items-center gap-2"
                >
                    <i class="fas fa-reply"></i> Reply
                </button>
            @endif
        </div>
        
        <div class="p-4 divide-y divide-gray-100 max-h-96 overflow-y-auto" id="message-thread">
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
                                
                                @if($message->files && $message->files->count() > 0)
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
                                                    <form method="POST" action="{{ route('writer.file.download') }}" class="inline">
                                                        @csrf
                                                        <input type="hidden" name="file_id" value="{{ $file->id }}">
                                                        <button type="submit" class="text-green-500 text-sm font-medium hover:text-green-600 transition-colors">
                                                            <i class="fas fa-download"></i>
                                                        </button>
                                                    </form>
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
    <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
        <div class="divide-y divide-gray-100">
            @if(isset($messageThreads) && $messageThreads->count() > 0)
                @php
                    // Get the current thread ID if viewing a specific thread
                    $currentThreadId = isset($type) && isset($messages) && $messages->count() > 0 ? 
                        ($type === 'order' ? 'order_' . $order->id : 'general_' . $title . '_' . $otherUser->id) : null;
                @endphp
                
                @foreach($messageThreads as $thread)
                    @php
                        // Determine thread ID based on message type
                        $threadId = $thread->is_general 
                            ? 'general_' . $thread->title . '_' . ($thread->user_id == Auth::id() ? $thread->receiver_id : $thread->user_id)
                            : 'order_' . $thread->order_id;
                        
                        // Skip this thread if it's the one being viewed
                        if ($currentThreadId === $threadId) continue;
                        
                        // Check if the order is assigned to another writer
                        $isAssignedToAnotherWriter = false;
                        if (!$thread->is_general && isset($thread->order) && $thread->order) {
                            $isAssignedToAnotherWriter = $thread->order->writer_id && $thread->order->writer_id != Auth::id();
                        }
                    @endphp

                    <div class="border-b border-gray-100 last:border-b-0 message-thread hover:bg-gray-50 transition-colors duration-200" 
                        data-order-id="{{ $thread->order_id ?? '' }}" 
                        data-message-type="{{ $thread->message_type ?? '' }}">
                        
                        <div 
                            class="flex items-start gap-4 py-3 px-4 cursor-pointer"
                            onclick="toggleMessage('message-{{$thread->id}}', this)"
                        >
                            <div class="flex-shrink-0">
                                <!-- FontAwesome Message Icon -->
                                <div class="w-10 h-10 rounded-full {{ $thread->read_at ? 'bg-gray-100' : 'bg-green-50' }} flex items-center justify-center">
                                    <i class="fas fa-envelope {{ $thread->read_at ? 'text-gray-400' : 'text-green-500' }}"></i>
                                </div>
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

                            <div class="text-gray-400 transform transition-transform" id="message-{{$thread->id}}-arrow">
                                <i class="fas fa-chevron-down"></i>
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
                                            <form method="POST" action="{{ route('writer.file.download') }}" class="inline">
                                                @csrf
                                                <input type="hidden" name="file_id" value="{{ $file->id }}">
                                                <button type="submit" class="text-green-500 text-sm font-medium hover:text-green-600 transition-colors">
                                                    <i class="fas fa-download"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                            <div class="mt-3 flex flex-wrap gap-2">
                                @if(!$isAssignedToAnotherWriter)
                                <button 
                                    onclick="openAnswerModal('{{ $threadId }}', '{{ $thread->user_id == Auth::id() ? ($thread->message_type == 'client' ? 'Customer' : 'Support') : $thread->user->name }}', '{{ isset($thread->order) ? 'Order #' . $thread->order_id . ': ' . Str::limit($thread->order->title, 40) : $thread->title }}', '{{ Str::limit($thread->message, 200) }}')"
                                    class="px-4 py-2 bg-green-500 text-white rounded-md text-sm hover:bg-green-600 transition-colors duration-200 flex items-center gap-2"
                                >
                                    <i class="fas fa-reply"></i> Answer
                                </button>
                                @endif
                                <a href="{{ route('writer.message.thread', $threadId) }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-600 hover:bg-gray-50 transition-colors duration-200 flex items-center gap-2">
                                    <i class="fas fa-eye"></i> View Thread
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
    </div>
</main>

<!-- New Message Modal -->
<div id="newMessageModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center hidden z-50">
    <div class="bg-white p-6 rounded-md w-full max-w-lg mx-4 sm:mx-auto">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">New Message</h2>
            <button onclick="closeNewMessageModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Order</label>
                <select id="selectOrder" class="select2-orders w-full p-2 border rounded-md text-sm">
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
                <select id="selectRecipient" class="select2-recipients w-full p-2 border rounded-md text-sm">
                    <option value="">Select recipient...</option>
                    <option value="client">Customer</option>
                    <option value="support">Support Team</option>
                    <option value="payment">Payment Dept.</option>
                    <option value="quality">Quality Assurance Dept.</option>
                    <option value="dissertation">Dissertation Dept.</option>
                    <option value="writers">Writers Dept.</option>
                    <option value="editors">Editors Dept.</option>
                    <option value="mentors">Mentors Dept.</option>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Subject</label>
                <input type="text" id="messageSubject" class="w-full p-2 border rounded-md text-sm" placeholder="Enter subject" />
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Message</label>
                <textarea id="messageContent" class="w-full h-32 p-2 border rounded-md text-sm" placeholder="Type your message here..." onkeyup="checkForbiddenWords(this)"></textarea>
                <div id="forbiddenWordsWarning" class="hidden mt-2 p-2 bg-yellow-100 text-yellow-800 text-xs rounded-lg border border-yellow-200">
                    <i class="fas fa-exclamation-triangle mr-1"></i> Warning: Your message contains prohibited keywords. Please avoid payment-related discussions.
                </div>
            </div>

            <!-- Attach File Section -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Attachments</label>
                <div id="attachments" class="space-y-2 mt-2 max-h-40 overflow-y-auto">
                    <!-- Attachment rows will be added here -->
                </div>
                
                <div class="mt-2">
                    <label class="cursor-pointer">
                        <input
                            type="file"
                            id="fileAttachments"
                            class="hidden"
                            onchange="handleFileAttach(event)"
                            multiple
                        />
                        <span class="inline-flex items-center px-4 py-2 text-green-500 border border-green-500 rounded-md text-sm hover:bg-green-50 transition-colors duration-200">
                            <i class="fas fa-paperclip mr-2"></i> Attach file
                        </span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <button 
                    onclick="closeNewMessageModal()"
                    class="px-4 py-2 border border-gray-300 text-gray-600 text-sm rounded-md hover:bg-gray-50 transition-colors duration-200"
                >
                    Cancel
                </button>
                <button type="button" id="sendMessageBtn" onclick="submitMessage()" class="px-4 py-2 bg-green-500 text-white text-sm rounded-md hover:bg-green-600 transition-colors duration-200">
                    Send
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Answer Message Modal -->
<div id="answerMessageModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center hidden z-50">
    <div class="bg-white p-6 rounded-md w-full max-w-lg mx-4 sm:mx-auto">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Reply to Message</h2>
            <button onclick="closeAnswerModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Replied Message -->
        <div class="bg-gray-50 p-4 rounded-md mb-4 border-l-4 border-green-500">
            <div class="text-sm text-gray-600 font-semibold">
                Original message:
            </div>
            <div class="text-sm text-gray-600 mt-1" id="originalMessagePreview">
                Does your excel sheet look like the picture I uploaded?...
            </div>
        </div>

        <form id="answerMessageForm" method="POST" action="{{ route('writer.message.reply') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="threadId" name="thread_id" value="">

            <div class="mb-4 hidden">
                <label class="block text-sm font-medium mb-2">Recipient</label>
                <input type="text" id="answerRecipient" class="w-full p-2 border border-gray-200 rounded-md text-sm bg-gray-50" readonly />
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Subject</label>
                <input type="text" id="answerSubject" class="w-full p-2 border border-gray-200 rounded-md text-sm bg-gray-50" readonly />
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Reply</label>
                <textarea name="message" id="answerContent" class="w-full h-32 p-2 border rounded-md text-sm" placeholder="Type your reply here..." onkeyup="checkForbiddenWords(this)"></textarea>
                <div id="answerForbiddenWordsWarning" class="hidden mt-2 p-2 bg-yellow-100 text-yellow-800 text-xs rounded-lg border border-yellow-200">
                    <i class="fas fa-exclamation-triangle mr-1"></i> Warning: Your message contains prohibited keywords. Please avoid payment-related discussions.
                </div>
            </div>

            <!-- Attach File Section for Answer -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Attachments</label>
                <div id="answerAttachments" class="space-y-2 mt-2 max-h-40 overflow-y-auto">
                    <!-- Attachment rows will be added here -->
                </div>
                
                <div class="mt-2">
                    <label class="cursor-pointer">
                        <input
                            type="file"
                            name="attachments[]"
                            id="answerFileInput"
                            class="hidden"
                            onchange="handleAnswerFileAttach(event)"
                            multiple
                        />
                        <span class="inline-flex items-center px-4 py-2 text-green-500 border border-green-500 rounded-md text-sm hover:bg-green-50 transition-colors duration-200">
                            <i class="fas fa-paperclip mr-2"></i> Attach file
                        </span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <button 
                    type="button"
                    onclick="closeAnswerModal()"
                    class="px-4 py-2 border border-gray-300 text-gray-600 text-sm rounded-md hover:bg-gray-50 transition-colors duration-200"
                >
                    Cancel
                </button>
                <button type="submit" id="sendAnswerBtn" class="px-4 py-2 bg-green-500 text-white text-sm rounded-md hover:bg-green-600 transition-colors duration-200">
                    Send Reply
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Include Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- Include Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    // Initialize Select2
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Select2 for departments
        $('.select2-departments').select2({
            placeholder: "Select a department",
            width: '100%',
            dropdownCssClass: "text-sm"
        });
        
        // Initialize Select2 for orders
        $('.select2-orders').select2({
            placeholder: "Select an order",
            width: '100%',
            dropdownCssClass: "text-sm"
        });
        
        // Initialize Select2 for recipients
        $('.select2-recipients').select2({
            placeholder: "Select a recipient",
            width: '100%',
            dropdownCssClass: "text-sm"
        });
        
        // Scroll to active thread if viewing one
        const messageThread = document.getElementById('message-thread');
        if (messageThread) {
            messageThread.scrollTop = messageThread.scrollHeight;
        }
    });

    // Toggle the message detail visibility
    function toggleMessage(messageId, button) {
        const messageDetail = document.getElementById(messageId);
        const messageArrow = document.getElementById(messageId + '-arrow');
        
        messageDetail.classList.toggle('hidden');
        
        // Rotate arrow icon
        if (messageDetail.classList.contains('hidden')) {
            messageArrow.classList.remove('rotate-180');
        } else {
            messageArrow.classList.add('rotate-180');
        }
        
        // Change the font weight based on visibility
        const label = document.getElementById(messageId + '-label');
        if (label) {
            if (messageDetail.classList.contains('hidden')) {
                label.classList.remove('font-bold');
                label.classList.add('font-normal');
            } else {
                label.classList.add('font-bold');
                label.classList.remove('font-normal');
            }
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
        $('#selectOrder').val('').trigger('change');
        $('#selectRecipient').val('').trigger('change');
        document.getElementById('messageSubject').value = '';
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
        const subject = document.getElementById('messageSubject').value;
        const message = document.getElementById('messageContent').value;
        
        if (!messageType || !message.trim()) {
            alert('Please fill in all required fields');
            return;
        }
        
        // Create form data
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('message', message);
        formData.append('message_type', messageType);
        formData.append('title', subject);
        
        if (orderId) {
            formData.append('order_id', orderId);
        }
        
        // Add any attachments
        const fileInput = document.getElementById('fileAttachments');
        if (fileInput.files.length > 0) {
            for (let i = 0; i < fileInput.files.length; i++) {
                formData.append('attachments[]', fileInput.files[i]);
            }
        }
        
        // Get attachment descriptions
        const descriptionInputs = document.querySelectorAll('input[name="attachment_description"]');
        descriptionInputs.forEach((input, index) => {
            formData.append(`attachment_descriptions[${index}]`, input.value);
        });
        
        // Show loading state
        const sendButton = document.getElementById('sendMessageBtn');
        const originalText = sendButton.innerHTML;
        sendButton.disabled = true;
        sendButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
        
        // Submit to the correct route
        let url = orderId ? `/writer/order/${orderId}/message` : '/writer/send-message';
        
        fetch(url, {
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
                // Reset button state
                sendButton.disabled = false;
                sendButton.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to send message');
            // Reset button state
            sendButton.disabled = false;
            sendButton.innerHTML = originalText;
        });
    }

    // Open Answer Modal and display the message
    function openAnswerModal(threadId, receiver, subject, message) {
        document.getElementById('threadId').value = threadId;
        document.getElementById('answerRecipient').value = receiver;
        document.getElementById('answerSubject').value = subject;
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
                newAttachment.classList.add('flex', 'items-center', 'gap-3', 'p-3', 'bg-gray-50', 'rounded-md', 'border', 'border-gray-200');
                newAttachment.innerHTML = `
                    <div class="flex-shrink-0 text-gray-400">
                        <i class="fas fa-file"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium truncate">${file.name}</div>
                        <div class="text-xs text-gray-500">${(file.size / 1024).toFixed(2)} KB</div>
                    </div>
                    <input type="text" name="attachment_description" placeholder="Description" class="flex-1 p-2 text-sm border rounded focus:outline-none focus:ring-1 focus:ring-green-500" />
                    <button type="button" onclick="removeAttachment(this)" class="text-gray-400 hover:text-red-500 transition-colors">
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
                newAttachment.classList.add('flex', 'items-center', 'gap-3', 'p-3', 'bg-gray-50', 'rounded-md', 'border', 'border-gray-200');
                newAttachment.innerHTML = `
                    <div class="flex-shrink-0 text-gray-400">
                        <i class="fas fa-file"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium truncate">${file.name}</div>
                        <div class="text-xs text-gray-500">${(file.size / 1024).toFixed(2)} KB</div>
                    </div>
                    <input type="text" name="attachment_descriptions[${i}]" placeholder="Description" class="flex-1 p-2 text-sm border rounded focus:outline-none focus:ring-1 focus:ring-green-500" />
                    <button type="button" onclick="removeAttachment(this)" class="text-gray-400 hover:text-red-500 transition-colors">
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
        
        // Handle form submission for answer form with AJAX
        const answerForm = document.getElementById('answerMessageForm');
        if (answerForm) {
            answerForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(answerForm);
                const submitButton = document.getElementById('sendAnswerBtn');
                const originalText = submitButton.innerHTML;
                
                // Show loading state
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
                
                fetch(answerForm.action, {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        closeAnswerModal();
                        // Refresh the page to show the reply
                        window.location.reload();
                    } else {
                        alert(data.message || 'Failed to send reply');
                        // Reset button state
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalText;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to send reply. Please try again.');
                    // Reset button state
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalText;
                });
            });
        }
    });
</script>

@endsection