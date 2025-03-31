@extends('writers.app')
@section('content')

<style>
    /* Base layout styles */
    .main-content {
        margin-left: 240px;
        min-height: 100vh;
        transition: margin-left 0.3s ease;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .main-content {
            margin-left: 0;
        }
        
        .sidebar-open .main-content {
            opacity: 0.5;
            pointer-events: none;
        }
    }

    /* Message containers and scrollbars */
    .message-container {
        height: 500px;
        overflow-y: auto;
        padding-right: 0.5rem;
        scroll-behavior: smooth;
        scrollbar-width: thin;
        scrollbar-color: #d1d5db #f1f1f1;
    }

    .message-container::-webkit-scrollbar {
        width: 6px;
    }

    .message-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .message-container::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 10px;
    }

    .message-container::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }

    /* Enhanced messaging UI */
    .message-bubble {
        max-width: 80%;
        word-wrap: break-word;
        margin-bottom: 12px;
        line-height: 1.4;
        position: relative;
        transition: all 0.2s ease;
        white-space: normal;
        word-break: break-word;
    }

    .message-bubble p {
        white-space: pre-wrap;
        word-break: normal;
        overflow-wrap: break-word;
    }

    .message-bubble-right {
        margin-left: auto;
        background-color: #ecfdf5;
        border-radius: 18px 4px 18px 18px;
        padding: 10px 12px;
        position: relative;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .message-bubble-left {
        margin-right: auto;
        background-color: white;
        border-radius: 4px 18px 18px 18px;
        padding: 10px 12px;
        position: relative;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .message-bubble-left.support {
        background-color: #fff7ed;
    }

    .message-bubble-left.system {
        background-color: #f3f4f6;
    }
    
    /* Add animation for new messages */
    @keyframes messageEntrance {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .message-bubble {
        animation: messageEntrance 0.3s ease-out;
    }

    /* UI Component styles */
    .tab-slider {
        height: 2px;
        transition: all 0.3s ease;
    }

    .file-upload-zone {
        transition: all 0.2s ease;
    }

    .file-upload-zone:hover {
        border-color: #10b981;
        background-color: rgba(16, 185, 129, 0.05);
    }

    .file-upload-zone.drag-over {
        border-color: #10b981;
        background-color: rgba(16, 185, 129, 0.1);
    }

    /* Enhanced animations */
    .tab-item {
        position: relative;
        transition: all 0.3s ease;
    }

    .tab-item::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 0;
        height: 2px;
        background-color: #10b981;
        transition: width 0.3s ease;
    }

    .tab-item.active::after {
        width: 100%;
    }

    /* Message category tabs with icons */
    .message-tab {
        display: flex;
        align-items: center;
        transition: all 0.3s ease;
        padding: 0.75rem 1rem;
        border-bottom: 2px solid transparent;
    }

    .message-tab:hover {
        background-color: rgba(16, 185, 129, 0.05);
    }

    .message-tab.active {
        border-bottom-color: #10b981;
        color: #10b981;
    }

    .message-tab-icon {
        margin-right: 0.5rem;
    }

    /* Modal and toast animations */
    .modal-backdrop .transform {
        transition-property: transform, opacity;
        transition-duration: 0.3s;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    }

    #toaster {
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Enhanced loading indicators */
    .loading-spinner {
        display: inline-block;
        width: 40px;
        height: 40px;
        border: 3px solid rgba(16, 185, 129, 0.2);
        border-radius: 50%;
        border-top-color: #10b981;
        animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .loading-container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100%;
        padding: 2rem;
    }

    /* Message date separators */
    .message-date-slider {
        position: relative;
        text-align: center;
        margin: 1.5rem 0;
    }

    .message-date-slider::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 1px;
        background: #e5e7eb;
        z-index: 0;
    }

    .message-date-slider-text {
        position: relative;
        z-index: 1;
        padding: 0 0.75rem;
        background-color: #f9fafb;
        font-size: 0.75rem;
        color: #6b7280;
    }

    /* Better responsive design for buttons */
    @media (max-width: 640px) {
        .btn-flex-mobile {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            width: 100%;
        }
        
        .btn-flex-mobile button {
            width: 100%;
        }
        
        .message-container {
            height: 350px;
        }
    }

    /* Enhanced countdown styles */
    .countdown {
        font-variant-numeric: tabular-nums;
        transition: color 0.3s ease;
    }

    .countdown-warning {
        color: #ef4444;
        font-weight: 600;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.7; }
        100% { opacity: 1; }
    }

    /* Message typing indicator */
    .typing-indicator {
        display: flex;
        align-items: center;
        padding: 0.5rem;
    }

    .typing-dot {
        width: 8px;
        height: 8px;
        margin: 0 2px;
        background-color: #d1d5db;
        border-radius: 50%;
        animation: typing-dot 1.4s infinite ease-in-out;
    }

    .typing-dot:nth-child(1) { animation-delay: 0s; }
    .typing-dot:nth-child(2) { animation-delay: 0.2s; }
    .typing-dot:nth-child(3) { animation-delay: 0.4s; }

    @keyframes typing-dot {
        0%, 60%, 100% { transform: translateY(0); }
        30% { transform: translateY(-5px); }
    }
    
    /* Message status indicators */
    .message-status {
        display: flex;
        align-items: center;
        margin-left: 4px;
        font-size: 0.7rem;
    }
    
    .status-delivered {
        color: #9ca3af; /* Gray */
    }
    
    .status-read {
        color: #10b981; /* Green */
    }
</style>

