@if($messageThreads->count() > 0)
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
            $canReply = $isAssignedToCurrentWriter || $isAvailableOrder;
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
                    <a href="{{ $isAssignedToCurrentWriter ? route('assigned', $thread->order_id) : route('availableOrderDetails', $thread->order_id) }}" class="px-2 py-1 border border-blue-300 rounded-md text-xs text-blue-600 hover:bg-blue-50 transition-colors duration-200 flex items-center gap-1">
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