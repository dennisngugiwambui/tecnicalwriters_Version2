<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <div class="text-2xl font-bold text-orange-500">
                    WritersCorp
                </div>
                <div class="flex space-x-6">
                    <a href="#" class="text-gray-600 hover:text-orange-500">Info</a>
                    <a href="#" class="text-gray-600 hover:text-orange-500">Terms & Policies</a>
                    <a href="#" class="text-gray-600 hover:text-orange-500">News</a>
                    <a href="#" class="text-gray-600 hover:text-orange-500">Blog</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-md">
            <div class="p-4">
                <h2 class="text-lg font-semibold mb-4">Orders</h2>
                <ul class="space-y-2">
                    <li>
                        <a href="#" class="flex items-center text-gray-600 hover:text-orange-500">
                            <span class="w-8">📝</span>
                            Available
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center text-gray-600 hover:text-orange-500">
                            <span class="w-8">📊</span>
                            Current
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center text-gray-600 hover:text-orange-500">
                            <span class="w-8">🔄</span>
                            Revision
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center text-gray-600 hover:text-orange-500">
                            <span class="w-8">⚠️</span>
                            Dispute
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center text-gray-600 hover:text-orange-500">
                            <span class="w-8">✅</span>
                            Finished
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Profile Content -->
        <div class="flex-1 p-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h1 class="text-2xl font-bold mb-6">Profile</h1>
                
                <!-- Warning Message -->
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                    <p class="text-yellow-700">
                        We would like to warn everyone that buying or selling our accounts is a wrongful act and a severe violation of our Rules and Policies. Such accounts are closed upon disclosure and the payments to such corporate account owners are suspended.
                    </p>
                </div>

                <!-- Profile Details -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold mb-4">Profile Details</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex items-center">
                            <span class="w-32 text-gray-600">Your ID:</span>
                            <span class="font-medium">433552</span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-32 text-gray-600">Writer category:</span>
                            <span class="font-medium">Advanced</span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-32 text-gray-600">Writer level:</span>
                            <span class="font-medium">Expert</span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-32 text-gray-600">Status:</span>
                            <select class="border rounded px-2 py-1">
                                <option selected>Looking for orders</option>
                                <option>On vacation</option>
                                <option>Not willing to work</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Basic Info -->
                <div>
                    <h2 class="text-xl font-semibold mb-4">Basic Info</h2>
                    <form class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-600 mb-1">First name <span class="text-red-500">*</span></label>
                                <input type="text" class="w-full border rounded px-3 py-2" value="John">
                            </div>
                            <div>
                                <label class="block text-gray-600 mb-1">Middle name</label>
                                <input type="text" class="w-full border rounded px-3 py-2">
                            </div>
                            <div>
                                <label class="block text-gray-600 mb-1">Last name <span class="text-red-500">*</span></label>
                                <input type="text" class="w-full border rounded px-3 py-2" value="Doe">
                            </div>
                            <div>
                                <label class="block text-gray-600 mb-1">Gender <span class="text-red-500">*</span></label>
                                <div class="flex space-x-4 mt-2">
                                    <label class="flex items-center">
                                        <input type="radio" name="gender" class="mr-2"> Female
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="gender" class="mr-2" checked> Male
                                    </label>
                                </div>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-gray-600 mb-1">Email address</label>
                                <input type="email" class="w-full border rounded px-3 py-2" value="john.doe@example.com">
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Experience & Skills -->
                <div class="mt-8">
                    <h2 class="text-xl font-semibold mb-4">Experience & Skills</h2>
                    <div class="space-y-6">
                        <div>
                            <label class="block text-gray-600 mb-2">What is your native language? <span class="text-red-500">*</span></label>
                            <select class="w-full border rounded px-3 py-2">
                                <option>English</option>
                                <option>Spanish</option>
                                <option>French</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-gray-600 mb-2">Did you ever work for any other online academic assistance companies? <span class="text-red-500">*</span></label>
                            <div class="flex space-x-4 mb-3">
                                <label class="flex items-center">
                                    <input type="radio" name="worked_before" class="mr-2"> Yes
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="worked_before" class="mr-2"> No
                                </label>
                            </div>
                            <div>
                                <label class="block text-gray-600 mb-2">List the companies:</label>
                                <input type="text" class="w-full border rounded px-3 py-2" placeholder="Ex.: Company A, Company B">
                            </div>
                        </div>

                        <div>
                            <label class="block text-gray-600 mb-2">Disciplines you are proficient in:</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" class="form-checkbox" checked>
                                    <span>Computer science</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" class="form-checkbox" checked>
                                    <span>Web programming</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" class="form-checkbox" checked>
                                    <span>Desktop applications</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" class="form-checkbox" checked>
                                    <span>Mobile applications</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" class="form-checkbox" checked>
                                    <span>Database design</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" class="form-checkbox" checked>
                                    <span>Data analysis</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Info -->
                <div class="mt-8">
                    <h2 class="text-xl font-semibold mb-4">Contact Info</h2>
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-600 mb-2">Time zone:</label>
                                <select class="w-full border rounded px-3 py-2">
                                    <option>GMT -8:00</option>
                                    <option>GMT -7:00</option>
                                    <option>GMT -6:00</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-gray-600 mb-2">Phone <span class="text-red-500">*</span></label>
                                <div class="grid grid-cols-3 gap-2">
                                    <input type="text" class="border rounded px-3 py-2" placeholder="Country">
                                    <input type="text" class="border rounded px-3 py-2" placeholder="Operator">
                                    <input type="text" class="border rounded px-3 py-2" placeholder="Number">
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-gray-600 mb-2">Country <span class="text-red-500">*</span></label>
                                <select class="w-full border rounded px-3 py-2">
                                    <option>United States</option>
                                    <option>Canada</option>
                                    <option>United Kingdom</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-gray-600 mb-2">State <span class="text-red-500">*</span></label>
                                <input type="text" class="w-full border rounded px-3 py-2">
                            </div>
                            <div>
                                <label class="block text-gray-600 mb-2">City <span class="text-red-500">*</span></label>
                                <input type="text" class="w-full border rounded px-3 py-2">
                            </div>
                        </div>

                        <div>
                            <label class="block text-gray-600 mb-2">Address <span class="text-red-500">*</span></label>
                            <input type="text" class="w-full border rounded px-3 py-2" placeholder="House Number Street, Apt. number or PO Box">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-600 mb-2">Available for night calls <span class="text-red-500">*</span></label>
                                <div class="flex space-x-4">
                                    <label class="flex items-center">
                                        <input type="radio" name="night_calls" class="mr-2"> Yes
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="night_calls" class="mr-2"> No
                                    </label>
                                </div>
                            </div>
                            <div>
                                <label class="block text-gray-600 mb-2">Available for force-assign <span class="text-red-500">*</span></label>
                                <div class="flex space-x-4">
                                    <label class="flex items-center">
                                        <input type="radio" name="force_assign" class="mr-2"> Yes
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="force_assign" class="mr-2"> No
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Social Networks -->
                <div class="mt-8">
                    <h2 class="text-xl font-semibold mb-4">Your Page in Social Networks</h2>
                    <p class="text-gray-600 mb-4">Insert at least one link to your social network profiles. We will not post content under your name, this information is used to verify your identity.</p>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-gray-600 mb-2">Facebook</label>
                            <input type="url" class="w-full border rounded px-3 py-2" placeholder="https://facebook.com/username">
                        </div>
                        <div>
                            <label class="block text-gray-600 mb-2">LinkedIn</label>
                            <input type="url" class="w-full border rounded px-3 py-2" placeholder="https://linkedin.com/in/username">
                        </div>
                    </div>
                </div>

                <!-- Education -->
                <div class="mt-8">
                    <h2 class="text-xl font-semibold mb-4">Education</h2>
                    <div class="bg-gray-50 p-4 rounded-lg border">
                        <p class="text-gray-600">You already have 1 place of study inserted</p>
                        <button class="mt-3 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors">
                            Add another place of study
                        </button>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="mt-8">
                    <h2 class="text-xl font-semibold mb-4">Additional Information about You</h2>
                    <textarea class="w-full border rounded px-3 py-2 h-32" placeholder="Share any additional information that might be relevant..."></textarea>
                </div>

                <!-- Save Button -->
                <div class="mt-8">
                    <button class="bg-orange-500 text-white px-6 py-2 rounded-lg hover:bg-orange-600 transition-colors">
                        Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>