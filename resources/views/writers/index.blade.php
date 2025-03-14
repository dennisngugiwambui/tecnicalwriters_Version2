@extends('writers.app')
@section('content')

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #16a34a !important;
    }
    .select2-container--default .select2-selection--single {
        block-size: 40px;
        display: flex;
        align-items: center;
        border-color: #e5e7eb;
        border-radius: 0.5rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        block-size: 40px;
    }
    /* Add this for icon rotation */
    .rotate-180 {
        transform: rotate(180deg);
    }
    /* Make sure transitions are smooth */
    #filterContent {
        transition: all 0.3s ease;
    }
    .fas {
        transition: transform 0.3s ease;
    }
    /* Mobile filter button and panel */
    .mobile-filter-toggle {
        display: flex;
        align-items: center;
        padding: 10px 16px;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        margin-bottom: 16px;
    }
    .mobile-filter-toggle i {
        color: #10b981;
        margin-right: 8px;
    }
    #mobileFilterPanel {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 50;
        display: none;
    }
    .mobile-filter-content {
        position: relative;
        background-color: white;
        width: 90%;
        max-width: 500px;
        margin: 10vh auto;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        max-height: 80vh;
        overflow-y: auto;
    }
    /* Clear filter button */
    .clear-filters-btn {
        display: none;
        align-items: center;
        color: #10b981;
        font-weight: 500;
        margin-left: 10px;
    }
</style>


