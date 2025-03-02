@extends('writers.app')

@section('content')

<main class="flex-1 px-4 lg:px-8 pb-8 lg:ml-72 transition-all duration-300 pt-24">
    <!-- Header with New Message button, Search bar, and dropdowns -->
    <div class="flex flex-wrap justify-between items-center gap-3 mb-4">
        <div class="flex items-center gap-2">
            <h1 class="text-xl font-semibold">Messages</h1>
            <button 
                onclick="openNewMessageModal()"
                class="px-2 py-1 bg-green-500 text-white rounded-md text-xs hover:bg-green-600 transition-colors duration-200"
            >
                New message
            </button>
        </div>

        <!-- Search and Filter Bar -->
        <div class="flex items-center gap-2">
            <div class="relative w-36 sm:w-48">
                <select id="message-type-filter" class="select2-departments w-full px-2 py-1 border border-gray-300 rounded-md text-xs text-gray-600 focus:outline-none focus:ring-1 focus:ring-green-500">
                    <option value="all">All departments</option>
                    <option value="support">Support</option>
                    <option value="client">Client</option>
                    <option value="writers">Writer's Department</option>
                    <option value="manager">Manager</option>
                    <option value="editors">Editors</option>
                    <option value="dissertation">Dissertation Dept</option>
                </select>
            </div>
            <div class="flex">
                <input 
                    type="text" 
                    id="search-order"
                    placeholder="Order"
                    class="w-24 sm:w-auto px-2 py-1 border border-gray-300 rounded-l-md text-xs text-gray-600 focus:outline-none focus:ring-1 focus:ring-green-500"
                />
                <button id="search-button" class="px-2 py-1 bg-green-500 text-white text-xs rounded-r-md hover:bg-green-600 transition-colors duration-200">
                    Search
                </button>
            </div>
        </div>
    </div>

    <!-- Alert for success or error messages -->
    <div id="alert-container" class="mb-4">
        @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-3 rounded-md alert-message">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
                <button class="ml-auto" onclick="this.parentElement.parentElement.remove()">
                    <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-3 rounded-md alert-message">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
                <button class="ml-auto" onclick="this.parentElement.parentElement.remove()">
                    <svg class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        @endif
    </div>

    <!-- Display Message Thread if we're viewing a specific thread -->
    @if(isset($messages) && isset($type))
    <div class="bg-white rounded-lg shadow mb-6 border border-gray-200">
        <div class="flex justify-between items-center p-3 border-b border-gray-200 bg-gray-50">
            <div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('writer.messages') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h2 class="text-base font-semibold flex items-center gap-2">
                        @if($type === 'order')
                            <a href="{{ route('availableOrderDetails', ['id' => $order->id]) }}" class="text-blue-500 hover:text-blue-700">
                                Order #{{ $order->id }}
                            </a>
                            <span class="text-gray-700">{{ Str::limit($order->title, 40) }}</span>
                        @else
                            {{ $title }}
                        @endif
                    </h2>
                </div>
                <p class="text-xs text-gray-500 mt-1">
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
                    $isAssignedToCurrentWriter = $order->writer_id == Auth::id();
                    $isAvailableOrder = $order->status == 'available';
                    $canReply = $isAssignedToCurrentWriter || $isAvailableOrder;
                @endphp
                
                @if($canReply)
                    <button 
                        onclick="openReplyModal('{{ $type === 'order' ? 'order_' . $order->id : 'general_' . $title . '_' . $otherUser->id }}', '{{ isset($otherUser) ? $otherUser->name : 'Support' }}', '{{ $type === 'order' ? 'Order #' . $order->id . ': ' . $order->title : $title }}', '{{ $messages->count() > 0 ? addslashes(Str::limit($messages->last()->message, 200)) : '' }}')"
                        class="px-2 py-1 bg-green-500 text-white rounded-md text-xs hover:bg-green-600 transition-colors duration-200 flex items-center gap-1"
                    >
                        <i class="fas fa-reply"></i> Reply
                    </button>
                @endif
            @else
                <button 
                    onclick="openReplyModal('{{ $type === 'order' ? 'order_' . $order->id : 'general_' . $title . '_' . $otherUser->id }}', '{{ $otherUser->name }}', '{{ $type === 'order' ? 'Order #' . $order->id . ': ' . $order->title : $title }}', '{{ $messages->count() > 0 ? addslashes(Str::limit($messages->last()->message, 200)) : '' }}')"
                    class="px-2 py-1 bg-green-500 text-white rounded-md text-xs hover:bg-green-600 transition-colors duration-200 flex items-center gap-1"
                >
                    <i class="fas fa-reply"></i> Reply
                </button>
            @endif
        </div>
        
        <div class="p-4 divide-y divide-gray-100 max-h-96 overflow-y-auto" id="message-thread">
            @if($messages->count() > 0)
                @foreach($messages as $message)
                    <div class="py-3 @if($loop->first) pt-2 @endif @if($loop->last) pb-2 @endif" data-message-id="{{ $message->id }}">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
                                <span class="text-gray-600 font-semibold text-xs">{{ substr($message->user->name, 0, 1) }}</span>
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-sm">
                                        @if($message->user_id == Auth::id())
                                            Me
                                        @else
                                            {{ $message->user->name }}
                                        @endif
                                    </span>
                                    <span class="text-xs text-gray-400">{{ $message->created_at->format('M d, Y h:i A') }}</span>
                                </div>
                                
                                <div class="mt-1 text-sm text-gray-700 whitespace-pre-line">
                                    {{ $message->message }}
                                </div>
                                
                                @if($message->files && $message->files->count() > 0)
                                    <div class="mt-2">
                                        <div class="text-xs font-semibold text-gray-500 mb-1">Attachments:</div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                            @foreach($message->files as $file)
                                                <div class="flex items-center p-2 border border-gray-200 rounded-md bg-gray-50">
                                                    <i class="fas fa-file text-gray-400 mr-2"></i>
                                                    <div class="flex-1 min-w-0">
                                                        <div class="text-xs font-medium truncate">{{ $file->name }}</div>
                                                        <div class="text-xs text-gray-500">{{ round($file->size / 1024, 2) }} KB</div>
                                                    </div>
                                                    <form method="POST" action="{{ route('writer.file.download') }}" class="inline">
                                                        @csrf
                                                        <input type="hidden" name="file_id" value="{{ $file->id }}">
                                                        <button type="submit" class="text-green-500 text-xs font-medium hover:text-green-600 transition-colors">
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
                    <p class="text-gray-500 text-sm">No messages in this thread yet.</p>
                </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Messages List -->
    <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
        <div id="message-list" class="divide-y divide-gray-100">
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
                        
                        // Check if the order is assigned to current writer or available
                        $isAssignedToCurrentWriter = false;
                        $isAvailableOrder = false;
                        if (!$thread->is_general && isset($thread->order) && $thread->order) {
                            $isAssignedToCurrentWriter = $thread->order->writer_id == Auth::id();
                            $isAvailableOrder = $thread->order->status == 'available';
                        }
                        $canReply = $isAssignedToCurrentWriter || $isAvailableOrder || $thread->is_general;
                    @endphp

                    <div class="border-b border-gray-100 last:border-b-0 message-thread hover:bg-gray-50 transition-colors duration-200" 
                        data-order-id="{{ $thread->order_id ?? '' }}" 
                        data-message-type="{{ $thread->message_type ?? '' }}"
                        data-message-id="{{ $thread->id }}">
                        
                        <div 
                            class="flex items-start gap-3 py-2 px-3 cursor-pointer"
                            onclick="toggleMessage('message-{{$thread->id}}', this)"
                        >
                            <div class="flex-shrink-0">
                                <!-- Icon - envelope for unread, eye for read -->
                                <div class="w-8 h-8 rounded-full {{ $thread->read_at ? 'bg-gray-100' : 'bg-green-50' }} flex items-center justify-center">
                                    <i class="fas {{ $thread->read_at ? 'fa-eye' : 'fa-envelope' }} {{ $thread->read_at ? 'text-gray-400' : 'text-green-500' }} text-xs"></i>
                                </div>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-1 text-xs">
                                    <span class="font-semibold {{ $thread->read_at ? 'text-normal' : 'text-bold' }}" id="message-{{$thread->id}}-label">
                                        {{ $thread->user_id == Auth::id() ? 'Me' : ($thread->message_type == 'client' ? 'Customer' : 'Support') }}
                                    </span>
                                    <span class="text-gray-400">▸</span>
                                    <span>{{ $thread->user_id == Auth::id() ? ($thread->message_type == 'client' ? 'Customer' : 'Support') : 'Me' }}</span>
                                </div>
                                <div class="text-xs text-gray-600 font-semibold">
                                    Order #{{ $thread->order_id ?? '0' }}: {{ isset($thread->order) ? Str::limit($thread->order->title, 40) : 'New message' }}
                                </div>
                                <div class="text-xs text-gray-400 truncate" id="message-{{$thread->id}}-preview">
                                    {{ Str::limit($thread->message, 70) }}
                                </div>
                            </div>

                            <div class="flex flex-col items-end text-xs text-gray-400 whitespace-nowrap">
                                <div class="font-semibold">#{{ $thread->order_id ?? '0' }}</div>
                                <div class="font-semibold">{{ $thread->created_at->format('d M, h:i A') }}</div>
                                @if($thread->files && $thread->files->count() > 0)
                                <div class="flex items-center mt-1">
                                    <i class="fas fa-paperclip text-gray-400 mr-1"></i>
                                    <span>{{ $thread->files->count() }}</span>
                                </div>
                                @endif
                            </div>

                            <div class="text-gray-400 transform transition-transform text-xs" id="message-{{$thread->id}}-arrow">
                                <i class="fas fa-chevron-down"></i>
                            </div>
                        </div>

                        <div id="message-{{$thread->id}}" class="px-12 py-2 bg-white border-t border-gray-100 hidden">
                            <div class="text-xs text-gray-600">
                                {{ $thread->message }}
                            </div>
                            @if($thread->files && $thread->files->count() > 0)
                            <div class="mt-2">
                                <div class="text-xs font-semibold text-gray-500 mb-1">Attachments:</div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    @foreach($thread->files as $file)
                                        <div class="flex items-center p-2 border border-gray-200 rounded-md bg-gray-50">
                                            <i class="fas fa-file text-gray-400 mr-1 text-xs"></i>
                                            <div class="flex-1 min-w-0">
                                                <div class="text-xs font-medium truncate">{{ $file->name }}</div>
                                                <div class="text-xs text-gray-500">{{ round($file->size / 1024, 2) }} KB</div>
                                            </div>
                                            <form method="POST" action="{{ route('writer.file.download') }}" class="inline">
                                                @csrf
                                                <input type="hidden" name="file_id" value="{{ $file->id }}">
                                                <button type="submit" class="text-green-500 text-xs font-medium hover:text-green-600 transition-colors">
                                                    <i class="fas fa-download"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                            <div class="mt-2 flex flex-wrap gap-2">
                                @if($canReply)
                                <button 
                                    onclick="openReplyModal('{{ $threadId }}', '{{ $thread->user_id == Auth::id() ? ($thread->message_type == 'client' ? 'Customer' : 'Support') : $thread->user->name }}', '{{ isset($thread->order) ? 'Order #' . $thread->order_id . ': ' . Str::limit($thread->order->title, 40) : $thread->title }}', '{{ addslashes(Str::limit($thread->message, 200)) }}')"
                                    class="px-2 py-1 bg-green-500 text-white rounded-md text-xs hover:bg-green-600 transition-colors duration-200 flex items-center gap-1"
                                >
                                    <i class="fas fa-reply"></i> Reply
                                </button>
                                @endif
                                <a href="{{ route('writer.message.thread', $threadId) }}" class="px-2 py-1 border border-gray-300 rounded-md text-xs text-gray-600 hover:bg-gray-50 transition-colors duration-200 flex items-center gap-1">
                                    <i class="fas fa-eye"></i> View Thread
                                </a>
                                @if(isset($thread->order) && $thread->order)
                                <a href="{{ $isAssignedToCurrentWriter ? route('assigned', ['id' => $thread->order_id]) : '/order/' . $thread->order_id }}" class="px-2 py-1 border border-blue-300 rounded-md text-xs text-blue-600 hover:bg-blue-50 transition-colors duration-200 flex items-center gap-1">
                                    <i class="fas fa-external-link-alt"></i> View Order
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="py-6 text-center">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <h3 class="text-base font-medium text-gray-600 mb-1">No messages yet</h3>
                    <p class="text-xs text-gray-500">Start a new conversation with support or clients</p>
                </div>
            @endif
        </div>
    </div>
