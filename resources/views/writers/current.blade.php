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
    .status-unconfirmed { color: #3b82f6; font-weight: bold; }
    .status-done { color: #f59e0b; font-weight: bold; }
    .status-delivered { color: #ef4444; font-weight: bold; }
    .truncate-text { max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    
    /* Time remaining colors */
    .time-safe { color: #16a34a; }
    .time-warning { color: #f59e0b; }
    .time-urgent { color: #ef4444; }
    .time-overdue { color: #ef4444; font-weight: bold; }
    .time-completed { color: #6b7280; }
    
    /* Hover effect */
    .hover-scale {
        transition: all 0.3s ease;
    }
    .hover-scale:hover {
        transform: scale(1.01);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    
    /* Animation */
    .animate-slide-in {
        animation: slide-in 0.3s ease-out forwards;
    }
    @keyframes slide-in {
        0% {
            opacity: 0;
            transform: translateY(10px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* All section titles should be visible on all screen sizes */
    .text-2xl, .text-lg {
        display: block !important;
    }
    
    /* Base order card styles */
    .order-card { 
        padding: 8px; 
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
</style>

<!-- Main Content -->
<main class="flex-1 px-4 lg:px-8 pb-8 lg:ml-72 transition-all duration-300 pt-16">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Current Orders</h1>
    </div>

    <!-- Active Orders (CONFIRMED, UNCONFIRMED) -->
    <div class="bg-white shadow-md rounded-lg p-6 animate-slide-in">
        <h3 class="text-lg font-semibold text-gray-700 mb-3">Active Orders</h3>
        
        <div class="font-semibold bg-gray-100 p-3 rounded-md grid-header">
            <div>Order ID</div>
            <div>Topic Title</div>
            <div>Discipline</div>
            <div>Deadline</div>
            <div>Notes</div>
            <div>Pages</div>
            <div>Cost</div>
        </div>
        
        @if(count($activeOrders) > 0)
            @foreach($activeOrders as $order)
            <a href="{{ route('assigned', ['id' => $order->id]) }}" class="border rounded-lg p-4 pt-2 mb-2 bg-gray-50 hover-scale order-card">
                    <div>
                        <div class="text-blue-600 font-semibold text-lg">#{{ $order->order_number }}</div>
                        <div class="status-{{ strtolower($order->status) }}">{{ $order->status }}</div>
                    </div>
                    <div class="truncate-text">{{ $order->title }}</div>
                    <div class="truncate-text">{{ $order->discipline }}</div>
                    <div class="text-gray-600 font-bold time-{{ $order->time_status }}">{{ $order->time_remaining }}</div>
                    <div class="truncate-text">{{ Str::limit($order->customer_comments, 30) }}</div>
                    <div>{{ $order->number_of_pages ?? '-' }}</div>
                    <div class="text-gray-800 font-semibold text-lg">${{ number_format($order->price, 2) }}</div>
                </a>
            @endforeach
        @else
            <div class="border rounded-lg p-6 text-center text-gray-500">
                <p>You don't have any active orders at the moment.</p>
            </div>
        @endif
    </div>

    <!-- Completed Orders (DONE, DELIVERED) -->
    <div class="bg-white shadow-md rounded-lg p-6 mt-6 animate-slide-in">
        <h3 class="text-lg font-semibold text-gray-700 mb-3">Done, Delivered Orders</h3>
        
        <div class="font-semibold bg-gray-100 p-3 rounded-md grid-header">
            <div>Order ID</div>
            <div>Topic Title</div>
            <div>Discipline</div>
            <div>Deadline</div>
            <div>Notes</div>
            <div>Pages</div>
            <div>Cost</div>
        </div>
        
        @if(count($completedOrders) > 0)
            @foreach($completedOrders as $order)
                <a href="{{ route('assigned', ['id' => $order->id]) }}" class="border rounded-lg p-4 pt-2 mb-2 bg-gray-50 hover-scale order-card">
                    <div>
                        <div class="text-blue-600 font-semibold text-lg">#{{ $order->order_number }}</div>
                        <div class="status-{{ strtolower($order->status) }}">{{ $order->status }}</div>
                    </div>
                    <div class="truncate-text">{{ $order->title }}</div>
                    <div class="truncate-text">{{ $order->discipline }}</div>
                    <div class="text-gray-600 font-bold">Completed</div>
                    <div class="truncate-text">{{ Str::limit($order->customer_comments, 30) }}</div>
                    <div>{{ $order->number_of_pages ?? '-' }}</div>
                    <div class="text-gray-800 font-semibold text-lg">${{ number_format($order->price, 2) }}</div>
                </a>
            @endforeach
        @else
            <div class="border rounded-lg p-6 text-center text-gray-500">
                <p>You don't have any completed orders yet.</p>
            </div>
        @endif
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize any JavaScript functionality here if needed
    });
</script>

@endsection