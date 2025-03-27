@extends('writers.app')

@section('title', 'Assigned Order #' . $order->id)

@section('content')
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
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <a href="#instructions" class="tab-link active whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm" data-target="instructions-panel">
                    Instructions
                </a>
                <a href="#files" class="tab-link whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm" data-target="files-panel">
                    All files <span class="ml-1 px-1.5 py-0.5 text-xs rounded-full bg-gray-100">{{ $order->files->count() }}</span>
                </a>
                <a href="#messages" class="tab-link whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm" data-target="messages-panel">
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
                        <div class="bg-gray-50 p-4 rounded-lg">
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
                    <h3 class="text-lg font-medium text-gray-900">Files ({{ $order->files->count() }})</h3>
                    
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
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">No files uploaded yet</p>
                </div>
                @else
                <div class="overflow-hidden bg-white rounded-md shadow">
                    <ul class="divide-y divide-gray-200">
                        @foreach($order->files as $file)
                        <li class="flex items-center justify-between py-3 px-4 hover:bg-gray-50">
                            <div class="flex items-center min-w-0">
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
                                        <span>{{ $file->size ? number_format($file->size / 1024, 1) . ' KB' : 'N/A' }}</span>
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
                            <textarea name="message" rows="3" class="block w-full rounded-lg shadow-sm border-gray-300 focus:ring-green-500 focus:border-green-500 sm:text-sm" placeholder="Type your message to the client here..."></textarea>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <label class="block text-sm text-gray-600 mr-2">
                                    <span class="sr-only">Choose file</span>
                                    <input type="file" name="attachment" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                                </label>
                            </div>
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                Send Message
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Support Messages -->
                <div id="support-messages" class="message-panel hidden">
                    <div class="message-container mb-4 h-96 overflow-y-auto p-4 bg-gray-50 rounded-lg">
                        @if($supportMessages->isEmpty())
                            <div class="flex items-center justify-center h-full">
                                <p class="text-gray-500 text-sm">No messages with support yet</p>
                            </div>
                        @else
                            @foreach($supportMessages as $date => $messages)
                                <div class="flex justify-center mb-4">
                                    <div class="bg-gray-200 px-3 py-1 rounded-full">
                                        <span class="text-xs text-gray-600">{{ $date }}</span>
                                    </div>
                                </div>
                                
                                @foreach($messages as $message)
                                    <div class="mb-4 {{ $message->user_id == Auth::id() ? 'flex flex-row-reverse' : 'flex' }}">
                                        <div class="{{ $message->user_id == Auth::id() ? 'ml-2' : 'mr-2' }} flex-shrink-0 h-8 w-8 rounded-full bg-{{ $message->user_id == Auth::id() ? 'green' : 'purple' }}-100 flex items-center justify-center">
                                            <span class="text-{{ $message->user_id == Auth::id() ? 'green' : 'purple' }}-600 font-medium text-sm">
                                                {{ $message->user_id == Auth::id() ? 'W' : 'S' }}
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
                    
                    <!-- Support Message Form -->
                    <form id="supportMessageForm" action="{{ route('writer.send.message', $order->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="message_type" value="support">
                        <div class="mb-2">
                            <textarea name="message" rows="3" class="block w-full rounded-lg shadow-sm border-gray-300 focus:ring-purple-500 focus:border-purple-500 sm:text-sm" placeholder="Type your message to support here..."></textarea>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <label class="block text-sm text-gray-600 mr-2">
                                    <span class="sr-only">Choose file</span>
                                    <input type="file" name="attachment" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                                </label>
                            </div>
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                Send Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Files Modal -->
<div id="uploadModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center hidden z-50 upload-modal">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>

    <!-- Modal Container -->
    <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
        <!-- Modal Content -->
        <div id="uploadModalContent" class="relative bg-white rounded-lg max-w-xl w-full mx-auto shadow-xl transform transition-all">
            <!-- Step 1: File Selection -->
            <div id="uploadStep1" class="block">
                <!-- Modal Header -->
                <div class="flex justify-between items-center p-6 border-b">
                    <h3 class="text-lg font-medium text-gray-900">Upload files</h3>
                    <button type="button" id="closeUploadModal" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6">
                    <p class="text-sm text-gray-600 mb-6">
                        Please make sure to upload a video preview together with the completed order. Select description "Preview" to your video file. Maximum file size allowed: 99 MB.
                    </p>

                    <!-- File List -->
                    <div id="uploadedFiles" class="space-y-3 mb-4"></div>

                    <!-- Upload Zone -->
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center cursor-pointer hover:border-green-500 transition-colors duration-200"
                         id="dropZone"
                         ondrop="handleFileDrop(event)"
                         ondragover="handleDragOver(event)"
                         ondragleave="handleDragLeave(event)"
                         onclick="document.getElementById('fileInput').click()">
                        <input type="file" id="fileInput" class="hidden" multiple onchange="handleFileSelect(event)">
                        <button type="button" class="text-green-500 font-medium">Choose file</button>
                        <span class="text-gray-500 ml-2">or drag file</span>
                    </div>

                    <!-- Modal Footer -->
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" id="cancelUpload" 
                                class="px-4 py-2 text-gray-700 hover:text-gray-900 transition-colors duration-200 rounded-md">
                            Cancel
                        </button>
                        <button type="button" id="gotoVerificationStep" 
                                class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors duration-200">
                            Continue
                        </button>
                    </div>
                </div>
            </div>

            <!-- Step 2: Verification Checklist -->
            <div id="uploadStep2" class="hidden">
                <!-- Modal Header -->
                <div class="flex justify-between items-center p-6 border-b">
                    <h3 class="text-lg font-medium text-gray-900">Upload files</h3>
                    <button type="button" id="closeUploadModalStep2" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6">
                    <h4 class="text-base font-medium text-gray-700 mb-3">Order details</h4>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Paper format</span>
                            <span class="text-gray-800">{{ $order->type_of_service ?? 'Not applicable' }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Pages</span>
                            <div>
                                <span class="text-gray-800">{{ $order->task_size ?? '0' }} pages</span>
                                <div class="text-xs text-gray-500">{{ $order->spacing ?? 'Double spaced' }}</div>
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Discipline</span>
                            <span class="text-gray-800">{{ $order->discipline ?? 'Not specified' }}</span>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <h4 class="text-base font-medium text-gray-700 mb-3">To be on the safe side, please double-check whether:</h4>
                        <div class="space-y-3">
                            <label class="flex items-start">
                                <input type="checkbox" class="form-checkbox h-5 w-5 text-green-500 rounded border-gray-300 mt-0.5" id="check1">
                                <span class="ml-2 text-gray-700">All order files are checked</span>
                            </label>
                            <label class="flex items-start">
                                <input type="checkbox" class="form-checkbox h-5 w-5 text-green-500 rounded border-gray-300 mt-0.5" id="check2">
                                <span class="ml-2 text-gray-700">All order messages are thoroughly read</span>
                            </label>
                            <label class="flex items-start">
                                <input type="checkbox" class="form-checkbox h-5 w-5 text-green-500 rounded border-gray-300 mt-0.5" id="check3">
                                <span class="ml-2 text-gray-700">All paper instructions are followed</span>
                            </label>
                            <label class="flex items-start">
                                <input type="checkbox" class="form-checkbox h-5 w-5 text-green-500 rounded border-gray-300 mt-0.5" id="check4">
                                <span class="ml-2 text-gray-700">Required formatting style is applied</span>
                            </label>
                            <label class="flex items-start">
                                <input type="checkbox" class="form-checkbox h-5 w-5 text-green-500 rounded border-gray-300 mt-0.5" id="check5">
                                <span class="ml-2 text-gray-700">Preview video demonstrates the solution</span>
                            </label>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" id="cancelVerification" 
                                class="px-4 py-2 text-gray-700 hover:text-gray-900 transition-colors duration-200 rounded-md">
                            Back
                        </button>
                        <button type="button" id="startUpload" 
                                class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors duration-200">
                            Submit
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Processing Modal (Step 3) -->
<div id="processingModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">
    <div class="bg-white rounded-lg p-8 max-w-md w-full">
        <h3 class="text-lg font-medium text-gray-700 text-center mb-6">Uploading files...</h3>
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
        <h3 class="text-xl font-medium text-gray-700 mb-2">Success!</h3>
        <p class="text-gray-500 mb-6">Your files have been uploaded successfully.</p>
        <button type="button" id="closeSuccessModal" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors duration-200">
            Close
        </button>
    </div>
</div>

<!-- Toaster Notification -->
<div id="toaster" class="fixed top-4 right-4 z-50 transform translate-x-full transition-transform duration-300 ease-in-out">
    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded shadow-lg flex items-start max-w-sm">
        <div class="text-green-500 mr-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <div>
            <p class="font-medium text-green-800">Success!</p>
            <p class="text-sm text-green-700 mt-1">Your files have been uploaded successfully.</p>
        </div>
        <button onclick="hideToaster()" class="ml-auto text-green-500 hover:text-green-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab navigation
        const tabLinks = document.querySelectorAll('.tab-link');
        const tabPanels = document.querySelectorAll('.tab-panel');
        
        tabLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Remove active state from all tabs
                tabLinks.forEach(tab => {
                    tab.classList.remove('active', 'text-green-600', 'border-green-500');
                    tab.classList.add('text-gray-500', 'border-transparent');
                });
                
                // Add active state to clicked tab
                this.classList.add('active', 'text-green-600', 'border-green-500');
                this.classList.remove('text-gray-500', 'border-transparent');
                
                // Hide all tab panels
                tabPanels.forEach(panel => {
                    panel.classList.add('hidden');
                });
                
                // Show target panel
                const targetPanel = document.getElementById(this.dataset.target);
                if (targetPanel) {
                    targetPanel.classList.remove('hidden');
                }
                
                // Mark messages as read if viewing messages tab
                if (this.dataset.target === 'messages-panel') {
                    markMessagesAsRead();
                }
            });
        });
        
        // Set initial active tab
        if (tabLinks.length > 0) {
            tabLinks[0].classList.add('active', 'text-green-600', 'border-green-500');
            tabLinks[0].classList.remove('text-gray-500', 'border-transparent');
        }
        
        // Message tab navigation
        const messageTabs = document.querySelectorAll('.message-tab');
        const messagePanels = document.querySelectorAll('.message-panel');
        
        messageTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                // Remove active state from all tabs
                messageTabs.forEach(t => {
                    t.classList.remove('text-green-600', 'border-green-500');
                    t.classList.add('text-gray-500', 'border-transparent');
                });
                
                // Add active state to clicked tab
                this.classList.add('text-green-600', 'border-green-500');
                this.classList.remove('text-gray-500', 'border-transparent');
                
                // Hide all message panels
                messagePanels.forEach(panel => {
                    panel.classList.add('hidden');
                });
                
                // Show target panel
                const targetPanel = document.getElementById(this.dataset.target);
                if (targetPanel) {
                    targetPanel.classList.remove('hidden');
                    
                    // Mark messages as read for this tab
                    markMessagesAsRead(this.dataset.target === 'client-messages' ? 'client' : 'support');
                    
                    // Scroll to bottom of messages
                    const container = targetPanel.querySelector('.message-container');
                    if (container) {
                        container.scrollTop = container.scrollHeight;
                    }
                }
            });
        });
        
        // Set initial active message tab
        if (messageTabs.length > 0) {
            messageTabs[0].classList.add('text-green-600', 'border-green-500');
            messageTabs[0].classList.remove('text-gray-500', 'border-transparent');
            
            if (messagePanels.length > 0) {
                messagePanels[0].classList.remove('hidden');
                
                // Scroll to bottom of messages
                const container = messagePanels[0].querySelector('.message-container');
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            }
        }
        
        // Function to mark messages as read
        function markMessagesAsRead(type = 'all') {
            fetch('{{ route("writer.mark.messages.read", $order->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ type: type })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update unread badge counts if needed
                    if (type === 'all' || type === 'client') {
                        const clientBadge = document.querySelector('.message-tab[data-target="client-messages"] span');
                        if (clientBadge) {
                            clientBadge.textContent = '0';
                            clientBadge.classList.add('hidden');
                        }
                    }
                    
                    if (type === 'all' || type === 'support') {
                        const supportBadge = document.querySelector('.message-tab[data-target="support-messages"] span');
                        if (supportBadge) {
                            supportBadge.textContent = '0';
                            supportBadge.classList.add('hidden');
                        }
                    }
                }
            })
            .catch(error => console.error('Error marking messages as read:', error));
        }
        
        // Handle message form submissions
        const clientMessageForm = document.getElementById('clientMessageForm');
        const supportMessageForm = document.getElementById('supportMessageForm');
        
        if (clientMessageForm) {
            clientMessageForm.addEventListener('submit', function(e) {
                e.preventDefault();
                sendMessage(this, 'client-messages');
            });
        }
        
        if (supportMessageForm) {
            supportMessageForm.addEventListener('submit', function(e) {
                e.preventDefault();
                sendMessage(this, 'support-messages');
            });
        }
        
        function sendMessage(form, containerId) {
            const formData = new FormData(form);
            
            // Disable form while submitting
            const submitButton = form.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.innerHTML;
            submitButton.disabled = true;
            submitButton.innerHTML = '<svg class="animate-spin h-5 w-5 text-white mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
            
            fetch('{{ route("writer.send.message", $order->id) }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reset form
                    form.reset();
                    
                    // Add new message to the container
                    const container = document.getElementById(containerId).querySelector('.message-container');
                    if (container) {
                        // If container was empty, clear it first
                        if (container.querySelector('.flex.items-center.justify-center.h-full')) {
                            container.innerHTML = '';
                        }
                        
                        // Check if we need to add a date header (for simplicity, always add today)
                        if (!container.querySelector('.flex.justify-center.mb-4:last-child')) {
                            const dateDiv = document.createElement('div');
                            dateDiv.className = 'flex justify-center mb-4';
                            dateDiv.innerHTML = `
                                <div class="bg-gray-200 px-3 py-1 rounded-full">
                                    <span class="text-xs text-gray-600">Today</span>
                                </div>
                            `;
                            container.appendChild(dateDiv);
                        }
                        
                        // Create message element
                        const messageDiv = document.createElement('div');
                        messageDiv.className = 'mb-4 flex flex-row-reverse';
                        
                        // Format time
                        const now = new Date();
                        const formattedTime = now.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' });
                        
                        // Format file size (kb)
                        const formatFileSize = (size) => {
                            return (size / 1024).toFixed(1) + ' KB';
                        };
                        
                        // Build message HTML
                        messageDiv.innerHTML = `
                            <div class="ml-2 flex-shrink-0 h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                                <span class="text-green-600 font-medium text-sm">W</span>
                            </div>
                            <div class="max-w-lg">
                                <div class="bg-green-50 p-3 rounded-lg shadow-sm">
                                    <p class="text-sm text-gray-800 whitespace-pre-line">${data.message.message}</p>
                                    ${data.message.files && data.message.files.length ? `
                                        <div class="mt-2 pt-2 border-t border-gray-200">
                                            ${data.message.files.map(file => `
                                                <div class="flex items-center text-xs mt-1">
                                                    <svg class="h-4 w-4 text-gray-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                                    </svg>
                                                    <form action="{{ route('writer.download') }}" method="POST" class="inline">
                                                        @csrf
                                                        <input type="hidden" name="file_id" value="${file.id}">
                                                        <button type="submit" class="text-blue-600 hover:underline">${file.name}</button>
                                                    </form>
                                                    <span class="ml-1 text-gray-500">(${formatFileSize(file.size)})</span>
                                                </div>
                                            `).join('')}
                                        </div>
                                    ` : ''}
                                </div>
                                <div class="text-right mt-1">
                                    <span class="text-xs text-gray-500">${formattedTime}</span>
                                </div>
                            </div>
                        `;
                        
                        container.appendChild(messageDiv);
                        
                        // Scroll to bottom
                        container.scrollTop = container.scrollHeight;
                    }
                } else {
                    alert(data.message || 'Failed to send message');
                }
            })
            .catch(error => {
                console.error('Error sending message:', error);
                alert('An error occurred while sending your message');
            })
            .finally(() => {
                // Re-enable form
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
            });
        }
        
        // Upload Files Modal
        const uploadFilesBtn = document.getElementById('uploadFilesBtn');
        const uploadModal = document.getElementById('uploadModal');
        const closeUploadModal = document.getElementById('closeUploadModal');
        const closeUploadModalStep2 = document.getElementById('closeUploadModalStep2');
        const cancelUpload = document.getElementById('cancelUpload');
        const gotoVerificationStep = document.getElementById('gotoVerificationStep');
        const uploadStep1 = document.getElementById('uploadStep1');
        const uploadStep2 = document.getElementById('uploadStep2');
        const cancelVerification = document.getElementById('cancelVerification');
        const startUpload = document.getElementById('startUpload');
        const processingModal = document.getElementById('processingModal');
        const successModal = document.getElementById('successModal');
        const closeSuccessModal = document.getElementById('closeSuccessModal');
        const uploadProgressBar = document.getElementById('uploadProgressBar');
        const toaster = document.getElementById('toaster');
        
        let selectedFiles = [];
        
        if (uploadFilesBtn) {
            uploadFilesBtn.addEventListener('click', function() {
                uploadModal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            });
        }
        
        if (closeUploadModal) {
            closeUploadModal.addEventListener('click', function() {
                uploadModal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
                resetUploadModal();
            });
        }
        
        if (closeUploadModalStep2) {
            closeUploadModalStep2.addEventListener('click', function() {
                uploadModal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
                resetUploadModal();
            });
        }
        
        if (cancelUpload) {
            cancelUpload.addEventListener('click', function() {
                uploadModal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
                resetUploadModal();
            });
        }
        
        if (gotoVerificationStep) {
            gotoVerificationStep.addEventListener('click', function() {
                if (selectedFiles.length === 0) {
                    alert('Please select at least one file to upload');
                    return;
                }
                
                // Check if there's at least one "completed" or "preview" file
                const hasCompletedFile = selectedFiles.some(file => 
                    file.description === 'completed' || file.description === 'preview'
                );
                
                if (!hasCompletedFile) {
                    alert('Please include at least one file marked as "completed" or "preview"');
                    return;
                }
                
                uploadStep1.classList.add('hidden');
                uploadStep2.classList.remove('hidden');
            });
        }
        
        if (cancelVerification) {
            cancelVerification.addEventListener('click', function() {
                uploadStep2.classList.add('hidden');
                uploadStep1.classList.remove('hidden');
            });
        }
        
        if (startUpload) {
            startUpload.addEventListener('click', function() {
                // Check all checkboxes are checked
                const allChecked = [
                    document.getElementById('check1'),
                    document.getElementById('check2'),
                    document.getElementById('check3'),
                    document.getElementById('check4'),
                    document.getElementById('check5')
                ].every(checkbox => checkbox.checked);
                
                if (!allChecked) {
                    alert('Please check all items to ensure your submission meets the requirements');
                    return;
                }
                
                // Hide verification step, show processing modal
                uploadModal.classList.add('hidden');
                processingModal.classList.remove('hidden');
                
                // Prepare FormData for upload
                const formData = new FormData();
                formData.append('order_id', '{{ $order->id }}');
                
                // Add files and their descriptions
                selectedFiles.forEach((fileObj, index) => {
                    formData.append(`files[${index}]`, fileObj.file);
                    formData.append(`descriptions[${index}]`, fileObj.description || '');
                });
                
                // Simulate progress (in a real app, you'd use XHR for actual progress)
                let progress = 0;
                const progressInterval = setInterval(() => {
                    progress += 5;
                    if (progress > 90) clearInterval(progressInterval);
                    uploadProgressBar.style.width = `${progress}%`;
                }, 100);
                
                // Send files to server
                fetch('{{ route("writer.upload.files") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    clearInterval(progressInterval);
                    
                    if (data.success) {
                        // Complete progress bar
                        uploadProgressBar.style.width = '100%';
                        
                        // Show success modal after a short delay
                        setTimeout(() => {
                            processingModal.classList.add('hidden');
                            successModal.classList.remove('hidden');
                            
                            // Check if status was changed to DONE
                            if (data.status_changed) {
                                // You could reload the page or update UI elements
                                // to reflect the new order status
                                setTimeout(() => {
                                    window.location.reload();
                                }, 2000);
                            }
                        }, 500);
                    } else {
                        processingModal.classList.add('hidden');
                        alert(data.message || 'Failed to upload files');
                    }
                })
                .catch(error => {
                    clearInterval(progressInterval);
                    processingModal.classList.add('hidden');
                    console.error('Error uploading files:', error);
                    alert('An error occurred while uploading files');
                });
            });
        }
        
        if (closeSuccessModal) {
            closeSuccessModal.addEventListener('click', function() {
                successModal.classList.add('hidden');
                resetUploadModal();
                
                // Show toaster notification
                showToaster();
                
                // Reload file list or update UI as needed
                // window.location.reload();
            });
        }
        
        // Handle file selection via input
        window.handleFileSelect = function(event) {
            const files = event.target.files;
            if (files.length > 0) {
                addFilesToUpload(files);
            }
        }
        
        // Handle drag and drop
        window.handleFileDrop = function(event) {
            event.preventDefault();
            event.stopPropagation();
            
            const dropZone = document.getElementById('dropZone');
            dropZone.classList.remove('border-green-500', 'bg-green-50');
            dropZone.classList.add('border-gray-300');
            
            const files = event.dataTransfer.files;
            if (files.length > 0) {
                addFilesToUpload(files);
            }
        }
        
        window.handleDragOver = function(event) {
            event.preventDefault();
            event.stopPropagation();
            
            const dropZone = document.getElementById('dropZone');
            dropZone.classList.remove('border-gray-300');
            dropZone.classList.add('border-green-500', 'bg-green-50');
        }
        
        window.handleDragLeave = function(event) {
            event.preventDefault();
            event.stopPropagation();
            
            const dropZone = document.getElementById('dropZone');
            dropZone.classList.remove('border-green-500', 'bg-green-50');
            dropZone.classList.add('border-gray-300');
        }
        
        // Add files to the upload list
        function addFilesToUpload(fileList) {
            const uploadedFilesList = document.getElementById('uploadedFiles');
            
            for (let i = 0; i < fileList.length; i++) {
                const file = fileList[i];
                
                // Check file size (max 99MB)
                if (file.size > 99 * 1024 * 1024) {
                    alert(`File "${file.name}" exceeds the maximum size limit of 99MB`);
                    continue;
                }
                
                // Add file to selected files array with empty description
                const fileObj = { file, description: '' };
                selectedFiles.push(fileObj);
                
                // Create file element
                const fileElement = document.createElement('div');
                fileElement.className = 'flex flex-col space-y-2 p-3 bg-gray-50 rounded-lg border border-gray-200';
                
                // Determine icon based on file type
                let iconClass = 'text-gray-400';
                let bgClass = 'bg-gray-100';
                const extension = file.name.split('.').pop().toLowerCase();
                
                if (['jpg', 'jpeg', 'png', 'gif'].includes(extension)) {
                    iconClass = 'text-purple-500';
                    bgClass = 'bg-purple-100';
                } else if (['doc', 'docx'].includes(extension)) {
                    iconClass = 'text-blue-500';
                    bgClass = 'bg-blue-100';
                } else if (['xls', 'xlsx'].includes(extension)) {
                    iconClass = 'text-green-500';
                    bgClass = 'bg-green-100';
                } else if (['pdf'].includes(extension)) {
                    iconClass = 'text-red-500';
                    bgClass = 'bg-red-100';
                } else if (['zip', 'rar'].includes(extension)) {
                    iconClass = 'text-yellow-500';
                    bgClass = 'bg-yellow-100';
                } else if (['mp4', 'mov', 'avi', 'wmv'].includes(extension)) {
                    iconClass = 'text-indigo-500';
                    bgClass = 'bg-indigo-100';
                }
                
                // Calculate file size in KB
                const fileSize = (file.size / 1024).toFixed(1) + ' KB';
                const fileIndex = selectedFiles.length - 1;
                
                fileElement.innerHTML = `
                    <div class="flex justify-between items-center">
                        <div class="flex items-center">
                            <div class="${bgClass} p-2 rounded-md">
                                <svg class="h-5 w-5 ${iconClass}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-700">${file.name}</p>
                                <p class="text-xs text-gray-500">${fileSize}</p>
                            </div>
                        </div>
                        <button type="button" class="text-gray-400 hover:text-gray-600" onclick="removeFile(${fileIndex})">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="flex items-center">
                        <select class="file-description block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm" onchange="updateFileDescription(${fileIndex}, this.value)">
                            <option value="">Select file type</option>
                            <option value="completed">Completed Order</option>
                            <option value="preview">Preview</option>
                            <option value="sources">Sources</option>
                            <option value="file with corrections">File with corrections</option>
                        </select>
                    </div>
                `;
                
                uploadedFilesList.appendChild(fileElement);
            }
        }
        
        // Update file description when dropdown changes
        window.updateFileDescription = function(index, value) {
            if (selectedFiles[index]) {
                selectedFiles[index].description = value;
            }
        };
        
        // Remove file from the upload list
        window.removeFile = function(index) {
            // Remove from selected files array
            selectedFiles.splice(index, 1);
            
            // Rebuild the file list UI
            const uploadedFilesList = document.getElementById('uploadedFiles');
            uploadedFilesList.innerHTML = '';
            
            selectedFiles.forEach((fileObj, idx) => {
                const file = fileObj.file;
                const fileElement = document.createElement('div');
                fileElement.className = 'flex flex-col space-y-2 p-3 bg-gray-50 rounded-lg border border-gray-200';
                
                // Determine icon based on file type
                let iconClass = 'text-gray-400';
                let bgClass = 'bg-gray-100';
                const extension = file.name.split('.').pop().toLowerCase();
                
                if (['jpg', 'jpeg', 'png', 'gif'].includes(extension)) {
                    iconClass = 'text-purple-500';
                    bgClass = 'bg-purple-100';
                } else if (['doc', 'docx'].includes(extension)) {
                    iconClass = 'text-blue-500';
                    bgClass = 'bg-blue-100';
                } else if (['xls', 'xlsx'].includes(extension)) {
                    iconClass = 'text-green-500';
                    bgClass = 'bg-green-100';
                } else if (['pdf'].includes(extension)) {
                    iconClass = 'text-red-500';
                    bgClass = 'bg-red-100';
                } else if (['zip', 'rar'].includes(extension)) {
                    iconClass = 'text-yellow-500';
                    bgClass = 'bg-yellow-100';
                } else if (['mp4', 'mov', 'avi', 'wmv'].includes(extension)) {
                    iconClass = 'text-indigo-500';
                    bgClass = 'bg-indigo-100';
                }
                
                // Calculate file size in KB
                const fileSize = (file.size / 1024).toFixed(1) + ' KB';
                
                fileElement.innerHTML = `
                    <div class="flex justify-between items-center">
                        <div class="flex items-center">
                            <div class="${bgClass} p-2 rounded-md">
                                <svg class="h-5 w-5 ${iconClass}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-700">${file.name}</p>
                                <p class="text-xs text-gray-500">${fileSize}</p>
                            </div>
                        </div>
                        <button type="button" class="text-gray-400 hover:text-gray-600" onclick="removeFile(${idx})">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="flex items-center">
                        <select class="file-description block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm" onchange="updateFileDescription(${idx}, this.value)">
                            <option value="">Select file type</option>
                            <option value="completed" ${fileObj.description === 'completed' ? 'selected' : ''}>Completed Order</option>
                            <option value="preview" ${fileObj.description === 'preview' ? 'selected' : ''}>Preview</option>
                            <option value="sources" ${fileObj.description === 'sources' ? 'selected' : ''}>Sources</option>
                            <option value="file with corrections" ${fileObj.description === 'file with corrections' ? 'selected' : ''}>File with corrections</option>
                        </select>
                    </div>
                `;
                
                uploadedFilesList.appendChild(fileElement);
            });
        };
        
        // Reset upload modal
        function resetUploadModal() {
            // Clear selected files
            selectedFiles = [];
            
            // Reset file list
            const uploadedFilesList = document.getElementById('uploadedFiles');
            if (uploadedFilesList) {
                uploadedFilesList.innerHTML = '';
            }
            
            // Reset file input
            const fileInput = document.getElementById('fileInput');
            if (fileInput) {
                fileInput.value = '';
            }
            
            // Reset checkboxes
            const checkboxes = document.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            
            // Reset progress bar
            if (uploadProgressBar) {
                uploadProgressBar.style.width = '0%';
            }
            
            // Reset view to step 1
            if (uploadStep1 && uploadStep2) {
                uploadStep1.classList.remove('hidden');
                uploadStep2.classList.add('hidden');
            }
            
            // Hide all modals
            if (uploadModal) uploadModal.classList.add('hidden');
            if (processingModal) processingModal.classList.add('hidden');
            if (successModal) successModal.classList.add('hidden');
            
            // Remove body overflow hidden
            document.body.classList.remove('overflow-hidden');
        }
        
        // Show toaster notification
        function showToaster() {
            toaster.classList.remove('translate-x-full');
            toaster.classList.add('translate-x-0');
            
            // Auto-hide after 5 seconds
            setTimeout(hideToaster, 5000);
        }
        
        // Hide toaster notification
        window.hideToaster = function() {
            toaster.classList.remove('translate-x-0');
            toaster.classList.add('translate-x-full');
        };
    });
</script>
@endsection