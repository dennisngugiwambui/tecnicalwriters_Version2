@extends('writers.app')

@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<style>
    .status-completed { color: #16a34a; font-weight: bold; }
    .status-paid { color: #059669; font-weight: bold; }
    .status-finished { color: #0ea5e9; font-weight: bold; }
    .truncate-text { max-inline-size: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    
    /* Tooltip styles */
    .tooltip {
        position: relative;
        display: inline-block;
        font-size: 1.5rem; /* Increase emoji size */
    }
    .tooltip .tooltiptext {
        visibility: hidden;
        inline-size: 150px;
        background-color: #555;
        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 5px 0;
        position: absolute;
        z-index: 1;
        inset-block-end: 125%; /* Position the tooltip above the text */
        inset-inline-start: 50%;
        margin-inline-start: -75px;
        opacity: 0;
        transition: opacity 0.3s;
    }
    
    /* Rating icons */
    .rating-icon {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    
    .rating-above {
        background-color: #16a34a;
        color: white;
        border: 1px solid #16a34a;
    }
    
    .rating-expected {
        background-color: #3b82f6;
        color: white;
        border: 1px solid #3b82f6;
    }
    
    .rating-below {
        background-color: #ef4444;
        color: white;
        border: 1px solid #ef4444;
    }
    
    .rating-icon .tooltiptext {
        visibility: hidden;
        width: 150px;
        background-color: #555;
        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 5px;
        position: absolute;
        z-index: 10;
        bottom: 125%;
        left: 50%;
        margin-left: -75px;
        opacity: 0;
        transition: opacity 0.3s;
    }
    
    .rating-icon:hover .tooltiptext {
        visibility: visible;
        opacity: 1;
    }
    .tooltip:hover .tooltiptext {
        visibility: visible;
        opacity: 1;
    }

    /* Media queries to hide emoji on mobile */
    @media (max-width: 1024px) {
        .tooltip {
            display: none;
        }
    }

    /* Add margin to the filter section */
    .filter-section {
        margin-bottom: 2rem;
    }
    
    /* Add card styles */
    .stats-card {
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
        padding: 1rem;
        margin-bottom: 1rem;
    }
    
    .stats-value {
        font-size: 1.25rem;
        font-weight: 700;
        color: #16a34a;
    }
    
    .stats-label {
        color: #4b5563;
        font-size: 0.75rem;
    }
    
    /* Responsive stats card */
    @media (min-width: 640px) {
        .stats-card {
            padding: 1.25rem;
        }
        .stats-value {
            font-size: 1.5rem;
        }
        .stats-label {
            font-size: 0.8rem;
        }
    }
    
    @media (min-width: 1024px) {
        .stats-card {
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .stats-value {
            font-size: 1.75rem;
        }
        .stats-label {
            font-size: 0.875rem;
        }
    }
    
    /* Empty state styles */
    .empty-state {
        padding: 2rem;
        text-align: center;
        background-color: #f9fafb;
        border-radius: 0.5rem;
        margin: 2rem 0;
    }
    
    .empty-state-icon {
        font-size: 3rem;
        color: #9ca3af;
        margin-bottom: 1rem;
    }
    
    /* Animation */
    .animate-slide-in {
        animation: slideIn 0.3s ease-out forwards;
    }
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<!-- Main Content -->
<main class="flex-1 px-4 lg:px-8 pb-8 lg:ml-72 transition-all duration-300 pt-16">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Finished Orders</h1>
    </div>

    <!-- Stats Card -->
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 sm:gap-3 lg:gap-4 mb-4 sm:mb-5 lg:mb-6">
        <div class="stats-card">
            <div class="stats-value">${{ number_format($totalEarnings ?? 0, 2) }}</div>
            <div class="stats-label">Total Earnings</div>
        </div>
        <div class="stats-card">
            <div class="stats-value">{{ $completedOrders->total() }}</div>
            <div class="stats-label">Completed Orders</div>
        </div>
        <div class="stats-card sm:col-span-2 md:col-span-1">
            <div class="stats-value">
                @if($completedOrders->total() > 0 && isset($totalEarnings))
                    ${{ number_format($totalEarnings / $completedOrders->total(), 2) }}
                @else
                    $0.00
                @endif
            </div>
            <div class="stats-label">Average Order Value</div>
        </div>
    </div>

    <!-- Mobile Filter Toggle Button -->
    <div class="lg:hidden flex items-center justify-between mb-4">
        <button onclick="toggleMobileFilter()" class="flex items-center bg-green-500 text-white px-4 py-2 rounded-full">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2.586a1 1 0 01-.293.707l-3 3V14a1 1 0 01-.293.707l-3 3A1 1 0 019 17v-5.586l-3-3A1 1 0 015 6.586V4z" clip-rule="evenodd" />
            </svg>
            <span>Filters</span>
        </button>
        <button id="clearFilters" class="text-red-500 text-sm font-medium">Clear filters</button>
    </div>

    <!-- Desktop Filter Section -->
    <div class="bg-white p-4 rounded-lg shadow hidden lg:block mt-4 filter-section" id="desktopFilter">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold">Filters</h3>
            <button onclick="toggleFilter()" class="text-gray-500">Less options ▲</button>
        </div>
        <form id="filterForm" method="GET" action="{{ route('finished') }}">
            <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="status[]" value="completed" class="form-checkbox text-green-500" {{ request()->has('status') && in_array('completed', request()->status) ? 'checked' : '' }} />
                    <span>Completed</span>
                </label>
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="status[]" value="paid" class="form-checkbox text-green-500" {{ request()->has('status') && in_array('paid', request()->status) ? 'checked' : '' }} />
                    <span>Paid</span>
                </label>
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="status[]" value="finished" class="form-checkbox text-green-500" {{ request()->has('status') && in_array('finished', request()->status) ? 'checked' : '' }} />
                    <span>Finished</span>
                </label>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="date" name="start_date" class="p-2 border rounded w-full" placeholder="Start Date" value="{{ request('start_date') }}" />
                        <input type="date" name="end_date" class="p-2 border rounded w-full" placeholder="End Date" value="{{ request('end_date') }}" />
                    </div>
                </div>
                
                <input type="text" name="order_id" class="p-2 border rounded w-full" placeholder="Order ID" value="{{ request('order_id') }}" />
                <input type="text" name="topic" class="p-2 border rounded w-full" placeholder="Topic Title" value="{{ request('topic') }}" />
            </div>
            <button type="submit" class="mt-4 p-2 bg-green-500 text-white rounded flex items-center justify-center w-full">
                <span class="mr-2 hidden lg:inline tooltip">🔍<span class="tooltiptext">Search</span></span> Search
            </button>
        </form>
    </div>

    <!-- Mobile Filter Modal -->
    <div class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 items-center justify-center" id="mobileFilter">
        <div class="bg-white p-6 rounded-lg shadow w-11/12 max-w-md">
            <div class="flex justify-between mb-4">
                <h3 class="font-semibold">Filters</h3>
                <button onclick="toggleMobileFilter()" class="text-gray-500">✖</button>
            </div>
            <form id="mobileFilterForm" method="GET" action="{{ route('finished') }}">
                <div class="grid grid-cols-1 gap-4">
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="status[]" value="completed" class="form-checkbox text-green-500" {{ request()->has('status') && in_array('completed', request()->status) ? 'checked' : '' }} />
                        <span>Completed</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="status[]" value="paid" class="form-checkbox text-green-500" {{ request()->has('status') && in_array('paid', request()->status) ? 'checked' : '' }} />
                        <span>Paid</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="status[]" value="finished" class="form-checkbox text-green-500" {{ request()->has('status') && in_array('finished', request()->status) ? 'checked' : '' }} />
                        <span>Finished</span>
                    </label>
                    
                    <label class="block text-sm font-medium text-gray-700">Date Range</label>
                    <input type="date" name="start_date" class="p-2 border rounded w-full" placeholder="Start Date" value="{{ request('start_date') }}" />
                    <input type="date" name="end_date" class="p-2 border rounded w-full" placeholder="End Date" value="{{ request('end_date') }}" />
                  
                    <input type="text" name="order_id" class="p-2 border rounded w-full" placeholder="Order ID" value="{{ request('order_id') }}" />
                    <input type="text" name="topic" class="p-2 border rounded w-full" placeholder="Topic Title" value="{{ request('topic') }}" />
                </div>
                <button type="submit" class="mt-4 p-2 bg-green-500 text-white rounded flex items-center justify-center w-full">
                    <span class="mr-2 hidden lg:inline">🔍</span> Search
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6 animate-slide-in">
        <h3 class="text-lg font-semibold text-gray-700 mb-3">Finished Orders</h3>
        
        @if($completedOrders->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr class="w-full bg-gray-100 p-3 rounded-md">
                            <th class="py-2 px-4 text-left">Order ID</th>
                            <th class="py-2 px-4 text-left hidden lg:table-cell">Topic Title</th>
                            <th class="py-2 px-4 text-left hidden lg:table-cell">Discipline</th>
                            <th class="py-2 px-4 text-left">Completion Date</th>
                            <th class="py-2 px-4 text-left hidden lg:table-cell">Payment Status</th>
                            <th class="py-2 px-4 text-right">Earnings</th>
                            <th class="py-2 px-4 text-center hidden lg:table-cell">Customer Rating</th>
                            <th class="py-2 px-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($completedOrders as $order)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3 px-4">
                                <div class="text-blue-600 font-semibold">#{{ $order->id }}</div>
                                <div class="
                                    @if($order->status == 'completed') status-completed @endif
                                    @if($order->status == 'paid') status-paid @endif
                                    @if($order->status == 'finished') status-finished @endif
                                ">
                                    {{ strtoupper($order->status) }}
                                </div>
                            </td>
                            <td class="py-3 px-4 truncate-text hidden lg:table-cell">{{ $order->title ?? 'No Title' }}</td>
                            <td class="py-3 px-4 truncate-text hidden lg:table-cell">{{ $order->discipline ?? 'General' }}</td>
                            <td class="py-3 px-4 text-gray-600">{{ $order->updated_at->format('Y-m-d') }}</td>
                            <td class="py-3 px-4 truncate-text text-sm hidden lg:table-cell">
                                @if(isset($order->payments) && $order->payments->where('type', 'writer')->count() > 0)
                                    <span class="text-green-600 font-medium">Paid</span>
                                @else
                                    <span class="text-yellow-600 font-medium">Pending Payment</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-gray-800 font-semibold text-right">
                                @if(isset($order->payments))
                                    ${{ number_format($order->payments->where('type', 'writer')->sum('amount'), 2) }}
                                @else
                                    $0.00
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center hidden lg:table-cell">
                                @php
                                    // Determine the rating - using a random one for demonstration
                                    // In production, this would come from the order model
                                    $ratings = ['above', 'expected', 'below'];
                                    $ratingType = $order->rating_type ?? $ratings[array_rand($ratings)];
                                    
                                    // Assign icon and text based on rating
                                    if($ratingType == 'above') {
                                        $icon = '😀';
                                        $class = 'rating-above';
                                        $text = 'Above Expectations';
                                    } elseif($ratingType == 'expected') {
                                        $icon = '🙂';
                                        $class = 'rating-expected';
                                        $text = 'As Expected';
                                    } else {
                                        $icon = '😔';
                                        $class = 'rating-below';
                                        $text = 'Below Expectations';
                                    }
                                @endphp
                                
                                <div class="rating-icon {{ $class }}">
                                    {{ $icon }}
                                    <span class="tooltiptext">{{ $text }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <a href="{{ route('assigned', $order->id) }}" class="text-blue-600 hover:text-blue-800">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="mt-6">
                {{ $completedOrders->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">📝</div>
                <h3 class="text-lg font-medium mb-1">No Finished Orders Yet</h3>
                <p class="text-gray-500 mb-4">When you complete orders, they will appear here.</p>
                <a href="{{ route('home') }}" class="inline-block px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors">
                    Find Available Orders
                </a>
            </div>
        @endif
    </div>
</main>

<script>
    function toggleFilter() {
        const filterContent = document.querySelector('#desktopFilter .grid');
        filterContent.classList.toggle('hidden');
        const button = document.querySelector('#desktopFilter button');
        if (filterContent.classList.contains('hidden')) {
            button.textContent = 'More options ▼';
        } else {
            button.textContent = 'Less options ▲';
        }
    }
    
    function toggleMobileFilter() {
        document.getElementById('mobileFilter').classList.toggle('hidden');
        document.getElementById('mobileFilter').classList.toggle('flex');
    }
    
    // Clear filters
    document.getElementById('clearFilters').addEventListener('click', function() {
        window.location.href = "{{ route('finished') }}";
    });
</script>
@endsection