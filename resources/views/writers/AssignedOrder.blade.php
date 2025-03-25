@extends('writers.app')
@section('content')

<style>
    /* Custom styles for assigned order page */
    .countdown {
        font-variant-numeric: tabular-nums;
    }
    
    .countdown-warning {
        color: #EF4444;
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
    
    .tab-slider {
        position: absolute;
        bottom: -1px;
        height: 2px;
        background-color: #22C55E;
        transition: all 0.3s ease;
    }
    
    .file-upload-zone {
        border: 2px dashed #e5e7eb;
        transition: all 0.3s ease;
    }
    
    .file-upload-zone.drag-over {
        border-color: #22C55E;
        background-color: rgba(34, 197, 94, 0.05);
    }
    
    /* Messages container styles - UPDATED for scrollable container */
    .messages-container {
        height: 420px;
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: #e2e8f0 #f8fafc;
        padding-right: 4px;
    }
    
    .messages-container::-webkit-scrollbar {
        width: 5px;
    }
    
    .messages-container::-webkit-scrollbar-track {
        background: #f8fafc;
    }
    
    .messages-container::-webkit-scrollbar-thumb {
        background-color: #e2e8f0;
        border-radius: 3px;
    }
    
    /* Smaller font for messages */
    .messages-container p {
        font-size: 0.875rem;
        line-height: 1.25rem;
    }
    
    .messages-container .text-xs {
        font-size: 0.75rem;
    }
    
    /* Message date separator */
    .message-date-separator {
        display: flex;
        align-items: center;
        margin: 10px 0;
    }
    
    .message-date-separator .line {
        flex-grow: 1;
        height: 1px;
        background-color: #e2e8f0;
    }
    
    .message-date-separator .date {
        padding: 0 10px;
        font-size: 0.75rem;
        color: #94a3b8;
        background-color: #f8fafc;
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
                            <h1 class="text-xl font-semibold text-gray-800">Order #{{ $order->id }}</h1>
                            <div class="flex items-center text-sm text-gray-500">
                                <div class="w-2 h-2 rounded-full 
                                    @if($order->status == 'DONE') bg-green-500
                                    @elseif($order->status == 'ON_REVISION') bg-yellow-500
                                    @else bg-blue-500 @endif
                                    mr-2"></div>
                                {{ ucfirst(strtolower(str_replace('_', ' ', $order->status))) }}
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-6">
                        <span class="text-2xl font-semibold text-gray-800">${{ number_format($order->price, 2) }}</span>
                        <div class="flex items-center border-l pl-6">
                            <div class="flex flex-col items-end">
                                <span class="text-sm font-medium text-gray-600">Customer</span>
                                <span class="text-sm text-gray-500">N/A</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                @if($order->status != 'DONE')
                <div class="mt-4">
                    <button class="inline-flex items-center text-blue-600 hover:text-blue-700 text-sm font-medium group" 
                            onclick="reassignOrder('{{ $order->id }}')">
                        <i class="fas fa-sync-alt mr-2 transform group-hover:rotate-180 transition-transform duration-300"></i>
                        Reassign this order
                    </button>
                </div>
                @endif
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
                        <span class="ml-2 bg-green-500 text-white text-xs px-2 py-0.5 rounded-full">{{ count($order->files) }}</span>
                    </button>
                    <button id="messages-tab" 
                            class="relative px-6 py-4 text-gray-500 hover:text-gray-700 focus:outline-none flex items-center" 
                            onclick="switchTab('messages')" 
                            role="tab"
                            data-width="110">
                        Messages
                        @if($unreadMessages > 0)
                        <span class="ml-2 bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $unreadMessages }}</span>
                        @endif
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
                                        <span class="text-gray-800 timeRemaining">{{ \Carbon\Carbon::parse($order->deadline)->format('d M, h:i A') }}</span>
                                        <span id="deadline-countdown" class="countdown" data-deadline="{{ $order->deadline }}"></span>
                                        @if($order->status != 'DONE')
                                        <button 
                                            class="px-3 py-1 bg-green-500 text-white text-sm rounded hover:bg-green-600 transition-colors duration-200"
                                            onclick="showExtensionDialog()">
                                            Extend
                                        </button>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Screening Deadline -->
                            <div class="p-4 bg-gradient-to-r from-gray-50 to-white rounded-lg border border-gray-100">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Screening deadline</span>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-gray-800">{{ \Carbon\Carbon::parse($order->screening_deadline)->format('d M, h:i A') }}</span>
                                        <span id="screening-countdown" class="countdown" data-deadline="{{ $order->screening_deadline }}"></span>
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
                                        <span class="text-gray-800">{{ ucfirst(strtolower($order->task_size)) }}</span>
                                        <div class="group relative">
                                            <i class="fas fa-info-circle text-gray-400 cursor-help"></i>
                                            <div class="absolute bottom-full right-0 mb-2 w-48 p-2 bg-gray-800 text-white text-sm rounded-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                                                @if($order->size === 'LARGE')
                                                    Large tasks require more time
                                                @elseif($order->size === 'MEDIUM')
                                                    Medium tasks require moderate time
                                                @else
                                                    Small tasks require less time
                                                @endif
                                            </div>
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

                            @if($order->programming_language)
                            <!-- Programming Language -->
                            <div class="p-4 bg-gradient-to-r from-gray-50 to-white rounded-lg border border-gray-100">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Programming language</span>
                                    <span class="text-gray-800">{{ $order->programming_language }}</span>
                                </div>
                            </div>
                            @endif
                            
                            @if($order->number_of_pages)
                            <!-- Number of pages -->
                            <div class="p-4 bg-gradient-to-r from-gray-50 to-white rounded-lg border border-gray-100">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Number of pages</span>
                                    <span class="text-gray-800">{{ $order->number_of_pages }}</span>
                                </div>
                            </div>
                            @endif
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
                                    {{ $order->instructions }}
                                </p>
                            </div>

                            @if($order->customer_comments)
                            <!-- Customer Comments -->
                            <div class="mt-6">
                                <div class="bg-cyan-50 rounded-lg p-4 border border-cyan-100">
                                    <h4 class="font-medium text-gray-800 mb-2">Comments from Customer</h4>
                                    <p class="text-gray-700">
                                        {{ $order->customer_comments }}
                                    </p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

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
                                    class="ml-4 text-gray-400 hover:text-gray-600 transition-colors bulk-download-btn hidden"
                                    id="bulk-download-btn"
                                    onclick="downloadSelectedFiles()">
                                    <i class="fas fa-download"></i>
                                </button>
                            </div>
                            
                            @if($order->status != 'DONE')
                            <div>
                                <button onclick="showUploadModal()" class="bg-green-500 text-white rounded-lg px-4 py-2 hover:bg-green-600 transition duration-200 flex items-center space-x-2">
                                    <i class="fas fa-upload"></i>
                                    <span>Upload files</span>
                                </button>
                            </div>
                            @endif
                        </div>

                        <!-- File Items -->
                        <div id="files-container" class="space-y-3">
                            @foreach($order->files as $file)
                            <div class="flex items-center space-x-4 border border-gray-100 p-4 rounded-lg hover:shadow-md transition duration-200 file-item" data-file-id="{{ $file->id }}" data-file-name="{{ $file->original_name }}">
                                <input type="checkbox" class="form-checkbox h-4 w-4 text-green-500 rounded border-gray-300 file-checkbox">
                                @if(strpos($file->mime_type, 'pdf') !== false)
                                    <i class="fas fa-file-pdf text-red-400"></i>
                                @elseif(strpos($file->mime_type, 'word') !== false || strpos($file->mime_type, 'document') !== false)
                                    <i class="fas fa-file-word text-blue-400"></i>
                                @elseif(strpos($file->mime_type, 'excel') !== false || strpos($file->mime_type, 'spreadsheet') !== false)
                                    <i class="fas fa-file-excel text-green-400"></i>
                                @elseif(strpos($file->mime_type, 'image') !== false)
                                    <i class="fas fa-file-image text-purple-400"></i>
                                @elseif(strpos($file->mime_type, 'zip') !== false || strpos($file->mime_type, 'archive') !== false)
                                    <i class="fas fa-file-archive text-yellow-400"></i>
                                @else
                                    <i class="fas fa-file text-gray-400"></i>
                                @endif
                                <div class="flex-grow">
                                    <p class="text-sm font-medium text-gray-700">{{ $file->original_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $file->description ? $file->description : 'No description' }}</p>
                                    <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($file->created_at)->format('d M, h:i A') }} • {{ formatFileSize($file->size) }}</p>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <span class="text-sm text-gray-600">{{ $file->uploader_type === 'CUSTOMER' ? 'Customer' : 'Writer' }}</span>
                                    <button class="text-gray-400 hover:text-gray-600 transition-colors" onclick="downloadFile('{{ $file->id }}', '{{ $file->original_name }}')">
                                        <i class="fas fa-download"></i>
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        @if(count($order->files) === 0)
                        <div id="no-files-message" class="text-center py-8 text-gray-500">
                            <i class="fas fa-file-alt text-gray-300 text-4xl mb-3"></i>
                            <p>No files have been uploaded yet</p>
                        </div>
                        @endif

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

                <!-- Message Tabs Content -->
                <div id="messages-panel" class="hidden" role="tabpanel">
                    <div class="flex space-x-4 mb-4 border-b">
                        <button id="client-messages-tab" class="px-4 py-2 text-sm font-medium text-gray-600 border-b-2 border-blue-500 focus:outline-none" onclick="switchMessageTab('client')">
                            Client Messages
                            @if(isset($clientMessages) && $clientUnreadCount > 0)
                                <span class="ml-2 bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $clientUnreadCount }}</span>
                            @endif
                        </button>
                        <button id="support-messages-tab" class="px-4 py-2 text-sm font-medium text-gray-600 focus:outline-none" onclick="switchMessageTab('support')">
                            Support Messages
                            @if(isset($supportMessages) && $supportUnreadCount > 0)
                                <span class="ml-2 bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $supportUnreadCount }}</span>
                            @endif
                        </button>
                    </div>

                    <!-- Client Messages Container -->
                    <div id="client-messages-container" class="flex flex-col h-[500px]">
                        <div class="messages-container flex-1 overflow-y-auto mb-4 space-y-3 px-2">
                            @if(isset($clientMessages) && count($clientMessages) > 0)
                                @foreach($clientMessages as $messageDate => $dailyMessages)
                                    <!-- Date Separator -->
                                    <div class="relative flex items-center py-2">
                                        <div class="flex-grow border-t border-gray-200"></div>
                                        <span class="flex-shrink-0 mx-4 text-xs text-gray-500 bg-gray-50 px-2 py-0.5 rounded-full">
                                            {{ $messageDate }}
                                        </span>
                                        <div class="flex-grow border-t border-gray-200"></div>
                                    </div>
                                    
                                    @foreach($dailyMessages as $message)
                                        @if($message->user_id == Auth::id())
                                            <!-- Writer Message (more compact) -->
                                            <div class="flex justify-end items-start space-x-2" id="message-{{ $message->id }}">
                                                <div class="max-w-xs sm:max-w-md rounded-lg p-2 bg-blue-50 shadow-sm">
                                                    <p class="text-sm text-gray-700">{{ $message->message }}</p>
                                                    @if($message->files->count() > 0)
                                                        <div class="mt-1 pt-1 border-t border-gray-200">
                                                            @foreach($message->files as $file)
                                                                <div class="flex items-center text-xs">
                                                                    <svg class="h-3 w-3 text-gray-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                                                    </svg>
                                                                    <a href="javascript:void(0)" onclick="downloadFile('{{ $file->id }}', '{{ $file->name }}')" class="text-blue-600 hover:underline">{{ $file->name }}</a>
                                                                    <span class="ml-1 text-gray-500">({{ formatFileSize($file->size) }})</span>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                    <div class="mt-1 flex justify-between items-center">
                                                        <span class="text-xs text-gray-400">{{ Carbon\Carbon::parse($message->created_at)->format('h:i A') }}</span>
                                                        <span class="text-xs {{ $message->read_at ? 'text-green-500' : 'text-gray-400' }}">
                                                            {{ $message->read_at ? 'Seen' : 'Sent' }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0 text-xs">
                                                    <span class="text-blue-600 font-medium">W</span>
                                                </div>
                                            </div>
                                        @else
                                            <!-- Client Message (more compact) -->
                                            <div class="flex justify-start items-start space-x-2" id="message-{{ $message->id }}">
                                                <div class="w-6 h-6 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0 text-xs">
                                                    <span class="text-gray-600 font-medium">C</span>
                                                </div>
                                                <div class="max-w-xs sm:max-w-md rounded-lg p-2 bg-white shadow-sm">
                                                    <p class="text-sm text-gray-700">{{ $message->message }}</p>
                                                    @if($message->files->count() > 0)
                                                        <div class="mt-1 pt-1 border-t border-gray-200">
                                                            @foreach($message->files as $file)
                                                                <div class="flex items-center text-xs">
                                                                    <svg class="h-3 w-3 text-gray-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                                                    </svg>
                                                                    <a href="javascript:void(0)" onclick="downloadFile('{{ $file->id }}', '{{ $file->name }}')" class="text-blue-600 hover:underline">{{ $file->name }}</a>
                                                                    <span class="ml-1 text-gray-500">({{ formatFileSize($file->size) }})</span>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                    <div class="mt-1">
                                                        <span class="text-xs text-gray-400">{{ Carbon\Carbon::parse($message->created_at)->format('h:i A') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @endforeach
                            @else
                                <div class="text-center py-8 text-gray-500">
                                    <i class="fas fa-comment-dots text-gray-300 text-4xl mb-3"></i>
                                    <p>No client messages yet</p>
                                </div>
                            @endif
                        </div>

                        <!-- Message Input for Client -->
                        <div class="border-t pt-3 bg-white">
                            <form id="clientMessageForm" class="flex items-center space-x-2">
                                @csrf
                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                                <input type="hidden" name="message_type" value="client">
                                <div class="flex-1 relative">
                                    <textarea id="clientMessageContent" name="message" 
                                        class="w-full px-3 py-2 pr-8 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 resize-none"
                                        placeholder="Type your message to client..."
                                        rows="1"
                                        onkeydown="if(event.keyCode == 13 && !event.shiftKey) { event.preventDefault(); sendClientMessage(); }"></textarea>
                                    <button type="button" 
                                            class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors duration-200"
                                            onclick="document.getElementById('clientMessageAttachment').click()">
                                        <i class="fas fa-paperclip text-sm"></i>
                                    </button>
                                    <input type="file" id="clientMessageAttachment" name="attachment" class="hidden">
                                </div>
                                <button type="button" 
                                        onclick="sendClientMessage()" 
                                        class="px-3 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors duration-200 flex items-center">
                                    <i class="fas fa-paper-plane text-sm"></i>
                                </button>
                            </form>
                            <div id="clientAttachmentPreview" class="hidden mt-2 p-2 bg-gray-50 rounded-lg flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="fas fa-file mr-2 text-gray-500 text-sm"></i>
                                    <span id="clientAttachmentName" class="text-sm text-gray-700 truncate"></span>
                                </div>
                                <button onclick="removeClientAttachment()" class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-times text-sm"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Support Messages Container -->
                    <div id="support-messages-container" class="flex flex-col h-[500px] hidden">
                        <div class="messages-container flex-1 overflow-y-auto mb-4 space-y-3 px-2">
                            @if(isset($supportMessages) && count($supportMessages) > 0)
                                @foreach($supportMessages as $messageDate => $dailyMessages)
                                    <!-- Date Separator -->
                                    <div class="relative flex items-center py-2">
                                        <div class="flex-grow border-t border-gray-200"></div>
                                        <span class="flex-shrink-0 mx-4 text-xs text-gray-500 bg-gray-50 px-2 py-0.5 rounded-full">
                                            {{ $messageDate }}
                                        </span>
                                        <div class="flex-grow border-t border-gray-200"></div>
                                    </div>
                                    
                                    @foreach($dailyMessages as $message)
                                        @if($message->user_id == Auth::id())
                                            <!-- Writer Message -->
                                            <div class="flex justify-end items-start space-x-2" id="message-{{ $message->id }}">
                                                <div class="max-w-xs sm:max-w-md rounded-lg p-2 bg-blue-50 shadow-sm">
                                                    <p class="text-sm text-gray-700">{{ $message->message }}</p>
                                                    @if($message->files->count() > 0)
                                                        <div class="mt-1 pt-1 border-t border-gray-200">
                                                            @foreach($message->files as $file)
                                                                <div class="flex items-center text-xs">
                                                                    <svg class="h-3 w-3 text-gray-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                                                    </svg>
                                                                    <a href="javascript:void(0)" onclick="downloadFile('{{ $file->id }}', '{{ $file->name }}')" class="text-blue-600 hover:underline">{{ $file->name }}</a>
                                                                    <span class="ml-1 text-gray-500">({{ formatFileSize($file->size) }})</span>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                    <div class="mt-1 flex justify-between items-center">
                                                        <span class="text-xs text-gray-400">{{ Carbon\Carbon::parse($message->created_at)->format('h:i A') }}</span>
                                                        <span class="text-xs {{ $message->read_at ? 'text-green-500' : 'text-gray-400' }}">
                                                            {{ $message->read_at ? 'Seen' : 'Sent' }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0 text-xs">
                                                    <span class="text-blue-600 font-medium">W</span>
                                                </div>
                                            </div>
                                        @elseif($message->message_type == 'system')
                                            <!-- System Message -->
                                            <div class="flex justify-start items-start space-x-2" id="message-{{ $message->id }}">
                                                <div class="w-6 h-6 rounded-full bg-yellow-100 flex items-center justify-center flex-shrink-0 text-xs">
                                                    <span class="text-yellow-600 font-medium">S</span>
                                                </div>
                                                <div class="max-w-xs sm:max-w-md rounded-lg p-2 bg-yellow-50 shadow-sm">
                                                    <p class="text-sm text-gray-700">{{ $message->message }}</p>
                                                    <div class="mt-1">
                                                        <span class="text-xs text-gray-400">{{ Carbon\Carbon::parse($message->created_at)->format('h:i A') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <!-- Support Message -->
                                            <div class="flex justify-start items-start space-x-2" id="message-{{ $message->id }}">
                                                <div class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0 text-xs">
                                                    <span class="text-green-600 font-medium">S</span>
                                                </div>
                                                <div class="max-w-xs sm:max-w-md rounded-lg p-2 bg-green-50 shadow-sm">
                                                    <p class="text-sm text-gray-700">{{ $message->message }}</p>
                                                    @if($message->files->count() > 0)
                                                        <div class="mt-1 pt-1 border-t border-gray-200">
                                                            @foreach($message->files as $file)
                                                                <div class="flex items-center text-xs">
                                                                    <svg class="h-3 w-3 text-gray-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                                                    </svg>
                                                                    <a href="javascript:void(0)" onclick="downloadFile('{{ $file->id }}', '{{ $file->name }}')" class="text-blue-600 hover:underline">{{ $file->name }}</a>
                                                                    <span class="ml-1 text-gray-500">({{ formatFileSize($file->size) }})</span>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                    <div class="mt-1">
                                                        <span class="text-xs text-gray-400">{{ Carbon\Carbon::parse($message->created_at)->format('h:i A') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @endforeach
                            @else
                                <div class="text-center py-8 text-gray-500">
                                    <i class="fas fa-comment-dots text-gray-300 text-4xl mb-3"></i>
                                    <p>No support messages yet</p>
                                </div>
                            @endif
                        </div>

                        <!-- Message Input for Support -->
                        <div class="border-t pt-3 bg-white">
                            <form id="supportMessageForm" class="flex items-center space-x-2">
                                @csrf
                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                                <input type="hidden" name="message_type" value="support">
                                <div class="flex-1 relative">
                                    <textarea id="supportMessageContent" name="message" 
                                        class="w-full px-3 py-2 pr-8 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 resize-none"
                                        placeholder="Type your message to support..."
                                        rows="1"
                                        onkeydown="if(event.keyCode == 13 && !event.shiftKey) { event.preventDefault(); sendSupportMessage(); }"></textarea>
                                    <button type="button" 
                                            class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors duration-200"
                                            onclick="document.getElementById('supportMessageAttachment').click()">
                                        <i class="fas fa-paperclip text-sm"></i>
                                    </button>
                                    <input type="file" id="supportMessageAttachment" name="attachment" class="hidden">
                                </div>
                                <button type="button" 
                                        onclick="sendSupportMessage()" 
                                        class="px-3 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors duration-200 flex items-center">
                                    <i class="fas fa-paper-plane text-sm"></i>
                                </button>
                            </form>
                            <div id="supportAttachmentPreview" class="hidden mt-2 p-2 bg-gray-50 rounded-lg flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="fas fa-file mr-2 text-gray-500 text-sm"></i>
                                    <span id="supportAttachmentName" class="text-sm text-gray-700 truncate"></span>
                                </div>
                                <button onclick="removeSupportAttachment()" class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-times text-sm"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
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
        <p class="text-gray-500 mt-2">Your files have been uploaded successfully</p>
        <div class="mt-6">
            <button onclick="closeSuccessModal()" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors duration-200">
                Close
            </button>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div id="errorModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">
    <div class="bg-white rounded-lg p-8 max-w-md w-full text-center">
        <div class="w-16 h-16 mx-auto mb-4 bg-red-50 rounded-full flex items-center justify-center">
            <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </div>
        <h3 class="text-xl font-medium text-gray-700">Error</h3>
        <p id="error-message" class="text-gray-500 mt-2">Something went wrong with the upload.</p>
        <div class="mt-6">
            <button onclick="closeErrorModal()" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors duration-200">
                Close
            </button>
        </div>
    </div>
</div>

<!-- Toaster Notification -->
<div id="toaster" class="fixed bottom-4 left-4 z-50 transform translate-y-full transition-transform duration-300 ease-in-out">
    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded shadow-lg flex items-start max-w-sm">
        <div class="text-green-500 mr-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <div>
            <p class="font-medium text-green-800" id="toaster-title">Success!</p>
            <p class="text-sm text-green-700 mt-1" id="toaster-message">Your files have been uploaded successfully.</p>
        </div>
        <button onclick="hideToaster()" class="ml-auto text-green-500 hover:text-green-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
</div>

@php
function formatFileSize($bytes)
{
    if ($bytes === 0) return '0 Bytes';
    
    $sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    $i = floor(log($bytes, 1024));
    
    return round($bytes / pow(1024, $i), 2) . ' ' . $sizes[$i];
}
@endphp

<script>
// Global variables
let uploadedFiles = new Map();
let allCheckboxesChecked = false;
let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

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
    
    // Mark messages as read if messages tab is opened
    if (tabName === 'messages') {
        markMessagesAsRead();
        scrollToBottom();
    }
}

// Deadline Countdown
function updateCountdowns() {
    const now = new Date();
    
    // Update main deadline
    const deadlineElement = document.getElementById('deadline-countdown');
    if (deadlineElement) {
        const deadline = new Date(deadlineElement.dataset.deadline);
        updateCountdownDisplay(deadlineElement, deadline, now);
    }
    
    // Update extension dialog deadline
    const extensionElement = document.getElementById('extension-countdown');
    if (extensionElement) {
        const deadline = new Date(extensionElement.dataset.deadline);
        updateCountdownDisplay(extensionElement, deadline, now);
    }
    
    // Update screening deadline
    const screeningElement = document.getElementById('screening-countdown');
    if (screeningElement) {
        const deadline = new Date(screeningElement.dataset.deadline);
        updateCountdownDisplay(screeningElement, deadline, now);
    }
}

function formatTimeRemaining(diff) {
    if (diff <= 0) {
        // Past deadline - show as negative time
        diff = Math.abs(diff);
        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));

        if (days > 0) {
            return `(-${days}d ${hours}h)`;
        } else if (hours > 0) {
            return `(-${hours}h ${minutes}m)`;
        } else {
            return `(-${minutes}m)`;
        }
    } else {
        // Before deadline - show as positive time
        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));

        if (days > 0) {
            return `(${days}d ${hours}h)`;
        } else if (hours > 0) {
            return `(${hours}h ${minutes}m)`;
        } else {
            return `(${minutes}m)`;
        }
    }
}

function updateCountdownDisplay(element, deadline, now) {
    const diff = deadline - now;
    
    element.textContent = formatTimeRemaining(diff);
    
    if (diff <= 0) {
        element.classList.add('countdown-warning');
    } else {
        element.classList.remove('countdown-warning');
    }
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

function submitExtension(orderId) {
    const time = document.getElementById('extensionTime').value;
    const reason = document.getElementById('extensionReason').value;

    if (!time || !reason.trim()) {
        showToaster('Error', 'Please fill in all fields', 'error');
        return;
    }

    // Send extension request to server
    fetch('/api/orders/' + orderId + '/extend', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            extension_hours: time,
            reason: reason
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            hideExtensionDialog();
            
            // Update deadline display
            const deadlineElement = document.getElementById('deadline-countdown');
            if (deadlineElement) {
                deadlineElement.dataset.deadline = data.new_deadline;
            }
            const extensionElement = document.getElementById('extension-countdown');
            if (extensionElement) {
                extensionElement.dataset.deadline = data.new_deadline;
            }
            
            showToaster('Success', 'Deadline extended successfully', 'success');
        } else {
            showToaster('Error', data.message || 'Failed to extend deadline', 'error');
        }
    })
    .catch(error => {
        console.error('Extension request failed:', error);
        showToaster('Error', 'Something went wrong', 'error');
    });
}

// Copy Instructions Function
async function copyInstructions() {
    const instructions = document.querySelector('.prose p')?.textContent;
    const customerComments = document.querySelector('.bg-cyan-50 p')?.textContent;
    
    if (!instructions) return;

    const textToCopy = `Instructions:\n${instructions.trim()}\n\n${customerComments ? 'Customer Comments:\n' + customerComments.trim() : ''}`;

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

// Expand Instructions Function
function expandInstructions() {
    // Implementation for expanding instructions in a full-screen modal
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    modal.style.backdropFilter = 'blur(4px)';
    
    const content = document.createElement('div');
    content.className = 'bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-y-auto p-6';
    
    const header = document.createElement('div');
    header.className = 'flex justify-between items-center mb-4';
    header.innerHTML = `
        <h3 class="text-xl font-semibold text-gray-800">Paper Instructions</h3>
        <button class="text-gray-400 hover:text-gray-600">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    const body = document.createElement('div');
    body.className = 'prose max-w-none';
    body.innerHTML = document.querySelector('.prose').innerHTML;
    
    // Add customer comments if they exist
    const customerComments = document.querySelector('.bg-cyan-50');
    if (customerComments) {
        const commentsDiv = document.createElement('div');
        commentsDiv.className = 'mt-6';
        commentsDiv.innerHTML = customerComments.outerHTML;
        body.appendChild(commentsDiv);
    }
    
    content.appendChild(header);
    content.appendChild(body);
    modal.appendChild(content);
    
    document.body.appendChild(modal);
    document.body.style.overflow = 'hidden';
    
    // Close modal when clicking backdrop or close button
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeExpandedInstructions(modal);
        }
    });
    
    header.querySelector('button').addEventListener('click', () => {
        closeExpandedInstructions(modal);
    });
    
    // Close on escape key
    document.addEventListener('keydown', function escapeClose(e) {
        if (e.key === 'Escape') {
            closeExpandedInstructions(modal);
            document.removeEventListener('keydown', escapeClose);
        }
    });
}

function closeExpandedInstructions(modal) {
    modal.remove();
    document.body.style.overflow = '';
}

// Reassign Order Function
function reassignOrder(orderId) {
    if (!confirm('Are you sure you want to reassign this order?')) {
        return;
    }
    
    fetch('/api/orders/' + orderId + '/reassign', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToaster('Success', 'Order has been reassigned successfully', 'success');
            // Redirect to orders list after short delay
            setTimeout(() => {
                window.location.href = '/writer/orders';
            }, 2000);
        } else {
            showToaster('Error', data.message || 'Failed to reassign order', 'error');
        }
    })
    .catch(error => {
        console.error('Reassign request failed:', error);
        showToaster('Error', 'Something went wrong', 'error');
    });
}

// File Upload Functions
function showUploadModal() {
    document.getElementById('uploadModal').classList.remove('hidden');
    document.getElementById('uploadStep1').classList.remove('hidden');
    document.getElementById('uploadStep2').classList.add('hidden');
    document.body.style.overflow = 'hidden';
    resetUpload();
}

function closeUploadModal() {
    document.getElementById('uploadModal').classList.add('hidden');
    document.getElementById('processingModal').classList.add('hidden');
    document.getElementById('successModal').classList.add('hidden');
    document.getElementById('errorModal').classList.add('hidden');
    document.body.style.overflow = '';
    resetUpload();
}

function resetUpload() {
    uploadedFiles.clear();
    document.getElementById('uploadedFiles').innerHTML = '';
    if (document.getElementById('fileInput')) {
        document.getElementById('fileInput').value = '';
    }
    
    // Reset verification checkboxes
    const checkboxes = document.querySelectorAll('.verification-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    
    allCheckboxesChecked = false;
}

function handleFileSelect(event) {
    const files = event.target.files;
    addFiles(files);
}

function handleFileDrop(event) {
    event.preventDefault();
    const dropZone = document.getElementById('dropZone');
    dropZone.classList.remove('drag-over');
    dropZone.classList.remove('border-green-500');
    const files = event.dataTransfer.files;
    addFiles(files);
}

function handleDragOver(event) {
    event.preventDefault();
    const dropZone = document.getElementById('dropZone');
    dropZone.classList.add('drag-over');
    dropZone.classList.add('border-green-500');
}

function handleDragLeave(event) {
    event.preventDefault();
    const dropZone = document.getElementById('dropZone');
    dropZone.classList.remove('drag-over');
    dropZone.classList.remove('border-green-500');
}

function addFiles(files) {
    const uploadedFilesContainer = document.getElementById('uploadedFiles');
    
    if (files.length === 0) return;
    
    // Check file size limit (99 MB = 99 * 1024 * 1024 bytes)
    const maxSize = 99 * 1024 * 1024;
    for (let i = 0; i < files.length; i++) {
        if (files[i].size > maxSize) {
            showToaster('Error', `File ${files[i].name} exceeds the maximum size limit of 99 MB`, 'error');
            return;
        }
    }
    
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
                   readonly
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
        const isInputClick = e.target.dataset && e.target.dataset.fileId === fileId;
        
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

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Enhanced Upload Workflow Functions
function gotoVerificationStep() {
    if (uploadedFiles.size === 0) {
        showToaster('Error', 'Please select at least one file to upload', 'error');
        return;
    }
    
    // Check if at least one file has the 'completed' description
    let hasCompletedFile = false;
    uploadedFiles.forEach(file => {
        if (file.description === 'completed') {
            hasCompletedFile = true;
        }
    });
    
    if (!hasCompletedFile) {
        showToaster('Warning', 'Please mark at least one file as "completed"', 'warning');
    }
    
    document.getElementById('uploadStep1').classList.add('hidden');
    document.getElementById('uploadStep2').classList.remove('hidden');
}

function backToFileSelection() {
    document.getElementById('uploadStep2').classList.add('hidden');
    document.getElementById('uploadStep1').classList.remove('hidden');
}

function checkVerificationStatus() {
    const checkboxes = document.querySelectorAll('.verification-checkbox');
    allCheckboxesChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);
    
    const submitBtn = document.getElementById('submitUploadBtn');
    
    if (allCheckboxesChecked) {
        submitBtn.classList.remove('bg-gray-400');
        submitBtn.classList.add('bg-green-500', 'hover:bg-green-600');
    } else {
        submitBtn.classList.remove('bg-green-500', 'hover:bg-green-600');
        submitBtn.classList.add('bg-gray-400');
    }
}

function startUpload() {
    // Verify all checkboxes are checked
    const checkboxes = document.querySelectorAll('.verification-checkbox');
    const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);
    
    if (!allChecked) {
        showToaster('Warning', 'Please check all verification items', 'warning');
        return;
    }
    
    document.getElementById('uploadModal').classList.add('hidden');
    document.getElementById('processingModal').classList.remove('hidden');
    
    // Reset progress bar to 0%
    const progressBar = document.getElementById('uploadProgressBar');
    progressBar.style.width = '0%';
    
    // Create form data
    const formData = new FormData();
    formData.append('order_id', '{{ $order->id }}');
    formData.append('_token', csrfToken);
    
    // Add files with descriptions
    let fileIndex = 0;
    uploadedFiles.forEach((fileData, fileId) => {
        formData.append(`files[${fileIndex}]`, fileData.file);
        formData.append(`descriptions[${fileIndex}]`, fileData.description);
        fileIndex++;
    });
    
    // Upload with progress tracking
    const xhr = new XMLHttpRequest();
    
    xhr.open('POST', '/api/writer/order/upload-files', true);
    
    xhr.upload.onprogress = function(e) {
        if (e.lengthComputable) {
            const percentComplete = (e.loaded / e.total) * 100;
            progressBar.style.width = percentComplete + '%';
        }
    };
    
    xhr.onload = function() {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.success) {
                setTimeout(() => {
                    document.getElementById('processingModal').classList.add('hidden');
                    document.getElementById('successModal').classList.remove('hidden');
                    
                    // Update files list in background
                    refreshFilesList(response.files);
                    
                    // Set order status as DONE if completed file was uploaded
                    if (response.status_changed) {
                        updateOrderStatus('DONE');
                    }
                }, 500);
            } else {
                showUploadError(response.message || 'Failed to upload files');
            }
        } else {
            showUploadError('Upload failed with status ' + xhr.status);
        }
    };
    
    xhr.onerror = function() {
        showUploadError('Network error occurred during upload');
    };
    
    xhr.send(formData);
}

function showUploadError(message) {
    document.getElementById('processingModal').classList.add('hidden');
    document.getElementById('error-message').textContent = message;
    document.getElementById('errorModal').classList.remove('hidden');
}

function closeErrorModal() {
    document.getElementById('errorModal').classList.add('hidden');
    document.body.style.overflow = '';
}

function closeSuccessModal() {
    document.getElementById('successModal').classList.add('hidden');
    document.body.style.overflow = '';
    showToaster('Success', 'Your files have been uploaded successfully', 'success');
}

function refreshFilesList(files) {
    const container = document.getElementById('files-container');
    const noFilesMessage = document.getElementById('no-files-message');
    
    if (noFilesMessage) {
        noFilesMessage.classList.add('hidden');
    }
    
    // Clear existing files and add new ones
    container.innerHTML = '';
    
    files.forEach(file => {
        const fileElement = document.createElement('div');
        fileElement.className = 'flex items-center space-x-4 border border-gray-100 p-4 rounded-lg hover:shadow-md transition duration-200 file-item';
        fileElement.dataset.fileId = file.id;
        fileElement.dataset.fileName = file.original_name;
        
        let fileIcon = 'fa-file';
        let iconColor = 'text-gray-400';
        
        if (file.mime_type.includes('pdf')) {
            fileIcon = 'fa-file-pdf';
            iconColor = 'text-red-400';
        } else if (file.mime_type.includes('word') || file.mime_type.includes('document')) {
            fileIcon = 'fa-file-word';
            iconColor = 'text-blue-400';
        } else if (file.mime_type.includes('excel') || file.mime_type.includes('spreadsheet')) {
            fileIcon = 'fa-file-excel';
            iconColor = 'text-green-400';
        } else if (file.mime_type.includes('image')) {
            fileIcon = 'fa-file-image';
            iconColor = 'text-purple-400';
        } else if (file.mime_type.includes('zip') || file.mime_type.includes('archive')) {
            fileIcon = 'fa-file-archive';
            iconColor = 'text-yellow-400';
        }
        
        const createdAt = new Date(file.created_at);
        const formattedDate = createdAt.toLocaleDateString('en-US', { 
            day: 'numeric', 
            month: 'short'
        });
        const formattedTime = createdAt.toLocaleTimeString('en-US', { 
            hour: 'numeric', 
            minute: '2-digit', 
            hour12: true 
        });
        
        fileElement.innerHTML = `
            <input type="checkbox" class="form-checkbox h-4 w-4 text-green-500 rounded border-gray-300 file-checkbox">
            <i class="fas ${fileIcon} ${iconColor}"></i>
            <div class="flex-grow">
                <p class="text-sm font-medium text-gray-700">${file.original_name}</p>
                <p class="text-xs text-gray-500">${file.description || 'No description'}</p>
                <p class="text-xs text-gray-500">${formattedDate} at ${formattedTime} • ${formatFileSize(file.size)}</p>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-600">${file.uploader_type === 'CUSTOMER' ? 'Customer' : 'Writer'}</span>
                <button class="text-gray-400 hover:text-gray-600 transition-colors" onclick="downloadFile('${file.id}', '${file.original_name}')">
                    <i class="fas fa-download"></i>
                </button>
            </div>
        `;
        
        // Add newest files at the top
        container.insertBefore(fileElement, container.firstChild);
    });
    
    // Update file count
    const fileCountBadge = document.querySelector('#files-tab span');
    fileCountBadge.textContent = document.querySelectorAll('.file-item').length;
    
    // Reinitialize file checkboxes
    initFileCheckboxes();
}

function updateOrderStatus(status) {
    // Update status indicator
    const statusDot = document.querySelector('.w-2.h-2.rounded-full');
    const statusText = statusDot.nextElementSibling;
    
    switch(status) {
        case 'DONE':
            statusDot.className = 'w-2 h-2 rounded-full bg-green-500 mr-2';
            statusText.textContent = 'Done';
            break;
        case 'ON_REVISION':
            statusDot.className = 'w-2 h-2 rounded-full bg-yellow-500 mr-2';
            statusText.textContent = 'On revision';
            break;
        default:
            statusDot.className = 'w-2 h-2 rounded-full bg-blue-500 mr-2';
            statusText.textContent = status.toLowerCase().replace('_', ' ');
    }
    
    // Hide reassign button if status is DONE
    if (status === 'DONE') {
        const reassignButton = document.querySelector('button[onclick^="reassignOrder"]');
        if (reassignButton) {
            reassignButton.parentElement.remove();
        }
        
        // Hide upload button
        const uploadButton = document.querySelector('button[onclick="showUploadModal()"]');
        if (uploadButton) {
            uploadButton.parentElement.remove();
        }
    }
}

// Download functions - FIXED
function downloadFile(fileId, fileName) {
    fetch(`/api/writer/file/download`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ file_id: fileId })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Download failed with status ' + response.status);
        }
        return response.blob();
    })
    .then(blob => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = fileName;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
    })
    .catch(error => {
        console.error('Download failed:', error);
        showToaster('Error', 'Failed to download file: ' + error.message, 'error');
    });
}

// Download multiple files - FIXED
function downloadSelectedFiles() {
    const selectedCheckboxes = document.querySelectorAll('.file-checkbox:checked');
    
    if (selectedCheckboxes.length === 0) {
        showToaster('Error', 'Please select at least one file to download', 'error');
        return;
    }
    
    // If only one file selected, download it directly
    if (selectedCheckboxes.length === 1) {
        const fileItem = selectedCheckboxes[0].closest('.file-item');
        downloadFile(fileItem.dataset.fileId, fileItem.dataset.fileName);
        return;
    }
    
    // For multiple files, use batch download endpoint
    const fileIds = Array.from(selectedCheckboxes).map(checkbox => 
        checkbox.closest('.file-item').dataset.fileId
    );
    
    fetch('/api/writer/files/batch-download', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ file_ids: fileIds })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Batch download failed with status ' + response.status);
        }
        return response.blob();
    })
    .then(blob => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'order_files.zip';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
    })
    .catch(error => {
        console.error('Batch download failed:', error);
        showToaster('Error', 'Failed to download files: ' + error.message, 'error');
    });
}

// File checkboxes and bulk download functionality
function initFileCheckboxes() {
    const selectAllCheckbox = document.getElementById('selectAllFiles');
    const fileCheckboxes = document.querySelectorAll('.file-checkbox');
    const bulkDownloadBtn = document.getElementById('bulk-download-btn');
    
    // Add event listeners to individual checkboxes
    fileCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            const checkedCount = document.querySelectorAll('.file-checkbox:checked').length;
            
            // Update "select all" checkbox state
            if (checkedCount === fileCheckboxes.length) {
                selectAllCheckbox.checked = true;
                selectAllCheckbox.indeterminate = false;
            } else if (checkedCount === 0) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            } else {
                selectAllCheckbox.indeterminate = true;
            }
            
            // Show/hide bulk download button
            if (checkedCount > 0) {
                bulkDownloadBtn.classList.remove('hidden');
            } else {
                bulkDownloadBtn.classList.add('hidden');
            }
        });
    });
    
    // Add event listener to "select all" checkbox
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', () => {
            const isChecked = selectAllCheckbox.checked;
            
            fileCheckboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
            });
            
            if (isChecked && fileCheckboxes.length > 0) {
                bulkDownloadBtn.classList.remove('hidden');
            } else {
                bulkDownloadBtn.classList.add('hidden');
            }
        });
    }
}

// Message Tab Functions
function switchMessageTab(tabName) {
    const clientTab = document.getElementById('client-messages-tab');
    const supportTab = document.getElementById('support-messages-tab');
    const clientContainer = document.getElementById('client-messages-container');
    const supportContainer = document.getElementById('support-messages-container');
    
    if (tabName === 'client') {
        clientTab.classList.add('border-b-2', 'border-blue-500');
        supportTab.classList.remove('border-b-2', 'border-blue-500');
        clientContainer.classList.remove('hidden');
        supportContainer.classList.add('hidden');
        
        // Mark client messages as read
        markMessagesAsRead('client');
    } else {
        supportTab.classList.add('border-b-2', 'border-blue-500');
        clientTab.classList.remove('border-b-2', 'border-blue-500');
        supportContainer.classList.remove('hidden');
        clientContainer.classList.add('hidden');
        
        // Mark support messages as read
        markMessagesAsRead('support');
    }
    
    scrollToBottom(tabName);
}

// Mark messages as read
function markMessagesAsRead(type = 'all') {
    const orderId = '{{ $order->id }}';
    
    fetch('/api/writer/messages/mark-read', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ 
            order_id: orderId,
            message_type: type
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update unread message count badges
            if (type === 'client' || type === 'all') {
                const badge = document.querySelector('#client-messages-tab span');
                if (badge) badge.remove();
            }
            
            if (type === 'support' || type === 'all') {
                const badge = document.querySelector('#support-messages-tab span');
                if (badge) badge.remove();
            }
            
            if (type === 'all') {
                const tabBadge = document.querySelector('#messages-tab span');
                if (tabBadge) tabBadge.remove();
            }
        }
    })
    .catch(error => {
        console.error('Error marking messages as read:', error);
    });
}

// Scroll message container to bottom
function scrollToBottom(type = 'client') {
    const container = type === 'client' 
        ? document.querySelector('#client-messages-container .messages-container') 
        : document.querySelector('#support-messages-container .messages-container');
    
    if (container) {
        container.scrollTop = container.scrollHeight;
    }
}

// Send client message
function sendClientMessage() {
    const form = document.getElementById('clientMessageForm');
    const messageContent = document.getElementById('clientMessageContent');
    const attachment = document.getElementById('clientMessageAttachment');
    
    if (messageContent.value.trim() === '' && attachment.files.length === 0) {
        showToaster('Error', 'Please enter a message or attach a file', 'error');
        return;
    }
    
    const formData = new FormData(form);
    
    // Show sending indicator
    const container = document.querySelector('#client-messages-container .messages-container');
    const sendingIndicator = document.createElement('div');
    sendingIndicator.className = 'flex justify-end items-start space-x-2 sending-indicator';
    sendingIndicator.innerHTML = `
        <div class="max-w-xs sm:max-w-md rounded-lg p-2 bg-blue-50 shadow-sm opacity-50">
            <p class="text-sm text-gray-700">${messageContent.value}</p>
            <div class="mt-1">
                <span class="text-xs text-gray-400">Sending...</span>
            </div>
        </div>
        <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0 text-xs">
            <span class="text-blue-600 font-medium">W</span>
        </div>
    `;
    
    container.appendChild(sendingIndicator);
    scrollToBottom('client');
    
    fetch('/api/writer/messages/send', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        // Remove sending indicator
        container.removeChild(sendingIndicator);
        
        if (data.success) {
            // Add message to the container
            const messageElement = document.createElement('div');
            messageElement.className = 'flex justify-end items-start space-x-2';
            messageElement.id = `message-${data.message.id}`;
            
            let attachmentHtml = '';
            if (data.message.files && data.message.files.length > 0) {
                attachmentHtml = `
                    <div class="mt-1 pt-1 border-t border-gray-200">
                        ${data.message.files.map(file => `
                            <div class="flex items-center text-xs">
                                <svg class="h-3 w-3 text-gray-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                </svg>
                                <a href="javascript:void(0)" onclick="downloadFile('${file.id}', '${file.name}')" class="text-blue-600 hover:underline">${file.name}</a>
                                <span class="ml-1 text-gray-500">(${formatFileSize(file.size)})</span>
                            </div>
                        `).join('')}
                    </div>
                `;
            }
            
            const createdAt = new Date(data.message.created_at);
            const formattedTime = createdAt.toLocaleTimeString('en-US', { 
                hour: 'numeric', 
                minute: '2-digit', 
                hour12: true 
            });
            
            messageElement.innerHTML = `
                <div class="max-w-xs sm:max-w-md rounded-lg p-2 bg-blue-50 shadow-sm">
                    <p class="text-sm text-gray-700">${data.message.message}</p>
                    ${attachmentHtml}
                    <div class="mt-1 flex justify-between items-center">
                        <span class="text-xs text-gray-400">${formattedTime}</span>
                        <span class="text-xs text-gray-400">Sent</span>
                    </div>
                </div>
                <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0 text-xs">
                    <span class="text-blue-600 font-medium">W</span>
                </div>
            `;
            
            container.appendChild(messageElement);
            scrollToBottom('client');
            
            // Clear input fields
            messageContent.value = '';
            attachment.value = '';
            document.getElementById('clientAttachmentPreview').classList.add('hidden');
        } else {
            showToaster('Error', data.message || 'Failed to send message', 'error');
        }
    })
    .catch(error => {
        console.error('Error sending message:', error);
        showToaster('Error', 'Something went wrong', 'error');
        
        // Remove sending indicator
        container.removeChild(sendingIndicator);
    });
}

// Send support message
function sendSupportMessage() {
    const form = document.getElementById('supportMessageForm');
    const messageContent = document.getElementById('supportMessageContent');
    const attachment = document.getElementById('supportMessageAttachment');
    
    if (messageContent.value.trim() === '' && attachment.files.length === 0) {
        showToaster('Error', 'Please enter a message or attach a file', 'error');
        return;
    }
    
    const formData = new FormData(form);
    
    // Show sending indicator
    const container = document.querySelector('#support-messages-container .messages-container');
    const sendingIndicator = document.createElement('div');
    sendingIndicator.className = 'flex justify-end items-start space-x-2 sending-indicator';
    sendingIndicator.innerHTML = `
        <div class="max-w-xs sm:max-w-md rounded-lg p-2 bg-blue-50 shadow-sm opacity-50">
            <p class="text-sm text-gray-700">${messageContent.value}</p>
            <div class="mt-1">
                <span class="text-xs text-gray-400">Sending...</span>
            </div>
        </div>
        <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0 text-xs">
            <span class="text-blue-600 font-medium">W</span>
        </div>
    `;
    
    container.appendChild(sendingIndicator);
    scrollToBottom('support');
    
    fetch('/api/writer/messages/send', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        // Remove sending indicator
        container.removeChild(sendingIndicator);
        
        if (data.success) {
            // Add message to the container
            const messageElement = document.createElement('div');
            messageElement.className = 'flex justify-end items-start space-x-2';
            messageElement.id = `message-${data.message.id}`;
            
            let attachmentHtml = '';
            if (data.message.files && data.message.files.length > 0) {
                attachmentHtml = `
                    <div class="mt-1 pt-1 border-t border-gray-200">
                        ${data.message.files.map(file => `
                            <div class="flex items-center text-xs">
                                <svg class="h-3 w-3 text-gray-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                </svg>
                                <a href="javascript:void(0)" onclick="downloadFile('${file.id}', '${file.name}')" class="text-blue-600 hover:underline">${file.name}</a>
                                <span class="ml-1 text-gray-500">(${formatFileSize(file.size)})</span>
                            </div>
                        `).join('')}
                    </div>
                `;
            }
            
            const createdAt = new Date(data.message.created_at);
            const formattedTime = createdAt.toLocaleTimeString('en-US', { 
                hour: 'numeric', 
                minute: '2-digit', 
                hour12: true 
            });
            
            messageElement.innerHTML = `
                <div class="max-w-xs sm:max-w-md rounded-lg p-2 bg-blue-50 shadow-sm">
                    <p class="text-sm text-gray-700">${data.message.message}</p>
                    ${attachmentHtml}
                    <div class="mt-1 flex justify-between items-center">
                        <span class="text-xs text-gray-400">${formattedTime}</span>
                        <span class="text-xs text-gray-400">Sent</span>
                    </div>
                </div>
                <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0 text-xs">
                    <span class="text-blue-600 font-medium">W</span>
                </div>
            `;
            
            container.appendChild(messageElement);
            scrollToBottom('support');
            
            // Clear input fields
            messageContent.value = '';
            attachment.value = '';
            document.getElementById('supportAttachmentPreview').classList.add('hidden');
        } else {
            showToaster('Error', data.message || 'Failed to send message', 'error');
        }
    })
    .catch(error => {
        console.error('Error sending message:', error);
        showToaster('Error', 'Something went wrong', 'error');
        
        // Remove sending indicator
        container.removeChild(sendingIndicator);
    });
}

// Message attachment preview functions
document.getElementById('clientMessageAttachment').addEventListener('change', function() {
    const preview = document.getElementById('clientAttachmentPreview');
    const nameEl = document.getElementById('clientAttachmentName');
    
    if (this.files.length > 0) {
        nameEl.textContent = this.files[0].name;
        preview.classList.remove('hidden');
    } else {
        preview.classList.add('hidden');
    }
});

document.getElementById('supportMessageAttachment').addEventListener('change', function() {
    const preview = document.getElementById('supportAttachmentPreview');
    const nameEl = document.getElementById('supportAttachmentName');
    
    if (this.files.length > 0) {
        nameEl.textContent = this.files[0].name;
        preview.classList.remove('hidden');
    } else {
        preview.classList.add('hidden');
    }
});

function removeClientAttachment() {
    document.getElementById('clientMessageAttachment').value = '';
    document.getElementById('clientAttachmentPreview').classList.add('hidden');
}

function removeSupportAttachment() {
    document.getElementById('supportMessageAttachment').value = '';
    document.getElementById('supportAttachmentPreview').classList.add('hidden');
}

// Toaster notification function
function showToaster(title, message, type = 'success') {
    const toaster = document.getElementById('toaster');
    const toasterTitle = document.getElementById('toaster-title');
    const toasterMessage = document.getElementById('toaster-message');
    
    toasterTitle.textContent = title;
    toasterMessage.textContent = message;
    
    // Change toaster style based on type
    const toasterContent = toaster.querySelector('div');
    
    // Reset classes first
    toasterContent.className = 'p-4 rounded shadow-lg flex items-start max-w-sm';
    const iconDiv = toasterContent.querySelector('div:first-child');
    
    if (type === 'success') {
        toasterContent.classList.add('bg-green-50', 'border-l-4', 'border-green-500');
        iconDiv.className = 'text-green-500 mr-3';
        iconDiv.innerHTML = `
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        `;
        toasterTitle.className = 'font-medium text-green-800';
        toasterMessage.className = 'text-sm text-green-700 mt-1';
    } else if (type === 'error') {
        toasterContent.classList.add('bg-red-50', 'border-l-4', 'border-red-500');
        iconDiv.className = 'text-red-500 mr-3';
        iconDiv.innerHTML = `
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        `;
        toasterTitle.className = 'font-medium text-red-800';
        toasterMessage.className = 'text-sm text-red-700 mt-1';
    } else if (type === 'warning') {
        toasterContent.classList.add('bg-yellow-50', 'border-l-4', 'border-yellow-500');
        iconDiv.className = 'text-yellow-500 mr-3';
        iconDiv.innerHTML = `
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
        `;
        toasterTitle.className = 'font-medium text-yellow-800';
        toasterMessage.className = 'text-sm text-yellow-700 mt-1';
    }
    
    // Show toaster
    toaster.classList.remove('translate-x-full');
    
    // Auto-hide after delay
    setTimeout(() => {
        hideToaster();
    }, 5000);
}

function hideToaster() {
    const toaster = document.getElementById('toaster');
    toaster.classList.add('translate-x-full');
}

// Real-time message updates
let messagePolling;

function startMessagePolling() {
    const orderId = '{{ $order->id }}';
    
    // Poll for new messages every 10 seconds
    messagePolling = setInterval(() => {
        fetch(`/api/writer/messages/check-new?order_id=${orderId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.has_new) {
                // Update the message containers with new messages
                if (data.client_messages && data.client_messages.length > 0) {
                    updateMessagesContainer('client', data.client_messages);
                }
                
                if (data.support_messages && data.support_messages.length > 0) {
                    updateMessagesContainer('support', data.support_messages);
                }
                
                // Update message counts
                if (data.client_count > 0) {
                    updateMessageCount('client', data.client_count);
                }
                
                if (data.support_count > 0) {
                    updateMessageCount('support', data.support_count);
                }
                
                // Update tab badge
                if ((data.client_count + data.support_count) > 0) {
                    updateTabBadge(data.client_count + data.support_count);
                }
                
                // If messages tab is active, mark as read
                const messagesPanel = document.getElementById('messages-panel');
                if (!messagesPanel.classList.contains('hidden')) {
                    markMessagesAsRead();
                }
            }
        })
        .catch(error => {
            console.error('Error checking for new messages:', error);
        });
    }, 10000);
}

