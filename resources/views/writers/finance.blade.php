@extends('writers.app')
@section('content')

<main class="flex-1 px-4 lg:px-8 pb-8 transition-all duration-300 pt-24">
    <div class="bg-white shadow-lg rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Finance</h2>
        <div class="bg-green-100 text-green-700 p-4 rounded flex justify-between items-center">
            <span>Your balance: <strong>$44</strong></span>
            <button class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Request payment</button>
        </div>
        <p class="text-sm text-gray-600 mt-2">You can request payment twice a month from the <strong>30th/31st to the 3rd</strong> and from the <strong>14th to the 18th</strong>.</p>
        
        <div class="mt-6">
            <div class="flex space-x-4 border-b pb-2 text-gray-500">
                <span class="font-semibold text-black border-b-2 border-yellow-500 pb-1">Unrequested</span>
                <span class="cursor-pointer">History</span>
                <span class="cursor-pointer">Accounts</span>
            </div>
        </div>
        
        <div class="bg-yellow-50 p-4 rounded-lg mt-4">
            <h3 class="font-semibold text-lg">WRITERA LIMITED</h3>
            <div class="mt-4">
                <div class="flex justify-between border-b py-2">
                    <span class="text-gray-500">Date</span>
                    <span class="text-gray-500">Order</span>
                    <span class="text-gray-500">Transaction Type</span>
                    <span class="text-gray-500">Comments</span>
                    <span class="text-gray-500">Value</span>
                </div>
                <div class="flex justify-between py-2 text-gray-700">
                    <span>19 Feb 2025, 3:24 AM</span>
                    <span>614312020</span>
                    <span class="font-semibold">Order approved (0 pages)</span>
                    <span>Data analysis and reports</span>
                    <span class="text-green-600">+ $40.00</span>
                </div>
                <div class="flex justify-between py-2 text-gray-700">
                    <span>13 Feb 2025, 4:56 PM</span>
                    <span>602218958</span>
                    <span class="font-semibold">Bonus</span>
                    <span>Requested writer</span>
                    <span class="text-green-600">+ $4.00</span>
                </div>
            </div>
            
            <div class="flex justify-between items-center mt-4 border-t pt-4 text-lg font-semibold">
                <span class="text-gray-700">Completed Orders</span>
                <span class="text-green-600">$40.00</span>
                <span class="text-gray-700">Bonuses</span>
                <span class="text-green-600">+ $4.00</span>
                <span class="text-gray-900">= $44.00</span>
            </div>
        </div>
    </div>
</main>


@endsection