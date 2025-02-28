@extends('writers.app')

@section('content')
<div class="flex h-full">
    <main class="flex-1 px-4 lg:px-8 pb-8 lg:ml-72 transition-all duration-300 pt-16">
        <!-- Order Header -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
            <div class="p-4 md:p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <div class="h-6 w-6 text-green-600">
                            <svg class="h-full w-full" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h1 class="text-lg md:text-xl font-semibold text-gray-800">Order #{{ $order->id }}</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-lg md:text-xl font-semibold text-gray-800">${{ number_format($order->price, 2) }}</span>
                        <div class="hidden md:flex items-center border-l pl-4">
                            <div class="flex flex-col items-end">
                                <span class="text-sm font-medium text-gray-600">Customer</span>
                                <span class="text-sm text-gray-500">{{ $order->created_at->format('h:i A') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages for keyword warnings -->
        @if(session('warning'))
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">{{ session('warning') }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Alert Messages Container for AJAX Messages -->
        <div id="alertMessages" class="mb-6"></div>

        @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            </div>
        </div>
        @endif

        @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Bid Section -->
        <div class="bg-green-50 border border-green-100 rounded-lg mb-6">
            <div class="p-4 md:p-6">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                    <p class="text-gray-700 mb-3 md:mb-0">Place your bid, if you are ready to execute this order for <span class="font-semibold">${{ number_format($order->price, 2) }}</span>.</p>
                    @if(!$userHasBid)
                    <button id="placeBidBtn" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors duration-200">
                        Place Bid
                    </button>
                    @else
                    <button disabled class="px-4 py-2 bg-gray-300 text-gray-600 rounded-lg cursor-not-allowed">
                        Bid Placed
                    </button>
                    @endif
                </div>
                <div class="mt-3 text-sm text-gray-500">
                    Number of bids placed for this order is {{ $bidCount }} • Take Order option is disabled by Support Team.
                </div>
            </div>
        </div>

        <!-- Tab Navigation with Slider -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
            <div class="border-b border-gray-100 relative">
                <nav class="flex" role="tablist">
                    <div id="tab-slider" class="tab-slider"></div>
                    
                    <button id="instructions-tab" 
                            class="relative px-4 py-4 md:px-6 text-gray-500 hover:text-gray-700 focus:outline-none" 
                            onclick="switchTab('instructions')" 
                            role="tab"
                            data-width="120">
                        Instructions
                    </button>
                    <button id="files-tab" 
                            class="relative px-4 py-4 md:px-6 text-gray-500 hover:text-gray-700 focus:outline-none flex items-center" 
                            onclick="switchTab('files')" 
                            role="tab"
                            data-width="100">
                        All files
                        <span class="ml-2 bg-green-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $order->files->count() }}</span>
                    </button>
                    <button id="messages-tab" 
                            class="relative px-4 py-4 md:px-6 text-gray-500 hover:text-gray-700 focus:outline-none" 
                            onclick="switchTab('messages')" 
                            role="tab"
                            data-width="110">
                        Messages
                    </button>
                </nav>
            </div>

            <!-- Content Area -->
            <div class="p-4 md:p-6">
                <!-- Instructions Panel -->
                <div id="instructions-panel" role="tabpanel">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8">
                        <!-- Left Column - Order Information -->
                        <div class="space-y-4">
                            <!-- Price Info -->
                            <div class="p-4 bg-gradient-to-r from-gray-50 to-white rounded-lg border border-gray-100">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Price</span>
                                    <span class="text-gray-800 font-medium">${{ number_format($order->price, 2) }}</span>
                                </div>
                            </div>

                            <!-- Deadline Info -->
                            <div class="p-4 bg-gradient-to-r from-gray-50 to-white rounded-lg border border-gray-100">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Deadline</span>
                                    <div>
                                        <span class="text-gray-800">{{ $deadline->format('j M, h:i A') }}</span>
                                        <span id="timeRemaining" class="text-green-500 ml-2">({{ $timeRemaining }})</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Task Size -->
                            <div class="p-4 bg-gradient-to-r from-gray-50 to-white rounded-lg border border-gray-100">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Task size</span>
                                    <div class="flex items-center">
                                        <span class="text-gray-800">{{ $order->task_size ?: 'N/A' }}</span>
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
                                    <span class="text-gray-800">{{ $order->type_of_service }}</span>
                                </div>
                            </div>

                            <!-- Discipline -->
                            <div class="p-4 bg-gradient-to-r from-gray-50 to-white rounded-lg border border-gray-100">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Discipline</span>
                                    <span class="text-gray-800">{{ $order->discipline }}</span>
                                </div>
                            </div>

                            <!-- Software -->
                            <div class="p-4 bg-gradient-to-r from-gray-50 to-white rounded-lg border border-gray-100">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Software</span>
                                    <span class="text-gray-800">{{ $order->software ?: 'N/A' }}</span>
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
                                <p>{{ $order->instructions }}</p>
                                
                                @if($order->customer_comments)
                                <div class="mt-6">
                                    <div class="bg-cyan-50 rounded-lg p-4 border border-cyan-100">
                                        <h4 class="font-medium text-gray-800 mb-2">Comments from Customer</h4>
                                        <p class="text-gray-700">{{ $order->customer_comments }}</p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Files Panel -->
                <div id="files-panel" class="hidden" role="tabpanel">
                    <div>
                        @if($order->files->count() > 0)
                        <!-- Select All Checkbox -->
                        <div class="mb-4 flex items-center">
                            <label class="inline-flex items-center">
                                <input type="checkbox" class="form-checkbox h-5 w-5 text-green-500 rounded border-gray-300" id="selectAllFiles">
                                <span class="ml-2 font-medium text-gray-700">All files</span>
                            </label>
                            <a href="#" class="text-gray-400 hover:text-gray-600 ml-4" id="downloadSelectedFiles">
                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                            </a>
                        </div>

                        <!-- Files List -->
                        <div class="space-y-4">
                            @foreach($order->files as $index => $file)
                            <div class="flex items-start sm:items-center border-b border-gray-100 pb-4 file-item" 
                                 data-file-id="{{ $file->id }}" 
                                 data-file-name="{{ $file->name }}">
                                <div class="flex-shrink-0 mt-1 sm:mt-0">
                                    <input type="checkbox" class="form-checkbox h-5 w-5 text-green-500 rounded border-gray-300 file-checkbox">
                                </div>
                                <div class="ml-4 flex-shrink-0">
                                    <svg class="w-6 h-6 {{ $file->exists ? 'text-gray-400' : 'text-red-400' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4 flex-grow">
                                    <p class="text-sm font-medium text-gray-700 truncate">{{ $index + 1 }}. {{ $file->name }}</p>
                                    <p class="text-xs text-gray-500">Instructions / Guidelines</p>
                                    @if(!$file->exists)
                                    <p class="text-xs text-red-500">File not found in storage</p>
                                    @endif
                                </div>
                                <div class="ml-4 flex-shrink-0 text-right">
                                    <p class="text-sm text-gray-600">Customer</p>
                                    <p class="text-xs text-gray-500">{{ $file->created_at->format('j M, h:i A') }} • {{ round($file->size / 1024) }} KB</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        <!-- File Conversion Notice -->
                        <div class="mt-6 text-sm text-gray-600 p-4 bg-gray-50 rounded-lg">
                            If you can't open a file (.pages, .numbers, etc.), download it anyway and use one of these links to convert it to .doc, .docx, .xlsx, etc.: 
                            <a href="https://cloudconvert.com" class="text-blue-600 hover:underline" target="_blank">cloudconvert.com</a> and 
                            <a href="https://online-convert.com" class="text-blue-600 hover:underline" target="_blank">online-convert.com</a>
                        </div>
                        @else
                        <div class="text-center py-8 text-gray-500">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-lg font-medium">No files attached to this order</p>
                            <p class="mt-2">The client has not uploaded any files for this order yet.</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Messages Panel -->
                <div id="messages-panel" class="hidden" role="tabpanel">
                    <!-- Communication Guide Alert -->
                    <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <p class="text-yellow-800">
                            Review the <a href="#" class="text-yellow-600 hover:underline font-medium">Communication Guide</a> when you work on the order.
                        </p>
                    </div>

                    <!-- Message Tabs Section -->
                    <div class="mb-6">
                        <div class="border-b border-gray-100 relative">
                            <nav class="flex" role="tablist">
                                <div id="message-tab-slider" class="message-tab-slider"></div>
                                
                                <button id="client-tab" 
                                        class="message-tab-btn relative px-4 py-3 md:px-6 md:py-4 text-green-600 font-medium hover:text-gray-700 focus:outline-none" 
                                        onclick="switchMessageTab('client')" 
                                        role="tab"
                                        data-width="80">
                                    Client
                                </button>
                                <button id="support-tab" 
                                        class="message-tab-btn relative px-4 py-3 md:px-6 md:py-4 text-gray-500 hover:text-gray-700 focus:outline-none" 
                                        onclick="switchMessageTab('support')" 
                                        role="tab"
                                        data-width="90">
                                    Support
                                </button>
                            </nav>
                        </div>
                    </div>

                    <!-- Messages Content -->
                    <div class="flex flex-col message-container bg-white rounded-lg border border-gray-100">
                        <!-- Client Messages Panel -->
                        <div id="client-messages" class="flex-1 overflow-y-auto message-list">
                            @forelse($clientMessages as $message)
                            <div class="message-item flex p-4 border-b border-gray-100 last:border-b-0" data-message-id="{{ $message->id }}">
                                <div class="{{ $message->getAvatarClasses() }}">
                                    <span class="text-gray-600">
                                        {{ $message->getSenderInitial() }}
                                    </span>
                                </div>
                                <div class="{{ $message->getMessageBubbleClasses() }}">
                                    <p class="message-text text-gray-700 mb-3">
                                        {{ $message->message }}
                                    </p>
                                    
                                    @if($message->files->count() > 0)
                                    <div class="border-t border-gray-200 pt-3 mt-3">
                                        <p class="text-xs text-gray-500 mb-2">Attachments:</p>
                                        <div class="space-y-2">
                                            @foreach($message->files as $file)
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 text-gray-400 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                                </svg>
                                                <a href="#" 
                                                class="text-xs text-blue-600 hover:underline" 
                                                onclick="downloadFile('{{ $file->id }}', '{{ $file->name }}'); return false;">
                                                    {{ $file->name }}
                                                </a>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                    
                                    <div class="mt-2 flex justify-between items-center">
                                        <span class="text-xs text-gray-500">{{ $message->created_at->format('j M, h:i A') }}</span>
                                        @if($message->read_at)
                                        <span class="text-xs text-green-500 flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Seen
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-4 text-gray-500" id="client-no-messages">
                                <p>No client messages yet. You can start the conversation.</p>
                                <p class="text-xs mt-2 text-gray-400">Messages between you and the client will appear here.</p>
                            </div>
                            @endforelse
                        </div>

                        <!-- Support Messages Panel -->
                        <div id="support-messages" class="hidden flex-1 overflow-y-auto message-list">
                            @forelse($supportMessages as $message)
                            <div class="message-item flex p-4 border-b border-gray-100 last:border-b-0" data-message-id="{{ $message->id }}">
                                <div class="{{ $message->getAvatarClasses() }}">
                                    <span class="text-gray-600">
                                        {{ $message->getSenderInitial() }}
                                    </span>
                                </div>
                                <div class="{{ $message->getMessageBubbleClasses() }}">
                                    <p class="message-text text-gray-700 mb-3">
                                        {{ $message->message }}
                                    </p>
                                    
                                    @if($message->files->count() > 0)
                                    <div class="border-t border-gray-200 pt-3 mt-3">
                                        <p class="text-xs text-gray-500 mb-2">Attachments:</p>
                                        <div class="space-y-2">
                                            @foreach($message->files as $file)
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 text-gray-400 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                                </svg>
                                                <a href="#" 
                                                class="text-xs text-blue-600 hover:underline" 
                                                onclick="downloadFile('{{ $file->id }}', '{{ $file->name }}'); return false;">
                                                    {{ $file->name }}
                                                </a>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                    
                                    <div class="mt-2 flex justify-between items-center">
                                        <span class="text-xs text-gray-500">{{ $message->created_at->format('j M, h:i A') }}</span>
                                        @if($message->read_at)
                                        <span class="text-xs text-green-500 flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Seen
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-4 text-gray-500" id="support-no-messages">
                                <p>No support messages yet. You can reach out for help.</p>
                                <p class="text-xs mt-2 text-gray-400">Messages between you and the support team will appear here.</p>
                            </div>
                            @endforelse
                        </div>

                        <!-- Message Input Section -->
                        <div class="border-t pt-4 p-4 bg-gray-50">
                            <form id="messageForm" class="flex items-center space-x-4" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="message_type" id="messageType" value="client">
                                <input type="hidden" name="receiver_id" id="receiverId" value="{{ $order->client_id ?? '' }}">
                                <input type="hidden" name="title" id="messageTitle" value="Order #{{ $order->id }} Message">
                                <div class="flex-1 relative">
                                    <input type="text" name="message" id="messageInput"
                                        class="w-full px-4 py-2 pr-10 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200"
                                        placeholder="Type your message..." required
                                        onkeyup="checkForbiddenWords(this)">
                                    <button type="button" onclick="document.getElementById('fileAttachment').click()" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors duration-200">
                                        <i class="fas fa-paperclip"></i>
                                    </button>
                                    <input type="file" id="fileAttachment" name="attachment" class="hidden" onchange="updateAttachmentLabel()">
                                    <div id="attachmentLabel" class="hidden absolute -top-8 left-0 p-1.5 bg-blue-50 text-blue-700 text-xs rounded border border-blue-100 max-w-full truncate">
                                        No file selected
                                    </div>
                                    <div id="forbiddenWordsWarning" class="hidden absolute left-0 bottom-full mb-2 p-2 bg-yellow-100 text-yellow-800 text-xs rounded-lg border border-yellow-200 w-full">
                                        Warning: Your message contains prohibited keywords. Please avoid payment-related discussions.
                                    </div>
                                </div>
                                <button type="button" id="sendMessageBtn" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors duration-200 flex items-center space-x-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                    <span class="hidden sm:inline">Send</span>
                                </button>
                            </form>
                        </div>
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
                    <p class="text-sm text-green-700 mt-1">Bid placed successfully. Check the "My Bids" section to track this order.</p>
                </div>
                <button onclick="hideBidToaster()" class="ml-auto text-green-500 hover:text-green-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </main>
</div>

<script>
// Store deadline timestamp for real-time updates
const deadlineTimestamp = {{ $deadline->timestamp * 1000 }}; // Convert to milliseconds for JS

// Main Tab Switching (Instructions, Files, Messages)
function switchTab(tabName) {
    const tabs = document.querySelectorAll('[role="tab"]');
    const panels = document.querySelectorAll('[role="tabpanel"]');
    const slider = document.getElementById('tab-slider');

    tabs.forEach(tab => {
        const isSelected = tab.id === `${tabName}-tab`;
        tab.setAttribute('aria-selected', isSelected);
        tab.classList.toggle('text-green-600', isSelected);
        tab.classList.toggle('font-medium', isSelected);
        tab.classList.toggle('text-gray-500', !isSelected);

        if (isSelected && slider) {
            const width = tab.dataset.width || tab.offsetWidth;
            slider.style.width = `${width}px`;
            slider.style.left = `${tab.offsetLeft}px`;
        }
    });

    panels.forEach(panel => {
        panel.classList.toggle('hidden', panel.id !== `${tabName}-panel`);
    });
}

// Message Tab Switching (Client/Support)
function switchMessageTab(tabName) {
    const tabs = document.querySelectorAll('.message-tab-btn');
    const messagePanels = document.querySelectorAll('.message-list');
    const slider = document.getElementById('message-tab-slider');
    
    // Update message type for form submission
    document.getElementById('messageType').value = tabName;
    
    // Update receiver_id based on message type
    if (tabName === 'client') {
        document.getElementById('receiverId').value = '{{ $order->client_id ?? '' }}';
    } else if (tabName === 'support') {
        // Set to ID of a support user - use an existing admin or support user ID
        document.getElementById('receiverId').value = '{{ App\Models\User::where("usertype", "admin")->first()->id ?? 1 }}';
    }
    
    tabs.forEach(tab => {
        if (tab.id === 'client-tab' || tab.id === 'support-tab') {
            const isSelected = tab.id === `${tabName}-tab`;
            tab.setAttribute('aria-selected', isSelected);
            tab.classList.toggle('text-green-600', isSelected);
            tab.classList.toggle('font-medium', isSelected);
            tab.classList.toggle('text-gray-500', !isSelected);
        }
    });

    const activeTab = document.getElementById(`${tabName}-tab`);
    
    if (slider && activeTab) {
        const width = activeTab.dataset.width || activeTab.offsetWidth;
        slider.style.width = `${width}px`;
        slider.style.left = `${activeTab.offsetLeft}px`;
    }

    // Toggle message panels
    document.getElementById('client-messages').classList.toggle('hidden', tabName !== 'client');
    document.getElementById('support-messages').classList.toggle('hidden', tabName !== 'support');
}

// Function to update the time remaining dynamically
function updateTimeRemaining() {
    const now = new Date().getTime();
    const timeDiff = deadlineTimestamp - now;
    
    if (timeDiff <= 0) {
        document.getElementById('timeRemaining').textContent = '(Time expired)';
        document.getElementById('timeRemaining').classList.remove('text-green-500');
        document.getElementById('timeRemaining').classList.add('text-red-500');
        return;
    }
    
    // Calculate days, hours, minutes, seconds
    const days = Math.floor(timeDiff / (1000 * 60 * 60 * 24));
    const hours = Math.floor((timeDiff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((timeDiff % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((timeDiff % (1000 * 60)) / 1000);
    
    let timeRemainingText = '(';
    if (days > 0) {
        timeRemainingText += `${days}d `;
    }
    if (hours > 0 || days > 0) {
        timeRemainingText += `${hours}h `;
    }
    timeRemainingText += `${minutes}m ${seconds}s)`;
    
    document.getElementById('timeRemaining').textContent = timeRemainingText;
}

// Check message for forbidden words
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
            sendButton.classList.remove('bg-blue-500', 'hover:bg-blue-600');
        } else {
            sendButton.classList.remove('bg-gray-400');
            sendButton.classList.add('bg-blue-500', 'hover:bg-blue-600');
        }
    }
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
    const copyButton = document.querySelector('.fa-copy')?.parentElement;
    if (!copyButton) return;
    
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
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    
    const instructions = document.querySelector('.prose p')?.textContent || '';
    
    modal.innerHTML = `
        <div class="bg-white rounded-lg max-w-4xl w-full max-h-screen overflow-auto p-6 m-4">
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Paper instructions</h3>
                <button class="text-gray-400 hover:text-gray-600" id="closeExpandedInstructions">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="prose max-w-none">
                <p>${instructions}</p>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    document.body.style.overflow = 'hidden';
    
    document.getElementById('closeExpandedInstructions').addEventListener('click', () => {
        document.body.removeChild(modal);
        document.body.style.overflow = '';
    });
}

// Store a Set of displayed message IDs to prevent duplicates
const displayedMessageIds = new Set();

// Initialize displayed message IDs from existing messages
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.message-item').forEach(message => {
        if (message.dataset.messageId) {
            displayedMessageIds.add(parseInt(message.dataset.messageId));
        }
    });
});

// Function to create the message element
function createMessageElement(message) {
    const isClient = message.message_type === 'client';
    const isSentByCurrentUser = message.user_id == {{ Auth::id() }};
    
    // Determine avatar classes and message bubble classes
    let avatarClasses = 'w-8 h-8 rounded-full flex items-center justify-center mr-4 flex-shrink-0 mt-1';
    
    if (isSentByCurrentUser) {
        avatarClasses += ' bg-blue-100';
    } else if (isClient) {
        avatarClasses += ' bg-gray-100';
    } else {
        avatarClasses += ' bg-green-100';
    }
    
    let messageBubbleClasses = 'max-w-lg rounded-lg p-4';
    
    if (isSentByCurrentUser) {
        messageBubbleClasses += ' bg-blue-50';
    } else if (isClient) {
        messageBubbleClasses += ' bg-gray-100';
    } else {
        messageBubbleClasses += ' bg-green-50';
    }
    
    // Get sender initial
    let senderInitial = 'U'; // Default
    if (isSentByCurrentUser) {
        senderInitial = 'W'; // Writer
    } else if (isClient) {
        senderInitial = 'C'; // Client
    } else {
        senderInitial = 'S'; // Support
    }
    
    // Format date for display
    const createdAt = new Date(message.created_at);
    const formattedDate = `${createdAt.getDate()} ${new Intl.DateTimeFormat('en-US', { month: 'short' }).format(createdAt)}, ${createdAt.getHours()}:${String(createdAt.getMinutes()).padStart(2, '0')} ${createdAt.getHours() >= 12 ? 'PM' : 'AM'}`;
    
    // Create a new message element
    const messageElement = document.createElement('div');
    messageElement.className = 'message-item flex p-4 border-b border-gray-100 last:border-b-0';
    messageElement.dataset.messageId = message.id;
    
    // Create the HTML structure
    messageElement.innerHTML = `
        <div class="${avatarClasses}">
            <span class="text-gray-600">
                ${senderInitial}
            </span>
        </div>
        <div class="${messageBubbleClasses}">
            <p class="message-text text-gray-700 mb-3">
                ${message.message}
            </p>
            
            <div class="mt-2 flex justify-between items-center">
                <span class="text-xs text-gray-500">${formattedDate}</span>
                ${message.read_at ? `
                <span class="text-xs text-green-500 flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Seen
                </span>
                ` : ''}
            </div>
        </div>
    `;
    
    return messageElement;
}

// Send message via AJAX
function sendMessage(event) {
    event.preventDefault();
    
    const form = document.getElementById('messageForm');
    const messageInput = document.getElementById('messageInput');
    const messageType = document.getElementById('messageType').value;
    const fileInput = document.getElementById('fileAttachment');
    
    // Check if message is empty
    if (!messageInput.value.trim()) {
        return;
    }
    
    // Check for forbidden words
    const forbiddenKeywords = ['dollar', 'money', 'pay', 'shillings', 'cash', 'price', 'payment'];
    const messageText = messageInput.value.toLowerCase();
    let containsForbiddenWord = false;
    
    forbiddenKeywords.forEach(keyword => {
        if (messageText.includes(keyword)) {
            containsForbiddenWord = true;
        }
    });
    
    if (containsForbiddenWord) {
        showAlert('Your message contains prohibited keywords. Please avoid payment-related discussions.', 'warning');
        return;
    }
    
    // Create FormData object to handle file uploads
    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('message', messageInput.value);
    formData.append('message_type', messageType);
    formData.append('receiver_id', document.getElementById('receiverId').value);
    formData.append('title', document.getElementById('messageTitle').value);
    
    if (fileInput.files.length > 0) {
        formData.append('attachment', fileInput.files[0]);
    }
    
    // Disable send button while processing
    const sendButton = document.getElementById('sendMessageBtn');
    sendButton.disabled = true;
    sendButton.innerHTML = '<svg class="animate-spin w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
    
    // Send AJAX request
    fetch('{{ route("writer.message.send", $order->id) }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Add the new message to the UI
            const messageContainer = document.getElementById(`${messageType}-messages`);
            const noMessagesElement = document.getElementById(`${messageType}-no-messages`);
            
            // Hide the "no messages" element if it exists
            if (noMessagesElement) {
                noMessagesElement.classList.add('hidden');
            }
            
            // Add the new message to the UI
            if (!displayedMessageIds.has(data.message.id)) {
                displayedMessageIds.add(data.message.id);
                const messageElement = createMessageElement(data.message);
                messageContainer.appendChild(messageElement);
            }
            
            // Scroll to the bottom of the message container
            messageContainer.scrollTop = messageContainer.scrollHeight;
            
            // Clear the input field and file attachment
            messageInput.value = '';
            fileInput.value = '';
            document.getElementById('attachmentLabel').classList.add('hidden');
            
            // Reset the send button
            sendButton.disabled = false;
            sendButton.innerHTML = `
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
                <span class="hidden sm:inline">Send</span>
            `;
        } else {
            // Show error message
            showAlert(data.message || 'Failed to send message. Please try again.', 'error');
            
            // Reset the send button
            sendButton.disabled = false;
            sendButton.innerHTML = `
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
                <span class="hidden sm:inline">Send</span>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while sending the message. Please try again.', 'error');
        
        // Reset the send button
        sendButton.disabled = false;
        sendButton.innerHTML = `
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
            </svg>
            <span class="hidden sm:inline">Send</span>
        `;
    });
}

// Show alert message
function showAlert(message, type = 'success') {
    const alertContainer = document.getElementById('alertMessages');
    
    const alertColors = {
        success: 'green',
        error: 'red',
        warning: 'yellow'
    };
    
    const color = alertColors[type] || 'green';
    
    const alertHTML = `
        <div class="bg-${color}-50 border-l-4 border-${color}-400 p-4 mb-6 alert-message">
            <div class="flex">
                <div class="flex-shrink-0">
                    ${type === 'success' ? `
                        <svg class="h-5 w-5 text-${color}-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    ` : type === 'warning' ? `
                        <svg class="h-5 w-5 text-${color}-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    ` : `
                        <svg class="h-5 w-5 text-${color}-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    `}
                </div>
                <div class="ml-3">
                    <p class="text-sm text-${color}-700">${message}</p>
                </div>
                <button class="ml-auto text-${color}-400 hover:text-${color}-600" onclick="this.parentNode.parentNode.remove()">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>
    `;
    
    alertContainer.innerHTML += alertHTML;
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert-message');
        if (alerts.length > 0) {
            alerts[0].remove();
        }
    }, 5000);
}

// Function to check for new messages
function checkForNewMessages() {
    const messageType = document.getElementById('messageType').value;
    const orderId = {{ $order->id }};
    
    // Get the highest message ID we've seen so far
    let highestMessageId = 0;
    displayedMessageIds.forEach(id => {
        highestMessageId = Math.max(highestMessageId, id);
    });
    
    fetch(`/writer/order/${orderId}/check-messages?message_type=${messageType}&last_id=${highestMessageId}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.hasNewMessages) {
            // Update the message container with new messages
            const messageContainer = document.getElementById(`${messageType}-messages`);
            const noMessagesElement = document.getElementById(`${messageType}-no-messages`);
            
            // Hide the "no messages" element if it exists
            if (noMessagesElement) {
                noMessagesElement.classList.add('hidden');
            }
            
            // Add new messages to the UI (only ones we haven't displayed yet)
            data.messages.forEach(message => {
                if (!displayedMessageIds.has(message.id)) {
                    displayedMessageIds.add(message.id);
                    const messageElement = createMessageElement(message);
                    messageContainer.appendChild(messageElement);
                }
            });
            
            // Scroll to bottom if there are new messages
            if (data.messages.length > 0) {
                messageContainer.scrollTop = messageContainer.scrollHeight;
            }
        }
    })
    .catch(error => {
        console.error('Error checking for new messages:', error);
    });
}