</main>

<!-- New Message Modal -->
<div id="newMessageModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center hidden z-50">
    <div class="bg-white p-4 rounded-md w-full max-w-md mx-4 sm:mx-auto">
        <div class="flex justify-between items-center mb-3">
            <h2 class="text-base font-semibold">New Message</h2>
            <button onclick="closeNewMessageModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="newMessageForm" method="POST" action="{{ route('writer.message.sendNew') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="block text-xs font-medium mb-1">Recipient</label>
                <select id="selectRecipient" name="receiver_id" class="select2-recipients w-full p-1.5 border rounded-md text-xs">
                    <option value="">Select recipient...</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->display_name }}</option>
                    @endforeach
                </select>
                @error('receiver_id')
                <span class="text-xs text-red-600">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="mb-3">
                <label class="block text-xs font-medium mb-1">Subject</label>
                <input type="text" id="messageSubject" name="title" class="w-full p-1.5 border rounded-md text-xs" placeholder="Enter your subject" />
                @error('title')
                <span class="text-xs text-red-600">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="mb-3">
                <label class="block text-xs font-medium mb-1">Order (Optional)</label>
                <select id="selectOrder" name="order_id" class="select2-orders w-full p-1.5 border rounded-md text-xs">
                    <option value="">Select an order...</option>
                    @if(isset($userOrders))
                        @foreach($userOrders as $order)
                        <option value="{{ $order->id }}">Order #{{ $order->id }}: {{ Str::limit($order->title, 40) }}</option>
                        @endforeach
                    @endif
                </select>
                @error('order_id')
                <span class="text-xs text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <label class="block text-xs font-medium mb-1">Message</label>
                <textarea id="messageContent" name="message" class="w-full h-24 p-1.5 border rounded-md text-xs" placeholder="Type your message here..." onkeyup="checkForbiddenWords(this)"></textarea>
                <div id="forbiddenWordsWarning" class="hidden mt-1 p-1.5 bg-yellow-100 text-yellow-800 text-xs rounded-lg border border-yellow-200">
                    <i class="fas fa-exclamation-triangle mr-1"></i> Warning: Your message contains prohibited keywords. Please avoid payment-related discussions.
                </div>
                @error('message')
                <span class="text-xs text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <!-- Attach File Section -->
            <div class="mb-3">
                <label class="block text-xs font-medium mb-1">Attachments</label>
                <div id="attachments" class="space-y-2 mt-1 max-h-28 overflow-y-auto">
                    <!-- Attachment rows will be added here -->
                </div>
                
                <div class="mt-1">
                    <label class="cursor-pointer">
                        <input
                            type="file"
                            id="fileAttachments"
                            name="attachments[]"
                            class="hidden"
                            onchange="handleFileAttach(event)"
                            multiple
                        />
                        <span class="inline-flex items-center px-2 py-1 text-green-500 border border-green-500 rounded-md text-xs hover:bg-green-50 transition-colors duration-200">
                            <i class="fas fa-paperclip mr-1"></i> Attach file
                        </span>
                    </label>
                    @error('attachments')
                    <span class="text-xs text-red-600 block mt-1">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-4">
                <button 
                    type="button"
                    onclick="closeNewMessageModal()"
                    class="px-2 py-1 border border-gray-300 text-gray-600 text-xs rounded-md hover:bg-gray-50 transition-colors duration-200"
                >
                    Cancel
                </button>
                <button 
                    type="submit" 
                    id="sendMessageBtn" 
                    class="px-2 py-1 bg-green-500 text-white text-xs rounded-md hover:bg-green-600 transition-colors duration-200">
                   Send
               </button>
           </div>
       </form>
   </div>
