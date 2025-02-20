<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechnicalWriters</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <link rel="icon" type="image/jpeg" href="writers/technicalwriters2.jpg">
    <style>
        @keyframes slideIn {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .select2-container .select2-selection--single {
            height: 38px !important;
            border-radius: 4px !important;
            border: 1px solid #e5e7eb !important;
            display: flex !important;
            align-items: center !important;
            padding: 0 10px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
        }
        .select2-dropdown {
            border: 1px solid #e5e7eb !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
        }
        .filter-section-enter {
            max-height: 0;
            opacity: 0;
            transition: max-height 0.3s ease-out, opacity 0.2s ease-out;
            overflow: hidden;
        }
        .filter-section-enter.show {
            max-height: 500px;
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
            height: 42px !important;
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
        right: 0;
        top: 100%;
        background-color: white;
        border-radius: 0.375rem;
        border: 1px solid #e5e7eb;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        z-index: 50;
        min-width: 12rem;
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
        margin-bottom: 0.25rem;
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
        margin-right: 0.75rem;
    }
    .orders-counter {
        background-color: #f3f4f6;
        color: #4b5563;
        border-radius: 9999px;
        font-size: 0.75rem;
        padding: 0.125rem 0.375rem;
        margin-left: auto;
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
                <button id="menuToggle" class="lg:hidden p-2 rounded-md text-gray-600 hover:bg-gray-100" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <a href="#" class="flex items-center space-x-2 ml-2 lg:ml-0">
                    <img src="{{ '../writers/technicalwriters.jpg' }}" alt="Logo" class="h-8 w-8">
                    <span class="text-xl font-semibold text-gray-800">Technicalwriters</span>
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
                        <div class="ml-2">
                            <i class="fas fa-circle text-green-500 animate-pulse" style="font-size: 0.75rem;"></i>
                        </div>
                        <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                    </button>
                    <!-- Dropdown Menu -->
                    <div class="dropdown-menu" id="userDropdown">
                        <div class="py-1">
                            <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                            <div class="border-t border-gray-200 my-1"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Logout
                                </button>
                            </form>
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
                <a href="{{ route('home') }}" class="menu-item {{ request()->routeIs('home') ? 'active' : '' }}">
                    <i class="fas fa-circle text-yellow-500 menu-icon"></i>
                    <span>Available</span>
                </a>
            </li>
            <li>
                <a href="{{ route('curret') }}" class="menu-item {{ request()->routeIs('curret') ? 'active' : '' }}">
                    <i class="fas fa-circle text-gray-400 menu-icon"></i>
                    <span>Current</span>
                    <span class="orders-counter bg-red-500 text-white">1</span>
                </a>
            </li>
            <li>
                <a href="{{ route('revision') }}" class="menu-item {{ request()->routeIs('revision') ? 'active' : '' }}">
                    <i class="fas fa-sync text-gray-400 menu-icon"></i>
                    <span>Revision</span>
                </a>
            </li>
            <li>
                <a href="{{ route('dispute') }}" class="menu-item {{ request()->routeIs('dispute') ? 'active' : '' }}">
                    <i class="fas fa-exclamation-circle text-gray-400 menu-icon"></i>
                    <span>Dispute</span>
                </a>
            </li>
            <li>
                <a href="{{ route('finished') }}" class="menu-item {{ request()->routeIs('finished') ? 'active' : '' }}">
                    <i class="fas fa-check-circle text-gray-400 menu-icon"></i>
                    <span>Finished</span>
                </a>
            </li>
            <li>
                <a href="{{ route('bids') }}" class="menu-item {{ request()->routeIs('bids') ? 'active' : '' }}">
                    <i class="fas fa-gavel text-gray-400 menu-icon"></i>
                    <span>Bids</span>
                </a>
            </li>
            <li>
                <a href="{{ route('messages') }}" class="menu-item {{ request()->routeIs('messages') ? 'active' : '' }}">
                    <i class="fas fa-comment text-gray-400 menu-icon"></i>
                    <span>Messages</span>
                    <span class="orders-counter bg-red-500 text-white">1</span>
                </a>
            </li>
            <li>
                <a href="#" class="menu-item">
                    <i class="fas fa-chart-bar text-gray-400 menu-icon"></i>
                    <span>Statistics</span>
                </a>
            </li>
            <li>
                <a href="{{ route('finance') }}" class="menu-item {{ request()->routeIs('finance') ? 'active' : '' }}">
                    <i class="fas fa-dollar-sign text-gray-400 menu-icon"></i>
                    <span>Finance</span>
                </a>
            </li>
        </ul>
    </div>
</aside>

        @yield('content')
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
                width: '100%',
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