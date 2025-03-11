@extends('new.app')


@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Profile header card -->
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden mb-10 transform transition-all duration-500 hover:shadow-2xl">
            <div class="h-36 bg-gradient-to-r from-blue-600 to-indigo-600 relative overflow-hidden">
                <!-- Decorative elements -->
                <div class="absolute inset-0 opacity-20">
                    <div class="absolute top-0 left-0 w-40 h-40 bg-white rounded-full -translate-x-20 -translate-y-20"></div>
                    <div class="absolute bottom-0 right-0 w-64 h-64 bg-indigo-300 rounded-full translate-x-32 translate-y-32"></div>
                </div>
        </form>

        <!-- Confirmation Modal -->
        <div id="confirmationModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl transform transition-all">
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Profile Submitted Successfully</h3>
                    <p class="text-gray-600 mb-6">Your profile has been set up. You will be redirected to the available orders page.</p>
                    <div class="flex justify-center">
                        <a href="{{ route('writer.available') }}" class="inline-flex justify-center py-3 px-6 border border-transparent shadow-sm text-base font-medium rounded-xl text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transform transition hover:scale-105 duration-200">
                            <svg class="mr-2 -ml-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                            Go to Available Orders
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


            </div>
            <div class="relative px-6 pb-8">
                <div class="absolute -top-16 left-6">
                    <div class="h-32 w-32 bg-white rounded-xl shadow-lg p-2">
                        <div class="h-full w-full rounded-lg bg-gradient-to-br from-blue-500 to-indigo-500 flex items-center justify-center">
                            <svg class="h-16 w-16 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 14.25c4.14 0 7.5 3.36 7.5 7.5H4.5c0-4.14 3.36-7.5 7.5-7.5zM12 13.5c-2.9 0-5.25-2.35-5.25-5.25S9.1 3 12 3s5.25 2.35 5.25 5.25S14.9 13.5 12 13.5z" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="mt-16">
                    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">{{ Auth::user()->name }}</h2>
                            <p class="text-sm font-medium text-gray-500">Writer ID: {{ sprintf('WR%06d', Auth::id()) }}</p>
                        </div>
                        <div class="mt-4 sm:mt-0">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                {{ Auth::user()->status === 'active' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-yellow-100 text-yellow-800 border border-yellow-200' }}">
                                <span class="flex-shrink-0 h-2 w-2 rounded-full 
                                    {{ Auth::user()->status === 'active' ? 'bg-green-500' : 'bg-yellow-500' }} mr-1.5"></span>
                                {{ Auth::user()->status === 'active' ? 'Looking for orders' : 'Pending' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toast Notifications -->
        @if(session('success'))
        <div id="toast-success" class="mb-8 fixed top-4 right-4 z-50 max-w-md bg-white rounded-xl shadow-xl p-4 border-l-4 border-green-500 transform transition ease-out duration-300 opacity-0">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3 w-0 flex-1">
                    <p class="text-sm font-medium text-gray-900">Success!</p>
                    <p class="mt-1 text-sm text-gray-500">{{ session('success') }}</p>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                    <button type="button" onclick="hideToast('toast-success')" class="inline-flex text-gray-400 hover:text-gray-500">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div id="toast-error" class="mb-8 fixed top-4 right-4 z-50 max-w-md bg-white rounded-xl shadow-xl p-4 border-l-4 border-red-500 transform transition ease-out duration-300 opacity-0">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3 w-0 flex-1">
                    <p class="text-sm font-medium text-gray-900">Error</p>
                    <p class="mt-1 text-sm text-gray-500">{{ session('error') }}</p>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                    <button type="button" onclick="hideToast('toast-error')" class="inline-flex text-gray-400 hover:text-gray-500">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        @endif

        <form method="POST" action="{{ route('profilesetup.submit') }}" enctype="multipart/form-data" class="space-y-10" id="profileForm">
            @csrf
            
            <!-- Progress Steps -->
            <div class="relative mb-10">
                <div class="overflow-hidden h-2 text-xs flex rounded-full bg-blue-100">
                    <div id="progress-bar" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-gradient-to-r from-blue-500 to-indigo-600" style="width: 0%"></div>
                </div>
                <div class="flex justify-between mt-3">
                    <div id="step-1-indicator" class="w-1/3 text-center">
                        <div class="bg-blue-600 text-white h-8 w-8 rounded-full flex items-center justify-center mx-auto mb-1 shadow-md">1</div>
                        <span class="text-xs font-medium text-blue-600">Personal Info</span>
                    </div>
                    <div id="step-2-indicator" class="w-1/3 text-center">
                        <div class="bg-gray-200 text-gray-600 h-8 w-8 rounded-full flex items-center justify-center mx-auto mb-1">2</div>
                        <span class="text-xs font-medium text-gray-500">Professional Details</span>
                    </div>
                    <div id="step-3-indicator" class="w-1/3 text-center">
                        <div class="bg-gray-200 text-gray-600 h-8 w-8 rounded-full flex items-center justify-center mx-auto mb-1">3</div>
                        <span class="text-xs font-medium text-gray-500">Availability & Payment</span>
                    </div>
                </div>
            </div>
            
            <!-- Step 1: Personal Information Card -->
            <div id="step-1" class="bg-white shadow-xl rounded-2xl overflow-hidden transition duration-500 hover:shadow-2xl">
                <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <svg class="mr-2 h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Personal Information
                    </h3>
                </div>
                <div class="p-8 bg-white">
                    <div class="grid grid-cols-1 gap-y-8 gap-x-6 sm:grid-cols-6">
                        <div class="sm:col-span-3">
                            <div class="relative floating-label">
                                <input type="text" name="name" id="name" value="{{ Auth::user()->name }}" readonly 
                                    class="peer block w-full rounded-xl border-gray-300 bg-gray-50 py-3 px-4 shadow-sm transition-all focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 hover:border-gray-400 disabled:bg-gray-100 disabled:cursor-not-allowed" 
                                    placeholder=" ">
                                <label for="name" class="absolute text-sm duration-300 transform z-10 origin-[0] bg-gray-50 peer-focus:bg-gray-50">Full Name</label>
                            </div>
                        </div>

                        <div class="sm:col-span-3">
                            <div class="relative floating-label">
                                <input type="email" name="email" id="email" value="{{ Auth::user()->email }}" readonly 
                                    class="peer block w-full rounded-xl border-gray-300 bg-gray-50 py-3 px-4 shadow-sm transition-all focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 hover:border-gray-400 disabled:bg-gray-100 disabled:cursor-not-allowed" 
                                    placeholder=" ">
                                <label for="email" class="absolute text-sm duration-300 transform z-10 origin-[0] bg-gray-50 peer-focus:bg-gray-50">Email Address</label>
                            </div>
                        </div>

                        <div class="sm:col-span-3">
                            <div class="relative floating-label">
                                <input type="text" name="writer_id" id="writer_id" value="{{ sprintf('WR%06d', Auth::id()) }}" readonly 
                                    class="peer block w-full rounded-xl border-gray-300 bg-gray-50 py-3 px-4 shadow-sm transition-all focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 hover:border-gray-400 disabled:bg-gray-100 disabled:cursor-not-allowed" 
                                    placeholder=" ">
                                <label for="writer_id" class="absolute text-sm duration-300 transform z-10 origin-[0] bg-gray-50 peer-focus:bg-gray-50">Writer ID</label>
                            </div>
                        </div>

                        <div class="sm:col-span-3">
                            <div class="relative floating-label">
                                <input type="text" name="level" id="level" value="Expert" readonly 
                                    class="peer block w-full rounded-xl border-gray-300 bg-gray-50 py-3 px-4 shadow-sm transition-all focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 hover:border-gray-400 disabled:bg-gray-100 disabled:cursor-not-allowed" 
                                    placeholder=" ">
                                <label for="level" class="absolute text-sm duration-300 transform z-10 origin-[0] bg-gray-50 peer-focus:bg-gray-50">Writer Level</label>
                            </div>
                        </div>

                        <div class="sm:col-span-3">
                            <div class="relative floating-label">
                                <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number') }}"
                                    class="peer block w-full rounded-xl border-gray-300 py-3 px-4 shadow-sm transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 hover:border-gray-400 focus:shadow-md focus:translate-y-px" 
                                    placeholder=" ">
                                <label for="phone_number" class="absolute text-sm duration-300 transform z-10 origin-[0]">Phone Number</label>
                                @error('phone_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="sm:col-span-3">
                            <div class="relative floating-label">
                                <input type="text" name="national_id" id="national_id" value="{{ old('national_id') }}"
                                    class="peer block w-full rounded-xl border-gray-300 py-3 px-4 shadow-sm transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 hover:border-gray-400 focus:shadow-md focus:translate-y-px" 
                                    placeholder=" ">
                                <label for="national_id" class="absolute text-sm duration-300 transform z-10 origin-[0]">National ID</label>
                                @error('national_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="sm:col-span-3">
                            <div class="relative">
                                <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                                <select id="country" name="country" 
                                    class="block w-full rounded-xl border-gray-300 py-3 px-4 shadow-sm transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 hover:border-gray-400 focus:shadow-md focus:translate-y-px">
                                    <option value="">Select a country</option>
                                    @foreach(['United States', 'Canada', 'United Kingdom', 'Australia', 'Germany', 'France', 'Kenya', 'Nigeria', 'South Africa', 'India', 'China', 'Japan', 'Brazil', 'Other'] as $country)
                                        <option value="{{ $country }}" {{ old('country') == $country ? 'selected' : '' }}>{{ $country }}</option>
                                    @endforeach
                                </select>
                                @error('country')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="sm:col-span-3">
                            <div class="relative floating-label">
                                <input type="text" name="county" id="county" value="{{ old('county') }}"
                                    class="peer block w-full rounded-xl border-gray-300 py-3 px-4 shadow-sm transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 hover:border-gray-400 focus:shadow-md focus:translate-y-px" 
                                    placeholder=" ">
                                <label for="county" class="absolute text-sm duration-300 transform z-10 origin-[0]">County/State</label>
                                @error('county')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="sm:col-span-3">
                            <div class="relative">
                                <label for="native_language" class="block text-sm font-medium text-gray-700 mb-1">Native Language</label>
                                <select id="native_language" name="native_language" 
                                    class="block w-full rounded-xl border-gray-300 py-3 px-4 shadow-sm transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 hover:border-gray-400 focus:shadow-md focus:translate-y-px">
                                    <option value="">Select your native language</option>
                                    @foreach(['English', 'Spanish', 'French', 'German', 'Swahili', 'Arabic', 'Chinese', 'Hindi', 'Japanese', 'Russian', 'Portuguese', 'Other'] as $language)
                                        <option value="{{ $language }}" {{ old('native_language') == $language ? 'selected' : '' }}>{{ $language }}</option>
                                    @endforeach
                                </select>
                                @error('native_language')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- National ID Upload Section -->
                        <div class="sm:col-span-6 mt-4">
                            <div class="bg-gray-50 p-6 rounded-xl border border-gray-200">
                                <h4 class="text-base font-medium text-gray-800 mb-3 flex items-center">
                                    <svg class="mr-1.5 h-5 w-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                                    </svg>
                                    National ID Verification (Required)
                                </h4>
                                
                                <div class="grid sm:grid-cols-2 gap-6">
                                    <div class="col-span-2 sm:col-span-1">
                                        <label for="national_id_image" class="block text-sm font-medium text-gray-700 mb-2">Upload ID Image</label>
                                        <div class="mt-1">
                                            <div class="flex items-center">
                                                <div class="upload-zone flex-shrink-0 h-40 w-full sm:w-full bg-white rounded-xl overflow-hidden border-2 border-dashed border-gray-300 hover:border-indigo-500 hover:bg-indigo-50 transition-colors duration-300 relative cursor-pointer group">
                                                    <img id="id-preview" class="h-full w-full object-cover hidden">
                                                    <div id="id-placeholder" class="h-full w-full flex flex-col items-center justify-center p-4">
                                                        <svg class="h-10 w-10 text-gray-400 mb-3 group-hover:text-indigo-500 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                        <p class="text-sm text-center text-gray-500 group-hover:text-indigo-600 transition-colors duration-300">
                                                            <span class="font-medium">Click to upload</span> or drag and drop
                                                        </p>
                                                        <p class="text-xs text-gray-400 group-hover:text-indigo-400 transition-colors duration-300 mt-1">
                                                            JPG, PNG up to 5MB
                                                        </p>
                                                    </div>
                                                    <input id="national_id_image" name="national_id_image" type="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept="image/*" onchange="previewImage(this, 'id-preview', 'id-placeholder')" required>
                                                </div>
                                            </div>
                                            @error('national_id_image')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-span-2 sm:col-span-1">
                                        <div class="h-full flex flex-col">
                                            <span class="text-sm font-medium text-gray-700 mb-2">ID Verification Status</span>
                                            <div class="flex-grow bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                                                <div class="flex items-center mb-3">
                                                    <div class="h-10 w-10 rounded-full bg-amber-100 flex items-center justify-center mr-3">
                                                        <svg class="h-5 w-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                    </div>
                                                    <span class="font-medium text-gray-900">Pending Verification</span>
                                                </div>
                                                <p class="text-sm text-gray-600">
                                                    Your ID will be reviewed by our team within 24-48 hours. You can still accept orders while verification is pending.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-4 p-4 bg-blue-50 rounded-xl">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-3 flex-1 md:flex md:justify-between">
                                            <p class="text-sm text-blue-700">
                                                Your ID is used only for verification and is stored securely. It will never be shared with clients.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="sm:col-span-6">
                            <label for="profile_picture" class="block text-sm font-medium text-gray-700 mb-2">Profile Picture (Optional)</label>
                            <div class="mt-1 flex items-center">
                                <div class="flex-shrink-0 h-24 w-24 bg-white rounded-xl overflow-hidden border-2 border-gray-200 shadow-sm">
                                    <img id="profile-preview" class="h-full w-full object-cover hidden">
                                    <div id="profile-placeholder" class="h-full w-full flex items-center justify-center text-gray-400">
                                        <svg class="h-12 w-12" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 14.25c4.14 0 7.5 3.36 7.5 7.5H4.5c0-4.14 3.36-7.5 7.5-7.5zM12 13.5c-2.9 0-5.25-2.35-5.25-5.25S9.1 3 12 3s5.25 2.35 5.25 5.25S14.9 13.5 12 13.5z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5">
                                    <div class="relative">
                                        <input id="profile_picture" name="profile_picture" type="file" class="sr-only" accept="image/*" onchange="previewImage(this, 'profile-preview', 'profile-placeholder')">
                                        <label for="profile_picture" class="cursor-pointer bg-white py-2.5 px-4 border border-gray-300 rounded-xl shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 hover:border-gray-400 hover:text-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 inline-flex items-center transition-all duration-200">
                                            <svg class="h-4 w-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            Upload photo
                                        </label>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">PNG, JPG, GIF up to 2MB</p>
                                    @error('profile_picture')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 text-right">
                    <button type="button" id="next-step-1" class="inline-flex justify-center py-3 px-6 border border-transparent shadow-sm text-base font-medium rounded-xl text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transform transition hover:scale-105 duration-200 focus:translate-y-px">
                        Next: Professional Details
                        <svg class="ml-2 -mr-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Step 2: Professional Information Card -->
            <div id="step-2" class="bg-white shadow-xl rounded-2xl overflow-hidden transition duration-500 hover:shadow-2xl hidden">
                <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-purple-50">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <svg class="mr-2 h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Professional Information
                    </h3>
                </div>
                <div class="p-8 bg-white">
                    <div class="grid grid-cols-1 gap-y-8 gap-x-6 sm:grid-cols-6">
                        <div class="sm:col-span-3">
                            <div class="relative">
                                <label for="education_level" class="block text-sm font-medium text-gray-700 mb-1">Education Level</label>
                                <select id="education_level" name="education_level" 
                                    class="block w-full rounded-xl border-gray-300 py-3 px-4 shadow-sm transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 hover:border-gray-400 focus:shadow-md focus:translate-y-px">
                                    <option value="">Select education level</option>
                                    <option value="high_school" {{ old('education_level') == 'high_school' ? 'selected' : '' }}>High School</option>
                                    <option value="bachelor" {{ old('education_level') == 'bachelor' ? 'selected' : '' }}>Bachelor's Degree</option>
                                    <option value="master" {{ old('education_level') == 'master' ? 'selected' : '' }}>Master's Degree</option>
                                    <option value="phd" {{ old('education_level') == 'phd' ? 'selected' : '' }}>PhD</option>
                                </select>
                                @error('education_level')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="sm:col-span-3">
                            <div class="relative floating-label">
                                <input type="number" name="experience_years" id="experience_years" min="0" max="30" value="{{ old('experience_years') }}"
                                    class="peer block w-full rounded-xl border-gray-300 py-3 px-4 shadow-sm transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 hover:border-gray-400 focus:shadow-md focus:translate-y-px" 
                                    placeholder=" ">
                                <label for="experience_years" class="absolute text-sm duration-300 transform z-10 origin-[0]">Years of Experience</label>
                                @error('experience_years')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="sm:col-span-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Subject Expertise (Select 2-5)</label>
                            <p id="subjects-count" class="text-xs text-gray-500 mb-4 flex items-center">
                                <span class="inline-block w-6 h-6 bg-indigo-100 text-indigo-800 rounded-full text-center mr-2 font-medium" id="selected-count">0</span>
                                subjects selected (min: 2, max: 5)
                            </p>
                            
                            <div class="mt-2 mb-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach([
                                    'English Literature', 'History', 'Mathematics', 'Physics', 'Chemistry', 
                                    'Biology', 'Computer Science', 'Economics', 'Business Studies', 'Psychology', 
                                    'Sociology', 'Political Science', 'Philosophy', 'Law', 'Medicine', 
                                    'Engineering', 'Architecture', 'Art & Design', 'Music', 'Film Studies',
                                    'Media Studies', 'Communications', 'Journalism', 'Marketing', 'Management', 
                                    'Finance', 'Accounting', 'Nursing', 'Education', 'Social Work'
                                ] as $subject)
                                    <div class="relative flex items-start">
                                        <div class="flex items-center h-6">
                                            <input type="checkbox" id="subject-{{ Str::slug($subject) }}" name="subjects[]" value="{{ $subject }}" 
                                                {{ in_array($subject, old('subjects', [])) ? 'checked' : '' }} 
                                                class="subject-checkbox h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded-md shadow-sm cursor-pointer"
                                                onclick="checkSubjectLimit(this)">
                                        </div>
                                        <div class="ml-3">
                                            <label for="subject-{{ Str::slug($subject) }}" class="text-sm font-medium text-gray-700 cursor-pointer hover:text-indigo-600 transition duration-200">{{ $subject }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('subjects')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p id="subjects-error" class="mt-1 text-sm text-red-600 hidden"></p>
                            
                            <div class="mt-4 p-4 bg-blue-50 rounded-xl">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-blue-700">
                                            Choose the subjects you're most qualified to write about. This helps us match you with the most relevant orders.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="sm:col-span-6">
                            <div class="relative">
                                <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">Professional Bio</label>
                                <div class="mt-1 relative">
                                    <textarea id="bio" name="bio" rows="5" 
                                        class="block w-full rounded-xl border-gray-300 py-3 px-4 shadow-sm transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 hover:border-gray-400 focus:shadow-md resize-none"
                                        placeholder="Tell us about your professional background, expertise, and writing experience...">{{ old('bio') }}</textarea>
                                    <div class="absolute bottom-2 right-2 text-xs text-gray-500">
                                        <span id="bio-chars">0</span>/1000 characters (min 100)
                                    </div>
                                    @error('bio')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 flex justify-between">
                    <button type="button" id="prev-step-2" class="inline-flex justify-center py-3 px-6 border border-gray-300 shadow-sm text-base font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transform transition duration-200 focus:translate-y-px">
                        <svg class="mr-2 -ml-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                        </svg>
                        Back
                    </button>
                    <button type="button" id="next-step-2" class="inline-flex justify-center py-3 px-6 border border-transparent shadow-sm text-base font-medium rounded-xl text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transform transition hover:scale-105 duration-200 focus:translate-y-px">
                        Next: Availability & Payment
                        <svg class="ml-2 -mr-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Step 3: Availability and Payment Card -->
            <div id="step-3" class="bg-white shadow-xl rounded-2xl overflow-hidden transition duration-500 hover:shadow-2xl hidden">
                <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-blue-50">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <svg class="mr-2 h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Availability & Payment Details
                    </h3>
                </div>
                <div class="p-8 bg-white">
                    <div class="grid grid-cols-1 gap-y-8 gap-x-6 sm:grid-cols-6">
                        <div class="sm:col-span-3">
                            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 hover:border-indigo-300 hover:shadow-md transition-all duration-300">
                                <div class="relative flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="night_calls" name="night_calls" type="checkbox" value="yes" {{ old('night_calls') ? 'checked' : '' }}
                                            class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded-md shadow-sm">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="night_calls" class="font-medium text-gray-900">Available for night calls</label>
                                        <p class="text-gray-500 mt-1">Select 'Yes' if you're willing to receive calls during night hours</p>
                                    </div>
                                </div>
                            </div>
                            @error('night_calls')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-3">
                            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 hover:border-indigo-300 hover:shadow-md transition-all duration-300">
                                <div class="relative flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="force_assign" name="force_assign" type="checkbox" value="yes" {{ old('force_assign') ? 'checked' : '' }}
                                            class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded-md shadow-sm">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="force_assign" class="font-medium text-gray-900">Available for force-assign</label>
                                        <p class="text-gray-500 mt-1">Select 'Yes' if you're willing to be assigned urgent orders</p>
                                    </div>
                                </div>
                            </div>
                            @error('force_assign')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-3">
                            <div class="relative floating-label">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                                        </svg>
                                    </div>
                                    <input type="url" id="linkedin" name="linkedin" 
                                        class="block w-full rounded-xl border-gray-300 py-3 px-4 pl-10 shadow-sm transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 hover:border-gray-400 focus:shadow-md focus:translate-y-px" 
                                        placeholder="https://linkedin.com/in/yourprofile" value="{{ old('linkedin') }}">
                                </div>
                                <label for="linkedin" class="block text-sm font-medium text-gray-700 mb-1">LinkedIn Profile (Optional)</label>
                                @error('linkedin')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="sm:col-span-3">
                            <div class="relative floating-label">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M22.675 0h-21.35c-.732 0-1.325.593-1.325 1.325v21.351c0 .731.593 1.324 1.325 1.324h11.495v-9.294h-3.128v-3.622h3.128v-2.671c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12v9.293h6.116c.73 0 1.323-.593 1.323-1.325v-21.35c0-.732-.593-1.325-1.325-1.325z"/>
                                        </svg>
                                    </div>
                                    <input type="url" id="facebook" name="facebook" 
                                        class="block w-full rounded-xl border-gray-300 py-3 px-4 pl-10 shadow-sm transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 hover:border-gray-400 focus:shadow-md focus:translate-y-px" 
                                        placeholder="https://facebook.com/yourprofile" value="{{ old('facebook') }}">
                                </div>
                                <label for="facebook" class="block text-sm font-medium text-gray-700 mb-1">Facebook Profile (Optional)</label>
                                @error('facebook')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="sm:col-span-6">
                            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                                <h4 class="text-base font-semibold text-gray-800 mb-4 flex items-center">
                                    <svg class="mr-2 h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    Payment Information
                                </h4>
                                
                                <div class="mb-5">
                                    <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                        <div>
                                            <input type="radio" id="mpesa" name="payment_method" value="mpesa" class="hidden peer" {{ old('payment_method') == 'mpesa' ? 'checked' : '' }}>
                                            <label for="mpesa" class="flex flex-col p-4 border-2 rounded-xl border-gray-200 cursor-pointer transition-all duration-200 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-600 hover:border-indigo-400">
                                                <span class="text-center text-lg font-semibold">M-Pesa</span>
                                            </label>
                                        </div>
                                        <div>
                                            <input type="radio" id="bank" name="payment_method" value="bank" class="hidden peer" {{ old('payment_method') == 'bank' ? 'checked' : '' }}>
                                            <label for="bank" class="flex flex-col p-4 border-2 rounded-xl border-gray-200 cursor-pointer transition-all duration-200 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-600 hover:border-indigo-400">
                                                <span class="text-center text-lg font-semibold">Bank Transfer</span>
                                            </label>
                                        </div>
                                        <div>
                                            <input type="radio" id="paypal" name="payment_method" value="paypal" class="hidden peer" {{ old('payment_method') == 'paypal' ? 'checked' : '' }}>
                                            <label for="paypal" class="flex flex-col p-4 border-2 rounded-xl border-gray-200 cursor-pointer transition-all duration-200 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-600 hover:border-indigo-400">
                                                <span class="text-center text-lg font-semibold">PayPal</span>
                                            </label>
                                        </div>
                                    </div>
                                    @error('payment_method')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="relative floating-label">
                                    <textarea id="payment_details" name="payment_details" rows="3" 
                                        class="block w-full rounded-xl border-gray-300 py-3 px-4 shadow-sm transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 hover:border-gray-400 focus:shadow-md resize-none"
                                        placeholder="Enter your payment details here...">{{ old('payment_details') }}</textarea>
                                    <label for="payment_details" class="block text-sm font-medium text-gray-700 mb-1">Payment Details</label>
                                    <p class="text-xs text-gray-500 mt-2">Enter your payment details such as M-Pesa phone number, bank account information, or PayPal email</p>
                                    @error('payment_details')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 flex justify-between">
                    <button type="button" id="prev-step-3" class="inline-flex justify-center py-3 px-6 border border-gray-300 shadow-sm text-base font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transform transition duration-200 focus:translate-y-px">
                        <svg class="mr-2 -ml-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                        </svg>
                        Back
                    </button>
                    <button type="submit" class="inline-flex justify-center py-3 px-6 border border-transparent shadow-sm text-base font-medium rounded-xl text-white bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transform transition hover:scale-105 duration-200 focus:translate-y-px">
                        <svg class="mr-2 -ml-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Complete Profile Setup
                    </button>
                </div>
            </div>


            <!-- JavaScript for form interactions -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Step navigation
        const step1 = document.getElementById('step-1');
        const step2 = document.getElementById('step-2');
        const step3 = document.getElementById('step-3');
        
        const step1Indicator = document.getElementById('step-1-indicator');
        const step2Indicator = document.getElementById('step-2-indicator');
        const step3Indicator = document.getElementById('step-3-indicator');
        
        const progressBar = document.getElementById('progress-bar');
        
        const nextStep1Button = document.getElementById('next-step-1');
        const nextStep2Button = document.getElementById('next-step-2');
        const prevStep2Button = document.getElementById('prev-step-2');
        const prevStep3Button = document.getElementById('prev-step-3');
        
        nextStep1Button.addEventListener('click', function() {
            // Validate step 1
            if (validateStep1()) {
                step1.classList.add('hidden');
                step2.classList.remove('hidden');
                
                // Update progress indicators
                step1Indicator.querySelector('div').classList.remove('bg-blue-600', 'text-white');
                step1Indicator.querySelector('div').classList.add('bg-green-600', 'text-white');
                step1Indicator.querySelector('span').classList.remove('text-blue-600');
                step1Indicator.querySelector('span').classList.add('text-green-600');
                
                step2Indicator.querySelector('div').classList.remove('bg-gray-200', 'text-gray-600');
                step2Indicator.querySelector('div').classList.add('bg-blue-600', 'text-white');
                step2Indicator.querySelector('span').classList.remove('text-gray-500');
                step2Indicator.querySelector('span').classList.add('text-blue-600');
                
                progressBar.style.width = '33%';
                
                // Scroll to top
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });
        
        nextStep2Button.addEventListener('click', function() {
            // Validate step 2
            if (validateStep2()) {
                step2.classList.add('hidden');
                step3.classList.remove('hidden');
                
                // Update progress indicators
                step2Indicator.querySelector('div').classList.remove('bg-blue-600', 'text-white');
                step2Indicator.querySelector('div').classList.add('bg-green-600', 'text-white');
                step2Indicator.querySelector('span').classList.remove('text-blue-600');
                step2Indicator.querySelector('span').classList.add('text-green-600');
                
                step3Indicator.querySelector('div').classList.remove('bg-gray-200', 'text-gray-600');
                step3Indicator.querySelector('div').classList.add('bg-blue-600', 'text-white');
                step3Indicator.querySelector('span').classList.remove('text-gray-500');
                step3Indicator.querySelector('span').classList.add('text-blue-600');
                
                progressBar.style.width = '66%';
                
                // Scroll to top
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });
        
        prevStep2Button.addEventListener('click', function() {
            step2.classList.add('hidden');
            step1.classList.remove('hidden');
            
            // Update progress indicators
            step2Indicator.querySelector('div').classList.remove('bg-blue-600', 'text-white');
            step2Indicator.querySelector('div').classList.add('bg-gray-200', 'text-gray-600');
            step2Indicator.querySelector('span').classList.remove('text-blue-600');
            step2Indicator.querySelector('span').classList.add('text-gray-500');
            
            step1Indicator.querySelector('div').classList.remove('bg-green-600');
            step1Indicator.querySelector('div').classList.add('bg-blue-600');
            
            progressBar.style.width = '0%';
            
            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
        
        prevStep3Button.addEventListener('click', function() {
            step3.classList.add('hidden');
            step2.classList.remove('hidden');
            
            // Update progress indicators
            step3Indicator.querySelector('div').classList.remove('bg-blue-600', 'text-white');
            step3Indicator.querySelector('div').classList.add('bg-gray-200', 'text-gray-600');
            step3Indicator.querySelector('span').classList.remove('text-blue-600');
            step3Indicator.querySelector('span').classList.add('text-gray-500');
            
            step2Indicator.querySelector('div').classList.remove('bg-green-600');
            step2Indicator.querySelector('div').classList.add('bg-blue-600');
            
            progressBar.style.width = '33%';
            
            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
        
        // Form validation
        function validateStep1() {
            let isValid = true;
            const phoneNumber = document.getElementById('phone_number');
            const nationalId = document.getElementById('national_id');
            const country = document.getElementById('country');
            const county = document.getElementById('county');
            const nativeLanguage = document.getElementById('native_language');
            const nationalIdImage = document.getElementById('national_id_image');
            
            // Reset previous error messages
            const errorMessages = document.querySelectorAll('.error-message');
            errorMessages.forEach(message => message.remove());
            
            // Validate each field
            if (!phoneNumber.value) {
                showError(phoneNumber, 'Phone number is required');
                isValid = false;
            }
            
            if (!nationalId.value) {
                showError(nationalId, 'National ID is required');
                isValid = false;
            }
            
            if (!country.value) {
                showError(country, 'Country is required');
                isValid = false;
            }
            
            if (!county.value) {
                showError(county, 'County/State is required');
                isValid = false;
            }
            
            if (!nativeLanguage.value) {
                showError(nativeLanguage, 'Native language is required');
                isValid = false;
            }
            
            // Check if a national ID image has been selected
            if (nationalIdImage.files.length === 0) {
                showError(nationalIdImage.parentElement, 'National ID image is required');
                isValid = false;
            }
            
            return isValid;
        }
        
        function validateStep2() {
            let isValid = true;
            const educationLevel = document.getElementById('education_level');
            const experienceYears = document.getElementById('experience_years');
            const subjectsCheckboxes = document.querySelectorAll('.subject-checkbox:checked');
            const bio = document.getElementById('bio');
            
            // Reset previous error messages
            const errorMessages = document.querySelectorAll('.error-message');
            errorMessages.forEach(message => message.remove());
            
            // Validate each field
            if (!educationLevel.value) {
                showError(educationLevel, 'Education level is required');
                isValid = false;
            }
            
            if (!experienceYears.value) {
                showError(experienceYears, 'Years of experience is required');
                isValid = false;
            }
            
            // Check if at least 2 subjects are selected
            if (subjectsCheckboxes.length < 2) {
                const subjectsCount = document.getElementById('subjects-count');
                showError(subjectsCount, 'Please select at least 2 subjects');
                isValid = false;
            }
            
            // Check if bio meets minimum length
            if (!bio.value || bio.value.length < 100) {
                showError(bio, 'Bio must be at least 100 characters');
                isValid = false;
            }
            
            return isValid;
        }
        
        function showError(element, message) {
            const errorDiv = document.createElement('p');
            errorDiv.classList.add('text-sm', 'text-red-600', 'mt-1', 'error-message');
            errorDiv.textContent = message;
            element.parentNode.appendChild(errorDiv);
            element.classList.add('border-red-300');
            
            // Scroll to first error
            element.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        
        // Bio character counter
        const bioField = document.getElementById('bio');
        const bioChars = document.getElementById('bio-chars');
        
        bioField.addEventListener('input', function() {
            bioChars.textContent = this.value.length;
            
            // Visual feedback based on character count
            if (this.value.length < 100) {
                bioChars.classList.remove('text-green-500');
                bioChars.classList.add('text-red-500');
            } else {
                bioChars.classList.remove('text-red-500');
                bioChars.classList.add('text-green-500');
            }
        });
        
        // Trigger initial count for bio
        bioField.dispatchEvent(new Event('input'));
        
        // Toast notifications
        function showToast(toastId) {
            const toast = document.getElementById(toastId);
            if (toast) {
                setTimeout(function() {
                    toast.classList.remove('opacity-0');
                    toast.classList.add('opacity-100');
                    
                    setTimeout(function() {
                        hideToast(toastId);
                    }, 5000);
                }, 500);
            }
        }
        
        function hideToast(toastId) {
            const toast = document.getElementById(toastId);
            if (toast) {
                toast.classList.remove('opacity-100');
                toast.classList.add('opacity-0');
                
                // Remove from DOM after animation completes
                setTimeout(() => {
                    toast.style.display = 'none';
                }, 300);
            }
        }
        
        // Show toast notifications if they exist
        if (document.getElementById('toast-success')) {
            showToast('toast-success');
        }
        
        if (document.getElementById('toast-error')) {
            showToast('toast-error');
        }
        
        // Initialize subject checkboxes count
        updateSubjectCount();
        
        // Form submission handling
        const profileForm = document.getElementById('profileForm');
        profileForm.addEventListener('submit', function(event) {
            // Validate all steps before submitting
            const isStep1Valid = validateStep1();
            const isStep2Valid = validateStep2();
            
            // Final validation for payment info
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
            const paymentDetails = document.getElementById('payment_details');
            
            let isStep3Valid = true;
            
            if (!paymentMethod) {
                isStep3Valid = false;
                const paymentMethodsContainer = document.querySelector('.grid.grid-cols-1.sm\\:grid-cols-3');
                showError(paymentMethodsContainer, 'Please select a payment method');
            }
            
            if (!paymentDetails.value) {
                isStep3Valid = false;
                showError(paymentDetails, 'Payment details are required');
            }
            
            // Check if all validations passed
            if (!isStep1Valid || !isStep2Valid || !isStep3Valid) {
                event.preventDefault();
                
                // Show the step with validation errors
                if (!isStep1Valid) {
                    step1.classList.remove('hidden');
                    step2.classList.add('hidden');
                    step3.classList.add('hidden');
                    
                    step1Indicator.querySelector('div').classList.add('bg-blue-600', 'text-white');
                    step2Indicator.querySelector('div').classList.add('bg-gray-200', 'text-gray-600');
                    step3Indicator.querySelector('div').classList.add('bg-gray-200', 'text-gray-600');
                    
                    progressBar.style.width = '0%';
                } else if (!isStep2Valid) {
                    step1.classList.add('hidden');
                    step2.classList.remove('hidden');
                    step3.classList.add('hidden');
                    
                    step1Indicator.querySelector('div').classList.add('bg-green-600', 'text-white');
                    step2Indicator.querySelector('div').classList.add('bg-blue-600', 'text-white');
                    step3Indicator.querySelector('div').classList.add('bg-gray-200', 'text-gray-600');
                    
                    progressBar.style.width = '33%';
                }
                
                // Scroll to top to see errors
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else {
                // Form is valid, show loading state
                progressBar.style.width = '100%';
                
                // Disable submit button to prevent double submission
                const submitButton = document.querySelector('button[type="submit"]');
                submitButton.disabled = true;
                submitButton.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Submitting...
                `;
            }
        });
    });
    
    // Image preview function
    function previewImage(input, previewId, placeholderId) {
        const preview = document.getElementById(previewId);
        const placeholder = document.getElementById(placeholderId);
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.setAttribute('src', e.target.result);
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
            };
            
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.classList.add('hidden');
            placeholder.classList.remove('hidden');
        }
    }
    
    // Update subject count display
    function updateSubjectCount() {
        const checkboxes = document.querySelectorAll('.subject-checkbox:checked');
        const countDisplay = document.getElementById('selected-count');
        countDisplay.textContent = checkboxes.length;
        
        // Style based on selection count
        if (checkboxes.length >= 2 && checkboxes.length <= 5) {
            countDisplay.classList.remove('bg-red-100', 'text-red-800');
            countDisplay.classList.add('bg-green-100', 'text-green-800');
        } else {
            countDisplay.classList.remove('bg-green-100', 'text-green-800');
            countDisplay.classList.add('bg-red-100', 'text-red-800');
        }
        
        return checkboxes.length;
    }
    
    // Check subject limit
    function checkSubjectLimit(checkbox) {
        const checkboxes = document.querySelectorAll('.subject-checkbox:checked');
        const errorMessage = document.getElementById('subjects-error');
        
        // If trying to select more than 5
        if (checkboxes.length > 5 && checkbox.checked) {
            checkbox.checked = false;
            errorMessage.classList.remove('hidden');
            errorMessage.textContent = "You can only select up to 5 subjects";
            errorMessage.classList.add('animate-pulse');
            setTimeout(() => {
                errorMessage.classList.remove('animate-pulse');
                errorMessage.classList.add('hidden');
            }, 3000);
        }
        
        // Update counter
        updateSubjectCount();
    }
</script>
@endsection