function updateAttachmentLabel() {
    const fileInput = document.getElementById('fileAttachment');
    const attachmentLabel = document.getElementById('attachmentLabel');
    
    if (fileInput && attachmentLabel) {
        if (fileInput.files && fileInput.files.length > 0) {
            attachmentLabel.textContent = `Selected: ${fileInput.files[0].name}`;
            attachmentLabel.classList.remove('hidden');
        } else {
            attachmentLabel.textContent = 'No file selected';
            attachmentLabel.classList.add('hidden');
        }
    }
}

function showBidToaster() {
    let toaster = document.getElementById('bidToaster');
    if (!toaster) return;

    // Show the toaster
    setTimeout(() => {
        toaster.classList.remove('translate-y-full');
    }, 100);

    // Auto hide after 5 seconds
    setTimeout(() => {
        hideBidToaster();
    }, 5000);
}

function hideBidToaster() {
    const toaster = document.getElementById('bidToaster');
    if (toaster) {
        toaster.classList.add('translate-y-full');
    }
}

// File Handling Functions
function handleFileSelection() {
    const selectAllCheckbox = document.getElementById('selectAllFiles');
    const fileCheckboxes = document.querySelectorAll('.file-checkbox');
    
    if (selectAllCheckbox && fileCheckboxes.length > 0) {
        // Handle "Select All" checkbox
        selectAllCheckbox.addEventListener('change', (e) => {
            fileCheckboxes.forEach(checkbox => {
                checkbox.checked = e.target.checked;
            });
        });

        // Update "Select All" checkbox based on individual selections
        fileCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                const allChecked = Array.from(fileCheckboxes).every(cb => cb.checked);
                const someChecked = Array.from(fileCheckboxes).some(cb => cb.checked);
                
                selectAllCheckbox.checked = allChecked;
                selectAllCheckbox.indeterminate = someChecked && !allChecked;
            });
        });
    }

    // Make file items clickable for download
    const fileItems = document.querySelectorAll('.file-item');
    fileItems.forEach(item => {
        // Exclude the checkbox from triggering the download
        const fileContent = item.querySelector('.flex-grow');
        if (fileContent) {
            fileContent.addEventListener('click', (e) => {
                const fileId = item.dataset.fileId;
                const fileName = item.dataset.fileName;
                downloadFile(fileId, fileName);
            });
        }
    });

    // Handle download button for selected files
    const downloadBtn = document.getElementById('downloadSelectedFiles');
    if (downloadBtn) {
        downloadBtn.addEventListener('click', (e) => {
            e.preventDefault();
            downloadSelectedFiles();
        });
    }
}

