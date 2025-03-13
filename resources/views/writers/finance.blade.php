@extends('writers.app')

@section('content')
    <div class="flex h-full pt-20 px-6 lg:px-8">
        <main class="flex-1 px-4 lg:px-8 pb-8 lg:ml-72 transition-all duration-300 pt-16 bg-white">
            <h3 class="text-xl font-semibold">Finance</h3>
            
            <!-- Balance Section -->
            <div class="flex flex-col md:flex-row justify-between items-center mt-4 bg-green-100 p-4 rounded-lg border border-green-300">
                <p class="text-gray-700 text-lg">Your balance: <span class="text-black font-semibold text-xl">${{ number_format($availableBalance, 2) }}</span></p>
                <button id="request-payment-btn" class="bg-green-500 text-white px-6 py-2 rounded-md font-semibold mt-2 md:mt-0">Request payment</button>
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
                <div class="overflow-x-auto">
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
                            @forelse($unRequestedEarnings as $earning)
                                <tr>
                                    <td class="border border-gray-200 p-2">{{ $earning['date'] }}</td>
                                    <td class="border border-gray-200 p-2">{{ $earning['id'] }}</td>
                                    <td class="border border-gray-200 p-2 font-bold hidden md:table-cell">{{ $earning['transaction_type'] }}</td>
                                    <td class="border border-gray-200 p-2 hidden md:table-cell">{{ $earning['description'] }}</td>
                                    <td class="border border-gray-200 p-2 text-green-600">+ ${{ number_format($earning['amount'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="border border-gray-200 p-2 text-center text-gray-600">No unrequested earnings found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 p-4 bg-white shadow-md rounded-md flex flex-col md:flex-row justify-between">
                    <p class="text-green-600 font-bold text-lg">${{ number_format($totalEarnings, 2) }} COMPLETED ORDERS</p>
                    <p class="text-red-600 font-bold text-lg">- ${{ number_format($totalFines, 2) }} FINES</p>
                    <p class="text-black font-bold text-lg">= ${{ number_format($availableBalance, 2) }} TOTAL</p>
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
                        <form action="{{ route('finance') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" checked name="all_time" value="1" class="form-checkbox text-green-500">
                                <span>All times</span>
                            </label>
                            <select name="company" class="border rounded-md p-2 w-full">
                                <option value="">Select company</option>
                                <option value="writera">Writera Limited</option>
                            </select>
                            <input type="date" name="start_date" class="border rounded-md p-2 w-full">
                            <input type="date" name="end_date" class="border rounded-md p-2 w-full">
                            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-md w-full"><i class="fas fa-search"></i></button>
                        </form>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse border border-gray-200 mt-4">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border border-gray-200 p-2 text-left">Date</th>
                                <th class="border border-gray-200 p-2 text-left">Request Number</th>
                                <th class="border border-gray-200 p-2 text-left">Status</th>
                                <th class="border border-gray-200 p-2 text-left">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($withdrawalHistory as $withdrawal)
                                <tr>
                                    <td class="border border-gray-200 p-2">{{ $withdrawal['date'] }}</td>
                                    <td class="border border-gray-200 p-2">{{ $withdrawal['id'] }}</td>
                                    <td class="border border-gray-200 p-2">{{ $withdrawal['status'] }}</td>
                                    <td class="border border-gray-200 p-2 text-green-600">${{ number_format($withdrawal['amount'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="border border-gray-200 p-2 text-center text-gray-600">No payment history found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Accounts Section -->
            <div id="accounts" class="tab-content mt-6 hidden">
                <h4 class="text-lg font-semibold">Account Details</h4>
                <p class="text-gray-600">Manage your payment details below.</p>
                <div class="mt-4 p-4 bg-white shadow-md rounded-md max-w-lg">
                    <form id="payment-details-form" action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-gray-700 font-semibold mb-2">Payment Method</label>
                            <select id="payment-method" name="payment_method" class="border rounded-md p-2 w-full">
                                <option value="mpesa" {{ $user->writerProfile && $user->writerProfile->payment_method == 'mpesa' ? 'selected' : '' }}>M-Pesa</option>
                                <option value="bank" {{ $user->writerProfile && $user->writerProfile->payment_method == 'bank' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="paypal" {{ $user->writerProfile && $user->writerProfile->payment_method == 'paypal' ? 'selected' : '' }}>PayPal</option>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 font-semibold mb-2" id="payment-details-label">
                                {{ $user->writerProfile && $user->writerProfile->payment_method == 'mpesa' ? 'M-Pesa Number' : 
                                   ($user->writerProfile && $user->writerProfile->payment_method == 'bank' ? 'Bank Account Details' : 'PayPal Email') }}
                            </label>
                            <input type="text" id="payment-details" name="payment_details" value="{{ $user->writerProfile ? $user->writerProfile->payment_details : '' }}" class="border rounded-md p-2 w-full">
                        </div>
                        
                        <button type="submit" id="update-button" class="mt-4 px-4 py-2 bg-green-500 text-white rounded-md w-full">Update</button>
                    </form>
                </div>
            </div>
            
            <!-- Payment Request Modal -->
            <div id="payment-request-modal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
                <div class="absolute inset-0 bg-black opacity-50"></div>
                <div class="bg-white rounded-lg p-8 max-w-md w-full relative z-10">
                    <h3 class="text-xl font-bold mb-4">Request Payment</h3>
                    <form id="payment-request-form" action="{{ route('finance.request-payment') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-gray-700 font-semibold mb-2">Amount</label>
                            <input type="number" name="amount" id="request-amount" value="{{ number_format($availableBalance, 2, '.', '') }}" min="10" max="{{ $availableBalance }}" step="0.01" class="border rounded-md p-2 w-full" readonly>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 font-semibold mb-2">Payment Method</label>
                            <select name="payment_method" class="border rounded-md p-2 w-full">
                                @if($user->writerProfile && $user->writerProfile->payment_method)
                                    <option value="{{ $user->writerProfile->payment_method }}">
                                        {{ ucfirst($user->writerProfile->payment_method) }} 
                                        ({{ $user->writerProfile->payment_details }})
                                    </option>
                                @else
                                    <option value="">Please update payment details first</option>
                                @endif
                            </select>
                        </div>
                        
                        <div class="flex justify-end space-x-4">
                            <button type="button" id="cancel-request" class="px-4 py-2 border border-gray-300 rounded-md">Cancel</button>
                            <button type="submit" id="confirm-request" class="px-4 py-2 bg-green-500 text-white rounded-md" {{ (!$user->writerProfile || !$user->writerProfile->payment_method) ? 'disabled' : '' }}>Request Payment</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.css" rel="stylesheet">
    
    <script>
        $(document).ready(function() {
            // Configure toastr
            toastr.options = {
                closeButton: true,
                progressBar: true,
                positionClass: "toast-top-right",
                timeOut: 5000
            };
            
            // Tab switching
            $('.tab-button').click(function() {
                $('.tab-button').removeClass('text-black border-b-2 border-yellow-500').addClass('text-gray-400');
                $(this).removeClass('text-gray-400').addClass('text-black border-b-2 border-yellow-500');
                $('.tab-content').hide();
                $('#' + $(this).data('tab')).show();
            });
            
            // Default tab shown
            $('#unrequested').show();
            
            // Payment method change
            $('#payment-method').change(function() {
                updatePaymentDetailsLabel();
            });
            
            function updatePaymentDetailsLabel() {
                let method = $('#payment-method').val();
                let label = '';
                
                if (method === 'mpesa') {
                    label = 'M-Pesa Number';
                } else if (method === 'bank') {
                    label = 'Bank Account Details';
                } else if (method === 'paypal') {
                    label = 'PayPal Email';
                }
                
                $('#payment-details-label').text(label);
            }
            
            // Payment details update form
            $('#payment-details-form').submit(function(e) {
                e.preventDefault();
                
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            toastr.success('Payment details updated successfully');
                        } else {
                            toastr.error(response.message || 'Failed to update payment details');
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Failed to update payment details';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        toastr.error(errorMessage);
                    }
                });
            });
            
            // Request payment
            $('#request-payment-btn').click(function() {
                $('#payment-request-modal').removeClass('hidden');
            });
            
            $('#cancel-request').click(function() {
                $('#payment-request-modal').addClass('hidden');
            });
            
            // Payment request form
            $('#payment-request-form').submit(function(e) {
                e.preventDefault();
                
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#payment-request-modal').addClass('hidden');
                        if (response.success) {
                            toastr.success('Payment request submitted successfully');
                            // Reload page after short delay to show updated data
                            setTimeout(function() {
                                window.location.reload();
                            }, 2000);
                        } else {
                            toastr.error(response.message || 'Failed to submit payment request');
                        }
                    },
                    error: function(xhr) {
                        $('#payment-request-modal').addClass('hidden');
                        let errorMessage = 'Failed to submit payment request';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        toastr.error(errorMessage);
                    }
                });
            });
            
            // Toggle search on mobile
            $('#toggleSearch').click(function() {
                $('#searchFilters').toggleClass('hidden');
            });
        });
    </script>
@endsection