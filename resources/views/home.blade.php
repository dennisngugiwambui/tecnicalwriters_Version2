<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Dashboard UI</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <style>
        @keyframes slideIn {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .select2-container .select2-selection--single {
            block-size: 38px !important;
            border-radius: 4px !important;
            border: 1px solid #e5e7eb !important;
            display: flex !important;
            align-items: center !important;
            padding: 0 10px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            block-size: 36px !important;
        }
        .select2-dropdown {
            border: 1px solid #e5e7eb !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
        }
        .filter-section-enter {
            max-block-size: 0;
            opacity: 0;
            transition: max-height 0.3s ease-out, opacity 0.2s ease-out;
            overflow: hidden;
        }
        .filter-section-enter.show {
            max-block-size: 500px;
            opacity: 1;
        }
        .animate-slide-in {
            animation: slideIn 0.3s ease-out;
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
        .hover-scale {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .hover-scale:hover {
            transform: scale(1.02);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .sidebar-overlay {
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
        }
        .select2-container .select2-selection--single {
            block-size: 42px !important;
            padding: 8px !important;
            border-radius: 0.5rem !important;
            border: 1px solid #e5e7eb !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 42px !important;
        }
        .select2-dropdown {
            border-radius: 0.5rem !important;
            border: none !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
        }
        .select2-search__field {
            border-radius: 0.375rem !important;
            padding: 0.5rem !important;
        }
        .filter-section-mobile {
            max-height: 0;
            opacity: 0;
            transition: max-height 0.5s ease-out, opacity 0.3s ease-out;
            overflow: hidden;
        }
        .filter-section-mobile.show {
            max-height: 500px;
            opacity: 1;
        }

        .dropdown-menu {
        position: absolute;
        inset-inline-end: 0;
        inset-block-start: 100%;
        background-color: white;
        border-radius: 0.375rem;
        border: 1px solid #e5e7eb;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        z-index: 50;
        min-inline-size: 12rem;
        display: none;
    }
    .dropdown-menu.show {
        display: block;
    }

    .sidebar-overlay {
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
    }
    .menu-item {
        display: flex;
        align-items: center;
        padding: 0.75rem 1rem;
        margin-block-end: 0.25rem;
        border-radius: 0.375rem;
        transition: all 0.2s;
    }
    .menu-item:hover {
        background-color: #f3f4f6;
    }
    .menu-item.active {
        background-color: #f9fafb;
        font-weight: 500;
    }
    .menu-icon {
        margin-inline-end: 0.75rem;
    }
    .orders-counter {
        background-color: #f3f4f6;
        color: #4b5563;
        border-radius: 9999px;
        font-size: 0.75rem;
        padding: 0.125rem 0.375rem;
        margin-inline-start: auto;
    }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <!-- Navbar -->
     <!-- Navbar -->
<nav class="bg-white shadow-sm fixed w-full z-50">
    <div class="max-w-full mx-auto px-4">
        <div class="flex justify-between h-16">
            <!-- Left side - Logo -->
            <div class="flex items-center">
                <button id="menuToggle" class="lg:hidden p-2 rounded-md text-gray-600 hover:bg-gray-100">
                    <i class="fas fa-bars"></i>
                </button>
                <a href="#" class="flex items-center space-x-2 ml-2 lg:ml-0">
                    <img src="/api/placeholder/32/32" alt="Logo" class="h-8 w-8">
                    <span class="text-xl font-semibold text-gray-800">LIVOCORP</span>
                </a>
            </div>

            <!-- Middle - Navigation -->
            <div class="hidden md:flex items-center space-x-4">
                <a href="#" class="text-gray-600 hover:text-gray-800">Info</a>
                <a href="#" class="text-gray-600 hover:text-gray-800">Terms & Policies</a>
                <a href="#" class="text-gray-600 hover:text-gray-800">News</a>
                <a href="#" class="text-gray-600 hover:text-gray-800">Blog</a>
            </div>

            <!-- Right side - User profile and badges -->
            <div class="flex items-center space-x-4">
                <!-- 80% Badge -->
                <div class="hidden md:flex items-center space-x-1">
                    <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded-lg flex items-center">
                        <i class="fas fa-badge-percent mr-1"></i>
                        <span>80%</span>
                        <i class="fas fa-chevron-down ml-1 text-xs text-gray-400"></i>
                    </span>
                </div>

                <!-- Profile Dropdown -->
                <div class="relative group" id="profileDropdown">
                    <button class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-50" id="profileButton">
                        <div class="flex flex-col text-right">
                            <span class="text-sm font-medium text-gray-700">Philip</span>
                            <span class="text-xs text-gray-500">Looking for orders</span>
                        </div>
                        <img src="/api/placeholder/32/32" alt="Profile" class="h-8 w-8 rounded-full">
                        <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                    </button>
                    <!-- Dropdown Menu -->
                    <div class="dropdown-menu" id="userDropdown">
                        <div class="py-1">
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
                            <div class="border-t border-gray-200 my-1"></div>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

   <!-- Sidebar Overlay - for mobile view -->
<div id="sidebarOverlay" class="fixed inset-0 z-40 hidden lg:hidden sidebar-overlay" onclick="toggleSidebar()"></div>

<!-- Sidebar -->
<aside id="sidebar" class="fixed left-0 top-0 h-full w-64 bg-white shadow-md transform -translate-x-full lg:translate-x-0 transition-transform duration-300 z-40 mt-16 overflow-y-auto">
    <div class="p-4">
        <h2 class="text-lg font-bold text-gray-800 mb-6">Orders</h2>
        <ul class="space-y-1">
            <li>
                <a href="#" class="menu-item active">
                    <i class="fas fa-circle text-yellow-500 menu-icon"></i>
                    <span>Available</span>
                </a>
            </li>
            <li>
                <a href="#" class="menu-item">
                    <i class="fas fa-circle text-gray-400 menu-icon"></i>
                    <span>Current</span>
                    <span class="orders-counter">1</span>
                </a>
            </li>
            <li>
                <a href="#" class="menu-item">
                    <i class="fas fa-sync text-gray-400 menu-icon"></i>
                    <span>Revision</span>
                </a>
            </li>
            <li>
                <a href="#" class="menu-item">
                    <i class="fas fa-exclamation-circle text-gray-400 menu-icon"></i>
                    <span>Dispute</span>
                </a>
            </li>
            <li>
                <a href="#" class="menu-item">
                    <i class="fas fa-check-circle text-gray-400 menu-icon"></i>
                    <span>Finished</span>
                </a>
            </li>
            <li>
                <a href="#" class="menu-item">
                    <i class="fas fa-gavel text-gray-400 menu-icon"></i>
                    <span>Bids</span>
                </a>
            </li>
            <li>
                <a href="#" class="menu-item">
                    <i class="fas fa-comment text-gray-400 menu-icon"></i>
                    <span>Messages</span>
                    <span class="orders-counter">1</span>
                </a>
            </li>
            <li>
                <a href="#" class="menu-item">
                    <i class="fas fa-chart-bar text-gray-400 menu-icon"></i>
                    <span>Statistics</span>
                </a>
            </li>
            <li>
                <a href="#" class="menu-item">
                    <i class="fas fa-dollar-sign text-gray-400 menu-icon"></i>
                    <span>Finance</span>
                </a>
            </li>
        </ul>
    </div>
</aside>





        <!-- Main Content -->
        <main class="flex-1 px-4 lg:px-8 pb-8 lg:ml-72 transition-all duration-300 pt-16">
            <!-- Header with New Order button -->
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-2xl font-bold text-gray-800">Available Orders</h1>
                <button class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition shadow-md hover:shadow-lg">
                    <i class="fas fa-plus mr-2"></i>New Order
                </button>
            </div>

            <!-- Filter Section -->
        <div class="max-w-7xl mx-auto px-4 py-4">
            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex flex-col lg:flex-row lg:items-center space-y-4 lg:space-y-0 lg:space-x-4">
                    <div class="flex-1">
                        <select class="select2-basic w-full" id="levelSelect">
                            <option value="">All available</option>
                            <option value="1">Level 1</option>
                            <option value="2">Level 2</option>
                            <option value="3">Level 3</option>
                        </select>
                    </div>
                    <div class="flex-1">
                        <select class="select2-basic w-full" id="disciplineSelect">
                            <option value="">All disciplines</option>
                            <option value="programming">Programming</option>
                            <option value="writing">Writing</option>
                            <option value="math">Mathematics</option>
                        </select>
                    </div>
                    <div class="flex space-x-2">
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" class="form-checkbox h-4 w-4 text-green-600">
                            <span class="text-sm text-gray-600">Only Orders</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" class="form-checkbox h-4 w-4 text-green-600">
                            <span class="text-sm text-gray-600">Only New</span>
                        </label>
                    </div>
                    <button class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors">
                        <i class="fas fa-search mr-2"></i>Search
                    </button>
                </div>
            </div>
            <!-- Enhanced Filter Section -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                <!-- Mobile Filter Toggle -->
                <button id="filterToggle" class="md:hidden w-full flex justify-between items-center mb-4">
                    <span class="text-lg font-semibold text-gray-700">Filters</span>
                    <i class="fas fa-filter text-green-500"></i>
                </button>

                <!-- Filter Content -->
                <div id="filterContent" class="filter-section-mobile md:block">
                    <div class="grid md:grid-cols-3 gap-4">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Level</label>
                            <select class="select2-search w-full" id="levelSelect">
                                <option value="">All available</option>
                                <option value="1">Level 1</option>
                                <option value="2">Level 2</option>
                                <option value="3">Level 3</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Discipline</label>
                            <select class="select2-search w-full" id="disciplineSelect">
                                <option value="">All disciplines</option>
                                <option value="programming">Programming</option>
                                <option value="writing">Writing</option>
                                <option value="math">Mathematics</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button class="w-full bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition shadow-md hover:shadow-lg flex items-center justify-center">
                                <i class="fas fa-search mr-2"></i>Search
                            </button>
                        </div>
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
    </div>

    <script>
         $(document).ready(function() {
        // Toggle Profile Dropdown
        $('#profileButton').click(function(e) {
            e.stopPropagation();
            $('#userDropdown').toggleClass('show');
        });

        // Close dropdown when clicking outside
        $(document).click(function() {
            $('#userDropdown').removeClass('show');
        });
    });
        // Initialize Select2
        $(document).ready(function() {
            $('.select2-search').select2({
                placeholder: "Select an option",
                allowClear: true,
                inline-size: '100%',
                minimumResultsForSearch: 5,
                dropdownParent: $('#filterContent')
            });
        });
        $(document).ready(function() {
            // Initialize Select2
            $('.select2-basic').select2({
                minimumResultsForSearch: 5,
                width: '100%',
                placeholder: "Select an option",
                allowClear: true
            });
        });


        // Sidebar Toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        // Enhanced Filter Toggle for Mobile
        document.getElementById('filterToggle').addEventListener('click', function() {
            const filterContent = document.getElementById('filterContent');
            filterContent.classList.toggle('filter-section-mobile');
            filterContent.classList.toggle('show');
            
            // Rotate filter icon
            const filterIcon = this.querySelector('i');
            filterIcon.style.transform = filterContent.classList.contains('show') 
                ? 'rotate(180deg)' 
                : 'rotate(0deg)';
        });

        // Menu Toggle
        document.getElementById('menuToggle').addEventListener('click', toggleSidebar);
    </script>
</body>
</html>