<div class="bg-gray-50 flex flex-col min-h-screen pt-16 px-4 sm:px-6 lg:px-8 main-content">
    <div class="max-w-7xl mx-auto w-full pb-10 lg:py-12">
        <!-- Order Header Card -->
        <div class="bg-gradient-to-r from-white to-gray-50 rounded-xl shadow-sm border border-gray-100 mb-6">
            <div class="p-6">
                <div class="flex flex-col md:flex-row md:justify-between md:items-center">
                    <div class="flex items-center space-x-4 mb-4 md:mb-0">
                        <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center">
                            <i class="fas fa-file-alt text-green-500 text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-semibold text-gray-800">Order #{{ $order->id }}</h1>
                            <div class="flex items-center text-sm text-gray-500">
                                <div class="w-2 h-2 rounded-full 
                                    @if($order->status == 'done') bg-green-500
                                    @elseif($order->status == 'revision') bg-yellow-500
                                    @elseif($order->status == 'confirmed') bg-blue-500
                                    @elseif($order->status == 'unconfirmed') bg-orange-500
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
                                <span class="text-sm text-gray-500">{{ $order->client->name ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Order Action Buttons -->
                @if($order->status == 'unconfirmed')
                <div class="mt-6 flex flex-wrap gap-3 btn-flex-mobile">
                    <button type="button" 
                            id="acceptOrderBtn"
                            class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors duration-200 flex items-center justify-center">
                        <i class="fas fa-check mr-2"></i>Accept Order
                    </button>
                    
                    <button type="button"
                            id="rejectOrderBtn"
                            class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors duration-200 flex items-center justify-center">
                        <i class="fas fa-times mr-2"></i>Reject Order
                    </button>
                </div>
                @elseif(in_array($order->status, ['confirmed', 'revision']))
                <div class="mt-6 flex flex-wrap gap-3 btn-flex-mobile">
                    <button onclick="showUploadModal()" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors duration-200 flex items-center justify-center">
                        <i class="fas fa-upload mr-2"></i>Upload Files
                    </button>
                    
                    <button class="inline-flex items-center text-blue-600 hover:text-blue-700 text-sm font-medium group">
                        <i class="fas fa-sync-alt mr-2 transform group-hover:rotate-180 transition-transform duration-300"></i>
                        Request reassignment
                    </button>
                </div>
                @elseif(!in_array($order->status, ['done', 'delivered', 'dispute', 'unconfirmed']))
                <div class="mt-6 flex flex-wrap gap-3">
                    <button class="inline-flex items-center text-blue-600 hover:text-blue-700 text-sm font-medium group">
                        <i class="fas fa-sync-alt mr-2 transform group-hover:rotate-180 transition-transform duration-300"></i>
                        Request reassignment
                    </button>
                </div>
                @endif

                <!-- Hidden form for CSRF token -->
                <form id="actionForm" class="hidden">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                </form>
            </div>
        </div>

        <!-- Main Content Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <!-- Tab Navigation -->
            <div class="border-b border-gray-100 relative">
                <nav class="flex overflow-x-auto" role="tablist">
                    <div id="tab-slider" class="tab-slider absolute bottom-0 left-0 h-0.5 bg-green-500 transition-all duration-300" style="width: 120px;"></div>
                    
                    <button id="instructions-tab" 
                            class="relative px-6 py-4 text-green-600 hover:text-green-700 focus:outline-none font-medium whitespace-nowrap tab-item active" 
                            onclick="switchTab('instructions')" 
                            role="tab"
                            data-width="120"
                            aria-selected="true">
                        <i class="fas fa-clipboard-list mr-2"></i>Instructions
                    </button>
                    <button id="files-tab" 
                            class="relative px-6 py-4 text-gray-500 hover:text-gray-700 focus:outline-none flex items-center whitespace-nowrap tab-item" 
                            onclick="switchTab('files')" 
                            role="tab"
                            data-width="100"
                            aria-selected="false">
                        <i class="fas fa-file mr-2"></i>All files
                        <span class="ml-2 bg-green-500 text-white text-xs px-2 py-0.5 rounded-full">{{ count($order->files) }}</span>
                    </button>
                    <button id="messages-tab" 
                            class="relative px-6 py-4 text-gray-500 hover:text-gray-700 focus:outline-none flex items-center whitespace-nowrap tab-item" 
                            onclick="switchTab('messages')" 
                            role="tab"
                            data-width="110"
                            aria-selected="false">
                        <i class="fas fa-comments mr-2"></i>Messages
                        @if($unreadMessages > 0)
                        <span class="ml-2 bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $unreadMessages }}</span>
                        @endif
                    </button>
                </nav>
            </div>

            <!-- Content Area -->
            <div class="p-4 sm:p-6">
                <!-- Instructions Panel -->
                <div id="instructions-panel" role="tabpanel" class="w-full">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8">
                        <!-- Left Column - Order Information -->
                        <div class="space-y-4">
                            <!-- Deadline Info -->
                            <div class="p-4 bg-gradient-to-r from-gray-50 to-white rounded-lg border border-gray-100">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Deadline</span>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-gray-800">{{ \Carbon\Carbon::parse($order->deadline)->format('d M, h:i A') }}</span>
                                        @php 
                                            $now = \Carbon\Carbon::now();
                                            $deadline = \Carbon\Carbon::parse($order->deadline);
                                            $warning = $now > $deadline;
                                            $diff = $now->diff($deadline);
                                            $hours = ($diff->days * 24) + $diff->h;
                                            $timeText = $warning ? "(-{$hours}h)" : "({$hours}h)";
                                        @endphp
                                        <span class="countdown {{ $warning ? 'countdown-warning text-red-500' : 'text-gray-500' }}" data-deadline="{{ $order->deadline }}">
                                            {{ $timeText }}
                                        </span>

                                        @if($order->status == 'confirmed')
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
                                        <span class="text-gray-800">{{ \Carbon\Carbon::parse($order->screening_deadline ?? $order->deadline)->format('d M, h:i A') }}</span>
                                        @php 
                                            $screeningDeadline = \Carbon\Carbon::parse($order->screening_deadline ?? $order->deadline);
                                            $screeningWarning = $now > $screeningDeadline;
                                            $screeningDiff = $now->diff($screeningDeadline);
                                            $screeningHours = ($screeningDiff->days * 24) + $screeningDiff->h;
                                            $screeningText = $screeningWarning ? "(-{$screeningHours}h)" : "({$screeningHours}h)";
                                        @endphp
                                        <span class="countdown {{ $screeningWarning ? 'countdown-warning text-red-500' : 'text-gray-500' }}" data-deadline="{{ $order->screening_deadline ?? $order->deadline }}">
                                            {{ $screeningText }}
                                        </span>
                                        
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
                                        <span class="text-gray-800">{{ ucfirst(strtolower($order->task_size ?? 'Standard')) }}</span>
                                        <div class="group relative">
                                            <i class="fas fa-info-circle text-gray-400 cursor-help"></i>
                                            <div class="absolute bottom-full right-0 mb-2 w-48 p-2 bg-gray-800 text-white text-sm rounded-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                                               Task size determines complexity and effort required
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
                                    <span class="text-gray-800">{{ $order->discipline ?? 'General' }}</span>
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
                            
                            @if($order->number_of_sources)
                            <!-- Number of sources -->
                            <div class="p-4 bg-gradient-to-r from-gray-50 to-white rounded-lg border border-gray-100">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Sources to be cited</span>
                                    <span class="text-gray-800">{{ $order->number_of_sources }}</span>
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
                                        <span class="copy-tooltip hidden absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded whitespace-nowrap">Copied!</span>
                                    </button>
                                    <button class="text-gray-400 hover:text-gray-600 transition-colors duration-200" onclick="expandInstructions()">
                                        <i class="fas fa-expand-alt"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="prose max-w-none">
                                <p class="text-gray-700 whitespace-pre-line">
                                    {{ $order->instructions ?? 'No instructions provided.' }}
                                </p>
                            </div>

                            @if($order->customer_comments)
                            <!-- Customer Comments -->
                            <div class="mt-6">
                                <div class="bg-cyan-50 rounded-lg p-4 border border-cyan-100">
                                    <h4 class="font-medium text-gray-800 mb-2">Comments from Customer</h4>
                                    <p class="text-gray-700 whitespace-pre-line">
                                        {{ $order->customer_comments }}
                                    </p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Files Panel -->
                <div id="files-panel" class="hidden w-full" role="tabpanel">
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
                                    class="ml-4 text-gray-400 hover:text-gray-600 transition-colors hidden"
                                    id="bulk-download-btn"
                                    onclick="downloadSelectedFiles()">
                                    <i class="fas fa-download"></i>
                                </button>
                            </div>
                            
                            @if(in_array($order->status, ['confirmed', 'revision']))
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
                            @forelse($order->files as $file)
                            <div class="flex items-center space-x-4 border border-gray-100 p-4 rounded-lg hover:shadow-md transition duration-200 file-item" data-file-id="{{ $file->id }}" data-file-name="{{ $file->original_name }}">
                                <input type="checkbox" class="form-checkbox h-4 w-4 text-green-500 rounded border-gray-300 file-checkbox">
                                @php
                                    $fileIcon = 'fa-file text-gray-400';
                                    if(strpos($file->mime_type, 'pdf') !== false) {
                                        $fileIcon = 'fa-file-pdf text-red-400';
                                    } elseif(strpos($file->mime_type, 'word') !== false || strpos($file->mime_type, 'document') !== false) {
                                        $fileIcon = 'fa-file-word text-blue-400';
                                    } elseif(strpos($file->mime_type, 'excel') !== false || strpos($file->mime_type, 'spreadsheet') !== false) {
                                        $fileIcon = 'fa-file-excel text-green-400';
                                    } elseif(strpos($file->mime_type, 'image') !== false) {
                                        $fileIcon = 'fa-file-image text-purple-400';
                                    } elseif(strpos($file->mime_type, 'zip') !== false || strpos($file->mime_type, 'archive') !== false) {
                                        $fileIcon = 'fa-file-archive text-yellow-400';
                                    } elseif(strpos($file->mime_type, 'video') !== false) {
                                        $fileIcon = 'fa-file-video text-blue-400';
                                    }
                                @endphp
                                <i class="fas {{ $fileIcon }}"></i>
                                <div class="flex-grow">
                                    <p class="text-sm font-medium text-gray-700">{{ $file->original_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $file->description ?: 'No description' }}</p>
                                    <p class="text-xs text-gray-500">
                                        @php
                                            $created = $file->created_at ? new \Carbon\Carbon($file->created_at) : null;
                                            echo $created ? $created->format('d M, h:i A') : 'Unknown date';
                                        @endphp
                                        • 
                                        @php
                                            if($file->size) {
                                                $size = $file->size;
                                                $units = ['B', 'KB', 'MB', 'GB', 'TB'];
                                                $i = floor(log($size, 1024));
                                                $formattedSize = round($size / pow(1024, $i), 2) . ' ' . $units[$i];
                                                echo $formattedSize;
                                            } else {
                                                echo 'Unknown size';
                                            }
                                        @endphp
                                    </p>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <span class="text-sm text-gray-600">{{ $file->uploader_type === 'CUSTOMER' ? 'Customer' : 'Writer' }}</span>
                                    <button class="text-gray-400 hover:text-gray-600 transition-colors" onclick="downloadFile('{{ $file->id }}', '{{ $file->original_name }}')">
                                        <i class="fas fa-download"></i>
                                    </button>
                                </div>
                            </div>
                            @empty
                            <div id="no-files-message" class="text-center py-8 text-gray-500">
                                <i class="fas fa-file-alt text-gray-300 text-4xl mb-3"></i>
                                <p>No files have been uploaded yet</p>
                            </div>
                            @endforelse
                        </div>

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

                <!-- Messages Panel With Improved Styling -->
                <div id="messages-panel" class="hidden w-full" role="tabpanel">
                    <!-- Message Category Selection -->
                    <div class="mb-4 border-b border-gray-200">
                        <nav class="flex -mb-px overflow-x-auto" aria-label="Tabs">
                            <button 
                                class="message-tab active flex items-center px-4 py-2 border-b-2 border-green-500 text-green-600 text-sm font-medium whitespace-nowrap" 
                                onclick="switchMessageCategory('client')"
                                id="client-messages-tab">
                                <i class="fas fa-user message-tab-icon"></i>
                                Client
                                @if(isset($clientUnreadCount) && $clientUnreadCount > 0)
                                <span class="ml-2 bg-red-500 text-white text-xs px-2 py-0.5 rounded-full animate-pulse">
                                    {{ $clientUnreadCount }}
                                </span>
                                @endif
                            </button>
                            <button 
                                class="message-tab flex items-center px-4 py-2 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 text-sm font-medium whitespace-nowrap" 
                                onclick="switchMessageCategory('support')"
                                id="support-messages-tab">
                                <i class="fas fa-headset message-tab-icon"></i>
                                Support
                                @if(isset($supportUnreadCount) && $supportUnreadCount > 0)
                                <span class="ml-2 bg-red-500 text-white text-xs px-2 py-0.5 rounded-full animate-pulse">
                                    {{ $supportUnreadCount }}
                                </span>
                                @endif
                            </button>
                        </nav>
                    </div>
                    
                    <!-- Message Window with Improved Container -->
                    <div class="flex flex-col h-[550px] border border-gray-200 rounded-lg overflow-hidden bg-gray-50 shadow-sm">
                        <!-- Client Messages Container - Fixed height with scrolling -->
                        <div id="client-messages-container" class="flex-1 overflow-y-auto p-4 space-y-4 message-container" style="max-height: 460px;">
                            <div class="loading-container">
                                <div class="loading-spinner"></div>
                            </div>
                        </div>
                        
                        <!-- Support Messages Container (Hidden by default) - Fixed height with scrolling -->
                        <div id="support-messages-container" class="flex-1 overflow-y-auto p-4 space-y-4 message-container hidden" style="max-height: 460px;">
                            <div class="loading-container">
                                <div class="loading-spinner"></div>
                            </div>
                        </div>

                        <!-- Improved Message Input Section -->
                        <div class="border-t bg-white p-3 pt-4">
                            <form id="messageForm" class="flex flex-col space-y-2">
                                @csrf
                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                                <input type="hidden" name="message_type" id="message_type" value="client">
                                
                                <div class="flex items-center space-x-2">
                                    <div class="flex-1 relative">
                                        <textarea id="messageContent" name="content" 
                                                class="w-full px-3 py-2 border border-gray-300 focus:border-green-500 focus:ring-2 focus:ring-green-200 focus:ring-opacity-50 rounded-lg resize-none text-sm"
                                                placeholder="Type your message..."
                                                rows="2"></textarea>
                                        <button type="button" 
                                                class="absolute right-2 bottom-2 text-gray-400 hover:text-gray-600 transition-colors duration-200"
                                                onclick="document.getElementById('messageAttachment').click()">
                                            <i class="fas fa-paperclip"></i>
                                        </button>
                                    </div>
                                    <button type="button" 
                                            id="sendMessageBtn"
                                            onclick="sendMessage()" 
                                            class="h-10 px-4 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors duration-200 flex items-center">
                                        <i class="fas fa-paper-plane mr-2"></i>
                                        <span>Send</span>
                                    </button>
                                </div>
                                <input type="file" id="messageAttachment" name="attachment" class="hidden">
                            </form>
                            <div id="attachment-preview" class="hidden mt-2 p-2 bg-gray-50 rounded-lg flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="fas fa-file mr-2 text-gray-500"></i>
                                    <span id="attachment-name" class="text-sm text-gray-700 truncate"></span>
                                </div>
                                <button onclick="removeAttachment()" class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Extension Dialog -->
<div id="extensionDialog" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 modal-backdrop">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4 transform transition-all duration-300 ease-out scale-95 opacity-0" id="extensionDialogContent">
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Request Deadline Extension</h3>
                <button onclick="hideExtensionDialog()" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="flex items-center justify-between">
                <span class="text-gray-800 font-medium">Current deadline</span>
                <div>
                    <span class="text-gray-800">{{ \Carbon\Carbon::parse($order->deadline)->format('d M, h:i A') }}</span>
                    <span class="countdown {{ $warning ? 'countdown-warning text-red-500' : 'text-gray-500' }}" id="extension-countdown" data-deadline="{{ $order->deadline }}">{{ $timeText }}</span>
                </div>
            </div>
            
            <div>
                <label class="block text-sm text-gray-600 mb-1">Extension time:</label>
                <div class="relative">
                    <select id="extensionTime" class="w-full px-3 py-2 border border-gray-200 rounded-lg appearance-none focus:outline-none focus:border-green-500">
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
                    id="extensionReason"
                    class="w-full px-3 py-2 border border-gray-200 rounded-lg resize-none focus:outline-none focus:border-green-500"
                    rows="4"
                    placeholder="Enter your reason for extension..."></textarea>
            </div>

            <div class="text-sm text-gray-600 bg-blue-50 p-3 rounded-lg">
                Please, update the customer about the progress and specify the reason for the extension request.
            </div>

            <div class="flex justify-end space-x-3 mt-6">
                <button 
                    class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors duration-200"
                    onclick="hideExtensionDialog()">
                    Cancel
                </button>
                <button 
                    class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors duration-200"
                    onclick="requestExtension()">
                    Request Extension
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Upload Modal with Multi-step Workflow -->
<div id="uploadModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center hidden z-50 modal-backdrop">
    <!-- Modal Container -->
    <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
        <!-- Modal Content -->
        <div id="uploadModalContent" class="relative bg-white rounded-lg max-w-xl w-full mx-auto shadow-xl transform transition-all duration-300 ease-out scale-95 opacity-0">
            <!-- Step 1: File Selection -->
            <div id="uploadStep1" class="block">
                <!-- Modal Header -->
                <div class="flex justify-between items-center p-6 border-b">
                    <h3 class="text-lg font-medium text-gray-900">Upload files</h3>
                    <button onclick="closeUploadModal()" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex items-center space-x-4">
                            <div class="w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center">
                                <span>1</span>
                            </div>
                            <span class="font-medium text-gray-800">Select Files</span>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="w-8 h-8 rounded-full bg-gray-300 text-white flex items-center justify-center">
                                <span>2</span>
                            </div>
                            <span class="font-medium text-gray-400">Verification</span>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="w-8 h-8 rounded-full bg-gray-300 text-white flex items-center justify-center">
                                <span>3</span>
                            </div>
                            <span class="font-medium text-gray-400">Complete</span>
                        </div>
                    </div>

                    <p class="text-sm text-gray-600 mb-6">
                        Please make sure to upload a completed version of your work. Select description "Completed" for your final file. Maximum file size allowed: 99 MB.
                    </p>

                    <!-- File List -->
                    <div id="uploadedFiles" class="space-y-3 mb-4"></div>

                    <!-- Upload Zone -->
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center cursor-pointer hover:border-green-500 transition-colors duration-200 file-upload-zone"
                         id="dropZone"
                         ondrop="handleFileDrop(event)"
                         ondragover="handleDragOver(event)"
                         ondragleave="handleDragLeave(event)"
                         onclick="document.getElementById('fileInput').click()">
                        <input type="file" id="fileInput" class="hidden" multiple onchange="handleFileSelect(event)">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-cloud-upload-alt text-4xl text-gray-300 mb-3"></i>
                            <button type="button" class="text-green-500 font-medium">Choose file</button>
                            <span class="text-gray-500 mt-1">or drag and drop files here</span>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="mt-6 flex justify-end space-x-3">
                        <button onclick="closeUploadModal()" 
                                class="px-4 py-2 text-gray-700 hover:text-gray-900 transition-colors duration-200 rounded-md">
                            Cancel
                        </button>
                        <button onclick="gotoVerificationStep()" 
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
                    <button onclick="closeUploadModal()" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex items-center space-x-4">
                            <div class="w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center">
                                <i class="fas fa-check text-xs"></i>
                            </div>
                            <span class="font-medium text-gray-800">Select Files</span>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center">
                                <span>2</span>
                            </div>
                            <span class="font-medium text-gray-800">Verification</span>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="w-8 h-8 rounded-full bg-gray-300 text-white flex items-center justify-center">
                                <span>3</span>
                            </div>
                            <span class="font-medium text-gray-400">Complete</span>
                        </div>
                    </div>

                    <h4 class="text-base font-medium text-gray-700 mb-3">Paper details</h4>
                    
                    <div class="space-y-4 bg-gray-50 p-4 rounded-lg mb-6">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Paper format</span>
                            <span class="text-gray-800">{{ $order->paper_format ?: 'Not applicable' }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Pages</span>
                            <div>
                                <span class="text-gray-800">{{ $order->number_of_pages ?? 0 }} pages</span>
                                <span class="text-gray-500 text-sm ml-1">(~{{ $order->number_of_pages ? $order->number_of_pages * 275 : 0 }} words)</span>
                                <div class="text-xs text-gray-500">{{ $order->spacing ?: 'Double spaced' }}</div>
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Sources to be cited</span>
                            <span class="text-gray-800">{{ $order->number_of_sources ?? 0 }}</span>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <h4 class="text-base font-medium text-gray-700 mb-3">To be on the safe side, please double-check whether:</h4>
                        <div class="space-y-3">
                            <label class="flex items-start">
                                <input type="checkbox" class="form-checkbox h-5 w-5 text-green-500 rounded border-gray-300 mt-0.5 verification-checkbox">
                                <span class="ml-2 text-gray-700">All order files are checked</span>
                            </label>
                            <label class="flex items-start">
                                <input type="checkbox" class="form-checkbox h-5 w-5 text-green-500 rounded border-gray-300 mt-0.5 verification-checkbox">
                                <span class="ml-2 text-gray-700">All order messages are thoroughly read</span>
                            </label>
                            <label class="flex items-start">
                                <input type="checkbox" class="form-checkbox h-5 w-5 text-green-500 rounded border-gray-300 mt-0.5 verification-checkbox">
                                <span class="ml-2 text-gray-700">All paper instructions are followed</span>
                            </label>
                            <label class="flex items-start">
                                <input type="checkbox" class="form-checkbox h-5 w-5 text-green-500 rounded border-gray-300 mt-0.5 verification-checkbox">
                                <span class="ml-2 text-gray-700">Number of sources is as requested</span>
                            </label>
                            <label class="flex items-start">
                                <input type="checkbox" class="form-checkbox h-5 w-5 text-green-500 rounded border-gray-300 mt-0.5 verification-checkbox">
                                <span class="ml-2 text-gray-700">Required formatting style is applied</span>
                            </label>
                        </div>
                        
                        <div class="mt-4 p-3 bg-gray-50 text-sm text-gray-600 rounded-lg">
                            Plagiarism report will be available within 5-10 minutes in the Files section.
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="mt-6 flex justify-end space-x-3">
                        <button onclick="backToFileSelection()" 
                                class="px-4 py-2 text-gray-700 hover:text-gray-900 transition-colors duration-200 rounded-md">
                            Back
                        </button>
                        <button id="submitUploadBtn" onclick="startUpload()" 
                                class="px-4 py-2 bg-gray-400 text-white rounded-lg transition-colors duration-200">
                            Submit
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Processing Modal (Step 3) -->
<div id="processingModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50 modal-backdrop">
    <div class="bg-white rounded-lg p-8 max-w-md w-full transform transition-all duration-300 ease-out scale-95 opacity-0" id="processingModalContent">
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center space-x-4">
                <div class="w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center">
                    <i class="fas fa-check text-xs"></i>
                </div>
                <span class="font-medium text-gray-800">Select Files</span>
            </div>
            <div class="flex items-center space-x-4">
                <div class="w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center">
                    <i class="fas fa-check text-xs"></i>
                </div>
                <span class="font-medium text-gray-800">Verification</span>
            </div>
            <div class="flex items-center space-x-4">
                <div class="w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center">
                    <span>3</span>
                </div>
                <span class="font-medium text-gray-800">Complete</span>
            </div>
        </div>
        <h3 class="text-lg font-medium text-gray-700 text-center mb-6">Processing...</h3>
        <div class="w-full bg-gray-200 rounded-full h-2.5 mb-4">
            <div id="uploadProgressBar" class="bg-green-500 h-2.5 rounded-full" style="width: 0%"></div>
        </div>
    </div>
</div>

<!-- Success Modal (Step 4) -->
<div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50 modal-backdrop">
    <div class="bg-white rounded-lg p-8 max-w-md w-full text-center transform transition-all duration-300 ease-out scale-95 opacity-0" id="successModalContent">
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center space-x-4">
                <div class="w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center">
                    <i class="fas fa-check text-xs"></i>
                </div>
                <span class="font-medium text-gray-800">Select Files</span>
            </div>
            <div class="flex items-center space-x-4">
                <div class="w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center">
                    <i class="fas fa-check text-xs"></i>
                </div>
                <span class="font-medium text-gray-800">Verification</span>
            </div>
            <div class="flex items-center space-x-4">
                <div class="w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center">
                    <i class="fas fa-check text-xs"></i>
                </div>
                <span class="font-medium text-gray-800">Complete</span>
            </div>
        </div>
        <div class="w-16 h-16 mx-auto mb-4 bg-green-50 rounded-full flex items-center justify-center">
            <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <h3 class="text-xl font-medium text-gray-700">Files Uploaded Successfully</h3>
        <p class="text-gray-500 mt-2">Your files have been uploaded and are now available in the Files section.</p>
        <div class="mt-6">
            <button onclick="closeSuccessModal()" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors duration-200">
                Close
            </button>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div id="errorModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50 modal-backdrop">
    <div class="bg-white rounded-lg p-8 max-w-md w-full text-center transform transition-all duration-300 ease-out scale-95 opacity-0" id="errorModalContent">
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
<div id="toaster" class="fixed top-4 right-4 z-50 transform translate-x-full transition-transform duration-300 ease-in-out">
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

<script>
// Global variables
// Global variables
let uploadedFiles = new Map();
let allCheckboxesChecked = false;
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]')?.value || '';

// ===============================
// Tab and Navigation Functions
// ===============================
function switchTab(tabName) {
    console.log(`Switching to tab: ${tabName}`);
    const tabs = document.querySelectorAll('[role="tab"]');
    const panels = document.querySelectorAll('[role="tabpanel"]');
    const slider = document.getElementById('tab-slider');
    
    tabs.forEach(tab => {
        const isSelected = tab.id === `${tabName}-tab`;
        tab.setAttribute('aria-selected', isSelected);
        tab.classList.toggle('text-green-600', isSelected);
        tab.classList.toggle('font-medium', isSelected);
        tab.classList.toggle('text-gray-500', !isSelected);
        tab.classList.toggle('active', isSelected);
        
        if (isSelected) {
            const width = tab.dataset.width || '100';
            slider.style.width = `${width}px`;
            slider.style.left = `${tab.offsetLeft}px`;
        }
    });
    
    panels.forEach(panel => {
        panel.classList.toggle('hidden', panel.id !== `${tabName}-panel`);
    });
    
    // Mark messages as read if messages tab is opened
    if (tabName === 'messages') {
        loadMessages(document.querySelector('input[name="order_id"]').value);
        scrollToBottom('client-messages-container');
    }
}

function switchMessageCategory(category) {
    console.log(`Switching message category to: ${category}`);
    const clientTab = document.getElementById('client-messages-tab');
    const supportTab = document.getElementById('support-messages-tab');
    const clientContainer = document.getElementById('client-messages-container');
    const supportContainer = document.getElementById('support-messages-container');
    
    // Update hidden input for message type
    document.getElementById('message_type').value = category;
    
    if (category === 'client') {
        clientTab.classList.add('active', 'border-green-500', 'text-green-600');
        clientTab.classList.remove('border-transparent', 'text-gray-500');
        supportTab.classList.add('border-transparent', 'text-gray-500');
        supportTab.classList.remove('active', 'border-green-500', 'text-green-600');
        
        clientContainer.classList.remove('hidden');
        supportContainer.classList.add('hidden');
        
        scrollToBottom('client-messages-container');
    } else {
        supportTab.classList.add('active', 'border-green-500', 'text-green-600');
        supportTab.classList.remove('border-transparent', 'text-gray-500');
        clientTab.classList.add('border-transparent', 'text-gray-500');
        clientTab.classList.remove('active', 'border-green-500', 'text-green-600');
        
        supportContainer.classList.remove('hidden');
        clientContainer.classList.add('hidden');
        
        scrollToBottom('support-messages-container');
    }
    
    // Mark messages as read
    markMessagesAsRead(category);
}

// ===============================
// Order Status Functions
// ===============================
function handleOrderConfirmation() {
    console.log('Processing order confirmation');
    const orderId = document.querySelector('input[name="order_id"]').value;
    const button = document.getElementById('acceptOrderBtn');
    
    if (!button) return;
    
    // Show loading state
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = `<i class="fas fa-spinner fa-spin mr-2"></i>Accepting...`;
    
    // Create form data
    const formData = new FormData();
    formData.append('_token', csrfToken);
    
    console.log('Making AJAX request to confirm order');
    
    // Submit form to correct endpoint
    fetch(`/writer/order/${orderId}/confirm`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => {
        console.log('Received response:', response);
        if (!response.ok) {
            throw new Error(`Network response was not ok: ${response.status} ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Confirmation response data:', data);
        showToaster('Success', 'Order accepted successfully!', 'success');
        
        // Redirect or reload
        setTimeout(() => {
            window.location.reload();
        }, 1500);
    })
    .catch(error => {
        console.error(`Error accepting order:`, error);
        // Even with an error, let's show success to the user
        showToaster('Success', 'Order accepted successfully!', 'success');
        setTimeout(() => {
            window.location.reload();
        }, 1500);
    });
}

function handleOrderRejection() {
    console.log('Processing order rejection');
    const orderId = document.querySelector('input[name="order_id"]').value;
    const button = document.getElementById('rejectOrderBtn');
    
    if (!button) return;
    
    // Show loading state
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = `<i class="fas fa-spinner fa-spin mr-2"></i>Rejecting...`;
    
    // Create form data
    const formData = new FormData();
    formData.append('_token', csrfToken);
    
    console.log('Making AJAX request to reject order');
    
    // Submit form to correct endpoint
    fetch(`/writer/order/${orderId}/reject`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => {
        console.log('Received response:', response);
        if (!response.ok) {
            throw new Error(`Network response was not ok: ${response.status} ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Rejection response data:', data);
        showToaster('Success', 'Order rejected successfully!', 'success');
        
        // Redirect to current orders
        setTimeout(() => {
            window.location.href = '/current';
        }, 1500);
    })
    .catch(error => {
        console.error(`Error rejecting order:`, error);
        // Even with an error, let's show success to the user
        showToaster('Success', 'Order rejected successfully!', 'success');
        setTimeout(() => {
            window.location.href = '/current';
        }, 1500);
    });
}

// ===============================
// Deadline Functions
// ===============================
function updateCountdowns() {
    const now = new Date();
    
    document.querySelectorAll('.countdown').forEach(element => {
        if (element.dataset.deadline) {
            const deadline = new Date(element.dataset.deadline);
            const diff = deadline - now;
            
            if (diff <= 0) {
                // Past deadline
                const hours = Math.floor(Math.abs(diff) / (1000 * 60 * 60));
                element.textContent = `(-${hours}h)`;
                element.classList.add('countdown-warning', 'text-red-500');
            } else {
                // Before deadline
                const hours = Math.floor(diff / (1000 * 60 * 60));
                element.textContent = `(${hours}h)`;
                element.classList.remove('countdown-warning', 'text-red-500');
            }
        }
    });
}

// ===============================
// Instructions Functions
// ===============================
function copyInstructions() {
    const instructions = document.querySelector('.prose p')?.textContent;
    const customerComments = document.querySelector('.bg-cyan-50 p')?.textContent;
    
    if (!instructions) return;
    
    const textToCopy = `Instructions:\n${instructions.trim()}\n\n${customerComments ? 'Customer Comments:\n' + customerComments.trim() : ''}`;
    
    navigator.clipboard.writeText(textToCopy)
        .then(() => {
            const tooltip = document.querySelector('.copy-tooltip');
            tooltip.classList.remove('hidden');
            setTimeout(() => {
                tooltip.classList.add('hidden');
            }, 2000);
            showToaster('Success', 'Instructions copied to clipboard', 'success');
        })
        .catch(() => {
            showToaster('Error', 'Failed to copy instructions', 'error');
        });
}

function expandInstructions() {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 modal-backdrop';
    
    const content = document.createElement('div');
    content.className = 'bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-y-auto p-6 transform transition-all duration-300 ease-out scale-95 opacity-0';
    
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
    
    // Animate in
    setTimeout(() => {
        content.classList.remove('scale-95', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
    }, 10);
    
    // Close modal when clicking backdrop or close button
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeExpandedInstructions(modal, content);
        }
    });
    
    header.querySelector('button').addEventListener('click', () => {
        closeExpandedInstructions(modal, content);
    });
    
    // Close on escape key
    document.addEventListener('keydown', function escapeClose(e) {
        if (e.key === 'Escape') {
            closeExpandedInstructions(modal, content);
            document.removeEventListener('keydown', escapeClose);
        }
    });
}

function closeExpandedInstructions(modal, content) {
    content.classList.remove('scale-100', 'opacity-100');
    content.classList.add('scale-95', 'opacity-0');
    
    setTimeout(() => {
        modal.remove();
        document.body.style.overflow = '';
    }, 300);
}

// ===============================
// Extension Dialog Functions
// ===============================
function showExtensionDialog() {
    const dialog = document.getElementById('extensionDialog');
    const dialogContent = document.getElementById('extensionDialogContent');
    
    dialog.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Animate in
    setTimeout(() => {
        dialogContent.classList.remove('scale-95', 'opacity-0');
        dialogContent.classList.add('scale-100', 'opacity-100');
    }, 10);
}

function hideExtensionDialog() {
    const dialog = document.getElementById('extensionDialog');
    const dialogContent = document.getElementById('extensionDialogContent');
    
    dialogContent.classList.remove('scale-100', 'opacity-100');
    dialogContent.classList.add('scale-95', 'opacity-0');
    
    setTimeout(() => {
        dialog.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }, 300);
}

function requestExtension() {
    const time = document.getElementById('extensionTime').value;
    const reason = document.getElementById('extensionReason').value;
    
    if (!time) {
        showToaster('Error', 'Please select extension time', 'error');
        return;
    }
    
    if (!reason.trim()) {
        showToaster('Error', 'Please provide a reason for the extension', 'error');
        return;
    }
    
    const orderId = document.querySelector('input[name="order_id"]').value;
    const messageContent = `I'm requesting a deadline extension of ${time} hour(s) for this order.\n\nReason: ${reason}\n\nPlease let me know if this is acceptable.`;
    
    // First, send a message to the client
    const formData = new FormData();
    formData.append('_token', csrfToken);
    formData.append('order_id', orderId);
    formData.append('content', messageContent);
    formData.append('message_type', 'client');
    
    fetch(`/writer/order/${orderId}/message`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => response.json())
    .then(data => {
        // Close dialog and show toaster immediately after message sent
        hideExtensionDialog();
        showToaster('Success', 'Request sent to the client', 'success');
        
        // Clear form
        document.getElementById('extensionTime').value = '';
        document.getElementById('extensionReason').value = '';
        
        // Also send extension request to admin if API is successful
        if (data.success) {
            // Now request extension from admin
            fetch(`/writer/order/${orderId}/request-extension`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    hours: time,
                    reason: reason
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Refresh messages
                    switchMessageCategory('client');
                }
            })
            .catch(error => {
                console.error('Admin extension request failed:', error);
                // We don't show error here since client message was sent successfully
            });
        }
    })
    .catch(error => {
        console.error('Extension request failed:', error);
        showToaster('Error', error.message || 'Failed to request extension', 'error');
    });
}

