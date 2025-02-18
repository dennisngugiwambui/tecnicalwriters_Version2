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
</style>


<main class="flex-1 px-4 lg:px-8 pb-8 lg:ml-72 transition-all duration-300 pt-16">
    <!-- Header with New Order button -->
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Available Orders</h1>
       
    </div>


    <!-- Filter Toggle Section -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex justify-between items-center mb-4 cursor-pointer" id="filterToggle">
            <span class="text-lg font-semibold text-gray-700">Advanced Filters</span>
            <i class="fas fa-filter text-green-500 transition-transform duration-300"></i>
        </div>

        <!-- Filter Content -->
        <!-- Filter Content -->
        <div id="filterContent" class="space-y-4 md:space-y-0">
            <!-- Desktop View: Single Line -->
            <div class="hidden md:flex items-center space-x-4">
                <!-- Search Bar -->
                <div class="flex-1">
                    <input type="text" placeholder="Search orders..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>

                <!-- Level Select -->
                <div class="w-48">
                    <select class="select2-basic w-full" id="levelSelect">
                        <option value="">All Levels</option>
                        <option value="1">Level 1</option>
                        <option value="2">Level 2</option>
                        <option value="3">Level 3</option>
                    </select>
                </div>

                <!-- Discipline Select -->
                <div class="w-48">
                    <select class="select2-basic w-full" id="disciplineSelect">
                        <option value="">All Disciplines</option>
                        <option value="programming">Programming</option>
                        <option value="writing">Writing</option>
                        <option value="math">Mathematics</option>
                    </select>
                </div>

                <!-- Checkboxes -->
                <div class="flex items-center space-x-4">
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" class="h-4 w-4 text-green-600 rounded border-gray-300 focus:ring-green-500">
                        <span class="text-sm text-gray-600">Only Orders</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" class="h-4 w-4 text-green-600 rounded border-gray-300 focus:ring-green-500">
                        <span class="text-sm text-gray-600">Only New</span>
                    </label>
                </div>

                <!-- Search Button -->
                <button class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition shadow-md hover:shadow-lg flex items-center">
                    <i class="fas fa-search mr-2"></i>Search
                </button>
            </div>

            <!-- Mobile View: Stacked -->
            <div class="md:hidden space-y-4">
                <!-- Search Bar -->
                <div class="w-full">
                    <input type="text" placeholder="Search orders..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" id="searchOrdersMobile">
                </div>

               

                <!-- Level Select -->
                <div class="w-full">
                    <select class="select2-basic w-full" id="levelSelectMobile">
                        <option value="">All Levels</option>
                        <option value="1">Level 1</option>
                        <option value="2">Level 2</option>
                        <option value="3">Level 3</option>
                    </select>
                </div>

                <!-- Discipline Select -->
                <div class="w-full">
                    <select class="select2-basic w-full" id="disciplineSelectMobile">
                        <option value="">All Disciplines</option>
                        <option value="programming">Programming</option>
                        <option value="writing">Writing</option>
                        <option value="math">Mathematics</option>
                    </select>
                </div>

                <!-- Checkboxes -->
                <div class="flex flex-col space-y-2">
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" class="h-4 w-4 text-green-600 rounded border-gray-300 focus:ring-green-500">
                        <span class="text-sm text-gray-600">Only Orders</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" class="h-4 w-4 text-green-600 rounded border-gray-300 focus:ring-green-500">
                        <span class="text-sm text-gray-600">Only New</span>
                    </label>
                </div>

                <!-- Search Button -->
                <button class="w-full bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition shadow-md hover:shadow-lg flex items-center justify-center">
                    <i class="fas fa-search mr-2"></i>Search
                </button>
            </div>
        </div>
    </div>


    <!-- Orders Grid -->
    <div class="space-y-6">
        <!-- Mobile Headers -->
        <div class="md:hidden bg-gray-50 p-4 rounded-lg">
            <div class="grid grid-cols-3 gap-4 text-sm font-medium text-gray-600">
                <div>Pages</div>
                <div>Deadline</div>
                <div>Cost</div>
            </div>
        </div>

        <!-- Desktop Headers -->
        <div class="hidden md:grid grid-cols-7 gap-4 bg-gray-50 p-4 rounded-lg text-sm font-medium text-gray-600">
            <div>Order ID</div>
            <div>Topic Title</div>
            <div>Discipline</div>
            <div>Pages</div>
            <div>Deadline</div>
            <div>CPP</div>
            <div>Cost</div>
        </div>

        <!-- First Order Item -->
        <div class="bg-white rounded-xl shadow-lg hover-scale p-6 transition-all duration-300">
            <a href="#" class="block">
                <!-- Mobile View -->
                <div class="md:hidden space-y-3">
                    <div class="text-sm text-green-600 font-medium">Available</div>
                    <div class="text-lg font-semibold text-gray-800">Programming - SPSS</div>
                    <div class="grid grid-cols-3 gap-4 text-sm mt-2">
                        <div>
                            <div class="font-medium">0</div>
                        </div>
                        <div>
                            <div class="font-medium">2d 22h 1m</div>
                        </div>
                        <div>
                            <div class="font-bold text-black">$45.00</div>
                        </div>
                    </div>
                </div>

                <!-- Desktop View -->
                <div class="hidden md:grid grid-cols-7 gap-4 items-center">
                    <div>
                        <div class="text-sm text-green-600 font-medium mb-1">Available</div>
                        <div class="text-gray-600">#605375174</div>
                    </div>
                    <div class="text-gray-800 font-medium">Data Analysis</div>
                    <div class="space-y-1">
                        <div>Programming</div>
                        <div class="text-sm text-gray-500">Medium</div>
                    </div>
                    <div>0</div>
                    <div>2d 22h 1m</div>
                    <div>Medium</div>
                    <div class="font-bold text-black">$45.00</div>
                </div>
            </a>
        </div>

        <!-- Second Order Item -->
        <div class="bg-white rounded-xl shadow-lg hover-scale p-6 transition-all duration-300">
            <a href="#" class="block">
                <!-- Mobile View -->
                <div class="md:hidden space-y-3">
                    <div class="text-sm text-green-600 font-medium">Available</div>
                    <div class="text-lg font-semibold text-gray-800">Programming - Java</div>
                    <div class="grid grid-cols-3 gap-4 text-sm mt-2">
                        <div>
                            <div class="font-medium">3</div>
                        </div>
                        <div>
                            <div class="font-medium">5d 12h 30m</div>
                        </div>
                        <div>
                            <div class="text-black">$32.00</div>
                        </div>
                    </div>
                </div>

                <!-- Desktop View -->
                <div class="hidden md:grid grid-cols-7 gap-4 items-center">
                    <div>
                        <div class="text-sm text-green-600 font-medium mb-1">Available</div>
                        <div class="text-gray-600">#605375175</div>
                    </div>
                    <div class="text-gray-800 font-medium">Software Development</div>
                    <div class="space-y-1">
                        <div>Programming</div>
                        <div class="text-sm text-gray-500">High</div>
                    </div>
                    <div>3</div>
                    <div>5d 12h 30m</div>
                    <div>High</div>
                    <div class="text-black">$32.00</div>
                </div>
            </a>
        </div>
    </div>
</main>


<script>
    $(document).ready(function() {
    // Initialize Select2
    $('.select2-basic').select2({
        theme: 'classic',
        width: '100%'
    });

    // Filter toggle functionality
    $('#filterToggle').click(function() {
        $('#filterContent').slideToggle(300);
        $(this).find('.fa-filter').toggleClass('rotate-180');
    });

    // Show filter content by default on desktop
    if (window.innerWidth >= 768) {
        $('#filterContent').show();
    }

    // Handle resize events
    $(window).resize(function() {
        if (window.innerWidth >= 768) {
            $('#filterContent').show();
        }
    });
});
</script>

@endsection

