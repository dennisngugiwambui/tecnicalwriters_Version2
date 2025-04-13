{{-- resources/views/orders/index.blade.php --}}
@extends('layouts.app')

@section('styles')
<style>
    .header {
        @apply bg-white border-b border-gray-200 fixed w-full z-50;
    }
    
    .sidebar {
        @apply fixed left-0 top-16 h-screen w-64 bg-white border-r border-gray-200 overflow-y-auto;
    }
    
    .main-content {
        @apply ml-64 pt-16 bg-gray-50 min-h-screen;
    }
    
    .nav-item {
        @apply flex items-center px-4 py-2 text-gray-600 hover:bg-gray-50;
    }
    
    .nav-item.active {
        @apply bg-blue-50 text-blue-600;
    }
    
    .filter-card {
        @apply bg-white rounded-lg shadow-sm p-4 mb-4;
    }
    
    .button-primary {
        @apply bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700;
    }
    
    .select-input {
        @apply w-full rounded-lg border-gray-300 shadow-sm;
    }
</style>
@endsection

@section('content')
<header class="header">
    <div class="flex justify-between items-center px-4 h-16">
        <div class="flex items-center">
            <button class="lg:hidden mr-2" @click="sidebarOpen = !sidebarOpen">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                </svg>
            </button>
            <a href="{{ route('dashboard') }}" class="flex items-center">
                <img src="{{ asset('images/logo.svg') }}" alt="Logo" class="h-8">
            </a>
        </div>
        
        <div class="flex items-center space-x-4">
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center space-x-2">
                    <span class="text-sm font-medium">{{ auth()->user()->name }}</span>
                    <span class="text-xs text-gray-500">Looking for orders</span>
                </button>
                
                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1">
                    <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                    <a href="{{ route('logout') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</a>
                </div>
            </div>
        </div>
    </div>
</header>

<nav class="sidebar">
    <div class="py-4">
        <div class="px-4 mb-4">
            <h2 class="text-lg font-medium">Orders</h2>
        </div>
        
        <ul>
            <li>
                <a href="{{ route('orders.available') }}" class="nav-item active">
                    <svg class="w-5 h-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Available
                </a>
            </li>
            {{-- Similar structure for other menu items --}}
        </ul>
    </div>
</nav>

<main class="main-content">
    <div class="p-6">
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-semibold text-gray-900">Available Orders</h1>
                <div class="flex items-center space-x-4">
                    <div class="bg-gray-100 rounded-full px-3 py-1 text-sm">
                        <span class="text-gray-600">Rating:</span>
                        <span class="font-medium">80%</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="filter-card">
            <form action="{{ route('orders.filter') }}" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Level</label>
                        <select name="level" class="select-input">
                            <option value="">All available</option>
                            <option value="high_school">High school</option>
                            <option value="undergraduate">Undergraduate</option>
                            <option value="graduate">Graduate</option>
                            <option value="phd">PhD</option>
                        </select>
                    </div>
                    
                    {{-- Add other filter inputs similarly --}}
                </div>
                
                <div class="mt-4 flex justify-end">
                    <button type="submit" class="button-primary">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow-sm mt-6">
            <div class="p-4">
                @if($orders->isEmpty())
                    <p class="text-gray-500 text-center py-8">There are currently no orders to display.</p>
                @else
                    {{-- Orders table implementation --}}
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-3 px-4">Order ID</th>
                                <th class="text-left py-3 px-4">Topic Title</th>
                                <th class="text-left py-3 px-4">Discipline</th>
                                <th class="text-left py-3 px-4">Pages</th>
                                <th class="text-left py-3 px-4">Deadline</th>
                                <th class="text-left py-3 px-4">Cost</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr class="border-b hover:bg-gray-50">
                                    {{-- Order details --}}
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</main>
@endsection

@section('scripts')
<script>
    // Add any necessary JavaScript
</script>
@endsection

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
