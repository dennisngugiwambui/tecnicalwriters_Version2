<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Technical Writers - Expert Academic Writing Services</title>
    <meta name="description" content="Join our community of expert writers dedicated to delivering high-quality, original content that helps clients achieve academic and professional excellence.">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="icon" type="image/jpeg" href="writers/technicalwriters2.jpg">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('writers/favicon.svg') }}">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        
        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fade-in {
            animation: fadeIn 0.8s ease-out forwards;
        }
        
        /* Mobile menu animation */
        @keyframes slideIn {
            from { transform: translateX(100%); }
            to { transform: translateX(0); }
        }
        
        @keyframes slideOut {
            from { transform: translateX(0); }
            to { transform: translateX(100%); }
        }
        
        .mobile-menu-open {
            display: block;
            animation: slideIn 0.3s forwards;
        }
        
        .mobile-menu-close {
            animation: slideOut 0.3s forwards;
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #3b82f6;
            border-radius: 5px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #2563eb;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header/Navigation -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo and site name -->
                <div class="flex items-center">
                    <a href="/" class="flex items-center">
                        <span class="text-2xl font-bold text-indigo-600">Technical<span class="text-blue-500">Writers</span></span>
                    </a>
                </div>
                
                <!-- Main Navigation - Desktop -->
                <nav class="hidden md:flex items-center space-x-4">
                    <a href="#features" class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50 transition duration-150">How It Works</a>
                    <a href="#testimonials" class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50 transition duration-150">Testimonials</a>
                    <a href="#faq" class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50 transition duration-150">FAQ</a>
                    <a href="#join-us" class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50 transition duration-150">Join Us</a>
                    
                    <!-- Authentication Links -->
                    @if (Route::has('login'))
                        <div class="flex space-x-2 ml-4">
                            @auth
                                <a href="{{ url('/home') }}" class="rounded-md px-4 py-2 bg-indigo-600 text-white hover:bg-indigo-700 transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="rounded-md px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Log in
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="rounded-md px-4 py-2 bg-indigo-600 text-white hover:bg-indigo-700 transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Register
                                    </a>
                                @endif
                            @endauth
                        </div>
                    @endif
                </nav
                
                <!-- Mobile menu button -->
                <div class="flex items-center md:hidden">
                    <button type="button" id="mobile-menu-button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                        <span class="sr-only">Open main menu</span>
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mobile menu, show/hide based on menu state -->
        <div id="mobile-menu" class="hidden md:hidden fixed top-16 right-0 bottom-0 w-4/5 max-w-sm bg-white shadow-xl z-50 transform transition-transform border-l border-gray-200 overflow-y-auto">
            <div class="px-4 pt-4 pb-6 space-y-4">
                <a href="#features" class="block px-4 py-3 rounded-md text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50">How It Works</a>
                <a href="#testimonials" class="block px-4 py-3 rounded-md text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50">Testimonials</a>
                <a href="#faq" class="block px-4 py-3 rounded-md text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50">FAQ</a>
                <a href="#join-us" class="block px-4 py-3 rounded-md text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50">Join Us</a>
                
                <!-- Authentication Links (Mobile) -->
                @if (Route::has('login'))
                    <div class="pt-4 pb-3 border-t border-gray-200">
                        @auth
                            <a href="{{ url('/home') }}" class="block w-full text-center rounded-md px-4 py-3 bg-indigo-600 text-white hover:bg-indigo-700 transition">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="block w-full text-center rounded-md px-4 py-3 mb-3 border border-gray-300 text-gray-700 hover:bg-gray-50 transition">
                                Log in
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="block w-full text-center rounded-md px-4 py-3 bg-indigo-600 text-white hover:bg-indigo-700 transition">
                                    Register
                                </a>
                            @endif
                        @endauth
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Mobile menu backdrop/overlay -->
        <div id="mobile-menu-backdrop" class="hidden fixed inset-0 bg-black bg-opacity-25 z-40 md:hidden"></div>
    </header>

    <!-- Main Content -->
    <main>
        <!-- Hero Section -->
        <section class="relative bg-gradient-to-br from-indigo-700 to-blue-800 py-16 md:py-24 px-4 md:px-8">
            <div class="max-w-7xl mx-auto">
                <div class="flex flex-col lg:flex-row items-center justify-between">
                    <div class="w-full lg:w-1/2 text-center lg:text-left mb-10 lg:mb-0 animate-fade-in">
                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight mb-6">
                            Welcome to <span class="text-yellow-300">Technical Writers</span>
                        </h1>
                        <p class="text-lg md:text-xl text-blue-100 mb-8 max-w-2xl mx-auto lg:mx-0">
                            Join our community of expert writers dedicated to delivering high-quality, original content that helps clients achieve academic and professional excellence.
                        </p>
                        <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4 justify-center lg:justify-start">
                            @auth
                                <a href="{{ url('/home') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-indigo-700 bg-white hover:bg-gray-50 transition duration-300 shadow-lg hover:shadow-xl">
                                    <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-indigo-700 bg-white hover:bg-gray-50 transition duration-300 shadow-lg hover:shadow-xl">
                                    <i class="fas fa-search mr-2"></i> Browse Orders
                                </a>
                                <a href="#join-us" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-yellow-500 hover:bg-yellow-600 transition duration-300 shadow-lg hover:shadow-xl">
                                    <i class="fas fa-user-plus mr-2"></i> Join Our Team
                                </a>
                            @endauth
                        </div>
                    </div>
                    <div class="w-full lg:w-1/2 flex justify-center lg:justify-end animate-fade-in" style="animation-delay: 0.3s;">
                        <!-- Using inline SVG instead of relying on an external file -->
                        <svg class="w-full max-w-lg h-auto rounded-lg shadow-2xl transform hover:scale-105 transition duration-500" viewBox="0 0 800 500" xmlns="http://www.w3.org/2000/svg">
                            <!-- SVG content is the same as the hero image but embedded directly -->
                            <defs>
                                <linearGradient id="bg-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                  <stop offset="0%" stop-color="#4338CA" />
                                  <stop offset="100%" stop-color="#1E40AF" />
                                </linearGradient>
                                
                                <pattern id="paper-texture" width="100" height="100" patternUnits="userSpaceOnUse">
                                  <rect width="100" height="100" fill="#ffffff" fill-opacity="0.03"/>
                                  <path d="M0 0L100 100M100 0L0 100" stroke="#ffffff" stroke-width="0.5" stroke-opacity="0.05"/>
                                </pattern>
                                
                                <filter id="glow" x="-20%" y="-20%" width="140%" height="140%">
                                  <feGaussianBlur stdDeviation="5" result="blur"/>
                                  <feComposite in="SourceGraphic" in2="blur" operator="over"/>
                                </filter>
                            </defs>
                              
                            <!-- Main background -->
                            <rect width="800" height="500" fill="url(#bg-gradient)"/>
                            <rect width="800" height="500" fill="url(#paper-texture)"/>
                              
                            <!-- Book stack -->
                            <g transform="translate(600, 300) rotate(-10)">
                                <rect x="-60" y="-20" width="120" height="20" rx="2" fill="#F59E0B" />
                                <rect x="-55" y="-20" width="110" height="18" rx="1" fill="#F59E0B" stroke="#FBBF24" stroke-width="1"/>
                                <rect x="-70" y="-40" width="130" height="20" rx="2" fill="#EF4444" />
                                <rect x="-65" y="-40" width="120" height="18" rx="1" fill="#EF4444" stroke="#FCA5A5" stroke-width="1"/>
                                <rect x="-65" y="-60" width="125" height="20" rx="2" fill="#10B981" />
                                <rect x="-60" y="-60" width="115" height="18" rx="1" fill="#10B981" stroke="#6EE7B7" stroke-width="1"/>
                                <rect x="-75" y="-80" width="135" height="20" rx="2" fill="#3B82F6" />
                                <rect x="-70" y="-80" width="125" height="18" rx="1" fill="#3B82F6" stroke="#93C5FD" stroke-width="1"/>
                            </g>
                              
                            <!-- Floating papers -->
                            <g transform="translate(150, 230) rotate(15)">
                                <rect x="-80" y="-100" width="160" height="200" rx="5" fill="#F3F4F6" stroke="#E5E7EB" stroke-width="2"/>
                                <line x1="-60" y1="-60" x2="60" y2="-60" stroke="#9CA3AF" stroke-width="1.5"/>
                                <line x1="-60" y1="-40" x2="40" y2="-40" stroke="#9CA3AF" stroke-width="1.5"/>
                                <line x1="-60" y1="-20" x2="60" y2="-20" stroke="#9CA3AF" stroke-width="1.5"/>
                                <line x1="-60" y1="0" x2="50" y2="0" stroke="#9CA3AF" stroke-width="1.5"/>
                                <line x1="-60" y1="20" x2="60" y2="20" stroke="#9CA3AF" stroke-width="1.5"/>
                                <line x1="-60" y1="40" x2="30" y2="40" stroke="#9CA3AF" stroke-width="1.5"/>
                                <line x1="-60" y1="60" x2="60" y2="60" stroke="#9CA3AF" stroke-width="1.5"/>
                            </g>
                              
                            <g transform="translate(400, 200) rotate(-10)">
                                <rect x="-90" y="-120" width="180" height="240" rx="5" fill="#F9FAFB" stroke="#E5E7EB" stroke-width="2"/>
                                <line x1="-70" y1="-80" x2="70" y2="-80" stroke="#9CA3AF" stroke-width="1.5"/>
                                <line x1="-70" y1="-60" x2="50" y2="-60" stroke="#9CA3AF" stroke-width="1.5"/>
                                <line x1="-70" y1="-40" x2="70" y2="-40" stroke="#9CA3AF" stroke-width="1.5"/>
                                <line x1="-70" y1="-20" x2="60" y2="-20" stroke="#9CA3AF" stroke-width="1.5"/>
                                <line x1="-70" y1="0" x2="70" y2="0" stroke="#9CA3AF" stroke-width="1.5"/>
                                <line x1="-70" y1="20" x2="40" y2="20" stroke="#9CA3AF" stroke-width="1.5"/>
                                <line x1="-70" y1="40" x2="70" y2="40" stroke="#9CA3AF" stroke-width="1.5"/>
                                <line x1="-70" y1="60" x2="60" y2="60" stroke="#9CA3AF" stroke-width="1.5"/>
                                <line x1="-70" y1="80" x2="70" y2="80" stroke="#9CA3AF" stroke-width="1.5"/>
                            </g>
                              
                            <!-- Laptop Screen content continued -->
<rect x="-85" y="-75" width="140" height="8" rx="2" fill="#F3F4F6" fill-opacity="0.15"/>
<rect x="-85" y="-60" width="160" height="8" rx="2" fill="#F3F4F6" fill-opacity="0.15"/>
<rect x="-85" y="-45" width="130" height="8" rx="2" fill="#F3F4F6" fill-opacity="0.15"/>
<rect x="-85" y="-30" width="150" height="8" rx="2" fill="#F3F4F6" fill-opacity="0.15"/>
</g>

<!-- Glowing pen -->
<g transform="translate(500, 320) rotate(45)" filter="url(#glow)">
    <rect x="-5" y="-60" width="10" height="120" rx="5" fill="#FBBF24"/>
    <rect x="-4" y="-59" width="8" height="118" rx="4" fill="#FCD34D"/>
    <path d="M-5,-60 L5,-60 L0,-80 Z" fill="#F59E0B"/>
</g>

<!-- Floating ideas -->
<g fill="#FCD34D" filter="url(#glow)">
    <circle cx="250" cy="100" r="5" />
    <circle cx="450" cy="120" r="4" />
    <circle cx="600" cy="200" r="6" />
    <circle cx="350" cy="150" r="3" />
    <circle cx="550" cy="90" r="4" />
    <circle cx="150" cy="140" r="5" />
    <circle cx="650" cy="350" r="3" />
</g>

<!-- Light bulb idea -->
<g transform="translate(200, 160)" filter="url(#glow)">
    <path d="M0,-25 C-15,-25 -25,-15 -25,0 C-25,12 -15,20 -8,25 L8,25 C15,20 25,12 25,0 C25,-15 15,-25 0,-25 Z" fill="#FCD34D" fill-opacity="0.8"/>
    <rect x="-6" y="25" width="12" height="5" rx="2" fill="#F59E0B"/>
    <rect x="-6" y="30" width="12" height="5" rx="2" fill="#F59E0B"/>
</g>

<!-- Quotation marks -->
<g transform="translate(450, 350)" filter="url(#glow)">
    <path d="M-20,-15 L-10,-15 L-5,0 L-15,0 Z" fill="#FBBF24" fill-opacity="0.8"/>
    <path d="M5,-15 L15,-15 L20,0 L10,0 Z" fill="#FBBF24" fill-opacity="0.8"/>
</g>
                        </svg>
                    </div>
                </div>
            </div>
            
            <!-- Wave Pattern -->
            <div class="absolute bottom-0 left-0 right-0">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" class="w-full h-auto" preserveAspectRatio="none">
                    <path fill="#ffffff" fill-opacity="1" d="M0,96L48,112C96,128,192,160,288,186.7C384,213,480,235,576,224C672,213,768,171,864,165.3C960,160,1056,192,1152,197.3C1248,203,1344,181,1392,170.7L1440,160L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
                </svg>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="bg-white py-12 lg:py-20 px-4 md:px-8">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-12 animate-on-scroll">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Why Choose Technical Writers?</h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">Our platform connects skilled writers with clients seeking quality content across various disciplines.</p>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 border border-gray-100 text-center hover:border-blue-200 animate-on-scroll">
                        <div class="w-16 h-16 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-users text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">5,000+</h3>
                        <p class="text-gray-600">Professional Writers</p>
                    </div>
                    
                    <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 border border-gray-100 text-center hover:border-blue-200 animate-on-scroll" style="animation-delay: 0.2s;">
                        <div class="w-16 h-16 bg-green-100 text-green-700 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-check-circle text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">98.7%</h3>
                        <p class="text-gray-600">Client Satisfaction</p>
                    </div>
                    
                    <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 border border-gray-100 text-center hover:border-blue-200 animate-on-scroll" style="animation-delay: 0.4s;">
                        <div class="w-16 h-16 bg-purple-100 text-purple-700 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-file-alt text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">200,000+</h3>
                        <p class="text-gray-600">Completed Orders</p>
                    </div>
                    
                    <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 border border-gray-100 text-center hover:border-blue-200 animate-on-scroll" style="animation-delay: 0.6s;">
                        <div class="w-16 h-16 bg-yellow-100 text-yellow-700 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-graduation-cap text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">50+</h3>
                        <p class="text-gray-600">Academic Disciplines</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="bg-gray-50 py-16 md:py-24 px-4 md:px-8">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16 animate-on-scroll">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">How It Works</h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">Our streamlined process makes it easy to find and complete writing assignments.</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                    <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 animate-on-scroll">
                        <div class="w-14 h-14 bg-indigo-600 text-white rounded-full flex items-center justify-center mb-6 mx-auto md:mx-0">
                            <span class="text-xl font-bold">1</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4 text-center md:text-left">Browse Available Orders</h3>
                        <p class="text-gray-600 text-center md:text-left">Browse through our marketplace of available writing assignments across various disciplines and complexity levels.</p>
                    </div>
                    
                    <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 animate-on-scroll" style="animation-delay: 0.3s;">
                        <div class="w-14 h-14 bg-indigo-600 text-white rounded-full flex items-center justify-center mb-6 mx-auto md:mx-0">
                            <span class="text-xl font-bold">2</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4 text-center md:text-left">Accept & Complete Work</h3>
                        <p class="text-gray-600 text-center md:text-left">Select orders that match your expertise and schedule. Complete the work within the specified deadline.</p>
                    </div>
                    
                    <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 animate-on-scroll" style="animation-delay: 0.6s;">
                        <div class="w-14 h-14 bg-indigo-600 text-white rounded-full flex items-center justify-center mb-6 mx-auto md:mx-0">
                            <span class="text-xl font-bold">3</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4 text-center md:text-left">Get Paid & Build Reputation</h3>
                        <p class="text-gray-600 text-center md:text-left">Receive payment for your work and build your reputation to access higher-paying opportunities.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section id="testimonials" class="bg-white py-16 md:py-24 px-4 md:px-8">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16 animate-on-scroll">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Our Writers Love Us</h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">Hear from some of our top-performing writers about their experience.</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="bg-white p-8 rounded-xl shadow-lg border border-gray-100 relative animate-on-scroll">
                        <div class="absolute -top-5 left-8 w-10 h-10 bg-indigo-600 text-white rounded-full flex items-center justify-center">
                            <i class="fas fa-quote-left"></i>
                        </div>
                        <p class="text-gray-600 mb-6 pt-3">
                            "Technical Writers has transformed my freelancing career. The consistent flow of orders and fair pay structure has allowed me to work full-time as a writer while pursuing my PhD."
                        </p>
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-gray-300 rounded-full overflow-hidden mr-4">
                                <div class="w-full h-full flex items-center justify-center bg-indigo-100 text-indigo-800 font-bold">
                                    JD
                                </div>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900">John D.</h4>
                                <p class="text-gray-600 text-sm">Academic Writer | 3+ years</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white p-8 rounded-xl shadow-lg border border-gray-100 relative animate-on-scroll" style="animation-delay: 0.3s;">
                        <div class="absolute -top-5 left-8 w-10 h-10 bg-indigo-600 text-white rounded-full flex items-center justify-center">
                            <i class="fas fa-quote-left"></i>
                        </div>
                        <p class="text-gray-600 mb-6 pt-3">
                            "The flexibility to choose my own assignments and work from anywhere has been incredible. The platform is intuitive, and the support team is always there when you need them."
                        </p>
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-gray-300 rounded-full overflow-hidden mr-4">
                                <div class="w-full h-full flex items-center justify-center bg-green-100 text-green-800 font-bold">
                                    SM
                                </div>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900">Sarah M.</h4>
                                <p class="text-gray-600 text-sm">Technical Writer | 2+ years</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white p-8 rounded-xl shadow-lg border border-gray-100 relative animate-on-scroll" style="animation-delay: 0.6s;">
                        <div class="absolute -top-5 left-8 w-10 h-10 bg-indigo-600 text-white rounded-full flex items-center justify-center">
                            <i class="fas fa-quote-left"></i>
                        </div>
                        <p class="text-gray-600 mb-6 pt-3">
                            "As an ESL professional, this platform has helped me grow my writing skills while earning consistently. The feedback system is particularly helpful for improvement."
                        </p>
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-gray-300 rounded-full overflow-hidden mr-4">
                                <div class="w-full h-full flex items-center justify-center bg-purple-100 text-purple-800 font-bold">
                                    RT
                                </div>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900">Robert T.</h4>
                                <p class="text-gray-600 text-sm">ESL Specialist | 4+ years</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Join Us Section -->
        <section id="join-us" class="bg-gradient-to-br from-indigo-700 to-blue-800 py-16 md:py-24 px-4 md:px-8 relative">
            <div class="max-w-7xl mx-auto relative z-10">
                <div class="text-center mb-12 animate-on-scroll">
                    <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Join Our Team Today</h2>
                    <p class="text-xl text-blue-100 max-w-3xl mx-auto">
                        Become part of our growing community of professional writers and start earning on your own terms.
                    </p>
                </div>
                
                <div class="bg-white p-8 md:p-10 rounded-2xl shadow-2xl max-w-3xl mx-auto animate-on-scroll">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-check-circle text-green-500 mr-2"></i> 
                                Benefits
                            </h3>
                            <ul class="space-y-2 text-gray-700">
                                <li class="flex items-start">
                                    <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                                    <span>Competitive rates based on complexity</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                                    <span>Weekly payments via secure methods</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                                    <span>Flexible work hours and location</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                                    <span>Performance bonuses and incentives</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                                    <span>Professional development opportunities</span>
                                </li>
                            </ul>
                        </div>
                        
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-user-graduate text-blue-500 mr-2"></i> 
                                Requirements
                            </h3>
                            <ul class="space-y-2 text-gray-700">
                                <li class="flex items-start">
                                    <i class="fas fa-check text-blue-500 mt-1 mr-2"></i>
                                    <span>Bachelor's degree or higher</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check text-blue-500 mt-1 mr-2"></i>
                                    <span>Strong command of English language</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check text-blue-500 mt-1 mr-2"></i>
                                    <span>Excellent research and writing skills</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check text-blue-500 mt-1 mr-2"></i>
                                    <span>Attention to detail and meeting deadlines</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check text-blue-500 mt-1 mr-2"></i>
                                    <span>Reliable internet connection</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        @auth
                            <a href="{{ url('/home') }}" class="inline-flex items-center justify-center px-8 py-4 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition duration-300 shadow-lg hover:shadow-xl w-full md:w-auto">
                                <i class="fas fa-tachometer-alt mr-2"></i> Go to Dashboard
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-4 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition duration-300 shadow-lg hover:shadow-xl w-full md:w-auto">
                                <i class="fas fa-user-plus mr-2"></i> Apply Now
                            </a>
                        @endauth
                    </div>
                </div>
                
                <div class="mt-12 text-center">
                    @guest
                        <p class="text-blue-100">Already have an account? <a href="{{ route('login') }}" class="text-white font-medium underline hover:text-yellow-300 transition duration-300">Sign in here</a></p>
                    @endguest
                </div>
            </div>
            
            <!-- Background Pattern -->
            <div class="absolute inset-0 overflow-hidden opacity-10">
                <div class="absolute top-0 left-0 w-full h-full">
                    <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%">
                        <defs>
                            <pattern id="dots" width="20" height="20" patternUnits="userSpaceOnUse">
                                <circle fill="white" cx="10" cy="10" r="1.5" />
                            </pattern>
                        </defs>
                        <rect width="100%" height="100%" fill="url(#dots)" />
                    </svg>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section id="faq" class="bg-white py-16 md:py-24 px-4 md:px-8">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-16 animate-on-scroll">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Frequently Asked Questions</h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">Find answers to common questions about working with Technical Writers.</p>
                </div>
                
                <div class="space-y-6">
                    <div class="bg-gray-50 rounded-xl p-6 md:p-8 shadow-md hover:shadow-lg transition-shadow duration-300 animate-on-scroll">
                        <button class="flex justify-between items-center w-full text-left focus:outline-none faq-toggle">
                            <h3 class="text-lg md:text-xl font-bold text-gray-900">How do I get started as a writer?</h3>
                            <i class="fas fa-chevron-down text-indigo-600 transform transition-transform duration-300"></i>
                        </button>
                        <div class="mt-4 faq-content hidden">
                            <p class="text-gray-600">
                                To get started, create an account on our platform, complete your profile with your academic background and areas of expertise, and pass our qualification test. Once approved, you'll be able to browse and accept available orders that match your skills.
                            </p>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 rounded-xl p-6 md:p-8 shadow-md hover:shadow-lg transition-shadow duration-300 animate-on-scroll" style="animation-delay: 0.2s;">
                        <button class="flex justify-between items-center w-full text-left focus:outline-none faq-toggle">
                            <h3 class="text-lg md:text-xl font-bold text-gray-900">How and when do I get paid?</h3>
                            <i class="fas fa-chevron-down text-indigo-600 transform transition-transform duration-300"></i>
                        </button>
                        <div class="mt-4 faq-content hidden">
                            <p class="text-gray-600">
                                Payments are processed weekly for all completed and approved orders. You can choose from multiple payment methods including bank transfer, PayPal, and mobile money services like M-Pesa. Payment processing typically takes 1-3 business days depending on your chosen method.
                            </p>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 rounded-xl p-6 md:p-8 shadow-md hover:shadow-lg transition-shadow duration-300 animate-on-scroll" style="animation-delay: 0.4s;">
                        <button class="flex justify-between items-center w-full text-left focus:outline-none faq-toggle">
                            <h3 class="text-lg md:text-xl font-bold text-gray-900">What types of writing assignments are available?</h3>
                            <i class="fas fa-chevron-down text-indigo-600 transform transition-transform duration-300"></i>
                        </button>
                        <div class="mt-4 faq-content hidden">
                            <p class="text-gray-600">
                                Our platform offers a wide range of writing assignments including academic essays, research papers, case studies, literature reviews, technical writing, business reports, and more. Assignments span across various disciplines such as humanities, business, STEM fields, social sciences, and many others.
                            </p>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 rounded-xl p-6 md:p-8 shadow-md hover:shadow-lg transition-shadow duration-300 animate-on-scroll" style="animation-delay: 0.6s;">
                        <button class="flex justify-between items-center w-full text-left focus:outline-none faq-toggle">
                            <h3 class="text-lg md:text-xl font-bold text-gray-900">How is my performance evaluated?</h3>
                            <i class="fas fa-chevron-down text-indigo-600 transform transition-transform duration-300"></i>
                        </button>
                        <div class="mt-4 faq-content hidden">
                            <p class="text-gray-600">
                                Your performance is evaluated based on several factors including quality of work, adherence to instructions, meeting deadlines, client satisfaction, and professionalism. Regular positive reviews and high ratings will improve your writer rank, giving you access to higher-paying assignments and special privileges.
                            </p>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 rounded-xl p-6 md:p-8 shadow-md hover:shadow-lg transition-shadow duration-300 animate-on-scroll" style="animation-delay: 0.8s;">
                        <button class="flex justify-between items-center w-full text-left focus:outline-none faq-toggle">
                            <h3 class="text-lg md:text-xl font-bold text-gray-900">Is there a minimum time commitment required?</h3>
                            <i class="fas fa-chevron-down text-indigo-600 transform transition-transform duration-300"></i>
                        </button>
                        <div class="mt-4 faq-content hidden">
                            <p class="text-gray-600">
                                No, there's no minimum time commitment required. You can work as much or as little as you want. However, maintaining an active status by completing at least a few orders per month will help you build your reputation and keep your account in good standing.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Call to Action -->
        <section class="bg-gradient-to-br from-gray-800 to-gray-900 py-16 px-4 md:px-8 text-center">
            <div class="max-w-5xl mx-auto animate-on-scroll">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">Ready to Start Your Writing Journey?</h2>
                <p class="text-xl text-gray-300 mb-8 max-w-3xl mx-auto">
                    Join thousands of writers who have found flexible, rewarding work with Technical Writers.
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-6">
                    @auth
                        <a href="{{ url('/home') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-indigo-700 bg-white hover:bg-gray-50 transition duration-300 shadow-lg hover:shadow-xl w-full sm:w-auto">
                            <i class="fas fa-tachometer-alt mr-2"></i> Go to Dashboard
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-indigo-700 bg-white hover:bg-gray-50 transition duration-300 shadow-lg hover:shadow-xl w-full sm:w-auto">
                            <i class="fas fa-user-plus mr-2"></i> Create an Account
                        </a>
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-6 py-3 border border-white text-base font-medium rounded-md text-white hover:bg-white hover:text-indigo-700 transition duration-300 w-full sm:w-auto">
                            <i class="fas fa-sign-in-alt mr-2"></i> Sign In
                        </a>
                    @endauth
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
<footer class="bg-gray-900 text-white py-12 px-4 md:px-8">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">
            <!-- Company Info -->
            <div>
                <h3 class="text-lg font-bold mb-4">Technical Writers</h3>
                <p class="text-gray-400 mb-4">Connecting talented writers with clients seeking quality content.</p>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-400 hover:text-white transition-colors duration-300">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors duration-300">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors duration-300">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors duration-300">
                        <i class="fab fa-instagram"></i>
                    </a>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div>
                <h3 class="text-lg font-bold mb-4">Quick Links</h3>
                <ul class="space-y-2">
                    <li><a href="#features" class="text-gray-400 hover:text-white transition-colors duration-300">How It Works</a></li>
                    <li><a href="#testimonials" class="text-gray-400 hover:text-white transition-colors duration-300">Testimonials</a></li>
                    <li><a href="#join-us" class="text-gray-400 hover:text-white transition-colors duration-300">Join Us</a></li>
                    <li><a href="#faq" class="text-gray-400 hover:text-white transition-colors duration-300">FAQ</a></li>
                </ul>
            </div>
            
            <!-- Resources -->
            <div>
                <h3 class="text-lg font-bold mb-4">Resources</h3>
                <ul class="space-y-2">
                    <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-300">Writer Guidelines</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-300">Blog</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-300">Knowledge Base</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-300">Writing Tools</a></li>
                </ul>
            </div>
            
            <!-- Contact -->
            <div>
                <h3 class="text-lg font-bold mb-4">Contact Us</h3>
                <ul class="space-y-2">
                    <li class="flex items-start">
                        <i class="fas fa-envelope text-indigo-400 mt-1 mr-2"></i>
                        <a href="mailto:support@technicalwriters.com" class="text-gray-400 hover:text-white transition-colors duration-300">support@technicalwriters.com</a>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-phone-alt text-indigo-400 mt-1 mr-2"></i>
                        <a href="tel:+1234567890" class="text-gray-400 hover:text-white transition-colors duration-300">+1 (234) 567-890</a>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-map-marker-alt text-indigo-400 mt-1 mr-2"></i>
                        <span class="text-gray-400">Nairobi, Kenya</span>
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- Bottom Footer -->
        <div class="pt-8 border-t border-gray-800 flex flex-col md:flex-row justify-between items-center">
            <p class="text-gray-400 text-center md:text-left mb-4 md:mb-0">
                &copy; {{ date('Y') }} Technical Writers. All rights reserved.
            </p>
            <div class="flex space-x-6">
                <a href="#" class="text-gray-400 hover:text-white transition-colors duration-300 text-sm">Privacy Policy</a>
                <a href="#" class="text-gray-400 hover:text-white transition-colors duration-300 text-sm">Terms of Service</a>
                <a href="#" class="text-gray-400 hover:text-white transition-colors duration-300 text-sm">Disclaimer</a>
            </div>
        </div>
    </div>
</footer>

<!-- Back to Top Button -->
<button id="backToTop" class="fixed bottom-6 right-6 bg-indigo-600 text-white w-12 h-12 rounded-full flex items-center justify-center shadow-lg transform transition-transform duration-300 scale-0 hover:bg-indigo-700 focus:outline-none">
    <i class="fas fa-arrow-up"></i>
</button>

<!-- Mobile Menu Overlay -->
<div id="mobile-menu-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden"></div>

<!-- Scripts -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mobile menu toggle with slide animation
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileMenuBackdrop = document.getElementById('mobile-menu-backdrop');
        const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');
        let menuOpen = false;
        
        function toggleMobileMenu() {
            if (menuOpen) {
                // Close menu
                mobileMenu.classList.add('mobile-menu-close');
                mobileMenuOverlay.classList.add('hidden');
                
                setTimeout(() => {
                    mobileMenu.classList.remove('mobile-menu-open');
                    mobileMenu.classList.remove('mobile-menu-close');
                    mobileMenu.classList.add('hidden');
                }, 280);
            } else {
                // Open menu
                mobileMenu.classList.remove('hidden');
                mobileMenu.classList.add('mobile-menu-open');
                mobileMenuOverlay.classList.remove('hidden');
            }
            menuOpen = !menuOpen;
        }
        
        mobileMenuButton.addEventListener('click', toggleMobileMenu);
        mobileMenuOverlay.addEventListener('click', toggleMobileMenu);
        
        // Close mobile menu when clicking on a link
        const mobileMenuLinks = mobileMenu.querySelectorAll('a');
        mobileMenuLinks.forEach(link => {
            link.addEventListener('click', toggleMobileMenu);
        });
        
        // FAQ Toggles
        const faqToggles = document.querySelectorAll('.faq-toggle');
        
        faqToggles.forEach(toggle => {
            toggle.addEventListener('click', function() {
                const content = this.nextElementSibling;
                const icon = this.querySelector('i');
                
                content.classList.toggle('hidden');
                icon.classList.toggle('rotate-180');
                
                // Close other FAQs
                faqToggles.forEach(otherToggle => {
                    if (otherToggle !== toggle) {
                        const otherContent = otherToggle.nextElementSibling;
                        const otherIcon = otherToggle.querySelector('i');
                        
                        otherContent.classList.add('hidden');
                        otherIcon.classList.remove('rotate-180');
                    }
                });
            });
        });
        
        // Animation on scroll
        const animateOnScroll = function() {
            const elementsToAnimate = document.querySelectorAll('.animate-on-scroll');
            
            elementsToAnimate.forEach(element => {
                const elementPosition = element.getBoundingClientRect().top;
                const windowHeight = window.innerHeight;
                
                if (elementPosition < windowHeight * 0.8) {
                    element.classList.add('animate-fade-in');
                }
            });
        };
        
        // Add animation class to elements that should animate on scroll
        const sections = document.querySelectorAll('section > div, .grid > div');
        sections.forEach(section => {
            section.classList.add('animate-on-scroll');
        });
        
        // Back to Top Button
        const backToTopButton = document.getElementById('backToTop');
        
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTopButton.classList.replace('scale-0', 'scale-100');
            } else {
                backToTopButton.classList.replace('scale-100', 'scale-0');
            }
            
            // Run animations on scroll
            animateOnScroll();
        });
        
        backToTopButton.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    const headerOffset = 80; // Account for fixed header
                    const elementPosition = targetElement.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                    
                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth'
                    });
                }
            });
        });
        
        // Run once on page load
        animateOnScroll();
    });
</script>
</body>
</html>