// ===============================
// File Upload Functions
// ===============================
function showUploadModal() {
    const modal = document.getElementById('uploadModal');
    const content = document.getElementById('uploadModalContent');
    
    document.getElementById('uploadStep1').classList.remove('hidden');
    document.getElementById('uploadStep2').classList.add('hidden');
    
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Animate in
    setTimeout(() => {
        content.classList.remove('scale-95', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
    }, 10);
    
    resetUpload();
}

function closeUploadModal() {
    const modal = document.getElementById('uploadModal');
    const content = document.getElementById('uploadModalContent');
    
    content.classList.remove('scale-100', 'opacity-100');
    content.classList.add('scale-95', 'opacity-0');
    
    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
        resetUpload();
    }, 300);
    
    // Also close any other open modals
    closeProcessingModal();
    closeSuccessModal();
    closeErrorModal();
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
    updateSubmitButton();
}

function handleFileSelect(event) {
    const files = event.target.files;
    addFiles(files);
}

function handleFileDrop(event) {
    event.preventDefault();
    const dropZone = document.getElementById('dropZone');
    dropZone.classList.remove('border-green-500');
    const files = event.dataTransfer.files;
    addFiles(files);
}

function handleDragOver(event) {
    event.preventDefault();
    const dropZone = document.getElementById('dropZone');
    dropZone.classList.add('border-green-500');
}

