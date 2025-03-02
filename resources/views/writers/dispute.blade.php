```php
@extends('writers.app')
@section('content')

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #16a34a !important;
    }
    .select2-container--default .select2-selection--single {
        height: 40px;
        display: flex;
        align-items: center;
        border-color: #e5e7eb;
        border-radius: 0.5rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
    }
    .status-confirmed { color: #16a34a; font-weight: bold; }
    .status-done { color: #f59e0b; font-weight: bold; }
    .status-delivered { color: #ef4444; font-weight: bold; }
    .status-dispute { color: #dc2626; font-weight: bold; }
    .truncate-text { max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    
    /* All section titles should be visible on all screen sizes */
    .text-2xl, .text-lg {
        display: block !important;
    }
    
    /* Base order card styles */
    .order-card { 
        padding: 8px;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    
    .order-card:hover {
        background-color: #f3f4f6;
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    
    /* Default desktop styles - all columns visible */
    @media (min-width: 1024px) {
        .order-card {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
        }
        .grid-header {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
        }
    }
    
    /* Tablet/medium screen styles */
    @media (min-width: 768px) and (max-width: 1023px) {
        .order-card {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
        }
        .grid-header {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
        }
        .order-card > div:nth-child(5),
        .order-card > div:nth-child(6),
        .grid-header > div:nth-child(5),
        .grid-header > div:nth-child(6) {
            display: none;
        }
    }
    
    /* Mobile styles - only show order ID, deadline and cost */
    @media (max-width: 767px) {
        .order-card {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 10px;
        }
        /* Hide all columns except order ID (1st), deadline (4th), and cost (7th) */
        .order-card > div:nth-child(2),
        .order-card > div:nth-child(3),
        .order-card > div:nth-child(5),
        .order-card > div:nth-child(6) {
            display: none;
        }
        /* Ensure deadline and cost are visible */
        .order-card > div:nth-child(4),
        .order-card > div:nth-child(7) {
            display: block;
        }
        
        /* Custom header for mobile */
        .grid-header {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
        }
        .grid-header > div:not(:nth-child(1)):not(:nth-child(4)):not(:nth-child(7)) {
            display: none;
        }
    }
    
    /* Empty state styles */
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
    }
    
    .empty-state-icon {
        margin: 0 auto;
        height: 3rem;
        width: 3rem;
        color: #9ca3af;
    }
    
    .empty-state-title {
        margin-top: 1rem;
        font-size: 1.125rem;
        font-weight: 500;
        color: #4b5563;
    }
    
    .empty-state-description {
        margin-top: 0.5rem;
        color: #6b7280;
    }
</style>

<!-- Main Content -->
<main class="flex-1 px-4 lg:px-8 pb-8 lg:ml-72 transition-all duration-300 pt-16">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Disputed Orders</h1>
        <div class="text-sm text-gray-600">
            <span class="font-semibold">Total:</span> {{ $disputeOrders->count() }} orders
        </div>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-700 mb-3">Orders on Dispute Status</h3>
        
        <div class="font-semibold bg-gray-100 p-3 rounded-md grid-header">
            <div>Order ID</div>
            <div>Topic Title</div>
            <div>Discipline</div>
            <div>Deadline</div>
            <div>Notes</div>
            <div>Pages</div>
            <div>Cost</div>
        </div>
        
        @if($disputeOrders->count() > 0)
            @foreach($disputeOrders as $order)
                <a href="{{ route('assigned', ['id' => $order->id]) }}" class="block mb-2">
                    <div class="border rounded-lg p-4 pt-2 bg-gray-50 order-card mt-2">
                        <div>
                            <div class="text-sm text-red-600 font-medium mb-1">Dispute</div>
                            <div class="text-gray-600">#{{ $order->id }}</div>
                        </div>
                        <div class="truncate-text">{{ $order->title }}</div>
                        <div class="truncate-text">{{ $order->discipline ?? 'General' }}</div>
                        <div class="text-gray-600 font-bold">
                            {{ Carbon\Carbon::parse($order->deadline)->diffForHumans() }}
                        </div>
                        <div class="truncate-text">{{ $order->notes ?? 'No notes available' }}</div>
                        <div>{{ $order->pages ?? 'N/A' }}</div>
                        <div class="text-gray-800 font-semibold text-lg">${{ number_format($order->price ?? 0, 2) }}</div>
                    </div>
                </a>
            @endforeach
        @else
            <div class="empty-state">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="empty-state-icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="empty-state-title">No disputed orders</h3>
                <p class="empty-state-description">You don't have any orders under dispute at the moment.</p>
            </div>
        @endif
    </div>
    
    @if($disputeOrders->count() > 0)
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mt-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700">
                    <strong>Important:</strong> Orders under dispute require special attention. Please contact support for more information about these orders.
                </p>
            </div>
        </div>
    </div>
    @endif
</main>

<script>
    // Any additional JavaScript can go here
    document.addEventListener("DOMContentLoaded", function() {
        // You can add any initialization code here if needed
    });
</script>

@endsection