function downloadFile(fileId, fileName) {
    // Create a form to download the file
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("writer.file.download") }}';
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    
    const fileIdInput = document.createElement('input');
    fileIdInput.type = 'hidden';
    fileIdInput.name = 'file_id';
    fileIdInput.value = fileId;
    
    form.appendChild(csrfToken);
    form.appendChild(fileIdInput);
    document.body.appendChild(form);
    
    // Show download toast
    showDownloadToast(1, fileName);
    
    // Submit the form to download
    form.submit();
    document.body.removeChild(form);
}

function downloadSelectedFiles() {
    const selectedFiles = document.querySelectorAll('.file-checkbox:checked');
    if (selectedFiles.length === 0) {
        alert('Please select at least one file to download');
        return;
    }

    // Create form to download multiple files
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("writer.file.downloadMultiple") }}';
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);
    
    // Add all selected file IDs
    Array.from(selectedFiles).forEach(checkbox => {
        const fileItem = checkbox.closest('.file-item');
        const fileId = fileItem.dataset.fileId;
        
        const fileIdInput = document.createElement('input');
        fileIdInput.type = 'hidden';
        fileIdInput.name = 'file_ids[]';
        fileIdInput.value = fileId;
        form.appendChild(fileIdInput);
    });
    
    document.body.appendChild(form);
    
    // Show download toast
    showDownloadToast(selectedFiles.length);
    
    // Submit the form to download
    form.submit();
    document.body.removeChild(form);
}