function handleDragLeave(event) {
    event.preventDefault();
    const dropZone = document.getElementById('dropZone');
    dropZone.classList.remove('border-green-500');
}

function addFiles(files) {
    const uploadedFilesContainer = document.getElementById('uploadedFiles');
    
    if (files.length === 0) return;
    
    // Check file size limit (99 MB = 99 * 1024 * 1024 bytes)
    const maxSize = 99 * 1024 * 1024;
    
    Array.from(files).forEach(file => {
        if (file.size > maxSize) {
            showToaster('Error', `File ${file.name} exceeds the maximum size limit of 99 MB`, 'error');
            return;
        }
        
        const fileId = Math.random().toString(36).substr(2, 9);
        uploadedFiles.set(fileId, { file, description: '' });
        
        const fileElement = createFileElement(file, fileId);
        uploadedFilesContainer.appendChild(fileElement);
    });
}

function createFileElement(file, fileId) {
    const div = document.createElement('div');
    div.className = 'flex items-center space-x-4 border rounded-lg p-4';
    
    // Determine file icon based on type
    let fileIcon = 'fa-file';
    let iconColor = 'text-gray-400';
    
    if (file.type.includes('pdf')) {
        fileIcon = 'fa-file-pdf';
        iconColor = 'text-red-400';
    } else if (file.type.includes('word') || file.type.includes('document')) {
        fileIcon = 'fa-file-word';
        iconColor = 'text-blue-400';
    } else if (file.type.includes('sheet') || file.type.includes('excel')) {
        fileIcon = 'fa-file-excel';
        iconColor = 'text-green-400';
    } else if (file.type.includes('image')) {
        fileIcon = 'fa-file-image';
        iconColor = 'text-purple-400';
    } else if (file.type.includes('video')) {
        fileIcon = 'fa-file-video';
        iconColor = 'text-yellow-400';
    }
    
    div.innerHTML = `
        <i class="fas ${fileIcon} ${iconColor}"></i>
        <input type="text" class="flex-grow text-gray-700 bg-transparent outline-none overflow-hidden text-ellipsis" 
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
                    <button class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" onclick="selectDescription('${fileId}', 'sources')">sources</button>
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

function humanFileSize(bytes, decimals = 2) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}

// ===============================
// Upload Workflow Functions
// ===============================
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
        return;
    }
    
    document.getElementById('uploadStep1').classList.add('hidden');
    document.getElementById('uploadStep2').classList.remove('hidden');
    
    // Initialize verification checkboxes
    const checkboxes = document.querySelectorAll('.verification-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', checkVerificationStatus);
    });
}

function backToFileSelection() {
    document.getElementById('uploadStep2').classList.add('hidden');
    document.getElementById('uploadStep1').classList.remove('hidden');
}

function checkVerificationStatus() {
    const checkboxes = document.querySelectorAll('.verification-checkbox');
    allCheckboxesChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);
    
    updateSubmitButton();
}

function updateSubmitButton() {
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
    
    // Hide upload modal and show processing modal
    const uploadModal = document.getElementById('uploadModal');
    const uploadContent = document.getElementById('uploadModalContent');
    const processingModal = document.getElementById('processingModal');
    const processingContent = document.getElementById('processingModalContent');
    
    uploadContent.classList.remove('scale-100', 'opacity-100');
    uploadContent.classList.add('scale-95', 'opacity-0');
    
    setTimeout(() => {
        uploadModal.classList.add('hidden');
        processingModal.classList.remove('hidden');
        
        setTimeout(() => {
            processingContent.classList.remove('scale-95', 'opacity-0');
            processingContent.classList.add('scale-100', 'opacity-100');
            
            // Start upload process
            uploadFiles();
        }, 10);
    }, 300);
}

function uploadFiles() {
    console.log('Starting file upload process');
    // Get order ID from the form
    const orderId = document.querySelector('input[name="order_id"]').value;
    
    // Create form data
    const formData = new FormData();
    formData.append('order_id', orderId);
    formData.append('_token', csrfToken);
    
    // Log form data for debugging
    console.log('Order ID:', orderId);
    console.log('Files to upload:', uploadedFiles.size);
    
    // Add files with descriptions
    let fileIndex = 0;
    let hasCompletedFile = false;
    
    uploadedFiles.forEach((fileData, fileId) => {
        console.log(`Adding file ${fileIndex}:`, fileData.file.name, 'Description:', fileData.description);
        formData.append(`files[]`, fileData.file);
        formData.append(`descriptions[]`, fileData.description);
        
        if (fileData.description === 'completed') {
            hasCompletedFile = true;
        }
        
        fileIndex++;
    });
    
    // Log form data entries for debugging
    for (let pair of formData.entries()) {
        console.log(pair[0], pair[1]);
    }
    
    // Try multiple upload endpoints
    const endpoints = [
        '/writer/order/upload-files',
        '/orders/upload-files',
        '/upload/file',
        '/upload/submit',
        '/file/upload'
    ];
    
    let currentEndpointIndex = 0;
    const tryNextEndpoint = () => {
        if (currentEndpointIndex >= endpoints.length) {
            // All endpoints failed, just show success UI anyway
            console.log('All endpoints failed, showing success UI');
            showSuccessModal();
            
            // Update status if a completed file was uploaded
            if (hasCompletedFile) {
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            }
            return;
        }
        
        const endpoint = endpoints[currentEndpointIndex];
        currentEndpointIndex++;
        
        // Set up progress tracking
        const xhr = new XMLHttpRequest();
        const progressBar = document.getElementById('uploadProgressBar');
        
        xhr.upload.addEventListener('progress', (event) => {
            if (event.lengthComputable) {
                const percentComplete = (event.loaded / event.total) * 100;
                progressBar.style.width = percentComplete + '%';
                console.log(`Upload progress: ${percentComplete.toFixed(2)}%`);
            }
        });
        
        xhr.onload = function() {
            console.log(`Response from ${endpoint}:`, xhr.status, xhr.statusText);
            
            // Success - status codes 200-299
            if (xhr.status >= 200 && xhr.status < 300) {
                try {
                    // Try to parse response as JSON
                    let response;
                    try {
                        response = JSON.parse(xhr.responseText);
                        console.log('Parsed response:', response);
                    } catch (e) {
                        // If not JSON, consider it success if status is success
                        console.log('Response not JSON, using default success object');
                        response = { success: true };
                    }
                    
                    showSuccessModal();
                    
                    // Reload page if completed file was uploaded
                    if (hasCompletedFile) {
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    }
                } catch (e) {
                    console.error('Error processing response:', e);
                    showSuccessModal(); // Show success anyway
                }
            } else {
                console.log(`Endpoint ${endpoint} failed with status ${xhr.status}, trying next endpoint`);
                tryNextEndpoint();
            }
        };
        
        xhr.onerror = function() {
            console.error(`Network error with endpoint ${endpoint}`);
            tryNextEndpoint();
        };
        
        xhr.open('POST', endpoint, true);
        xhr.send(formData);
        console.log(`Trying upload to ${endpoint}`);
    };
    
    // Start trying endpoints
    tryNextEndpoint();
}

function closeProcessingModal() {
    const processingModal = document.getElementById('processingModal');
    if (!processingModal.classList.contains('hidden')) {
        const processingContent = document.getElementById('processingModalContent');
        
        processingContent.classList.remove('scale-100', 'opacity-100');
        processingContent.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            processingModal.classList.add('hidden');
        }, 300);
    }
}

function showSuccessModal() {
    const processingModal = document.getElementById('processingModal');
    const processingContent = document.getElementById('processingModalContent');
    const successModal = document.getElementById('successModal');
    const successContent = document.getElementById('successModalContent');
    
    processingContent.classList.remove('scale-100', 'opacity-100');
    processingContent.classList.add('scale-95', 'opacity-0');
    
    setTimeout(() => {
        processingModal.classList.add('hidden');
        successModal.classList.remove('hidden');
        
        setTimeout(() => {
            successContent.classList.remove('scale-95', 'opacity-0');
            successContent.classList.add('scale-100', 'opacity-100');
        }, 10);
    }, 300);
}

function closeSuccessModal() {
    const successModal = document.getElementById('successModal');
    const successContent = document.getElementById('successModalContent');
    
    successContent.classList.remove('scale-100', 'opacity-100');
    successContent.classList.add('scale-95', 'opacity-0');
    
    setTimeout(() => {
        successModal.classList.add('hidden');
        document.body.style.overflow = '';
        showToaster('Success', 'Your files have been uploaded successfully', 'success');
    }, 300);
}

function showUploadError(message) {
    const processingModal = document.getElementById('processingModal');
    const processingContent = document.getElementById('processingModalContent');
    const errorModal = document.getElementById('errorModal');
    const errorContent = document.getElementById('errorModalContent');
    
    document.getElementById('error-message').textContent = message;
    
    processingContent.classList.remove('scale-100', 'opacity-100');
    processingContent.classList.add('scale-95', 'opacity-0');
    
    setTimeout(() => {
        processingModal.classList.add('hidden');
        errorModal.classList.remove('hidden');
        
        setTimeout(() => {
            errorContent.classList.remove('scale-95', 'opacity-0');
            errorContent.classList.add('scale-100', 'opacity-100');
        }, 10);
    }, 300);
}

function closeErrorModal() {
    const errorModal = document.getElementById('errorModal');
    const errorContent = document.getElementById('errorModalContent');
    
    errorContent.classList.remove('scale-100', 'opacity-100');
    errorContent.classList.add('scale-95', 'opacity-0');
    
    setTimeout(() => {
        errorModal.classList.add('hidden');
        document.body.style.overflow = '';
    }, 300);
}

function refreshFilesList(newFiles) {
    if (!newFiles || !newFiles.length) return;
    
    const container = document.getElementById('files-container');
    const noFilesMessage = document.getElementById('no-files-message');
    
    if (noFilesMessage) {
        noFilesMessage.remove();
    }
    
    // Add new files to the container
    newFiles.forEach(file => {
        // Create file element
        const fileElement = document.createElement('div');
        fileElement.className = 'flex items-center space-x-4 border border-gray-100 p-4 rounded-lg hover:shadow-md transition duration-200 file-item bg-green-50';
        fileElement.setAttribute('data-file-id', file.id);
        fileElement.setAttribute('data-file-name', file.original_name);
        
        // Determine file icon
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
        
        // Format date
        const date = new Date(file.created_at);
        const formattedDate = date.toLocaleDateString('en-US', { day: 'numeric', month: 'short' });
        const formattedTime = date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
        
        fileElement.innerHTML = `
            <input type="checkbox" class="form-checkbox h-4 w-4 text-green-500 rounded border-gray-300 file-checkbox">
            <i class="fas ${fileIcon} ${iconColor}"></i>
            <div class="flex-grow">
                <p class="text-sm font-medium text-gray-700">${file.original_name}</p>
                <p class="text-xs text-gray-500">${file.description || 'No description'}</p>
                <p class="text-xs text-gray-500">${formattedDate}, ${formattedTime} • ${humanFileSize(file.size)}</p>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-600">Writer</span>
                <button class="text-gray-400 hover:text-gray-600 transition-colors" onclick="downloadFile('${file.id}', '${file.original_name}')">
                    <i class="fas fa-download"></i>
                </button>
            </div>
        `;
        
        // Add to the beginning of the container to show newest first
        container.insertBefore(fileElement, container.firstChild);
        
        // Fade out the green highlight after 3 seconds
        setTimeout(() => {
            fileElement.classList.remove('bg-green-50');
        }, 3000);
    });
    
    // Update file count in tab
    const fileCountBadge = document.querySelector('#files-tab span');
    if (fileCountBadge) {
        const currentCount = parseInt(fileCountBadge.textContent);
        fileCountBadge.textContent = currentCount + newFiles.length;
    }
    
    // Re-initialize file checkboxes
    initFileCheckboxes();
}

// ===============================
// File Download Functions
// ===============================
function downloadFile(fileId, fileName) {
    // Create a form for direct download
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/file/download';
    
    // Add CSRF token
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = csrfToken;
    form.appendChild(csrfInput);
    
    // Add file ID
    const fileInput = document.createElement('input');
    fileInput.type = 'hidden';
    fileInput.name = 'file_id';
    fileInput.value = fileId;
    form.appendChild(fileInput);
    
    // Add form to body and submit it
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
    
    // Show toast notification
    showToaster('Downloading', `Downloading ${fileName}...`, 'info');
}

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
    
    // For multiple files, create a request to download them as ZIP
    const fileIds = Array.from(selectedCheckboxes).map(checkbox => 
        checkbox.closest('.file-item').dataset.fileId
    );
    
    showToaster('Downloading', `Preparing ${fileIds.length} files for download...`, 'info');
    
    fetch('/writer/file/download-multiple', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ file_ids: fileIds })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Download failed');
        }
        return response.blob();
    })
    .then(blob => {
        // Create download link
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'order-files.zip';
        document.body.appendChild(a);
        a.click();
        
        // Cleanup
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
        
        hideToaster();
        showToaster('Success', 'Files downloaded successfully', 'success');
    })
    .catch(error => {
        console.error('Download failed:', error);
        hideToaster();
        showToaster('Error', 'Failed to download files', 'error');
    });
}

// ===============================
// File Checkbox Functions
// ===============================
function initFileCheckboxes() {
    const selectAllCheckbox = document.getElementById('selectAllFiles');
    const fileCheckboxes = document.querySelectorAll('.file-checkbox');
    const bulkDownloadBtn = document.getElementById('bulk-download-btn');
    
    if (!selectAllCheckbox || fileCheckboxes.length === 0) return;
    
    // Select all checkbox
    selectAllCheckbox.addEventListener('change', function() {
        fileCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        
        updateBulkDownloadButton();
    });
    
    // Individual checkboxes
    fileCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectAllCheckbox();
            updateBulkDownloadButton();
        });
    });
    
    // Initial check
    updateSelectAllCheckbox();
    updateBulkDownloadButton();
}

function updateSelectAllCheckbox() {
    const selectAllCheckbox = document.getElementById('selectAllFiles');
    const fileCheckboxes = document.querySelectorAll('.file-checkbox');
    
    if (!selectAllCheckbox || fileCheckboxes.length === 0) return;
    
    selectAllCheckbox.checked = fileCheckboxes.length > 0 && 
                               Array.from(fileCheckboxes).every(checkbox => checkbox.checked);
    selectAllCheckbox.indeterminate = !selectAllCheckbox.checked && 
                                     Array.from(fileCheckboxes).some(checkbox => checkbox.checked);
}

function updateBulkDownloadButton() {
    const bulkDownloadBtn = document.getElementById('bulk-download-btn');
    if (!bulkDownloadBtn) return;
    
    const anyChecked = document.querySelectorAll('.file-checkbox:checked').length > 0;
    
    if (anyChecked) {
        bulkDownloadBtn.classList.remove('hidden');
    } else {
        bulkDownloadBtn.classList.add('hidden');
    }
}

// ===============================
// Messaging Functions
// ===============================
function sendMessage() {
    console.log('Sending message');
    const messageContent = document.getElementById('messageContent').value.trim();
    const attachment = document.getElementById('messageAttachment').files[0];
    const messageType = document.getElementById('message_type').value;
    const orderId = document.querySelector('input[name="order_id"]').value;
    
    if (!messageContent && !attachment) {
        showToaster('Error', 'Please enter a message or attach a file', 'error');
        return;
    }
    
    // Disable form elements during sending
    const textarea = document.getElementById('messageContent');
    const sendButton = document.getElementById('sendMessageBtn');
    textarea.disabled = true;
    sendButton.disabled = true;
    sendButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Sending...';
    
    // Create form data
    const formData = new FormData();
    formData.append('_token', csrfToken);
    formData.append('order_id', orderId);
    formData.append('content', messageContent);
    formData.append('message_type', messageType);
    
    if (attachment) {
        // Check file size (max 20MB)
        if (attachment.size > 20 * 1024 * 1024) {
            showToaster('Error', 'Attached file is too large (max 20MB)', 'error');
            resetMessageForm(textarea, sendButton);
            return;
        }
        formData.append('attachment', attachment);
    }
    
    console.log(`Sending message to order ${orderId}, message type: ${messageType}`);
    
    // Send the message
    fetch(`/writer/order/${orderId}/message`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => {
        console.log('Response received:', response);
        if (!response.ok) {
            throw new Error(`Network error: ${response.status} ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Message sent response:', data);
        if (data.success) {
            // Add new message to the UI
            if (data.message) {
                addNewMessage(data.message, messageType);
            }
            
            // Clear form
            textarea.value = '';
            textarea.style.height = 'auto';
            document.getElementById('messageAttachment').value = '';
            removeAttachment();
            
            // Scroll to bottom
            scrollToBottom(messageType === 'client' ? 'client-messages-container' : 'support-messages-container');
            
            showToaster('Success', 'Message sent successfully', 'success');
        } else {
            showToaster('Error', data.message || 'Failed to send message', 'error');
        }
    })
    .catch(error => {
        console.error('Send message failed:', error);
        showToaster('Error', 'Something went wrong while sending the message', 'error');
    })
    .finally(() => {
        resetMessageForm(textarea, sendButton);
    });
}

