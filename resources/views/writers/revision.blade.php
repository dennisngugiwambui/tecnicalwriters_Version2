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
    .truncate-text { max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    
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
        <h1 class="text-2xl font-bold text-gray-800">TechnicalWriters</h1>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6 animate-slide-in">
        <h3 class="text-lg font-semibold text-gray-700 mb-3">Orders on Revision status</h3>
        
        <div class="font-semibold bg-gray-100 p-3 rounded-md grid-header">
            <div>Order ID</div>
            <div>Topic Title</div>
            <div>Discipline</div>
            <div>Deadline</div>
            <div>Notes</div>
            <div>Pages</div>
            <div>Cost</div>
        </div>
        
        @if(isset($revisedOrders) && count($revisedOrders) > 0)
            @foreach($revisedOrders as $order)
            <div class="border rounded-lg p-4 pt-2 bg-gray-50 hover-scale order-card mt-2">
                <div>
                    <div class="text-sm text-green-600 font-medium mb-1">Revision</div>
                    <div class="text-gray-600">#{{ $order->id }}</div>
                </div>
                <div class="truncate-text">{{ $order->title }}</div>
                <div class="truncate-text">{{ $order->discipline }}</div>
                <div class="text-gray-600 font-bold">
                    @php
                        $deadline = \Carbon\Carbon::parse($order->deadline);
                        $now = \Carbon\Carbon::now();
                        $diff = $now->diff($deadline);
                        
                        if ($now->gt($deadline)) {
                            echo 'Overdue';
                        } else if ($diff->days > 0) {
                            echo $diff->days . 'd ' . $diff->h . 'h';
                        } else if ($diff->h > 0) {
                            echo $diff->h . 'h ' . $diff->i . 'm';
                        } else {
                            echo $diff->i . 'm';
                        }
                    @endphp
                </div>
                <div class="truncate-text">{{ $order->notes }}</div>
                <div>{{ $order->pages }}</div>
                <div class="text-gray-800 font-semibold text-lg">${{ number_format($order->cost, 2) }}</div>
            </div>
            @endforeach
        @else
            <div class="border rounded-lg p-6 text-center bg-gray-50 mt-4">
                <p class="text-gray-500">No orders under revision currently.</p>
            </div>
        @endif
        </div
</main>

@endsection