function updateMessagesContainer(type, messages) {
    const container = type === 'client' 
        ? document.querySelector('#client-messages-container .messages-container') 
        : document.querySelector('#support-messages-container .messages-container');
    
    if (!container) return;
    
    const currentTab = type === 'client' 
        ? !document.getElementById('client-messages-container').classList.contains('hidden')
        : !document.getElementById('support-messages-container').classList.contains('hidden');
    
    messages.forEach(message => {
        // Skip if message already exists
        if (document.getElementById(`message-${message.id}`)) {
            return;
        }
        
        // Create message element
        const messageElement = document.createElement('div');
        messageElement.id = `message-${message.id}`;
        
        if (message.user_id == '{{ Auth::id() }}') {
            // Writer's own message (shouldn't happen with polling, but for consistency)
            messageElement.className = 'flex justify-end items-start space-x-2';
            
            let attachmentHtml = '';
            if (message.files && message.files.length > 0) {
                attachmentHtml = `
                    <div class="mt-1 pt-1 border-t border-gray-200">
                        ${message.files.map(file => `
                            <div class="flex items-center text-xs">
                                <svg class="h-3 w-3 text-gray-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                </svg>
                                <a href="javascript:void(0)" onclick="downloadFile('${file.id}', '${file.name}')" class="text-blue-600 hover:underline">${file.name}</a>
                                <span class="ml-1 text-gray-500">(${formatFileSize(file.size)})</span>
                            </div>
                        `).join('')}
                    </div>
                `;
            }
            
            const createdAt = new Date(message.created_at);
            const formattedTime = createdAt.toLocaleTimeString('en-US', { 
                hour: 'numeric', 
                minute: '2-digit', 
                hour12: true 
            });
            
            messageElement.innerHTML = `
                <div class="max-w-xs sm:max-w-md rounded-lg p-2 bg-blue-50 shadow-sm">
                    <p class="text-sm text-gray-700">${message.message}</p>
                    ${attachmentHtml}
                    <div class="mt-1 flex justify-between items-center">
                        <span class="text-xs text-gray-400">${formattedTime}</span>
                        <span class="text-xs ${message.read_at ? 'text-green-500' : 'text-gray-400'}">
                            ${message.read_at ? 'Seen' : 'Sent'}
                        </span>
                    </div>
                </div>
                <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0 text-xs">
                    <span class="text-blue-600 font-medium">W</span>
                </div>
            `;
        } else if (type === 'client') {
            // Client message
            messageElement.className = 'flex justify-start items-start space-x-2';
            
            let attachmentHtml = '';
            if (message.files && message.files.length > 0) {
                attachmentHtml = `
                    <div class="mt-1 pt-1 border-t border-gray-200">
                        ${message.files.map(file => `
                            <div class="flex items-center text-xs">
                                <svg class="h-3 w-3 text-gray-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                </svg>
                                <a href="javascript:void(0)" onclick="downloadFile('${file.id}', '${file.name}')" class="text-blue-600 hover:underline">${file.name}</a>
                                <span class="ml-1 text-gray-500">(${formatFileSize(file.size)})</span>
                            </div>
                        `).join('')}
                    </div>
                `;
            }
            
            const createdAt = new Date(message.created_at);
            const formattedTime = createdAt.toLocaleTimeString('en-US', { 
                hour: 'numeric', 
                minute: '2-digit', 
                hour12: true 
            });
            
            messageElement.innerHTML = `
                <div class="w-6 h-6 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0 text-xs">
                    <span class="text-gray-600 font-medium">C</span>
                </div>
                <div class="max-w-xs sm:max-w-md rounded-lg p-2 bg-white shadow-sm">
                    <p class="text-sm text-gray-700">${message.message}</p>
                    ${attachmentHtml}
                    <div class="mt-1">
                        <span class="text-xs text-gray-400">${formattedTime}</span>
                    </div>
                </div>
            `;
        } else if (message.message_type === 'system') {
            // System message
            messageElement.className = 'flex justify-start items-start space-x-2';
            
            const createdAt = new Date(message.created_at);
            const formattedTime = createdAt.toLocaleTimeString('en-US', { 
                hour: 'numeric', 
                minute: '2-digit', 
                hour12: true 
            });
            
            messageElement.innerHTML = `
                <div class="w-6 h-6 rounded-full bg-yellow-100 flex items-center justify-center flex-shrink-0 text-xs">
                    <span class="text-yellow-600 font-medium">S</span>
                </div>
                <div class="max-w-xs sm:max-w-md rounded-lg p-2 bg-yellow-50 shadow-sm">
                    <p class="text-sm text-gray-700">${message.message}</p>
                    <div class="mt-1">
                        <span class="text-xs text-gray-400">${formattedTime}</span>
                    </div>
                </div>
            `;
        } else {
            // Support message
            messageElement.className = 'flex justify-start items-start space-x-2';
            
            let attachmentHtml = '';
            if (message.files && message.files.length > 0) {
                attachmentHtml = `
                    <div class="mt-1 pt-1 border-t border-gray-200">
                        ${message.files.map(file => `
                            <div class="flex items-center text-xs">
                                <svg class="h-3 w-3 text-gray-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                </svg>
                                <a href="javascript:void(0)" onclick="downloadFile('${file.id}', '${file.name}')" class="text-blue-600 hover:underline">${file.name}</a>
                                <span class="ml-1 text-gray-500">(${formatFileSize(file.size)})</span>
                            </div>
                        `).join('')}
                    </div>
                `;
            }
            
            const createdAt = new Date(message.created_at);
            const formattedTime = createdAt.toLocaleTimeString('en-US', { 
                hour: 'numeric', 
                minute: '2-digit', 
                hour12: true 
            });
            
            messageElement.innerHTML = `
                <div class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0 text-xs">
                    <span class="text-green-600 font-medium">S</span>
                </div>
                <div class="max-w-xs sm:max-w-md rounded-lg p-2 bg-green-50 shadow-sm">
                    <p class="text-sm text-gray-700">${message.message}</p>
                    ${attachmentHtml}
                    <div class="mt-1">
                        <span class="text-xs text-gray-400">${formattedTime}</span>
                    </div>
                </div>
            `;
        }
        
        container.appendChild(messageElement);
    });
    
    // If current tab, scroll to bottom
    if (currentTab) {
        scrollToBottom(type);
        
        // Play notification sound if tab is active but container not in focus
        if (!document.hasFocus()) {
            playNotificationSound();
        }
    }
}

