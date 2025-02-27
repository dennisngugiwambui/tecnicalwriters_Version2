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
        margin-bottom: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .order-card:hover {
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
        padding: 40px 20px;
        background-color: #f9fafb;
        border-radius: 8px;
        border: 1px dashed #d1d5db;
    }
    
    .empty-state svg {
        width: 64px;
        height: 64px;
        margin: 0 auto 16px;
        color: #9ca3af;
    }
</style>

<!-- Main Content -->
<main class="flex-1 px-4 lg:px-8 pb-8 lg:ml-72 transition-all duration-300 pt-16">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-bold text-gray-800">My Bids</h1>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6 animate-slide-in">
        <h3 class="text-lg font-semibold text-gray-700 mb-3">Orders You've Bid On</h3>
        
        @if(isset($bidOrders) && $bidOrders->count() > 0)
            <div class="font-semibold bg-gray-100 p-3 rounded-md grid-header">
                <div>Order ID</div>
                <div>Topic Title</div>
                <div>Discipline</div>
                <div>Deadline</div>
                <div>Notes</div>
                <div>Pages</div>
                <div>Cost</div>
            </div>
            
            @foreach($bidOrders as $order)
                @php
                    // Calculate time remaining until deadline
                    $deadline = \Carbon\Carbon::parse($order->deadline);
                    $now = \Carbon\Carbon::now();
                    $diff = $now->diff($deadline);
                    
                    $timeRemaining = '';
                    if ($diff->days > 0) {
                        $timeRemaining .= $diff->days . 'd ';
                    }
                    if ($diff->h > 0) {
                        $timeRemaining .= $diff->h . 'h ';
                    }
                    $timeRemaining .= $diff->i . 'm';
                    
                    // Get the first bid for this user
                    $userBid = $order->bids->first();
                @endphp
                <a href="{{ route('availableOrderDetails', $order->id) }}" class="block">
                    <div class="border rounded-lg p-4 pt-2 bg-gray-50 hover-scale order-card">
                        <div>
                            <div class="text-sm text-blue-600 font-medium mb-1">Bid Placed</div>
                            <div class="text-gray-600">#{{ $order->id }}</div>
                        </div>
                        <div class="truncate-text" title="{{ $order->title }}">{{ $order->title }}</div>
                        <div class="truncate-text" title="{{ $order->discipline }}">{{ $order->discipline }}</div>
                        <div class="text-gray-600 font-bold">{{ $timeRemaining }}</div>
                        <div class="truncate-text" title="{{ $order->customer_comments }}">
                            {{ Str::limit($order->customer_comments, 30) ?? 'No notes' }}
                        </div>
                        <div>{{ $order->task_size ?? 'N/A' }}</div>
                        <div class="text-gray-800 font-semibold text-lg">${{ number_format($order->price, 2) }}</div>
                    </div>
                </a>
            @endforeach
            
        @else
            <div class="empty-state">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h4 class="text-lg font-medium text-gray-700 mb-2">No Bids Yet</h4>
                <p class="text-gray-500 mb-4">You haven't placed any bids on available orders.</p>
                <a href="{{ route('home') }}" class="inline-block px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors duration-200">
                    Browse Available Orders
                </a>
            </div>
        @endif
    </div>
</main>

@endsection