function showDownloadToast(fileCount, singleFileName = null) {
    // Create toaster
    let toaster = document.createElement('div');
    toaster.className = 'fixed bottom-4 left-4 z-50 transform transition-transform duration-300 ease-in-out';
    
    const message = singleFileName 
        ? `Downloading: ${singleFileName}`
        : `Downloading ${fileCount} file(s)`;
    
    toaster.innerHTML = `
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded shadow-lg flex items-start max-w-sm">
            <div class="text-blue-500 mr-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
            </div>
            <div>
                <p class="font-medium text-blue-800">Download Started</p>
                <p class="text-sm text-blue-700 mt-1">${message}</p>
            </div>
            <button onclick="this.parentNode.parentNode.remove()" class="ml-auto text-blue-500 hover:text-blue-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `;
    document.body.appendChild(toaster);

    // Auto hide after 3 seconds
    setTimeout(() => {
        toaster.remove();
    }, 3000);
}

// Bid Handling
document.addEventListener('DOMContentLoaded', function() {
    // Initialize the displayed message IDs set
    document.querySelectorAll('.message-item').forEach(message => {
        if (message.dataset.messageId) {
            displayedMessageIds.add(parseInt(message.dataset.messageId));
        }
    });
    
    // Set up the real-time deadline counter
    updateTimeRemaining();
    setInterval(updateTimeRemaining, 1000);
    
    const placeBidBtn = document.getElementById('placeBidBtn');
    if (placeBidBtn) {
        placeBidBtn.addEventListener('click', function() {
            // Send AJAX request to place bid
            fetch('{{ route("writer.bid.submit", $order->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    order_id: '{{ $order->id }}',
                    amount: '{{ $order->price }}'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showBidToaster();
                    // Disable bid button
                    placeBidBtn.disabled = true;
                    placeBidBtn.classList.remove('bg-green-500', 'hover:bg-green-600');
                    placeBidBtn.classList.add('bg-gray-300', 'text-gray-600', 'cursor-not-allowed');
                    placeBidBtn.textContent = 'Bid Placed';
                    
                    // After 3 seconds, redirect to the bids page
                    setTimeout(() => {
                        window.location.href = '{{ route("home") }}';
                    }, 3000);
                } else {
                    alert(data.message || 'Failed to place bid');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error placing bid');
            });
        });
    }

    // Initialize file selection handling
    handleFileSelection();
    
    // Add event listener to the message form
    const messageForm = document.getElementById('messageForm');
    if (messageForm) {
        messageForm.addEventListener('submit', sendMessage);
        
        // Also add listener to the send button
        const sendButton = document.getElementById('sendMessageBtn');
        if (sendButton) {
            sendButton.addEventListener('click', function(e) {
                e.preventDefault();
                sendMessage(e);
            });
        }
    }
    
    // Set initial tab - always start with instructions
    switchTab('instructions');
    
    // Initialize message tabs (default to client)
    if (document.getElementById('client-tab')) {
        switchMessageTab('client');
    }
    
    // Auto-scroll messages to bottom
    const clientMessages = document.getElementById('client-messages');
    const supportMessages = document.getElementById('support-messages');
    
    if (clientMessages) {
        clientMessages.scrollTop = clientMessages.scrollHeight;
    }
    
    if (supportMessages) {
        supportMessages.scrollTop = supportMessages.scrollHeight;
    }
    
    // Start polling for new messages (every 3 seconds)
    setInterval(checkForNewMessages, 3000);
});

