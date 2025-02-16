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
            <img src="{{ asset('../writers/technicalwriters.jpg') }}" alt="Uvocorp Logo" class="w-20 mb-3">
            <h1 class="text-2xl font-bold text-gray-800">TechnicalWriters</h1>
        </div>

        <!-- Facebook Login -->
        <button class="w-full mt-5 bg-purple-700 text-white py-2 rounded-full text-sm font-semibold hover:bg-purple-800 transition flex items-center justify-center shadow-md">
            <i class="fab fa-facebook-f mr-2"></i> LOG IN WITH FACEBOOK
        </button>

        <!-- Divider -->
        <div class="my-4 flex items-center justify-center text-gray-500 text-sm">
            <span class="w-20 border-t border-gray-300"></span>
            <span class="mx-2">OR</span>
            <span class="w-20 border-t border-gray-300"></span>
        </div>

        <!-- Login Form -->
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Input -->
            <div class="relative mb-4">
                <span class="absolute left-3 top-2 text-green-500"><i class="fas fa-user"></i></span>
                <input type="email" name="email" class="w-full pl-10 px-3 py-2 border border-gray-300 rounded-full focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                    placeholder="Ellyruchu@gmail.com" value="{{ old('email') }}" required />
            </div>

            <!-- Password Input -->
            <div class="relative mb-4">
                <span class="absolute left-3 top-2 text-green-500"><i class="fas fa-lock"></i></span>
                <input type="password" name="password" class="w-full pl-10 px-3 py-2 border border-gray-300 rounded-full focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                    placeholder="•••••" required />
            </div>

            <!-- Login Button -->
            <button type="submit" class="w-full bg-yellow-500 text-white font-semibold py-2 rounded-full hover:bg-yellow-600 transition shadow-md">
                LOG IN
            </button>
        </form>

        <!-- Forgot Password Link -->
        <div class="mt-3 text-right">
            <a href="{{ route('password.request') }}" class="text-sm text-yellow-500 hover:underline">Forgot your password?</a>
        </div>

        <!-- Sign Up Link -->
        <p class="mt-4 text-sm text-gray-700">
            Need an account?
            <a href="{{ route('register') }}" class="text-yellow-500 hover:underline">Sign up</a>
        </p>

        <!-- reCAPTCHA Notice -->
        <p class="mt-3 text-xs text-gray-500">
            This site is protected by reCAPTCHA and the Google
            <a href="https://policies.google.com/privacy" class="underline">Privacy Policy</a> and
            <a href="https://policies.google.com/terms" class="underline">Terms of Service</a>.
        </p>

        <!-- Bottom Links -->
        <div class="mt-5 flex justify-center space-x-4 text-sm font-medium text-yellow-600">
            <a href="{{ url('/') }}" class="hover:underline">Home</a>
            <a href="{{ url('/contact') }}" class="hover:underline">Contacts</a>
            <a href="{{ url('/pricing') }}" class="hover:underline">Pricing policy</a>
        </div>
    </div>
</body>
</html>
