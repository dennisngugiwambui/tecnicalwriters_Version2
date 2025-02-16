<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechnicalWriters</title>
    <link rel="icon" type="image/jpeg" href="writers/technicalwriters2.jpg">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome for icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen px-4">
    <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-lg text-center">
        <!-- Logo -->
        <div class="flex flex-col items-center">
            <img src="{{ asset('writers/technicalwriters.jpg') }}" alt="TechnicalWriters Logo" class="w-20 mb-3">
            <h1 class="text-2xl font-bold text-gray-800">TechnicalWriters</h1>
        </div>

        <!-- Display All Validation Errors -->
        @if ($errors->any())
            <div class="mb-4 text-left">
                <ul class="list-disc list-inside text-sm text-red-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Registration Form -->
        <form method="POST" action="{{ route('register') }}" class="mt-6">
            @csrf

            <!-- Name Input -->
            <div class="relative mb-4">
                <span class="absolute left-3 top-2 text-green-500"><i class="fas fa-user"></i></span>
                <input
                    type="text"
                    name="name"
                    class="w-full pl-10 px-3 py-2 border @error('name') border-red-500 @else border-gray-300 @enderror rounded-full focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                    placeholder="Your Name"
                    value="{{ old('name') }}"
                    required
                    autocomplete="name"
                    autofocus
                />
                @error('name')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Phoene Input -->
            <div class="relative mb-4">
                <span class="absolute left-3 top-2 text-green-500"><i class="fas fa-phone"></i></span>
                <input
                    type="number"
                    name="phone"
                    class="w-full pl-10 px-3 py-2 border @error('phone') border-red-500 @else border-gray-300 @enderror rounded-full focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                    placeholder="Phone number"
                    value="{{ old('phone') }}"
                    required
                    autocomplete="phone"
                    autofocus
                />
                @error('phone')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Email Input -->
            <div class="relative mb-4">
                <span class="absolute left-3 top-2 text-green-500"><i class="fas fa-envelope"></i></span>
                <input
                    type="email"
                    name="email"
                    class="w-full pl-10 px-3 py-2 border @error('email') border-red-500 @else border-gray-300 @enderror rounded-full focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                    placeholder="Your Email"
                    value="{{ old('email') }}"
                    required
                    autocomplete="email"
                />
                @error('email')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password Input -->
            <div class="relative mb-4">
                <span class="absolute left-3 top-2 text-green-500"><i class="fas fa-lock"></i></span>
                <input
                    type="password"
                    name="password"
                    class="w-full pl-10 px-3 py-2 border @error('password') border-red-500 @else border-gray-300 @enderror rounded-full focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                    placeholder="Password"
                    required
                    autocomplete="new-password"
                />
                @error('password')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Confirm Password Input -->
            <div class="relative mb-4">
                <span class="absolute left-3 top-2 text-green-500"><i class="fas fa-lock"></i></span>
                <input
                    type="password"
                    name="password_confirmation"
                    class="w-full pl-10 px-3 py-2 border border-gray-300 rounded-full focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                    placeholder="Confirm Password"
                    required
                    autocomplete="new-password"
                />
            </div>

            <!-- Register Button -->
            <button
                type="submit"
                class="w-full bg-yellow-500 text-white font-semibold py-2 rounded-full hover:bg-yellow-600 transition shadow-md"
            >
                Register
            </button>
        </form>

        <!-- Already have an account -->
        <p class="mt-4 text-sm text-gray-700">
            Already have an account?
            <a href="{{ route('login') }}" class="text-yellow-500 hover:underline">Log in</a>
        </p>

        <!-- Footer -->
        <p class="mt-6 text-xs text-gray-500">
            This site is protected by reCAPTCHA and the Google
            <a href="https://policies.google.com/privacy" class="underline">Privacy Policy</a> and
            <a href="https://policies.google.com/terms" class="underline">Terms of Service</a> apply.
        </p>

        <!-- Bottom Links -->
        <div class="mt-4 flex justify-center space-x-4 text-sm font-medium text-yellow-600">
            <a href="{{ url('/') }}" class="hover:underline">Home</a>
            <a href="{{ url('/contact') }}" class="hover:underline">Contacts</a>
            <a href="{{ url('/pricing') }}" class="hover:underline">Pricing Policy</a>
        </div>
    </div>
</body>
</html
