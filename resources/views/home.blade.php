<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders - UI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {},
            },
        };
    </script>
</head>
<body class="bg-gray-100">
    <div class="flex flex-col min-h-screen">
        <!-- Navbar -->
        <nav class="bg-white shadow-md py-3 px-6 flex justify-between items-center">
            <div class="hidden md:flex space-x-6 text-gray-700">
                <a href="#" class="hover:text-black">Info</a>
                <a href="#" class="hover:text-black">Terms & Policies</a>
                <a href="#" class="hover:text-black">News</a>
                <a href="#" class="hover:text-black">Blog</a>
            </div>
            <div class="relative">
                <button id="rating-toggle" class="flex items-center bg-gray-200 p-2 rounded-full">
                    ⭐ 80%
                </button>
                <div id="rating-dropdown" class="absolute right-0 mt-2 bg-white shadow-lg p-4 rounded-md w-64 hidden">
                    <p class="text-green-600 font-bold">Auto-take</p>
                    <div class="mt-2">
                        <p class="text-sm font-semibold">With rating <span class="float-right">You can take</span></p>
                        <p class="font-bold">0-69% <span class="float-right">2 orders</span></p>
                        <p class="font-bold">70-89% <span class="float-right">3 orders</span></p>
                        <p class="font-bold">90-100% <span class="float-right">3 orders</span></p>
                    </div>
                    <p class="text-sm text-gray-500 mt-2">Your rating determines how many orders you can auto-take at a time.</p>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <button class="flex items-center space-x-1 text-gray-600 hover:text-black">
                    <span>80%</span>
                    <span class="text-gray-500">★</span>
                </button>
                <div class="relative">
                    <button class="flex items-center space-x-2 focus:outline-none" id="userMenuButton">
                        <span class="text-gray-700 font-medium">Philip</span>
                        <div class="bg-gray-200 px-3 py-1 rounded-lg text-sm text-gray-500">Looking for orders</div>
                    </button>
                    <div id="userMenu" class="hidden absolute right-0 mt-2 w-48 bg-white shadow-lg rounded-lg py-2">
                        <div class="flex items-center px-4 py-2 text-gray-700">
                            <span class="mr-2">👤</span> CD Writers
                        </div>
                        <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Profile</a>
                        <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Log Out</a>
                    </div>
                </div>
            </div>
        </nav>

        <div class="flex flex-1 flex-col md:flex-row">
            <!-- Sidebar -->
            <aside class="w-full md:w-64 bg-white shadow-md p-4 flex flex-col">
                <nav class="mt-6 flex flex-col space-y-4">
                    <a href="#" class="flex items-center text-gray-900 font-medium">
                        <span class="mr-2">📄</span> Available
                    </a>
                    <a href="#" class="flex items-center text-gray-500">
                        <span class="mr-2">📂</span> Current
                    </a>
                    <a href="#" class="flex items-center text-gray-500">
                        <span class="mr-2">✏️</span> Revision
                    </a>
                    <a href="#" class="flex items-center text-gray-500">
                        <span class="mr-2">⚠️</span> Dispute
                    </a>
                    <a href="#" class="flex items-center text-gray-500">
                        <span class="mr-2">✅</span> Finished
                    </a>
                    <a href="#" class="flex items-center text-gray-500">
                        <span class="mr-2">💰</span> Bids
                    </a>
                    <a href="#" class="flex items-center text-gray-500">
                        <span class="mr-2">💬</span> Messages
                    </a>
                    <a href="#" class="flex items-center text-gray-500">
                        <span class="mr-2">📊</span> Statistics
                    </a>
                    <a href="#" class="flex items-center text-gray-500">
                        <span class="mr-2">💳</span> Finance
                    </a>
                </nav>
            </aside>
            
            <!-- Main Content -->
            <main class="flex-1 p-6">
                <h2 class="text-2xl font-semibold">Available Orders</h2>
                <!-- Filters -->
                <div class="bg-gray-50 shadow-md p-4 rounded-lg mt-4 flex flex-wrap items-center space-x-4">
                    <span class="text-gray-500">⚙️</span>
                    <input type="checkbox" id="onlyOrders" class="form-checkbox">
                    <label for="onlyOrders" class="text-gray-700">Only Orders</label>
                    
                    <input type="checkbox" id="onlyNew" class="form-checkbox">
                    <label for="onlyNew" class="text-gray-700">Only New</label>
                    
                    <div class="flex space-x-2">
                        <span class="bg-gray-200 px-2 py-1 rounded-lg text-gray-500">Level</span>
                        <select class="border rounded p-2 text-gray-700">
                            <option>All available</option>
                        </select>
                    </div>
                    
                    <div class="flex space-x-2">
                        <span class="bg-gray-200 px-2 py-1 rounded-lg text-gray-500">Discipline</span>
                        <select class="border rounded p-2 text-gray-700">
                            <option>All available</option>
                        </select>
                    </div>
                    
                    <button class="bg-green-500 text-white px-4 py-2 rounded">🔍</button>
                </div>
            </main>
        </div>
    </div>

    <script>
        document.getElementById('userMenuButton').addEventListener('click', function () {
            document.getElementById('userMenu').classList.toggle('hidden');
        });
    </script>
</body>
</html>
