@extends('writers.app')
@section('title', 'Assigned Order #' . $order->id)

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
        background: rgba(0, 0, 0, 0.5);
    }

    .modal-content {
        width: 100%;
        max-width: 42rem;
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
    
    /* Messages container styles */
    .messages-container {
        height: 500px;
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: #e2e8f0 #f8fafc;
    }
    
    .messages-container::-webkit-scrollbar {
        width: 6px;
    }
    
    .messages-container::-webkit-scrollbar-track {
        background: #f8fafc;
    }
    
    .messages-container::-webkit-scrollbar-thumb {
        background-color: #e2e8f0;
        border-radius: 3px;
    }
    
    /* Message slider styles */
    .message-slider {
        height: 3px;
        background-color: #22C55E;
        margin-top: 12px;
        margin-bottom: 12px;
        border-radius: 1.5px;
    }
    
    .message-slider-container {
        padding: 8px 0;
        position: relative;
    }
    
    .message-slider-label {
        position: absolute;
        top: 4px;
        left: 50%;
        transform: translateX(-50%);
        background-color: white;
        padding: 0 8px;
        font-size: 0.75rem;
        color: #22C55E;
    }

    /* Time countdown styles */
    .countdown {
        font-variant-numeric: tabular-nums;
    }
    
    .countdown-warning {
        color: #EF4444;
    }
    
    /* Status indicators */
    .status-confirmed { color: #16a34a; font-weight: bold; }
    .status-unconfirmed { color: #3b82f6; font-weight: bold; }
    .status-done { color: #f59e0b; font-weight: bold; }
    .status-delivered { color: #ef4444; font-weight: bold; }
    
    /* Time remaining colors */
    .time-safe { color: #16a34a; }
    .time-warning { color: #f59e0b; }
    .time-urgent { color: #ef4444; }
    .time-overdue { color: #ef4444; font-weight: bold; }
    .time-completed { color: #6b7280; }
</style>

<div class="container mx-auto px-4 py-6">
    <!-- Order Header -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <div class="flex items-center">
                <svg class="h-6 w-6 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h1 class="text-xl font-semibold text-gray-800">Order #{{ $order->id }}</h1>
                <span class="ml-2 px-2 py-1 text-xs rounded-full {{ $order->status == 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
            <div class="flex items-center space-x-2">
                <span class="text-gray-600">${{ number_format($order->price, 2) }}</span>
                
                @if($order->status == 'unconfirmed')
                <div class="flex space-x-2">
                    <form action="{{ route('writer.confirm.assignment', $order->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-3 py-2 bg-green-500 text-white text-sm rounded hover:bg-green-600 transition-colors duration-200">
                            Accept Order
                        </button>
                    </form>
                    <form action="{{ route('writer.reject.assignment', $order->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-3 py-2 bg-gray-200 text-gray-700 text-sm rounded hover:bg-gray-300 transition-colors duration-200" onclick="return confirm('Are you sure you want to reject this order?')">
                            Reject
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Navigation Tabs -->
        <div class="border-b border-gray-200 relative">
            <nav class="flex -mb-px">
                <div id="tab-slider" class="tab-slider"></div>
                
                <a href="#instructions" id="instructions-tab" class="tab-link active whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm" data-target="instructions-panel" data-width="120">
                    Instructions
                </a>
                <a href="#files" id="files-tab" class="tab-link whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm" data-target="files-panel" data-width="100">
                    All files <span class="ml-1 px-1.5 py-0.5 text-xs rounded-full bg-gray-100">{{ $order->files->count() }}</span>
                </a>
                <a href="#messages" id="messages-tab" class="tab-link whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm" data-target="messages-panel" data-width="110">
                    Messages <span class="ml-1 px-1.5 py-0.5 text-xs rounded-full bg-gray-100">{{ $unreadMessages }}</span>
                </a>
            </nav>
        </div>
        
        <!-- Tab Panels -->
        <div class="px-6 py-6">
            <!-- Instructions Panel -->
            <div id="instructions-panel" class="tab-panel">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Order Details</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500">Price</p>
                                    <p class="font-medium">${{ number_format($order->price, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Deadline</p>
                                    <div class="flex items-center">
                                        <p class="font-medium">{{ $order->deadline->format('M d, h:i A') }}</p>
                                        <span id="deadline-countdown" class="countdown ml-2" data-deadline="{{ $order->deadline }}"></span>
                                        @if($order->deadline->gt(now()))
                                            <span class="ml-2 text-xs px-2 py-1 rounded-full bg-green-100 text-green-700">
                                                {{ $order->deadline->diffForHumans() }}
                                            </span>
                                        @else
                                            <span class="ml-2 text-xs px-2 py-1 rounded-full bg-red-100 text-red-700">
                                                Overdue
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                
                                @php
                                    // Calculate screening deadline (2% of time between created_at and deadline)
                                    $createdAt = $order->created_at;
                                    $deadline = $order->deadline;
                                    $totalTimeInSeconds = $deadline->diffInSeconds($createdAt);
                                    $screeningTimeInSeconds = $totalTimeInSeconds * 0.02;
                                    $screeningDeadline = $createdAt->copy()->addSeconds($screeningTimeInSeconds);
                                @endphp
                                
                                <div>
                                    <p class="text-sm text-gray-500">Screening Deadline</p>
                                    <div class="flex items-center">
                                        <p class="font-medium">{{ $screeningDeadline->format('M d, h:i A') }}</p>
                                        <span id="screening-countdown" class="countdown ml-2" data-deadline="{{ $screeningDeadline }}"></span>
                                    </div>
                                </div>
                                
                                <div>
                                    <p class="text-sm text-gray-500">Task size</p>
                                    <p class="font-medium">{{ $order->task_size ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Type of service</p>
                                    <p class="font-medium">{{ $order->type_of_service ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Discipline</p>
                                    <p class="font-medium">{{ $order->discipline ?? 'N/A' }}</p>
                                </div>
                                @if($order->software)
                                <div>
                                    <p class="text-sm text-gray-500">Software</p>
                                    <p class="font-medium">{{ $order->software }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Paper instructions</h3>
                        <div class="bg-gray-50 p-4 rounded-lg relative">
                            <div class="flex justify-between items-start absolute top-2 right-2">
                                <button 
                                    class="text-gray-400 hover:text-gray-600 transition-colors duration-200 relative mr-2" 
                                    onclick="copyInstructions()">
                                    <i class="fas fa-copy"></i>
                                </button>
                                <button class="text-gray-400 hover:text-gray-600 transition-colors duration-200" onclick="expandInstructions()">
                                    <i class="fas fa-expand-alt"></i>
                                </button>
                            </div>
                            <p class="text-sm whitespace-pre-line">{{ $order->instructions }}</p>
                        </div>
                        
                        @if($order->customer_comments)
                        <div class="mt-4">
                            <h4 class="text-base font-medium text-gray-700 mb-1">Comments from Customer</h4>
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <p class="text-sm whitespace-pre-line">{{ $order->customer_comments }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Files Panel -->
            <div id="files-panel" class="tab-panel hidden">
                <div class="mb-6 flex justify-between items-center">
                    <div class="flex items-center">
                        <label class="inline-flex items-center">
                            <input type="checkbox" class="form-checkbox h-4 w-4 text-green-500 rounded border-gray-300" id="selectAllFiles">
                            <span class="ml-2 text-gray-700">All files</span>
                        </label>
                        <button 
                            class="ml-4 text-gray-400 hover:text-gray-600 transition-colors hidden"
                            id="bulk-download-btn"
                            onclick="downloadSelectedFiles()">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>
                    
                    @if(in_array($order->status, ['confirmed', 'in_progress', 'revision']))
                    <button id="uploadFilesBtn" class="flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        Upload Files
                    </button>
                    @endif
                </div>
                
                @if($order->files->isEmpty())
                <div id="no-files-message" class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">No files uploaded yet</p>
                </div>
                @else
                <div id="files-container">
                    <div class="overflow-hidden bg-white rounded-md shadow">
                        <ul class="divide-y divide-gray-200">
                            @foreach($order->files as $file)
                            <li class="flex items-center justify-between py-3 px-4 hover:bg-gray-50 file-item" data-file-id="{{ $file->id }}" data-file-name="{{ $file->name }}">
                                <div class="flex items-center min-w-0">
                                    <input type="checkbox" class="form-checkbox h-4 w-4 text-green-500 rounded border-gray-300 file-checkbox mr-3">
                                    <div class="flex-shrink-0">
                                        @php
                                            $extension = pathinfo($file->name, PATHINFO_EXTENSION);
                                            $iconClass = 'text-gray-400';
                                            $bgClass = 'bg-gray-100';
                                            
                                            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                                                $iconClass = 'text-purple-500';
                                                $bgClass = 'bg-purple-100';
                                            } elseif (in_array($extension, ['doc', 'docx'])) {
                                                $iconClass = 'text-blue-500';
                                                $bgClass = 'bg-blue-100';
                                            } elseif (in_array($extension, ['xls', 'xlsx'])) {
                                                $iconClass = 'text-green-500';
                                                $bgClass = 'bg-green-100';
                                            } elseif (in_array($extension, ['pdf'])) {
                                                $iconClass = 'text-red-500';
                                                $bgClass = 'bg-red-100';
                                            } elseif (in_array($extension, ['zip', 'rar'])) {
                                                $iconClass = 'text-yellow-500';
                                                $bgClass = 'bg-yellow-100';
                                            }
                                        @endphp
                                        
                                        <div class="{{ $bgClass }} p-2 rounded-md">
                                            <svg class="h-5 w-5 {{ $iconClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-3 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $file->name }}</p>
                                        <div class="flex items-center text-xs text-gray-500">
                                            <span>{{ $file->created_at->format('M d, Y') }}</span>
                                            <span class="mx-1">•</span>
                                            <span>{{ number_format($file->size / 1024, 1) }} KB</span>
                                            @if($file->description)
                                            <span class="mx-1">•</span>
                                            <span class="px-1.5 py-0.5 bg-gray-100 rounded-full">{{ $file->description }}</span>
                                            @endif
                                            @if($file->uploader)
                                            <span class="mx-1">•</span>
                                            <span class="capitalize">{{ $file->uploader->usertype }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="ml-6 flex items-center space-x-2">
                                    <form action="{{ route('writer.download') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="file_id" value="{{ $file->id }}">
                                        <button type="submit" class="p-1 rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-100">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif
                
                <div class="mt-4 text-sm text-gray-500">
                    <p>If you can't open a file (pages, numbers, etc.), download it anyway and use one of these links to convert it to .doc, .docx, .xlsx, etc.:</p>
                    <div class="mt-1">
                        <a href="https://cloudconvert.com" target="_blank" class="text-blue-500 hover:underline">cloudconvert.com</a> or 
                        <a href="https://online-convert.com" target="_blank" class="text-blue-500 hover:underline">online-convert.com</a>
                    </div>
                </div>
            </div>
            
            <!-- Messages Panel -->
            <div id="messages-panel" class="tab-panel hidden">
                <div class="mb-4">
                    <div class="flex border-b">
                        <button class="message-tab py-2 px-4 border-b-2 font-medium focus:outline-none" data-target="client-messages">
                            Client 
                            @if($clientUnreadCount > 0)
                            <span class="ml-1 px-1.5 py-0.5 text-xs rounded-full bg-red-100 text-red-800">{{ $clientUnreadCount }}</span>
                            @endif
                        </button>
                        <button class="message-tab py-2 px-4 border-b-2 font-medium focus:outline-none" data-target="support-messages">
                            Support 
                            @if($supportUnreadCount > 0)
                            <span class="ml-1 px-1.5 py-0.5 text-xs rounded-full bg-red-100 text-red-800">{{ $supportUnreadCount }}</span>
                            @endif
                        </button>
                    </div>
                </div>
                
                <!-- Client Messages -->
                <div id="client-messages" class="message-panel">
                    <div class="message-container mb-4 h-96 overflow-y-auto p-4 bg-gray-50 rounded-lg">
                        @if($clientMessages->isEmpty())
                            <div class="flex items-center justify-center h-full">
                                <p class="text-gray-500 text-sm">No messages with the client yet</p>
                            </div>
                        @else
                            @foreach($clientMessages as $date => $messages)
                                <div class="flex justify-center mb-4">
                                    <div class="bg-gray-200 px-3 py-1 rounded-full">
                                        <span class="text-xs text-gray-600">{{ $date }}</span>
                                    </div>
                                </div>
                                
                                @foreach($messages as $message)
                                    <div class="mb-4 {{ $message->user_id == Auth::id() ? 'flex flex-row-reverse' : 'flex' }}">
                                        <div class="{{ $message->user_id == Auth::id() ? 'ml-2' : 'mr-2' }} flex-shrink-0 h-8 w-8 rounded-full bg-{{ $message->user_id == Auth::id() ? 'green' : 'blue' }}-100 flex items-center justify-center">
                                            <span class="text-{{ $message->user_id == Auth::id() ? 'green' : 'blue' }}-600 font-medium text-sm">
                                                {{ $message->user_id == Auth::id() ? 'W' : 'C' }}
                                            </span>
                                        </div>
                                        <div class="max-w-lg">
                                            <div class="bg-{{ $message->user_id == Auth::id() ? 'green-50' : 'white' }} p-3 rounded-lg shadow-sm">
                                                <p class="text-sm text-gray-800 whitespace-pre-line">{{ $message->message }}</p>
                                                
                                                @if($message->files->count() > 0)
                                                    <div class="mt-2 pt-2 border-t border-gray-200">
                                                        @foreach($message->files as $file)
                                                            <div class="flex items-center text-xs mt-1">
                                                                <svg class="h-4 w-4 text-gray-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                                                </svg>
                                                                <form action="{{ route('writer.download') }}" method="POST" class="inline">
                                                                    @csrf
                                                                    <input type="hidden" name="file_id" value="{{ $file->id }}">
                                                                    <button type="submit" class="text-blue-600 hover:underline">{{ $file->name }}</button>
                                                                </form>
                                                                <span class="ml-1 text-gray-500">({{ number_format($file->size / 1024, 1) }} KB)</span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="{{ $message->user_id == Auth::id() ? 'text-right' : 'text-left' }} mt-1">
                                                <span class="text-xs text-gray-500">{{ $message->created_at->format('g:i A') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endforeach
                        @endif
                    </div>
                    
                    <!-- Client Message Form -->
                    <form id="clientMessageForm" action="{{ route('writer.send.message', $order->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="message_type" value="client">
                        <div class="mb-2">
                            <textarea name="message" rows="3" class="block w-full rounded-lg shadow-sm border-gray-300 focus:ring-green-500 focus:border-green-500 sm:text-sm" placeholder="Type yor message to the client here..."></textarea>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <label class="block text-sm text-gray-600 mr-2">
                                    <span class="sr-only">Choose file</span>
                                    <input type="file" name="attachment" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">