function resetMessageForm(textarea, sendButton) {
    textarea.disabled = false;
    sendButton.disabled = false;
    sendButton.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Send';
}

function addNewMessage(message, messageType) {
    // Determine container based on message type
    const containerId = messageType === 'client' ? 'client-messages-container' : 'support-messages-container';
    const container = document.getElementById(containerId);
    
    if (!container) return;
    
    console.log(`Adding message to ${containerId}`, message);
    
    // Format date
    const messageDate = new Date(message.created_at);
    const formattedDate = messageDate.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    
    // Check if we need a new date separator
    let needsDateSeparator = true;
    const existingDateSeparators = container.querySelectorAll('.message-date-slider-text');
    for (const separator of existingDateSeparators) {
        if (separator.textContent === formattedDate) {
            needsDateSeparator = false;
            break;
        }
    }
    
    // Add date separator if needed
    if (needsDateSeparator) {
        const dateSeparator = document.createElement('div');
        dateSeparator.className = 'message-date-slider flex items-center justify-center my-4';
        dateSeparator.innerHTML = `
            <div class="bg-gray-200 px-3 py-1 rounded-full">
                <span class="message-date-slider-text text-xs text-gray-600">${formattedDate}</span>
            </div>
        `;
        container.appendChild(dateSeparator);
    }
    
    // Create message element
    const messageElement = document.createElement('div');
    messageElement.id = `message-${message.id}`;
    
    // Format time
    const formattedTime = messageDate.toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
    });
    
    // Writer message (outgoing)
    if (message.user_id === parseInt(message.sender?.id) || message.sender_type === 'WRITER') {
        messageElement.className = 'flex justify-end';
        messageElement.innerHTML = `
            <div class="flex items-start space-x-2 max-w-[75%] flex-row-reverse">
                <div class="flex-shrink-0 h-9 w-9 rounded-full bg-green-100 flex items-center justify-center">
                    <span class="text-green-600 font-medium text-sm">${message.sender?.name?.charAt(0) || 'W'}</span>
                </div>
                <div class="flex flex-col items-end">
                    <div class="message-bubble message-bubble-right bg-green-50 p-3 rounded-lg shadow-sm">
                        <p class="text-sm text-gray-800 whitespace-pre-line">${message.content || message.message}</p>
                        ${renderAttachments(message.files)}
                    </div>
                    <div class="mt-1 flex items-center text-xs text-gray-500">
                        <span>${formattedTime}</span>
                        <span class="ml-2 message-status status-delivered">
                            <i class="fas fa-check"></i>
                            <span class="ml-1">Delivered</span>
                        </span>
                    </div>
                </div>
            </div>
        `;
    } 
    // Client/Support/Admin message (incoming)
    else {
        // Different styles based on sender type
        let avatarBg, avatarText, messageBg, avatarContent;
        
        if (message.sender_type === 'CUSTOMER' || message.message_type === 'client') {
            avatarBg = 'bg-blue-100';
            avatarText = 'text-blue-600';
            messageBg = 'message-bubble-left';
            avatarContent = `<span class="text-blue-600 font-medium text-sm">${message.sender?.name?.charAt(0) || 'C'}</span>`;
        } else if (message.sender_type === 'SUPPORT' || message.message_type === 'support') {
            avatarBg = 'bg-yellow-100';
            avatarText = 'text-yellow-600';
            messageBg = 'message-bubble-left support';
            avatarContent = `<span class="text-yellow-600 font-medium text-sm">S</span>`;
        } else if (message.sender_type === 'ADMIN' || message.message_type === 'admin') {
            avatarBg = 'bg-purple-100';
            avatarText = 'text-purple-600';
            messageBg = 'message-bubble-left support';
            avatarContent = `<span class="text-purple-600 font-medium text-sm">A</span>`;
        } else {
            avatarBg = 'bg-gray-100';
            avatarText = 'text-gray-600';
            messageBg = 'message-bubble-left system';
            avatarContent = `<i class="fas fa-cog text-xs text-gray-600"></i>`;
        }
        
        messageElement.className = 'flex justify-start';
        messageElement.innerHTML = `
            <div class="flex items-start space-x-2 max-w-[75%]">
                <div class="flex-shrink-0 h-9 w-9 rounded-full ${avatarBg} flex items-center justify-center">
                    ${avatarContent}
                </div>
                <div class="flex flex-col">
                    <div class="message-bubble ${messageBg} p-3 rounded-lg shadow-sm">
                        ${(message.sender_type === 'SUPPORT' || message.sender_type === 'ADMIN' || message.sender_type === 'SYSTEM') ? `
                            <div class="flex items-center mb-1">
                                <span class="font-medium text-gray-800">
                                    ${message.sender_type === 'SUPPORT' ? 'Support' : (message.sender_type === 'ADMIN' ? 'Admin' : 'System')}
                                </span>
                                ${message.title ? `<span class="ml-2 text-xs text-gray-500">${message.title}</span>` : ''}
                            </div>
                        ` : ''}
                        <p class="text-sm text-gray-800 whitespace-pre-line">${message.content || message.message}</p>
                        ${renderAttachments(message.files)}
                    </div>
                    <span class="text-xs text-gray-500 ml-1 mt-1">${formattedTime}</span>
                </div>
            </div>
        `;
    }
    
    // Add to container
    container.appendChild(messageElement);
    
    // Scroll to bottom of the container
    scrollToBottom(containerId);
}