</div>

<!-- Reply Message Modal -->
<div id="replyMessageModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center hidden z-50">
   <div class="bg-white p-4 rounded-md w-full max-w-md mx-4 sm:mx-auto">
       <div class="flex justify-between items-center mb-3">
           <h2 class="text-base font-semibold">Reply to Message</h2>
           <button onclick="closeReplyModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
               <i class="fas fa-times"></i>
           </button>
       </div>

       <form id="replyMessageForm" method="POST" action="{{ route('writer.message.reply') }}" enctype="multipart/form-data">
           @csrf
           <input type="hidden" id="threadId" name="thread_id" value="">

           <!-- Original Message -->
           <div class="mb-3">
               <label class="block text-xs font-medium mb-1">Original message:</label>
               <div class="bg-gray-50 p-2 rounded-md border-l-4 border-green-500 max-h-28 overflow-y-auto">
                   <div class="text-xs text-gray-600 whitespace-pre-line" id="originalMessagePreview"></div>
               </div>
           </div>

           <div class="mb-3">
               <label class="block text-xs font-medium mb-1">Subject</label>
               <input type="text" id="replySubject" name="subject" class="w-full p-1.5 border border-gray-200 rounded-md text-xs bg-gray-50" readonly />
           </div>

           <div class="mb-3">
            <label class="block text-xs font-medium mb-1">Reply</label>
            <textarea name="message" id="replyContent" class="w-full h-24 p-1.5 border rounded-md text-xs" placeholder="Type your reply here..." onkeyup="checkForbiddenWords(this, 'reply')"></textarea>
            <div id="replyForbiddenWordsWarning" class="hidden mt-1 p-1.5 bg-yellow-100 text-yellow-800 text-xs rounded-lg border border-yellow-200">
                <i class="fas fa-exclamation-triangle mr-1"></i> Warning: Your message contains prohibited keywords. Please avoid payment-related discussions.
            </div>
            @error('message')
            <span class="text-xs text-red-600">{{ $message }}</span>
            @enderror
         </div>
         
         <!-- Attach File Section for Reply -->
         <div class="mb-3">
            <label class="block text-xs font-medium mb-1">Attachments</label>
            <div id="replyAttachments" class="space-y-2 mt-1 max-h-28 overflow-y-auto">
                <!-- Attachment rows will be added here -->
            </div>
            
            <div class="mt-1">
                <label class="cursor-pointer">
                    <input
                        type="file"
                        name="attachments[]"
                        id="replyFileInput"
                        class="hidden"
                        onchange="handleReplyFileAttach(event)"
                        multiple
                    />
                    <span class="inline-flex items-center px-2 py-1 text-green-500 border border-green-500 rounded-md text-xs hover:bg-green-50 transition-colors duration-200">
                        <i class="fas fa-paperclip mr-1"></i> Attach file
                    </span>
                </label>
                @error('attachments')
                <span class="text-xs text-red-600 block mt-1">{{ $message }}</span>
                @enderror
            </div>
         </div>
         
         <div class="flex justify-end gap-2 mt-4">
            <button 
                type="button"
                onclick="closeReplyModal()"
                class="px-2 py-1 border border-gray-300 text-gray-600 text-xs rounded-md hover:bg-gray-50 transition-colors duration-200"
            >
                Cancel
            </button>
            <button 
                type="submit" 
                id="sendReplyBtn" 
                class="px-2 py-1 bg-green-500 text-white text-xs rounded-md hover:bg-green-600 transition-colors duration-200"
            >
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
         let pollingTimer = null;
         
         // Initialize Select2
         document.addEventListener('DOMContentLoaded', function() {
            // Initialize Select2 for departments
            $('.select2-departments').select2({
                placeholder: "All departments",
                width: '100%',
                dropdownCssClass: "text-xs"
            });
            
            // Initialize Select2 for orders
            $('.select2-orders').select2({
                placeholder: "Select an order",
                width: '100%',
                dropdownCssClass: "text-xs"
            });
            
            // Initialize Select2 for recipients
            $('.select2-recipients').select2({
                placeholder: "Select a recipient",
                width: '100%',
                dropdownCssClass: "text-xs"
            });
            
            // Scroll to active thread if viewing one
            const messageThread = document.getElementById('message-thread');
            if (messageThread) {
                messageThread.scrollTop = messageThread.scrollHeight;
            }
            
            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                document.querySelectorAll('.alert-message').forEach(function(alert) {
                    alert.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                    setTimeout(function() {
                        alert.remove();
                    }, 500);
                });
            }, 5000);
            
            // Set up search functionality
            setupSearch();
            
            // Start polling for new messages - check more frequently (every second)
            startPolling();
         
            // Form submission handling for new message form
            document.getElementById('newMessageForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const recipient = document.getElementById('selectRecipient').value;
                const message = document.getElementById('messageContent').value;
                
                if (!recipient || !message.trim()) {
                    showAlert('Please select a recipient and enter a message', 'error');
                    return false;
                }
         
                // Check for forbidden words
                const forbiddenKeywords = ['dollar', 'money', 'pay', 'shillings', 'cash', 'price', 'payment'];
                const messageText = message.toLowerCase();
                
                let containsForbiddenWord = false;
                
                forbiddenKeywords.forEach(keyword => {
                    if (messageText.includes(keyword)) {
                        containsForbiddenWord = true;
                    }
                });
                
                if (containsForbiddenWord) {
                    showAlert('Your message contains prohibited keywords. Please avoid payment-related discussions.', 'error');
                    return false;
                }
         
                // Show loading state
                const sendButton = document.getElementById('sendMessageBtn');
                sendButton.disabled = true;
                sendButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
                
                // Submit the form normally
                this.submit();
            });
         
            // Form submission handling for reply form
            document.getElementById('replyMessageForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const message = document.getElementById('replyContent').value;
                
                if (!message.trim()) {
                    showAlert('Please enter a message', 'error');
                    return false;
                }
         
                // Check for forbidden words
                const forbiddenKeywords = ['dollar', 'money', 'pay', 'shillings', 'cash', 'price', 'payment'];
                const messageText = message.toLowerCase();
                
                let containsForbiddenWord = false;
                
                forbiddenKeywords.forEach(keyword => {
                    if (messageText.includes(keyword)) {
                        containsForbiddenWord = true;
                    }
                });
                
                if (containsForbiddenWord) {
                    showAlert('Your message contains prohibited keywords. Please avoid payment-related discussions.', 'error');
                    return false;
                }
         
                // Show loading state
                const sendButton = document.getElementById('sendReplyBtn');
                sendButton.disabled = true;
                sendButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
                
                // Submit the form normally
                this.submit();
            });
         
            // Check for validation errors and open modals if needed
            if (document.querySelectorAll('#newMessageForm .text-red-600').length > 0) {
                openNewMessageModal();
            }
            
            if (document.querySelectorAll('#replyMessageForm .text-red-600').length > 0) {
                openReplyModal();
            }
         });
         
         // Function to start polling for new messages
         function startPolling() {
            // Clear any existing polling
            clearInterval(pollingTimer);
            
            // Poll every second for new messages (real-time updates)
            pollingTimer = setInterval(function() {
                checkForNewMessages();
            }, 1000); // 1 second
         }
         
         // Function to check for new messages
         function checkForNewMessages() {
            // Get the thread ID if we're viewing a specific thread
            const messageThread = document.getElementById('message-thread');
            
            // If we're viewing a thread, check for new messages in that thread
            if (messageThread) {
                const threadId = document.getElementById('threadId')?.value;
                if (threadId) {
                    const parts = threadId.split('_');
                    if (parts[0] === 'order') {
                        const orderId = parts[1];
                        checkThreadMessages(orderId);
                    }
                }
            }
            
            // In any case, refresh the message list
            refreshMessageList();
         }
         
         // Function to check for new messages in a specific thread
         function checkThreadMessages(orderId) {
            // Get the latest message ID in the thread
            const messages = document.querySelectorAll('#message-thread > div');
            const lastMessageId = messages.length > 0 ? messages[messages.length - 1].dataset.messageId : 0;
            
            fetch(`/writer/order/${orderId}/check-messages?last_id=${lastMessageId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.hasNewMessages) {
                    // Instead of reloading the entire page, we can append the new messages dynamically
                    // Or reload if that's simpler for the current implementation
                    if (data.messages && data.messages.length > 0) {
                        data.messages.forEach(message => {
                            appendMessageToThread(message);
                        });
                    } else {
                        // If we don't have the message details, reload the page
                        window.location.reload();
                    }
                }
            })
            .catch(error => {
                console.error('Error checking messages:', error);
            });
         }
         
         // Function to refresh message list without reloading the page
         function refreshMessageList() {
            const messageList = document.getElementById('message-list');
            if (!messageList) return;
            
            fetch('/writer/messages/list', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to load messages');
                }
                return response.json();
            })
            .then(data => {
                if (data.html) {
                    messageList.innerHTML = data.html;
                    
                    // Initialize any new toggle events
                    document.querySelectorAll('.message-thread').forEach(thread => {
                        const messageId = thread.querySelector('[id^="message-"]')?.id;
                        if (messageId) {
                            const toggleButton = thread.querySelector('[onclick^="toggleMessage"]');
                            if (toggleButton && !toggleButton.hasAttribute('data-initialized')) {
                                toggleButton.setAttribute('data-initialized', 'true');
                            }
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Error refreshing messages:', error);
            });
         }
         
         // Function to show a custom alert
         function showAlert(message, type) {
            const alertContainer = document.getElementById('alert-container');
            const alertClass = type === 'success' ? 'bg-green-50 border-green-500 text-green-700' : 'bg-red-50 border-red-500 text-red-700';
            const iconClass = type === 'success' ? 'text-green-500' : 'text-red-500';
            const iconPath = type === 'success' 
                ? 'M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z'
                : 'M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z';
            
            const alertHtml = `
                <div class="${alertClass} border-l-4 p-3 rounded-md alert-message">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 ${iconClass}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="${iconPath}" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-xs">${message}</p>
                        </div>
                        <button class="ml-auto" onclick="this.parentElement.parentElement.remove()">
                            <svg class="h-5 w-5 ${iconClass}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            `;
            
            alertContainer.innerHTML = alertHtml + alertContainer.innerHTML;
            
            // Auto-hide after 5 seconds
            setTimeout(function() {
                const newAlert = alertContainer.querySelector('.alert-message');
                if (newAlert) {
                    newAlert.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                    setTimeout(function() {
                        newAlert.remove();
                    }, 500);
                }
            }, 5000);
         }
         
         // Toggle the message detail visibility
         function toggleMessage(messageId, button) {
            const messageDetail = document.getElementById(messageId);
            const messageArrow = document.getElementById(messageId + '-arrow');
            
            if (!messageDetail || !messageArrow) return;
            
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
         function checkForbiddenWords(inputElement, type = 'new') {
            const forbiddenKeywords = ['dollar', 'money', 'pay', 'shillings', 'cash', 'price', 'payment'];
            const messageText = inputElement.value.toLowerCase();
            
            const warningElementId = type === 'reply' ? 'replyForbiddenWordsWarning' : 'forbiddenWordsWarning';
            const buttonId = type === 'reply' ? 'sendReplyBtn' : 'sendMessageBtn';
            
            const warningElement = document.getElementById(warningElementId);
            const sendButton = document.getElementById(buttonId);
            
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
         
         // Open Reply Modal
         function openReplyModal(threadId, receiver, subject, message) {
            const modal = document.getElementById('replyMessageModal');
            if (!modal) return;
            
            modal.classList.remove('hidden');
            
            // Only set these values if parameters are provided (for validation errors case)
            if (threadId) {
                document.getElementById('threadId').value = threadId;
                document.getElementById('replySubject').value = subject || '';
                document.getElementById('originalMessagePreview').textContent = message || '';
            }
            
            // Only clear inputs if not coming from validation error
            if (threadId) {
                // Clear previous inputs
                document.getElementById('replyContent').value = '';
                document.getElementById('replyFileInput').value = '';
                document.getElementById('replyAttachments').innerHTML = '';
                document.getElementById('replyForbiddenWordsWarning').classList.add('hidden');
            }
            
            // Enable the send button (in case it was disabled from a previous attempt)
            const sendButton = document.getElementById('sendReplyBtn');
            sendButton.disabled = false;
            sendButton.classList.remove('bg-gray-400');
            sendButton.classList.add('bg-green-500', 'hover:bg-green-600');
         }
         
         // Close Reply Modal
         function closeReplyModal() {
            document.getElementById('replyMessageModal').classList.add('hidden');
         }
         
         // Function to append a new message to the thread
         function appendMessageToThread(message) {
            const messageThread = document.getElementById('message-thread');
            if (!messageThread) return;
            
            // Check if this message already exists in the thread
            if (document.querySelector(`[data-message-id="${message.id}"]`)) {
                return; // Skip if already displayed
            }
            
            const newMessageElement = document.createElement('div');
            newMessageElement.className = 'py-3';
            newMessageElement.dataset.messageId = message.id;
            
            // Format date for display
            let formattedDate;
            try {
                const messageDate = new Date(message.created_at);
                formattedDate = messageDate.toLocaleString('en-US', {
                    month: 'short',
                    day: 'numeric',
                    year: 'numeric',
                    hour: 'numeric',
                    minute: '2-digit',
                    hour12: true
                });
            } catch (e) {
                formattedDate = message.created_at; // Fallback to the raw date
            }
            
            // Build message HTML
            let messageHtml = `
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
                        <span class="text-gray-600 font-semibold text-xs">${message.user ? (message.user.name.charAt(0)) : 'U'}</span>
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-sm">
                                ${message.user_id == {{ Auth::id() }} ? 'Me' : (message.user ? message.user.name : 'User')}
                            </span>
                            <span class="text-xs text-gray-400">${formattedDate}</span>
                        </div>
                        
                        <div class="mt-1 text-sm text-gray-700 whitespace-pre-line">
                            ${message.message}
                        </div>
            `;
            
            // Add attachments if present
            if (message.files && message.files.length > 0) {
                messageHtml += `
                    <div class="mt-2">
                        <div class="text-xs font-semibold text-gray-500 mb-1">Attachments:</div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                `;
                
                message.files.forEach(file => {
                    messageHtml += `
                        <div class="flex items-center p-2 border border-gray-200 rounded-md bg-gray-50">
                            <i class="fas fa-file text-gray-400 mr-2"></i>
                            <div class="flex-1 min-w-0">
                                <div class="text-xs font-medium truncate">${file.name}</div>
                                <div class="text-xs text-gray-500">${Math.round(file.size / 1024 * 100) / 100} KB</div>
                            </div>
                            <form method="POST" action="/file/download" class="inline">
                                @csrf
                                <input type="hidden" name="file_id" value="${file.id}">
                                <button type="submit" class="text-green-500 text-xs font-medium hover:text-green-600 transition-colors">
                                    <i class="fas fa-download"></i>
                                </button>
                            </form>
                        </div>
                    `;
                });
                
                messageHtml += `
                        </div>
                    </div>
                `;
            }
            
            messageHtml += `
                    </div>
                </div>
            `;
            
            newMessageElement.innerHTML = messageHtml;
            messageThread.appendChild(newMessageElement);
            messageThread.scrollTop = messageThread.scrollHeight;
         }
         
         // Handle file attachment for new message
         function handleFileAttach(event) {
            const files = event.target.files;
            if (files.length > 0) {
                const attachmentContainer = document.getElementById('attachments');
                
                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    const newAttachment = document.createElement('div');
                    newAttachment.classList.add('flex', 'items-center', 'gap-2', 'p-2', 'bg-gray-50', 'rounded-md', 'border', 'border-gray-200');
                    newAttachment.innerHTML = `
                        <div class="flex-shrink-0 text-gray-400">
                            <i class="fas fa-file text-xs"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-xs font-medium truncate">${file.name}</div>
                            <div class="text-xs text-gray-500">${(file.size / 1024).toFixed(2)} KB</div>
                        </div>
                        <input type="text" name="attachment_descriptions[${i}]" placeholder="Description" class="flex-1 p-1 text-xs border rounded focus:outline-none focus:ring-1 focus:ring-green-500" />
                        <button type="button" onclick="removeAttachment(this)" class="text-gray-400 hover:text-red-500 transition-colors">
                            <i class="fas fa-trash-alt text-xs"></i>
                        </button>
                    `;
                    attachmentContainer.appendChild(newAttachment);
                }
            }
         }
         
         // Handle file attachment for reply
         function handleReplyFileAttach(event) {
            const files = event.target.files;
            if (files.length > 0) {
                const attachmentContainer = document.getElementById('replyAttachments');
                
                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    const newAttachment = document.createElement('div');
                    newAttachment.classList.add('flex', 'items-center', 'gap-2', 'p-2', 'bg-gray-50', 'rounded-md', 'border', 'border-gray-200');
                    newAttachment.innerHTML = `
                        <div class="flex-shrink-0 text-gray-400">
                            <i class="fas fa-file text-xs"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-xs font-medium truncate">${file.name}</div>
                            <div class="text-xs text-gray-500">${(file.size / 1024).toFixed(2)} KB</div>
                        </div>
                        <input type="text" name="attachment_descriptions[${i}]" placeholder="Description" class="flex-1 p-1 text-xs border rounded focus:outline-none focus:ring-1 focus:ring-green-500" />
                        <button type="button" onclick="removeAttachment(this)" class="text-gray-400 hover:text-red-500 transition-colors">
                            <i class="fas fa-trash-alt text-xs"></i>
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
         
         // Setup search functionality
         function setupSearch() {
            const searchInput = document.getElementById('search-order');
            const searchButton = document.getElementById('search-button');
            const typeFilter = document.getElementById('message-type-filter');
            
            // Function to perform AJAX search
            function performSearch() {
                const searchTerm = searchInput.value.trim();
                const messageType = typeFilter.value;
                
                // Show loading state in the message list
                const messageList = document.getElementById('message-list');
                messageList.innerHTML = `
                    <div class="py-6 text-center">
                        <div class="inline-block animate-spin rounded-full h-6 w-6 border-t-2 border-b-2 border-green-500"></div>
                        <p class="mt-2 text-gray-500 text-xs">Searching messages...</p>
                    </div>
                `;
                
                // AJAX request to search messages
                fetch(`/writer/messages/search?search=${encodeURIComponent(searchTerm)}&type=${encodeURIComponent(messageType)}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Search failed');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.html) {
                        // Replace the message list with the returned HTML
                        messageList.innerHTML = data.html;
                    } else {
                        // Show no results message
                        messageList.innerHTML = `
                            <div class="py-6 text-center">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <h3 class="text-base font-medium text-gray-600 mb-1">No messages found</h3>
                                <p class="text-xs text-gray-500">Try a different search term or filter</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Search error:', error);
                    showAlert('Search failed. Please try again.', 'error');
                    
                    // Restore original content
                    refreshMessageList();
                });
            }
            
            // Bind search events
            if (searchButton) {
                searchButton.addEventListener('click', performSearch);
            }
            
            if (typeFilter) {
                typeFilter.addEventListener('change', performSearch);
            }
            
            if (searchInput) {
                searchInput.addEventListener('keyup', function(e) {
                    if (e.key === 'Enter') {
                        performSearch();
                    }
                });
            }
         }
         </script>
         
         @endsection