<main class="flex-1 px-4 lg:px-8 pb-8 lg:ml-72 transition-all duration-300 pt-16">
    <!-- Header with New Order button -->
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Available Orders</h1>
    </div>

    <!-- Filter Button for Mobile -->
    <div class="md:hidden">
        <div class="flex items-center justify-between">
            <button id="mobileFilterToggle" class="mobile-filter-toggle">
                <i class="fas fa-filter"></i>
                <span>Filter Orders</span>
            </button>
            <div id="clearFiltersBtn" class="clear-filters-btn">
                Clear filters
            </div>
        </div>
    </div>

    <!-- Mobile Filter Panel -->
    <div id="mobileFilterPanel">
        <div class="mobile-filter-content p-5">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Filters</h3>
                <button id="closeMobileFilter" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="space-y-4 mb-6">
                <!-- Search input -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" id="mobileSearchInput" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Search by order ID or title">
                </div>
                
                <!-- Education Level -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Education Level</label>
                    <select id="mobileLevelSelect" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">All Levels</option>
                        <option value="High School">High School</option>
                        <option value="Undergraduate">Undergraduate</option>
                        <option value="PostGraduate">PostGraduate</option>
                    </select>
                </div>
                
                <!-- Discipline -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Discipline</label>
                    <select id="mobileDisciplineSelect" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">All Disciplines</option>
                        @foreach($disciplines as $discipline)
                            <option value="{{ $discipline }}">{{ $discipline }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Subject Select -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Your Subjects</label>
                    <select id="mobileSubjectSelect" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">All Subjects</option>
                        @foreach($userSubjects as $subject)
                            <option value="{{ $subject }}">{{ $subject }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Additional options -->
                <div class="space-y-2">
                    <label class="flex items-center">
                        <input type="checkbox" id="mobileOnlyOrdersCheckbox" class="h-4 w-4 text-green-600 rounded border-gray-300">
                        <span class="ml-2 text-sm text-gray-700">Only Orders</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" id="mobileOnlyNewCheckbox" class="h-4 w-4 text-green-600 rounded border-gray-300">
                        <span class="ml-2 text-sm text-gray-700">Only New</span>
                    </label>
                </div>
            </div>
            
            <div class="flex space-x-3">
                <button id="applyMobileFilters" class="w-full bg-green-600 text-white py-2 rounded-lg font-medium">
                    Apply Filters
                </button>
                <button id="clearMobileFilters" class="w-full border border-gray-300 text-gray-700 py-2 rounded-lg font-medium">
                    Clear
                </button>
                <button id="cancelMobileFilters" class="w-full border border-gray-300 text-gray-700 py-2 rounded-lg font-medium">
                    Cancel
                </button>
            </div>
        </div>
    </div>

    <!-- Filter Toggle Section (Desktop) -->
    <div class="hidden md:block bg-white rounded-xl shadow-lg p-6">
        <div class="flex justify-between items-center mb-4 cursor-pointer" id="filterToggle">
            <span class="text-lg font-semibold text-gray-700">Advanced Filters</span>
            <i class="fas fa-filter text-green-500 transition-transform duration-300"></i>
        </div>

        <!-- Filter Content -->
        <div id="filterContent" class="space-y-4 md:space-y-0 hidden">
            <!-- Desktop View: Single Line -->
            <div class="hidden md:flex items-center space-x-4">
                <!-- Search Bar -->
                <div class="flex-1">
                    <input type="text" id="desktopSearchInput" placeholder="Search orders..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>

                <!-- Level Select -->
                <div class="w-48">
                    <select class="select2-basic w-full" id="levelSelect">
                        <option value="">All Levels</option>
                        <option value="High School">High School</option>
                        <option value="Undergraduate">Undergraduate</option>
                        <option value="PostGraduate">PostGraduate</option>
                    </select>
                </div>

                <!-- Discipline Select -->
                <div class="w-48">
                    <select class="select2-basic w-full" id="disciplineSelect">
                        <option value="">All Disciplines</option>
                        @foreach($disciplines as $discipline)
                            <option value="{{ $discipline }}">{{ $discipline }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Checkboxes -->
                <div class="flex items-center space-x-4">
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" id="onlyOrdersCheckbox" class="h-4 w-4 text-green-600 rounded border-gray-300 focus:ring-green-500">
                        <span class="text-sm text-gray-600">Only Orders</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" id="onlyNewCheckbox" class="h-4 w-4 text-green-600 rounded border-gray-300 focus:ring-green-500">
                        <span class="text-sm text-gray-600">Only New</span>
                    </label>
                </div>

                <!-- Search Button -->
                <button id="desktopSearchButton" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition shadow-md hover:shadow-lg flex items-center">
                    <i class="fas fa-search mr-2"></i>Search
                </button>
            </div>           
        </div>
    </div>

    <!-- Orders Grid -->
    <div class="space-y-6 mt-6">
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

        <!-- Orders Container -->
        <div id="ordersContainer">
            @if(count($availableOrders) > 0)
                @foreach($availableOrders as $order)
                    @php
                        $deadlineDate = \Carbon\Carbon::parse($order->deadline);
                        $now = \Carbon\Carbon::now();
                        $diff = $now->diff($deadlineDate);
                        $timeLeft = '';
                        
                        if ($diff->days > 0) {
                            $timeLeft .= "{$diff->days}d ";
                        }
                        if ($diff->h > 0) {
                            $timeLeft .= "{$diff->h}h ";
                        }
                        $timeLeft .= "{$diff->i}m";
                    @endphp
                    <!-- Order Item -->
                    <div class="order-item bg-white rounded-xl shadow-lg hover-scale p-6 transition-all duration-300 mb-4" 
                         data-discipline="{{ $order->discipline }}" 
                         data-level="{{ $order->education_level ?? '' }}"
                         data-title="{{ $order->title }}"
                         data-id="{{ $order->id }}">
                        <a href="{{ route('availableOrderDetails', ['id' => $order->id]) }}" class="block">
                            <!-- Mobile View -->
                            <div class="md:hidden space-y-3">
                                <div class="text-sm text-green-600 font-medium">Available</div>
                                <div class="text-lg font-semibold text-gray-800">{{ $order->type_of_service }} - {{ $order->discipline }}</div>
                                <div class="grid grid-cols-3 gap-4 text-sm mt-2">
                                    <div>
                                        <div class="font-medium">{{ $order->task_size ?? '0' }}</div>
                                    </div>
                                    <div>
                                        <div class="font-medium">{{ $timeLeft }}</div>
                                    </div>
                                    <div>
                                        <div class="font-bold text-black">${{ number_format($order->price, 2) }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Desktop View -->
                            <div class="hidden md:grid grid-cols-7 gap-4 items-center">
                                <div>
                                    <div class="text-sm text-green-600 font-medium mb-1">Available</div>
                                    <div class="text-gray-600">#{{ $order->id }}</div>
                                </div>
                                <div class="text-gray-800 font-medium">{{ $order->title }}</div>
                                <div class="space-y-1">
                                    <div>{{ $order->discipline }}</div>
                                    <div class="text-sm text-gray-500">{{ $order->task_size ?? 'Medium' }}</div>
                                </div>
                                <div>{{ $order->task_size ?? '0' }}</div>
                                <div>{{ $timeLeft }}</div>
                                <div>{{ $order->software ?? 'N/A' }}</div>
                                <div class="font-bold text-black">${{ number_format($order->price, 2) }}</div>
                            </div>
                        </a>
                    </div>
                @endforeach
            @else
                <div class="bg-white rounded-xl shadow-lg p-6 text-center">
                    <p class="text-gray-600">No available orders at the moment. Check back later.</p>
                </div>
            @endif
        </div>
        
        <!-- No matching orders message (hidden by default) -->
        <div id="noMatchingOrdersMessage" class="bg-white rounded-xl shadow-lg p-6 text-center hidden">
            <p class="text-gray-600 mb-4">No orders match your filter criteria. Try adjusting your filters.</p>
            <button id="clearAllFilters" class="bg-green-600 text-white px-6 py-2 rounded-lg">
                Clear All Filters
            </button>
        </div>
    </div>
</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2
        $('.select2-basic').select2();
        
        // Track filter state
        let filtersApplied = false;
        
        // Toggle desktop filter content
        $('#filterToggle').click(function() {
            $('#filterContent').toggleClass('hidden');
            $(this).find('i').toggleClass('rotate-180');
        });
        
        // Open mobile filter panel
        $('#mobileFilterToggle').click(function() {
            $('#mobileFilterPanel').fadeIn(200);
        });
        
        // Close mobile filter panel
        $('#closeMobileFilter, #cancelMobileFilters').click(function() {
            $('#mobileFilterPanel').fadeOut(200);
        });
        
        // Apply filters from mobile
        $('#applyMobileFilters').click(function() {
            applyFilters('mobile');
            $('#mobileFilterPanel').fadeOut(200);
        });
        
        // Apply filters from desktop
        $('#desktopSearchButton').click(function() {
            applyFilters('desktop');
        });
        
        // Clear filters from mobile
        $('#clearMobileFilters').click(function() {
            clearFilters();
            $('#mobileFilterPanel').fadeOut(200);
        });
        
        // Clear filters from clear button
        $('#clearFiltersBtn, #clearAllFilters').click(function() {
            clearFilters();
        });
        
        // Apply filters function
        function applyFilters(source) {
            // Get filter values based on source
            const searchValue = source === 'desktop' ? 
                $('#desktopSearchInput').val().toLowerCase() : 
                $('#mobileSearchInput').val().toLowerCase();
                
            const disciplineValue = source === 'desktop' ? 
                $('#disciplineSelect').val() : 
                $('#mobileDisciplineSelect').val();
                
            const levelValue = source === 'desktop' ? 
                $('#levelSelect').val() : 
                $('#mobileLevelSelect').val();
                
            const subjectValue = source === 'desktop' ? 
                '' : // Desktop doesn't have subject filter yet
                $('#mobileSubjectSelect').val();
                
            const onlyOrders = source === 'desktop' ? 
                $('#onlyOrdersCheckbox').is(':checked') : 
                $('#mobileOnlyOrdersCheckbox').is(':checked');
                
            const onlyNew = source === 'desktop' ? 
                $('#onlyNewCheckbox').is(':checked') : 
                $('#mobileOnlyNewCheckbox').is(':checked');
            
            // Check if any filters are applied
            filtersApplied = searchValue !== '' || 
                            disciplineValue !== '' && disciplineValue !== null || 
                            levelValue !== '' && levelValue !== null || 
                            subjectValue !== '' && subjectValue !== null || 
                            onlyOrders || 
                            onlyNew;
            
            // Show/hide clear filters button based on filter state
            if (filtersApplied) {
                $('#clearFiltersBtn').css('display', 'flex');
            } else {
                $('#clearFiltersBtn').css('display', 'none');
            }
            
            // Filter the orders
            let visibleCount = 0;
            
            $('.order-item').each(function() {
                const order = $(this);
                const orderTitle = order.data('title').toLowerCase();
                const orderId = order.data('id').toString();
                const orderDiscipline = order.data('discipline').toLowerCase();
                const orderLevel = order.data('level').toLowerCase();
                
                let isVisible = true;
                
                // Apply search filter
                if (searchValue && !(orderTitle.includes(searchValue) || orderId.includes(searchValue))) {
                    isVisible = false;
                }
                
                // Apply discipline filter
                if (disciplineValue && orderDiscipline !== disciplineValue.toLowerCase()) {
                    isVisible = false;
                }
                
                // Apply level filter
                if (levelValue && orderLevel !== levelValue.toLowerCase()) {
                    isVisible = false;
                }
                
                // Apply subject filter
                if (subjectValue && orderDiscipline !== subjectValue.toLowerCase()) {
                    isVisible = false;
                }
                
                // Set visibility
                if (isVisible) {
                    order.show();
                    visibleCount++;
                } else {
                    order.hide();
                }
            });
            
            // Show no matching orders message if needed
            if (visibleCount === 0 && filtersApplied) {
                $('#noMatchingOrdersMessage').removeClass('hidden');
            } else {
                $('#noMatchingOrdersMessage').addClass('hidden');
            }
        }
        
        // Clear filters function
        function clearFilters() {
            // Reset desktop filters
            $('#desktopSearchInput').val('');
            $('#disciplineSelect, #levelSelect').val('').trigger('change');
            $('#onlyOrdersCheckbox, #onlyNewCheckbox').prop('checked', false);
            
            // Reset mobile filters
            $('#mobileSearchInput').val('');
            $('#mobileDisciplineSelect, #mobileLevelSelect, #mobileSubjectSelect').val('');
            $('#mobileOnlyOrdersCheckbox, #mobileOnlyNewCheckbox').prop('checked', false);
            
            // Show all orders
            $('.order-item').show();
            $('#noMatchingOrdersMessage').addClass('hidden');
            
            // Reset filter state
            filtersApplied = false;
            $('#clearFiltersBtn').css('display', 'none');
        }
        
        // Check for filter query params on page load
        checkQueryParams();
        
        function checkQueryParams() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('filter') && urlParams.get('filter') === 'true') {
                // Load filter values from URL
                $('#desktopSearchInput').val(urlParams.get('search') || '');
                $('#disciplineSelect').val(urlParams.get('discipline') || '').trigger('change');
                $('#levelSelect').val(urlParams.get('level') || '').trigger('change');
                $('#onlyOrdersCheckbox').prop('checked', urlParams.has('only_orders'));
                $('#onlyNewCheckbox').prop('checked', urlParams.has('only_new'));
                
                // Apply filters
                applyFilters('desktop');
            }
        }
        
        // Desktop filter state on load
        if ($(window).width() >= 768) {
            $('#filterContent').addClass('hidden');
            $('#filterToggle').find('i').removeClass('rotate-180');
        }
    });
</script>

@endsection