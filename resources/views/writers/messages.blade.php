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
                <select class="px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option>All departments</option>
                    <option>Support</option>
                    <option>Client</option>
                    <option>Writer's Department</option>
                    <option>Manager</option>
                    <option>Editors</option>
                    <option>Dissertation Dept</option>
                </select>
                <span class="absolute inset-y-0 left-0 flex items-center pl-2">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </span>
            </div>
            <input 
                type="text" 
                placeholder="Order"
                class="px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-green-500"
            />
            <button class="px-4 py-2 bg-green-500 text-white text-sm rounded-md hover:bg-green-600">
                Search
            </button>
        </div>
    </div>

    <!-- Messages List -->
    <div class="divide-y divide-gray-100">
        <!-- Example Message Row (Repeat this for each message) -->
        @foreach(range(1, 10) as $index)
        <div class="border-b border-gray-100 last:border-b-0">
            <div 
                class="flex items-start gap-4 py-2 px-4 cursor-pointer hover:bg-gray-50"
                onclick="toggleMessage('message-{{$index}}', this)"
            >
                <div>
                    <!-- FontAwesome Message Icon -->
                    <i class="fas fa-envelope text-green-500"></i>
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 text-sm">
                        <span class="font-semibold {{ $index == 1 ? 'text-bold' : 'text-normal' }}" id="message-{{$index}}-label">
                            {{ $index == 1 ? 'Customer' : 'Me' }}
                        </span>
                        <span class="text-gray-400">▸</span>
                        <span>{{ $index == 1 ? 'Me' : 'Customer' }}</span>
                    </div>
                    <div class="text-sm text-gray-600 font-semibold">
                        Order #614312020: New message
                    </div>
                    <div class="text-sm text-gray-400 truncate">
                        {{ $index == 1 ? 'Does your excel sheet look like the picture I uploaded? I\'m not sure if you can see the previous work by the past writer, but I need the excel sheet to look simple and easy to understand.' : 'Will my assignment be approved for the new submission deadline?' }}
                    </div>
                </div>

                <div class="flex flex-col items-end text-sm text-gray-400 whitespace-nowrap">
                    <div class="font-semibold">#614312020</div>
                    <div class="font-semibold">{{ now()->subMinutes($index*5)->format('d M, h:i A') }}</div>
                </div>

                <div class="text-gray-400">
                    <svg class="w-4 h-4 transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>

            <div id="message-{{$index}}" class="px-16 py-3 bg-white border-t border-gray-100 hidden">
                <div class="text-sm text-gray-600">
                    {{ $index == 1 ? 'Does your excel sheet look like the picture I uploaded? I\'m not sure if you can see the previous work by the past writer, but I need the excel sheet to look simple and easy to understand.' : 'Will my assignment be approved for the new submission deadline?' }}
                </div>
                <div class="mt-3">
                    <button 
                        onclick="openAnswerModal('message-{{$index}}')"
                        class="px-4 py-1.5 text-green-500 border border-green-500 rounded-md text-sm hover:bg-green-50"
                    >
                        Answer
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</main>

<!-- New Message Modal -->
<div id="newMessageModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center hidden">
    <div class="bg-white p-8 rounded-md w-full max-w-md">
        <h2 class="text-xl mb-4">New Message</h2>
        
        <div class="mb-4">
            <label class="block text-sm font-medium mb-2">Recipient</label>
            <select class="w-full p-2 border rounded-md text-sm">
                <option value="">Select an option...</option>
                <option value="customer">Customer</option>
                <option value="support">Support</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-2">Subject</label>
            <input type="text" class="w-full p-2 border rounded-md text-sm" placeholder="Enter your subject" />
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-2">Message</label>
            <textarea class="w-full h-32 p-2 border rounded-md text-sm" placeholder="Type your message here..."></textarea>
        </div>

        <!-- Attach File Section -->
        <div id="attachments" class="mb-4 space-y-2">
            <!-- Attachment Row -->
            <div class="flex items-center gap-4 p-2 bg-gray-50 rounded-md">
               
            </div>
            <!-- End Attachment Row -->
        </div>

        <div class="flex justify-between gap-2">
            <label class="cursor-pointer">
                <input
                    type="file"
                    class="hidden"
                    onchange="handleFileAttach(event)"
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
                <button class="px-4 py-2 bg-green-500 text-white text-sm rounded-md hover:bg-green-600">
                    Send
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Answer Message Modal -->
<div id="answerMessageModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center hidden">
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

        <div class="mb-4">
            <label class="block text-sm font-medium mb-2">Reply</label>
            <textarea class="w-full h-32 p-2 border rounded-md text-sm" placeholder="Type your reply here..."></textarea>
        </div>

        <div class="flex justify-end gap-2">
            <button 
                onclick="closeAnswerModal()"
                class="px-4 py-2 text-gray-600 text-sm hover:bg-gray-50 rounded-md"
            >
                Cancel
            </button>
            <button class="px-4 py-2 bg-green-500 text-white text-sm rounded-md hover:bg-green-600">
                Send
            </button>
        </div>
    </div>
</div>

<script>
    // Toggle the message detail visibility
    function toggleMessage(messageId, button) {
        const messageDetail = document.getElementById(messageId);
        const truncatedMessage = button.querySelector('.truncate');
        
        messageDetail.classList.toggle('hidden');
        truncatedMessage.classList.toggle('hidden');

        // Change the font weight based on visibility
        const label = button.querySelector('span');
        if (messageDetail.classList.contains('hidden')) {
            label.classList.remove('font-bold');
            label.classList.add('font-normal');
        } else {
            label.classList.add('font-bold');
            label.classList.remove('font-normal');
        }
    }

    // Open New Message Modal
    function openNewMessageModal() {
        document.getElementById('newMessageModal').classList.remove('hidden');
    }

    // Close New Message Modal
    function closeNewMessageModal() {
        document.getElementById('newMessageModal').classList.add('hidden');
    }

    // Open Answer Message Modal and display the first 100 characters of the message
    function openAnswerModal(messageId) {
        const messageContent = document.querySelector(`#${messageId} .text-sm.text-gray-600`).textContent;
        const truncatedContent = messageContent.length > 100 ? messageContent.substring(0, 100) + '...' : messageContent;
        
        document.getElementById('originalMessagePreview').textContent = truncatedContent;
        document.getElementById('answerMessageModal').classList.remove('hidden');
    }

    // Close Answer Message Modal
    function closeAnswerModal() {
        document.getElementById('answerMessageModal').classList.add('hidden');
    }

    // Handle file attachment
    function handleFileAttach(event) {
        const file = event.target.files[0];
        if (file) {
            const attachmentContainer = document.getElementById('attachments');
            const newAttachment = document.createElement('div');
            newAttachment.classList.add('flex', 'items-center', 'gap-4', 'p-2', 'bg-gray-50', 'rounded-md');
            newAttachment.innerHTML = `
                <span class="text-sm text-gray-600 truncate" style="max-width: 180px;">${file.name}</span>
                <input type="text" placeholder="Description" class="flex-1 p-1 text-sm border rounded focus:outline-none focus:ring-1 focus:ring-green-500" />
                <button onclick="removeAttachment(this)" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-trash-alt"></i>
                </button>
            `;
            attachmentContainer.appendChild(newAttachment);
        }
    }

    // Remove attachment
    function removeAttachment(button) {
        button.closest('div').remove();
    }
</script>

@endsection