function updateMessageCount(type, count) {
    const tab = type === 'client' 
        ? document.getElementById('client-messages-tab')
        : document.getElementById('support-messages-tab');
    
    let badge = tab.querySelector('span');
    
    if (!badge) {
        badge = document.createElement('span');
        badge.className = 'ml-2 bg-red-500 text-white text-xs px-2 py-0.5 rounded-full';
        tab.appendChild(badge);
    }
    
    badge.textContent = count;
}

function updateTabBadge(count) {
    const tab = document.getElementById('messages-tab');
    let badge = tab.querySelector('span');
    
    if (!badge) {
        badge = document.createElement('span');
        badge.className = 'ml-2 bg-red-500 text-white text-xs px-2 py-0.5 rounded-full';
        tab.appendChild(badge);
    }
    
    badge.textContent = count;
}

function playNotificationSound() {
    const audio = new Audio('/sounds/notification.mp3');
    audio.volume = 0.5;
    audio.play().catch(e => {
        // Browser may block autoplay
        console.log('Notification sound blocked by browser');
    });
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

// Auto-resize text areas
function initAutoResizeTextareas() {
    const textareas = document.querySelectorAll('textarea');
    
    textareas.forEach(textarea => {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            const newHeight = Math.min(this.scrollHeight, 150);
            this.style.height = newHeight + 'px';
        });
    });
}

// Initialize everything when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tabs
    switchTab('instructions');
    
    // Start countdown updates
    updateCountdowns();
    setInterval(updateCountdowns, 60000); // Update every minute
    
    // Initialize file checkboxes
    initFileCheckboxes();
    
    // Initialize auto-resize for textareas
    initAutoResizeTextareas();
    
    // Start message polling
    startMessagePolling();
    
    // Add event listener for tab visibility
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            // When tab becomes visible, mark messages as read if messages tab is active
            const messagesPanel = document.getElementById('messages-panel');
            if (!messagesPanel.classList.contains('hidden')) {
                markMessagesAsRead();
            }
        }
    });
    
    // Clean up when page unloads
    window.addEventListener('beforeunload', function() {
        clearInterval(messagePolling);
    });
});

</script>

@endsection