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
                        <h1 class="text-lg md:text-xl font-semibold text-gray-800">Order #614973494</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-lg md:text-xl font-semibold text-gray-800">$65</span>
                        <div class="hidden md:flex items-center border-l pl-4">
                            <div class="flex flex-col items-end">
                                <span class="text-sm font-medium text-gray-600">Customer</span>
                                <span class="text-sm text-gray-500">09:00 PM</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bid Section -->
        <div class="bg-green-50 border border-green-100 rounded-lg mb-6">
            <div class="p-4 md:p-6">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                    <p class="text-gray-700 mb-3 md:mb-0">Place your bid, if you are ready to execute this order for <span class="font-semibold">$65</span>.</p>
                    <button id="placeBidBtn" 
                            class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors duration-200">
                        Place Bid
                    </button>
                </div>
                <div class="mt-3 text-sm text-gray-500">
                    Number of bids placed for this order is 18 • Take Order option is disabled by Support Team.
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
                        <span class="ml-2 bg-green-500 text-white text-xs px-2 py-0.5 rounded-full">11</span>
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
                                    <span class="text-gray-800 font-medium">$65</span>
                                </div>
                            </div>

                            <!-- Deadline Info -->
                            <div class="p-4 bg-gradient-to-r from-gray-50 to-white rounded-lg border border-gray-100">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Deadline</span>
                                    <div>
                                        <span class="text-gray-800">2 Mar, 05:17 AM</span>
                                        <span class="text-green-500 ml-2">(4d 19h)</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Task Size -->
                            <div class="p-4 bg-gradient-to-r from-gray-50 to-white rounded-lg border border-gray-100">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Task size</span>
                                    <div class="flex items-center">
                                        <span class="text-gray-800">Large</span>
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
                                    <span class="text-gray-800">Calculations</span>
                                </div>
                            </div>

                            <!-- Discipline -->
                            <div class="p-4 bg-gradient-to-r from-gray-50 to-white rounded-lg border border-gray-100">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Discipline</span>
                                    <span class="text-gray-800">Project Planning and Control</span>
                                </div>
                            </div>

                            <!-- Software -->
                            <div class="p-4 bg-gradient-to-r from-gray-50 to-white rounded-lg border border-gray-100">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Software</span>
                                    <span class="text-gray-800">AstaPowerProject</span>
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
                                <p>
                                    The site layout plan should be in diagrammatic/drawn format utilising methods shown in tutorials (PowerPoint), and also the word count is 2000 words excluding the references and charts and the software and the powerpoint, it should be structured professionally with a cover page and a summary and an Introduction to both task1 and task2 with conclusion and references, please do not repeat the same reference twice and also make sure they are reliable please. 7 to 10 references or as much as needed.
                                </p>
                                <p>Thank you.</p><!-- Customer Comments -->
                                <div class="mt-6">
                                    <div class="bg-cyan-50 rounded-lg p-4 border border-cyan-100">
                                        <h4 class="font-medium text-gray-800 mb-2">Comments from Customer</h4>
                                        <p class="text-gray-700">
                                            This task should be completed with the original code/solutions. Please make sure you do not use any open source solutions.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Customer Files -->
                            <!-- Customer Comments -->
                            <div class="mt-6">
                                <div class="bg-cyan-50 rounded-lg p-4 border border-cyan-100">
                                    <h4 class="font-medium text-gray-800 mb-2">Comments from Customer</h4>
                                    <p class="text-gray-700">
                                        This task should be completed with the original code/solutions. Please make sure you do not use any open source solutions.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Files Panel -->
                <div id="files-panel" class="hidden" role="tabpanel">
                    <div>
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
                            <!-- File 1 -->
                            <div class="flex items-start sm:items-center border-b border-gray-100 pb-4 file-item" data-file-id="file1" data-file-name="614973494_5511BEQR_R_652202189608277.xlsx">
                                <div class="flex-shrink-0 mt-1 sm:mt-0">
                                    <input type="checkbox" class="form-checkbox h-5 w-5 text-green-500 rounded border-gray-300 file-checkbox">
                                </div>
                                <div class="ml-4 flex-shrink-0">
                                    <svg class="w-6 h-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4 flex-grow">
                                    <p class="text-sm font-medium text-gray-700 truncate">11. 614973494_5511BEQR_R...652202189608277.xlsx</p>
                                    <p class="text-xs text-gray-500">Instructions / Guidelines</p>
                                </div>
                                <div class="ml-4 flex-shrink-0 text-right">
                                    <p class="text-sm text-gray-600">Customer</p>
                                    <p class="text-xs text-gray-500">17 Feb, 04:40 AM • 133 KB</p>
                                </div>
                            </div>

                            <!-- File 2 -->
                            <div class="flex items-start sm:items-center border-b border-gray-100 pb-4 file-item" data-file-id="file2" data-file-name="614973494_5511BEQR_H_431511081503133.docx">
                                <div class="flex-shrink-0 mt-1 sm:mt-0">
                                    <input type="checkbox" class="form-checkbox h-5 w-5 text-green-500 rounded border-gray-300 file-checkbox">
                                </div>
                                <div class="ml-4 flex-shrink-0">
                                    <svg class="w-6 h-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4 flex-grow">
                                    <p class="text-sm font-medium text-gray-700 truncate">10. 614973494_5511BEQR_H...431511081503133.docx</p>
                                    <p class="text-xs text-gray-500">Instructions / Guidelines</p>
                                </div>
                                <div class="ml-4 flex-shrink-0 text-right">
                                    <p class="text-sm text-gray-600">Customer</p>
                                    <p class="text-xs text-gray-500">17 Feb, 04:40 AM • 14 KB</p>
                                </div>
                            </div>

                            <!-- File 3 -->
                            <div class="flex items-start sm:items-center border-b border-gray-100 pb-4 file-item" data-file-id="file3" data-file-name="614973494_5511BEBEQR_970454992330531.docx">
                                <div class="flex-shrink-0 mt-1 sm:mt-0">
                                    <input type="checkbox" class="form-checkbox h-5 w-5 text-green-500 rounded border-gray-300 file-checkbox">
                                </div>
                                <div class="ml-4 flex-shrink-0">
                                    <svg class="w-6 h-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4 flex-grow">
                                    <p class="text-sm font-medium text-gray-700 truncate">9. 614973494_5511BEBEQR...970454992330531.docx</p>
                                    <p class="text-xs text-gray-500">Instructions / Guidelines</p>
                                </div>
                                <div class="ml-4 flex-shrink-0 text-right">
                                    <p class="text-sm text-gray-600">Customer</p>
                                    <p class="text-xs text-gray-500">17 Feb, 04:40 AM • 2 MB</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- File Conversion Notice -->
                        <div class="mt-6 text-sm text-gray-600 p-4 bg-gray-50 rounded-lg">
                            If you can't open a file (.pages, .numbers, etc.), download it anyway and use one of these links to convert it to .doc, .docx, .xlsx, etc.: 
                            <a href="https://cloudconvert.com" class="text-blue-600 hover:underline" target="_blank">cloudconvert.com</a> and 
                            <a href="https://online-convert.com" class="text-blue-600 hover:underline" target="_blank">online-convert.com</a>
                        </div>
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
                                <div id="message-tab-slider" class="tab-slider"></div>
                                
                                <button id="client-tab" 
                                        class="relative px-4 py-3 md:px-6 md:py-4 text-green-600 font-medium hover:text-gray-700 focus:outline-none" 
                                        onclick="switchMessageTab('client')" 
                                        role="tab"
                                        data-width="80">
                                    Client
                                </button>
                                <button id="support-tab" 
                                        class="relative px-4 py-3 md:px-6 md:py-4 text-gray-500 hover:text-gray-700 focus:outline-none" 
                                        onclick="switchMessageTab('support')" 
                                        role="tab"
                                        data-width="90">
                                    Support
                                </button>
                            </nav>
                        </div>
                    </div>

                   
                    <!-- Messages Content -->
                    <div class="flex flex-col h-[400px] sm:h-[500px]">
                        <!-- Client Messages Panel -->
                        <div id="client-messages" class="flex-1 overflow-y-auto mb-4 space-y-6">
                            <!-- Writer Message 1 -->
                            <div class="flex">
                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-4 flex-shrink-0 mt-1">
                                    <span class="font-medium text-blue-500">W</span>
                                </div>
                                <div class="max-w-lg rounded-lg p-4 bg-blue-50">
                                    <p class="text-gray-700 mb-3 text-sm sm:text-base">
                                        I have reviewed the order instructions and attached files, and everything is clear. Therefore, I have started working on your paper. It will be ready on time. Nonetheless, I will not hesitate to get in touch if I need your input or clarification.
                                    </p>
                                    <p class="text-gray-700 mb-3 text-sm sm:text-base">
                                        Please let me know whether you would like to receive an outline/first paragraph/plan/list of key ideas/sources in advance via the messages section.
                                    </p>
                                    <p class="text-gray-700 text-sm sm:text-base">
                                        Sincere Regards
                                    </p>
                                    <div class="mt-2 flex justify-between items-center">
                                        <span class="text-xs text-gray-500">Today at 2:15 AM</span>
                                        <span class="text-xs text-green-500 flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Seen
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Client Message 1 -->
                            <div class="flex">
                                <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center mr-4 flex-shrink-0 mt-1">
                                    <span class="font-medium text-gray-500">C</span>
                                </div>
                                <div class="max-w-lg rounded-lg p-4 bg-gray-100">
                                    <p class="text-gray-700 text-sm sm:text-base">
                                        Hello I will respond with details as soon as I reach home. Thank you.
                                    </p>
                                    <div class="mt-2 flex justify-between items-center">
                                        <span class="text-xs text-gray-500">Today at 2:47 AM</span>
                                        <span class="text-xs text-green-500 flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Seen
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Support Messages Panel -->
                        <div id="support-messages" class="hidden flex-1 overflow-y-auto mb-4 space-y-6">
                            <!-- Writer to Support -->
                            <div class="flex">
                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-4 flex-shrink-0 mt-1">
                                    <span class="font-medium text-blue-500">W</span>
                                </div>
                                <div class="max-w-lg rounded-lg p-4 bg-blue-50">
                                    <p class="text-gray-700 mb-3 text-sm sm:text-base">
                                        Hello Support Team,
                                    </p>
                                    <p class="text-gray-700 mb-3 text-sm sm:text-base">
                                        I have a question about the deadline for this order. Is it possible to get a 24-hour extension? The requirements are quite extensive.
                                    </p>
                                    <p class="text-gray-700 text-sm sm:text-base">
                                        Thank you!
                                    </p>
                                    <div class="mt-2 flex justify-between items-center">
                                        <span class="text-xs text-gray-500">Today at 1:45 AM</span>
                                        <span class="text-xs text-green-500 flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Seen
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Support Response -->
                            <div class="flex">
                                <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center mr-4 flex-shrink-0 mt-1">
                                    <span class="font-medium text-green-500">S</span>
                                </div>
                                <div class="max-w-lg rounded-lg p-4 bg-green-50">
                                    <p class="text-gray-700 mb-3 text-sm sm:text-base">
                                        Hello Writer,
                                    </p>
                                    <p class="text-gray-700 mb-3 text-sm sm:text-base">
                                        I've checked this order. The deadline is firm as the client needs this by the specified time. However, I recommend communicating with the client directly about your progress and any challenges.
                                    </p>
                                    <p class="text-gray-700 text-sm sm:text-base">
                                        Let me know if you need any other assistance.
                                    </p>
                                    <div class="mt-2 flex justify-between items-center">
                                        <span class="text-xs text-gray-500">Today at 2:05 AM</span>
                                        <span class="text-xs text-green-500 flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Seen
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Message Input Section -->
                        <div class="border-t pt-4">
                            <div class="flex items-center space-x-4">
                                <div class="flex-1 relative">
                                    <input type="text" 
                                        class="w-full px-4 py-2 pr-10 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200"
                                        placeholder="Type your message...">
                                    <button class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors duration-200">
                                        <i class="fas fa-paperclip"></i>
                                    </button>
                                </div>
                                <button class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors duration-200 flex items-center space-x-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                    <span class="hidden sm:inline">Send</span>
                                </button>
                            </div>
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
                    <p class="text-sm text-green-700 mt-1">Bid clicked successfully.</p>
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
    const tabs = document.querySelectorAll('[id$="-tab"]');
    const messagePanels = document.querySelectorAll('[id$="-messages"]');
    const slider = document.getElementById('message-tab-slider');
    
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
    if (tabName === 'client') {
        document.getElementById('client-messages').classList.remove('hidden');
        document.getElementById('support-messages').classList.add('hidden');
    } else if (tabName === 'support') {
        document.getElementById('client-messages').classList.add('hidden');
        document.getElementById('support-messages').classList.remove('hidden');
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
    // Create a full-screen modal for expanded instructions view
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

// Bid Toaster Functions
function showBidToaster() {
    // Create toaster if it doesn't exist
    let toaster = document.getElementById('bidToaster');
    if (!toaster) {
        toaster = document.createElement('div');
        toaster.id = 'bidToaster';
        toaster.className = 'fixed bottom-4 left-4 z-50 transform translate-y-full transition-transform duration-300 ease-in-out';
        toaster.innerHTML = `
            <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded shadow-lg flex items-start max-w-sm">
                <div class="text-green-500 mr-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div>
                    <p class="font-medium text-green-800">Success!</p>
                    <p class="text-sm text-green-700 mt-1">Bid clicked successfully.</p>
                </div>
                <button onclick="hideBidToaster()" class="ml-auto text-green-500 hover:text-green-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `;
        document.body.appendChild(toaster);
    }

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
    // In a real implementation, you would fetch the file from the server
    console.log(`Downloading file: ${fileName} (ID: ${fileId})`);
    
    // Show download toast
    showDownloadToast(1, fileName);
    
    // Simulate file download (in a real implementation, you would create a download link)
    setTimeout(() => {
        console.log(`Downloaded ${fileName}`);
    }, 1000);
}

function downloadSelectedFiles() {
    const selectedFiles = document.querySelectorAll('.file-checkbox:checked');
    if (selectedFiles.length === 0) {
        alert('Please select at least one file to download');
        return;
    }

    // Get file names of selected files
    const selectedFileNames = Array.from(selectedFiles).map(checkbox => {
        const fileItem = checkbox.closest('.file-item');
        return fileItem.dataset.fileName;
    });

    console.log(`Downloading ${selectedFiles.length} file(s):`, selectedFileNames);
    
    // Show download toast
    showDownloadToast(selectedFiles.length);
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

// Add responsive text sizing based on viewport
function setupResponsiveText() {
    function adjustTextSize() {
        const vw = Math.max(document.documentElement.clientWidth || 0, window.innerWidth || 0);
        
        // Set base size classes based on viewport width
        const contentArea = document.body;
        
        if (vw < 640) { // Mobile
            contentArea.classList.add('text-sm');
            contentArea.classList.remove('text-base', 'text-lg');
        } else if (vw < 1024) { // Tablet
            contentArea.classList.add('text-base');
            contentArea.classList.remove('text-sm', 'text-lg');
        } else { // Desktop
            contentArea.classList.add('text-base');
            contentArea.classList.remove('text-sm', 'text-lg');
        }
    }
    
    // Run on page load
    adjustTextSize();
    
    // Run on window resize
    window.addEventListener('resize', adjustTextSize);
}

// Initialize when the DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    // Set initial tab - always start with instructions
    switchTab('instructions');
    
    // Initialize message tabs (default to client)
    if (document.getElementById('client-tab')) {
        switchMessageTab('client');
    }
    
    // Initialize Place Bid button
    const placeBidBtn = document.getElementById('placeBidBtn');
    if (placeBidBtn) {
        placeBidBtn.addEventListener('click', () => {
            console.log('Bid placed for $65');
            showBidToaster();
        });
    }
    
    // Initialize file selection handling
    handleFileSelection();
    
    // Setup responsive text sizing
    setupResponsiveText();
    
    // Add styles for tab slider if not already in stylesheet
    if (!document.querySelector('style#tab-slider-styles')) {
        const style = document.createElement('style');
        style.id = 'tab-slider-styles';
        style.textContent = `
            .tab-slider {
                position: absolute;
                bottom: -1px;
                height: 2px;
                background-color: #22C55E;
                transition: all 0.3s ease;
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
                margin-bottom: 0.5rem;
                z-index: 50;
            }
            
            @media (max-width: 640px) {
                .prose p {
                    font-size: 0.875rem;
                    line-height: 1.5;
                }
            }
            
            @media (min-width: 1536px) {
                .prose p {
                    font-size: 1.05rem;
                    line-height: 1.7;
                }
            }
        `;
        document.head.appendChild(style);
    }
});
</script>
@endsection