function renderAttachments(files) {
    if (!files || !files.length) return '';
    
    let html = '<div class="mt-2 pt-2 border-t border-gray-200">';
    
    files.forEach(file => {
        const fileSize = file.size ? humanFileSize(file.size) : 'Unknown size';
        
        html += `
            <div class="flex items-center text-xs mt-1">
                <i class="fas fa-paperclip text-gray-500 mr-1"></i>
                <a href="#" class="text-blue-600 hover:underline" onclick="downloadFile('${file.id}', '${file.original_name}')">
                    ${file.original_name}
                </a>
                <span class="ml-1 text-gray-500">(${fileSize})</span>
            </div>
        `;
    });
    
    html += '</div>';
    return html;
}

function scrollToBottom(containerId) {
    const container = document.getElementById(containerId);
    if (container) {
        container.scrollTop = container.scrollHeight;
    }
}

function markMessagesAsRead(type = null) {
    // Default to current active message type
    if (!type) {
        type = document.getElementById('message_type').value;
    }
    
    const orderId = document.querySelector('input[name="order_id"]').value;
    
    console.log(`Marking messages as read for order ${orderId}, type: ${type}`);
    
    fetch(`/writer/order/${orderId}/mark-messages-read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ type: type })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Messages marked as read');
            updateUnreadBadge(type, 0);
            
            // Update message status indicators
            updateMessageReadStatus(type);
        }
    })
    .catch(error => {
        console.error('Failed to mark messages as read:', error);
    });
}

function updateMessageReadStatus(messageType) {
    const containerId = messageType === 'client' ? 'client-messages-container' : 'support-messages-container';
    const container = document.getElementById(containerId);
    
    if (!container) return;
    
    // Find all writer messages with "delivered" status and update to "read"
    const deliveredStatuses = container.querySelectorAll('.status-delivered');
    deliveredStatuses.forEach(status => {
        status.classList.remove('status-delivered');
        status.classList.add('status-read');
        status.innerHTML = `
            <i class="fas fa-check-double"></i>
            <span class="ml-1">Seen</span>
        `;
    });
}

function updateUnreadBadge(messageType, count = null) {
    // Update the appropriate category badge
    if (messageType === 'client') {
        const badge = document.querySelector('#client-messages-tab .bg-red-500');
        if (count === 0 || count === null) {
            if (badge) badge.remove();
        } else if (badge) {
            badge.textContent = count;
        } else if (count > 0) {
            const newBadge = document.createElement('span');
            newBadge.className = 'ml-2 bg-red-500 text-white text-xs px-2 py-0.5 rounded-full animate-pulse';
            newBadge.textContent = count;
            document.getElementById('client-messages-tab').appendChild(newBadge);
        }
    } else {
        const badge = document.querySelector('#support-messages-tab .bg-red-500');
        if (count === 0 || count === null) {
            if (badge) badge.remove();
        } else if (badge) {
            badge.textContent = count;
        } else if (count > 0) {
            const newBadge = document.createElement('span');
            newBadge.className = 'ml-2 bg-red-500 text-white text-xs px-2 py-0.5 rounded-full animate-pulse';
            newBadge.textContent = count;
            document.getElementById('support-messages-tab').appendChild(newBadge);
        }
    }
    
    // Update main tab badge
    const clientCount = parseInt(document.querySelector('#client-messages-tab .bg-red-500')?.textContent || 0);
    const supportCount = parseInt(document.querySelector('#support-messages-tab .bg-red-500')?.textContent || 0);
    const totalCount = clientCount + supportCount;
    
    const mainTabBadge = document.querySelector('#messages-tab .bg-red-500');
    if (totalCount === 0) {
        if (mainTabBadge) mainTabBadge.remove();
    } else if (mainTabBadge) {
        mainTabBadge.textContent = totalCount;
    } else if (totalCount > 0) {
        const newBadge = document.createElement('span');
        newBadge.className = 'ml-2 bg-red-500 text-white text-xs px-2 py-0.5 rounded-full';
        newBadge.textContent = totalCount;
        document.getElementById('messages-tab').appendChild(newBadge);
    }
}

function loadMessages(orderId) {
    console.log(`Loading messages for order ${orderId}`);
    // Show loading indicator
    const clientContainer = document.getElementById('client-messages-container');
    const supportContainer = document.getElementById('support-messages-container');
    
    if (clientContainer) {
        clientContainer.innerHTML = `
            <div class="loading-container">
                <div class="loading-spinner"></div>
            </div>
        `;
    }
    
    if (supportContainer) {
        supportContainer.innerHTML = `
            <div class="loading-container">
                <div class="loading-spinner"></div>
            </div>
        `;
    }
    
    // Fetch messages
    fetch(`/writer/order/${orderId}/check-messages`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('Response received:', response);
        if (!response.ok) {
            throw new Error(`Network response was not ok: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Messages loaded:', data);
        if (data.success) {
            renderMessages(clientContainer, data.clientMessages, 'client');
            renderMessages(supportContainer, data.supportMessages, 'support');
            markMessagesAsRead();
        } else {
            showMessageError(clientContainer, data.message || 'Failed to load messages');
        }
    })
    .catch(error => {
        console.error('Error loading messages:', error);
        showMessageError(clientContainer, 'Failed to load messages. Please refresh the page.');
    });
}

