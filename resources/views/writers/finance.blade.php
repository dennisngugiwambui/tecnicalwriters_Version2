@extends('writers.app')

@section('content')
    <div class="flex h-full pt-20 px-6 lg:px-8">
        <main class="flex-1 px-4 lg:px-8 pb-8 lg:ml-72 transition-all duration-300 pt-16 bg-white">
            <h3 class="text-xl font-semibold">Finance</h3>
            
            <!-- Balance Section -->
            <div class="flex flex-col md:flex-row justify-between items-center mt-4 bg-green-100 p-4 rounded-lg border border-green-300">
                <p class="text-gray-700 text-lg">Your balance: <span class="text-black font-semibold text-xl">$18</span></p>
                <button class="bg-green-500 text-white px-6 py-2 rounded-md font-semibold mt-2 md:mt-0">Request payment</button>
            </div>
            
            <p class="text-gray-600 text-sm mt-2">
                You can request payment twice a month from the <span class="font-bold">30th/31st day to the 3rd</span> and from the 
                <span class="font-bold">14th to the 18th</span> day (start-end at 8 PM (GMT+0)).<br>
                Your payment request will be processed up to and including the 5th or the 20th day of the month accordingly.
            </p>
            <p class="text-green-600 text-sm mt-1"><a href="#" class="underline">Check our Payment Guide</a> for more details.</p>
            
            <!-- Tab Navigation -->
            <div class="mt-6 border-b flex space-x-6 text-lg">
                <button class="tab-button text-black font-semibold border-b-2 border-yellow-500 pb-2" data-tab="unrequested">Unrequested</button>
                <button class="tab-button text-gray-400 font-semibold pb-2" data-tab="requested">History</button>
                <button class="tab-button text-gray-400 font-semibold pb-2" data-tab="accounts">Accounts</button>
            </div>
            
            <!-- Unrequested Section (Default Open) -->
            <div id="unrequested" class="tab-content mt-6">
                <h4 class="text-lg font-semibold">WRITERA LIMITED</h4>
                <div class="block md:block">
                    <table class="w-full border-collapse border border-gray-200 mt-4">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border border-gray-200 p-2 text-left">Date</th>
                                <th class="border border-gray-200 p-2 text-left">Order</th>
                                <th class="border border-gray-200 p-2 text-left hidden md:table-cell">Transaction Type</th>
                                <th class="border border-gray-200 p-2 text-left hidden md:table-cell">Comments</th>
                                <th class="border border-gray-200 p-2 text-left">Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="border border-gray-200 p-2">18 Feb 2025, 4:24 PM</td>
                                <td class="border border-gray-200 p-2">614312020</td>
                                <td class="border border-gray-200 p-2 font-bold hidden md:table-cell">order approved (0 pages)</td>
                                <td class="border border-gray-200 p-2 hidden md:table-cell">Data analysis and reports</td>
                                <td class="border border-gray-200 p-2 text-green-600">+ $40.00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 p-4 bg-white shadow-md rounded-md flex justify-between">
                    <p class="text-green-600 font-bold text-lg">$34.00 COMPLETED ORDERS</p>
                    <p class="text-red-600 font-bold text-lg">- $16.00 FINES</p>
                    <p class="text-black font-bold text-lg">= $18.00 TOTAL</p>
                </div>
            </div>

            <!-- History Section -->
            <div id="requested" class="tab-content mt-6 hidden">
                <h4 class="text-lg font-semibold">Payment History</h4>

                <!-- Search and Filter Section -->
                <div class="p-4 rounded-md mt-4">
                    <div class="flex justify-between items-center">
                        <p class="font-semibold text-gray-700 flex items-center">
                            <i class="fas fa-filter mr-2"></i> Period
                        </p>
                        <button id="toggleSearch" class="md:hidden bg-green-500 text-white px-4 py-2 rounded-md">Toggle Search</button>
                    </div>
                    <div id="searchFilters" class="mt-4 md:block hidden">
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" checked class="form-checkbox text-green-500">
                                <span>All times</span>
                            </label>
                            <select class="border rounded-md p-2 w-full">
                                <option>Select company</option>
                            </select>
                            <input type="date" class="border rounded-md p-2 w-full">
                            <input type="date" class="border rounded-md p-2 w-full">
                            <button class="bg-green-500 text-white px-4 py-2 rounded-md w-full"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </div>
                <div class="block md:block">
                    <table class="w-full border-collapse border border-gray-200 mt-4">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border border-gray-200 p-2 text-left">Date</th>
                                <th class="border border-gray-200 p-2 text-left">Request Number</th>
                                <th class="border border-gray-200 p-2 text-left">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="border border-gray-200 p-2">18 Feb 2025, 4:24 PM</td>
                                <td class="border border-gray-200 p-2">614312020</td>
                                <td class="border border-gray-200 p-2 text-green-600">$40.00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Accounts Section -->
            <div id="accounts" class="tab-content mt-6 hidden">
                <h4 class="text-lg font-semibold">Account Details</h4>
                <p class="text-gray-600">Manage your payment details below.</p>
                <div class="mt-4 p-4 bg-white shadow-md rounded-md max-w-lg">
                    <label class="block text-gray-700 font-semibold">MPesa Number</label>
                    <input type="text" id="mpesa-number" value="+254712345678" class="border rounded-md p-2 w-full">
                    <button id="update-button" class="mt-4 px-4 py-2 bg-green-500 text-white rounded-md w-full">Update</button>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        $(document).ready(function() {
            $('.tab-button').click(function() {
                $('.tab-button').removeClass('text-black border-b-2 border-yellow-500');
                $(this).addClass('text-black border-b-2 border-yellow-500');
                $('.tab-content').hide();
                $('#' + $(this).data('tab')).show();
            });
            
            $('#unrequested').show();

            $('#update-button').click(function() {
                let toast = $('<div class="fixed bottom-5 right-5 bg-red-500 text-white px-4 py-2 rounded shadow-lg">Warning: Changing your MPesa number is a sensitive action and may require support verification. Please notify the support team before proceeding.</div>');
                $('body').append(toast);
                setTimeout(() => toast.fadeOut(400, function() { $(this).remove(); }), 5000);
            });

            $('#toggleSearch').click(function() {
                $('#searchFilters').toggleClass('hidden');
            });
        });
    </script>
@endsection
