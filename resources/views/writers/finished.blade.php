@extends('writers.app')

@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<style>
    .status-confirmed { color: #16a34a; font-weight: bold; }
    .status-done { color: #f59e0b; font-weight: bold; }
    .status-delivered { color: #ef4444; font-weight: bold; }
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
</style>

<!-- Main Content -->
<main class="flex-1 px-4 lg:px-8 pb-8 lg:ml-72 transition-all duration-300 pt-16">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Finished Orders</h1>
    </div>

    <!-- Mobile Filter Toggle Button -->
    <div class="lg:hidden flex items-center justify-between">
        <button onclick="toggleMobileFilter()" class="flex items-center bg-green-500 text-white px-4 py-2 rounded-full">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2.586a1 1 0 01-.293.707l-3 3V14a1 1 0 01-.293.707l-3 3A1 1 0 019 17v-5.586l-3-3A1 1 0 015 6.586V4z" clip-rule="evenodd" />
            </svg>
            <span>Filters</span>
        </button>
        <button class="text-red-500 text-sm font-medium">Clear filters</button>
    </div>

    <!-- Desktop Filter Section -->
    <div class="bg-white p-4 rounded-lg shadow hidden lg:block mt-4 filter-section" id="desktopFilter">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold">Filters</h3>
            <button onclick="toggleFilter()" class="text-gray-500">Less options ▲</button>
        </div>
        <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
            <label class="flex items-center space-x-2">
                <input type="checkbox" checked class="form-checkbox text-green-500" />
                <span>Finished</span>
            </label>
            <label class="flex items-center space-x-2">
                <input type="checkbox" class="form-checkbox text-green-500" />
                <span>Cancelled</span>
            </label>
            <input type="date" class="p-2 border rounded w-full" />
            <input type="date" class="p-2 border rounded w-full" />
            <select class="p-2 border rounded w-full">
                <option>All available</option>
            </select>
            <select class="p-2 border rounded w-full">
                <option>All available</option>
            </select>
            <input type="text" class="p-2 border rounded w-full" placeholder="Order ID" />
            <input type="text" class="p-2 border rounded w-full" placeholder="Topic Title" />
        </div>
        <button class="mt-4 p-2 bg-green-500 text-white rounded flex items-center justify-center w-full">
            <span class="mr-2 hidden lg:inline tooltip">🔍<span class="tooltiptext">Search</span></span> Search
        </button>
    </div>

    <!-- Mobile Filter Modal -->
    <div class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center" id="mobileFilter">
        <div class="bg-white p-6 rounded-lg shadow w-11/12 max-w-md">
            <div class="flex justify-between mb-4">
                <h3 class="font-semibold">Filters</h3>
                <button onclick="toggleMobileFilter()" class="text-gray-500">✖</button>
            </div>
            <div class="grid grid-cols-1 gap-4">
                <label class="flex items-center space-x-2">
                    <input type="checkbox" checked class="form-checkbox text-green-500" />
                    <span>Finished</span>
                </label>
                <label class="flex items-center space-x-2">
                    <input type="checkbox" class="form-checkbox text-green-500" />
                    <span>Cancelled</span>
                </label>
                <input type="date" class="p-2 border rounded w-full" />
                <input type="date" class="p-2 border rounded w-full" />
                <select class="p-2 border rounded w-full">
                    <option>All available</option>
                </select>
                <select class="p-2 border rounded w-full">
                    <option>All available</option>
                </select>
                <input type="text" class="p-2 border rounded w-full" placeholder="Order ID" />
                <input type="text" class="p-2 border rounded w-full" placeholder="Topic Title" />
            </div>
            <button class="mt-4 p-2 bg-green-500 text-white rounded flex items-center justify-center w-full">
                <span class="mr-2 hidden lg:inline">🔍</span> Search
            </button>
        </div>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6 animate-slide-in">
        <h3 class="text-lg font-semibold text-gray-700 mb-3">Finished Orders</h3>
        
        <table class="min-w-full bg-white">
            <thead>
                <tr class="w-full bg-gray-100 p-3 rounded-md">
                    <th class="py-2">Order ID</th>
                    <th class="py-2 hidden lg:table-cell">Topic Title</th>
                    <th class="py-2 hidden lg:table-cell">Discipline</th>
                    <th class="py-2">Date</th>
                    <th class="py-2 hidden lg:table-cell">Paid</th>
                    <th class="py-2">Effect on Balance</th>
                    <th class="py-2 hidden lg:table-cell">Customer Experience</th>
                </tr>
            </thead>
            <tbody>
                @for ($i = 0; $i < 5; $i++)
                <tr class="border-b">
                    <td class="py-2">
                        <div class="text-blue-600 font-semibold text-lg">#604116322</div>
                        <div class="status-confirmed">FINISHED</div>
                    </td>
                    <td class="py-2 truncate-text hidden lg:table-cell">Programming - Python</td>
                    <td class="py-2 truncate-text hidden lg:table-cell">Computer Science</td>
                    <td class="py-2 text-gray-600 font-bold">2024-12-29</td>
                    <td class="py-2 truncate-text text-sm hidden lg:table-cell">Paid $100.00</td>
                    <td class="py-2 text-gray-800 font-semibold text-lg">$100.00</td>
                    <td class="py-2 truncate-text text-green-600 hidden lg:table-cell">Below Expectation</td>
                </tr>
                @endfor
            </tbody>
        </table>
    </div>
</main>

<script>
    function toggleFilter() {
        document.getElementById('desktopFilter').classList.toggle('hidden');
    }
    function toggleMobileFilter() {
        document.getElementById('mobileFilter').classList.toggle('hidden');
    }
</script>
@endsection