// Add stylesheet for responsive text and tab slider
if (!document.querySelector('style#custom-styles')) {
    const style = document.createElement('style');
    style.id = 'custom-styles';
    style.textContent = `
        /* Tab Slider Styles */
        .tab-slider {
            position: absolute;
            bottom: 0;
            height: 3px;
            background-color: #22C55E;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 3px 3px 0 0;
        }
        
        /* Message Tab Slider Styles */
        .message-tab-slider {
            position: absolute;
            bottom: 0;
            height: 3px;
            background: linear-gradient(90deg, #22C55E, #10B981);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 3px 3px 0 0;
            box-shadow: 0 1px 2px rgba(34, 197, 94, 0.3);
        }
        
        .message-tab-btn {
            position: relative;
            overflow: hidden;
        }
        
        .message-tab-btn::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 3px;
            background-color: transparent;
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        
        .message-tab-btn[aria-selected="true"]::after {
            width: 100%;
        }
        
        /* Message Container Styles */
        .message-container {
            height: 400px;
            max-height: 60vh;
        }
        
        .message-list {
            height: calc(100% - 60px);
            overflow-y: auto;
            scroll-behavior: smooth;
            padding: 0;
            margin: 0;
        }
        
        /* Message Text Responsive Styles */
        .message-text {
            font-size: 0.875rem;
            line-height: 1.5;
        }
        
        @media (max-width: 640px) {
            .message-text {
                font-size: 0.813rem;
                line-height: 1.4;
            }
        }
        
        @media (min-width: 1024px) {
            .message-text {
                font-size: 0.938rem;
                line-height: 1.6;
            }
        }
        
        /* Tooltip Styles */
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
</script>
@endsection