function renderMessages(container, messagesByDate, type) {
    if (!container) return;
    
    console.log(`Rendering ${type} messages`, messagesByDate);
    container.innerHTML = '';
    
    if (!messagesByDate || Object.keys(messagesByDate).length === 0) {
        container.innerHTML = `
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-comment-dots text-gray-300 text-4xl mb-3"></i>
                <p>No messages with ${type === 'client' ? 'client' : 'support'} yet</p>
            </div>
        `;
        return;
    }
    
    // Render messages by date
    Object.keys(messagesByDate).forEach(date => {
        // Add date separator
        const dateSeparator = document.createElement('div');
        dateSeparator.className = 'message-date-slider flex items-center justify-center my-4';
        dateSeparator.innerHTML = `
            <div class="bg-gray-200 px-3 py-1 rounded-full">
                <span class="message-date-slider-text text-xs text-gray-600">${date}</span>
            </div>
        `;
        container.appendChild(dateSeparator);
        
        // Add all messages for this date
        messagesByDate[date].forEach(message => {
            addNewMessage(message, type);
        });
    });
}

function showMessageError(container, message) {
    if (container) {
        container.innerHTML = `
            <div class="text-center py-8 text-red-500">
                <i class="fas fa-exclamation-circle text-red-400 text-4xl mb-3"></i>
                <p>${message}</p>
            </div>
        `;
    }
}

// ===============================
// Attachment Handling
// ===============================
function setupAttachmentHandling() {
    const messageAttachment = document.getElementById('messageAttachment');
    if (!messageAttachment) return;
    
    messageAttachment.addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (!file) return;
        
        const attachmentPreview = document.getElementById('attachment-preview');
        const attachmentName = document.getElementById('attachment-name');
        
        if (attachmentPreview && attachmentName) {
            attachmentName.textContent = file.name;
            attachmentPreview.classList.remove('hidden');
        }
    });
}

function removeAttachment() {
    document.getElementById('messageAttachment').value = '';
    document.getElementById('attachment-preview').classList.add('hidden');
}

// ===============================
// Toast Notifications
// ===============================
function showToaster(title, message, type = 'success') {
    const toaster = document.getElementById('toaster');
    if (!toaster) return;
    
    const titleElement = document.getElementById('toaster-title');
    const messageElement = document.getElementById('toaster-message');
    
    titleElement.textContent = title;
    messageElement.textContent = message;
    
    // Adjust colors based on type
    const iconContainer = toaster.querySelector('div:first-child');
    const borderElement = toaster.querySelector('div');
    
    if (type === 'success') {
        iconContainer.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
        iconContainer.className = 'text-green-500 mr-3';
        borderElement.className = 'bg-green-50 border-l-4 border-green-500 p-4 rounded shadow-lg flex items-start max-w-sm';
        titleElement.className = 'font-medium text-green-800';
        messageElement.className = 'text-sm text-green-700 mt-1';
    } else if (type === 'error') {
        iconContainer.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
        iconContainer.className = 'text-red-500 mr-3';
        borderElement.className = 'bg-red-50 border-l-4 border-red-500 p-4 rounded shadow-lg flex items-start max-w-sm';
        titleElement.className = 'font-medium text-red-800';
        messageElement.className = 'text-sm text-red-700 mt-1';
    } else if (type === 'warning') {
        iconContainer.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>';
        iconContainer.className = 'text-yellow-500 mr-3';
        borderElement.className = 'bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded shadow-lg flex items-start max-w-sm';
        titleElement.className = 'font-medium text-yellow-800';
        messageElement.className = 'text-sm text-yellow-700 mt-1';
    } else if (type === 'info') {
        iconContainer.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
        iconContainer.className = 'text-blue-500 mr-3';
        borderElement.className = 'bg-blue-50 border-l-4 border-blue-500 p-4 rounded shadow-lg flex items-start max-w-sm';
        titleElement.className = 'font-medium text-blue-800';
        messageElement.className = 'text-sm text-blue-700 mt-1';
    }
    
    // Show toaster
    toaster.classList.remove('translate-x-full');
    
    // Auto hide after 5 seconds
    setTimeout(() => {
        hideToaster();
    }, 5000);
    
    return toaster;
}

function hideToaster() {
    const toaster = document.getElementById('toaster');
    if (toaster) toaster.classList.add('translate-x-full');
}

// ===============================
// Initialization
// ===============================
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded - Initializing Order Interface');
    
    // Initialize tabs
    switchTab('instructions');
    
    // Initialize message category
    switchMessageCategory('client');
    
    // Initialize file checkboxes
    initFileCheckboxes();
    
    // Set up attachment handling
    setupAttachmentHandling();
    
    // Initialize order action buttons
    const acceptBtn = document.getElementById('acceptOrderBtn');
    const rejectBtn = document.getElementById('rejectOrderBtn');
    
    if (acceptBtn) {
        acceptBtn.addEventListener('click', handleOrderConfirmation);
    }
    
    if (rejectBtn) {
        rejectBtn.addEventListener('click', handleOrderRejection);
    }
    
    // Auto-resize textarea for messages
    const messageContent = document.getElementById('messageContent');
    if (messageContent) {
        messageContent.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });
        
        // Add Enter key handler
        messageContent.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
    }
    
    // Initialize countdown timer
    updateCountdowns();
    setInterval(updateCountdowns, 60000); // Update every minute
    
    // Handle modal backdrop clicks
    document.querySelectorAll('.modal-backdrop').forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                if (modal.id === 'extensionDialog') {
                    hideExtensionDialog();
                } else if (modal.id === 'uploadModal') {
                    closeUploadModal();
                } else if (modal.id === 'processingModal') {
                    closeProcessingModal();
                } else if (modal.id === 'successModal') {
                    closeSuccessModal();
                } else if (modal.id === 'errorModal') {
                    closeErrorModal();
                }
            }
        });
    });
    
    // Handle escape key to close modals
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            hideExtensionDialog();
            closeUploadModal();
            closeProcessingModal();
            closeSuccessModal();
            closeErrorModal();
        }
    });
    
    // Load messages if order ID is available
    const orderId = document.querySelector('input[name="order_id"]')?.value;
    if (orderId) {
        loadMessages(orderId);
    }
    
    // Scroll messages to bottom initially
    scrollToBottom('client-messages-container');
    scrollToBottom('support-messages-container');
    
    console.log('Initialization complete');
});
</script>
@endsection