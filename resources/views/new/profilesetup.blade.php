@extends('new.app')

@section('title', 'Profile Setup')

@section('content')
<div class="bg-gray-50 py-6 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Profile header card -->
        <div class="bg-white shadow-sm rounded-lg overflow-hidden mb-6">
            <div class="h-24 bg-gradient-to-r from-indigo-500 to-purple-600"></div>
            <div class="relative px-4 pb-6">
                <div class="absolute -top-12 left-4">
                    <div class="h-24 w-24 bg-white rounded-lg shadow-sm p-1">
                        <div class="h-full w-full rounded-lg bg-indigo-100 flex items-center justify-center">
                            <svg class="h-12 w-12 text-indigo-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 14.25c4.14 0 7.5 3.36 7.5 7.5H4.5c0-4.14 3.36-7.5 7.5-7.5zM12 13.5c-2.9 0-5.25-2.35-5.25-5.25S9.1 3 12 3s5.25 2.35 5.25 5.25S14.9 13.5 12 13.5z" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="mt-14">
                    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800">{{ Auth::user()->name }}</h2>
                            <p class="text-sm font-medium text-gray-500">Writer ID: {{ sprintf('WR%06d', Auth::id()) }}</p>
                        </div>
                        <div class="mt-2 sm:mt-0">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ Auth::user()->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                <span class="flex-shrink-0 h-1.5 w-1.5 rounded-full 
                                    {{ Auth::user()->status === 'active' ? 'bg-green-500' : 'bg-yellow-500' }} mr-1.5"></span>
                                {{ Auth::user()->status === 'active' ? 'Looking for orders' : 'Pending' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="mb-6">
            <div class="relative pt-1">
                <div class="flex mb-2 items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-indigo-600 bg-indigo-100">
                            Setup Progress
                        </span>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-semibold inline-block text-indigo-600" id="progress-percentage">
                            0%
                        </span>
                    </div>
                </div>
                <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-indigo-100">
                    <div id="progress-bar" style="width: 0%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-indigo-600 transition-all duration-300"></div>
                </div>
            </div>
        </div>

        <!-- Notification Alerts -->
        @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-800">{{ session('success') }}</p>
                </div>
                <div class="ml-auto pl-3">
                    <div class="-mx-1.5 -my-1.5">
                        <button type="button" class="inline-flex bg-green-50 rounded-md p-1.5 text-green-500 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-600">
                            <span class="sr-only">Dismiss</span>
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-800">{{ session('error') }}</p>
                </div>
                <div class="ml-auto pl-3">
                    <div class="-mx-1.5 -my-1.5">
                        <button type="button" class="inline-flex bg-red-50 rounded-md p-1.5 text-red-500 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-600">
                            <span class="sr-only">Dismiss</span>
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Form Tabs Navigation -->
        <div class="mb-4 border-b border-gray-200">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
                <li class="mr-2" role="presentation">
                    <button type="button" id="personal-tab" class="inline-block py-2 px-4 border-b-2 border-indigo-600 rounded-t-lg text-indigo-600 active">Personal Information</button>
                </li>
                <li class="mr-2" role="presentation">
                    <button type="button" id="professional-tab" class="inline-block py-2 px-4 border-b-2 border-transparent rounded-t-lg hover:border-gray-300 hover:text-gray-600">Professional Details</button>
                </li>
                <li class="mr-2" role="presentation">
                    <button type="button" id="payment-tab" class="inline-block py-2 px-4 border-b-2 border-transparent rounded-t-lg hover:border-gray-300 hover:text-gray-600">Availability & Payment</button>
                </li>
            </ul>
        </div>

        <form method="POST" action="{{ route('profilesetup.submit') }}" enctype="multipart/form-data" id="profileForm">
            @csrf
            
            <!-- Personal Information Tab Panel -->
            <div id="personal-content" class="tab-content bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="p-4 sm:p-6">
                    <div class="grid grid-cols-1 gap-y-4 gap-x-4 sm:grid-cols-6">
                        <div class="sm:col-span-3">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <input type="text" name="name" id="name" value="{{ Auth::user()->name }}" readonly 
                                class="shadow-sm bg-gray-50 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>

                        <div class="sm:col-span-3">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <input type="email" name="email" id="email" value="{{ Auth::user()->email }}" readonly 
                                class="shadow-sm bg-gray-50 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>

                        <div class="sm:col-span-3">
                            <label for="writer_id" class="block text-sm font-medium text-gray-700 mb-1">Writer ID</label>
                            <input type="text" name="writer_id" id="writer_id" value="{{ sprintf('WR%06d', Auth::id()) }}" readonly 
                                class="shadow-sm bg-gray-50 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>

                        <div class="sm:col-span-3">
                            <label for="level" class="block text-sm font-medium text-gray-700 mb-1">Writer Level</label>
                            <input type="text" name="level" id="level" value="Expert" readonly 
                                class="shadow-sm bg-gray-50 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>

                        <div class="sm:col-span-3">
                            <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                            <input type="text" name="phone_number" id="phone_number" 
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                                value="{{ old('phone_number') }}">
                            @error('phone_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-3">
                            <label for="national_id" class="block text-sm font-medium text-gray-700 mb-1">National ID</label>
                            <input type="text" name="national_id" id="national_id" 
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                                value="{{ old('national_id') }}">
                            @error('national_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-3">
                            <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                            <select id="country" name="country" 
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                <option value="">Select a country</option>
                                @foreach(['United States', 'Canada', 'United Kingdom', 'Australia', 'Germany', 'France', 'Kenya', 'Nigeria', 'South Africa', 'India', 'China', 'Japan', 'Brazil', 'Other'] as $country)
                                    <option value="{{ $country }}" {{ old('country') == $country ? 'selected' : '' }}>{{ $country }}</option>
                                @endforeach
                            </select>
                            @error('country')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-3">
                            <label for="county" class="block text-sm font-medium text-gray-700 mb-1">County/State</label>
                            <input type="text" name="county" id="county" 
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                                value="{{ old('county') }}">
                            @error('county')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-3">
                            <label for="native_language" class="block text-sm font-medium text-gray-700 mb-1">Native Language</label>
                            <select id="native_language" name="native_language" 
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                <option value="">Select your native language</option>
                                @foreach(['English', 'Spanish', 'French', 'German', 'Swahili', 'Arabic', 'Chinese', 'Hindi', 'Japanese', 'Russian', 'Portuguese', 'Other'] as $language)
                                    <option value="{{ $language }}" {{ old('native_language') == $language ? 'selected' : '' }}>{{ $language }}</option>
                                @endforeach
                            </select>
                            @error('native_language')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- National ID Upload Section -->
                        <div class="sm:col-span-6 mt-2">
                            <div class="bg-gray-50 p-4 rounded-md border border-gray-200">
                                <h4 class="text-sm font-medium text-gray-800 mb-2 flex items-center">
                                    <svg class="mr-1.5 h-4 w-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                                    </svg>
                                    National ID Verification (Required)
                                </h4>
                                
                                <div class="grid sm:grid-cols-2 gap-4">
                                    <div class="col-span-2 sm:col-span-1">
                                        <label for="national_id_image" class="block text-sm font-medium text-gray-700 mb-1">Upload ID Image</label>
                                        <div class="mt-1">
                                            <div class="flex items-center">
                                                <div class="upload-zone flex-shrink-0 h-32 w-full bg-white rounded-md overflow-hidden border-2 border-dashed border-gray-300 hover:border-indigo-500 transition-colors duration-200 relative cursor-pointer">
                                                    <img id="id-preview" class="h-full w-full object-cover hidden">
                                                    <div id="id-placeholder" class="h-full w-full flex flex-col items-center justify-center p-4">
                                                        <svg class="h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                        <p class="text-xs text-center text-gray-500">Click to upload or drag and drop</p>
                                                        <p class="text-xs text-gray-400 mt-1">JPG, PNG up to 5MB</p>
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
                                            <span class="text-sm font-medium text-gray-700 mb-1">ID Verification Status</span>
                                            <div class="flex-grow bg-white rounded-md border border-gray-200 p-3">
                                                <div class="flex items-center mb-2">
                                                    <div class="h-6 w-6 rounded-full bg-yellow-100 flex items-center justify-center mr-2">
                                                        <svg class="h-3 w-3 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                    </div>
                                                    <span class="text-sm font-medium text-gray-900">Pending Verification</span>
                                                </div>
                                                <p class="text-xs text-gray-600">
                                                    Your ID will be reviewed by our team within 24-48 hours. You can still accept orders while verification is pending.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-3 p-2 bg-blue-50 rounded-md">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-4 w-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-3 flex-1 md:flex md:justify-between">
                                            <p class="text-xs text-blue-700">
                                                Your ID is used only for verification and is stored securely. It will never be shared with clients.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="sm:col-span-6">
                            <label for="profile_picture" class="block text-sm font-medium text-gray-700 mb-1">Profile Picture (Optional)</label>
                            <div class="mt-1 flex items-center">
                                <div class="flex-shrink-0 h-12 w-12 bg-gray-100 rounded-md overflow-hidden">
                                    <img id="profile-preview" class="h-full w-full object-cover hidden">
                                    <div id="profile-placeholder" class="h-full w-full flex items-center justify-center text-gray-400">
                                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 14.25c4.14 0 7.5 3.36 7.5 7.5H4.5c0-4.14 3.36-7.5 7.5-7.5zM12 13.5c-2.9 0-5.25-2.35-5.25-5.25S9.1 3 12 3s5.25 2.35 5.25 5.25S14.9 13.5 12 13.5z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="relative">
                                        <input id="profile_picture" name="profile_picture" type="file" class="sr-only" accept="image/*" onchange="previewImage(this, 'profile-preview', 'profile-placeholder')">
                                        <label for="profile_picture" class="cursor-pointer bg-white py-1.5 px-3 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                            Upload
                                        </label>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF up to 2MB</p>
                                    @error('profile_picture')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                    <button type="button" id="next-personal" class="inline-flex justify-center py-1.5 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Next: Professional Details
                    </button>
                </div>
            </div>

            <!-- Professional Information Tab Panel -->
            <div id="professional-content" class="tab-content hidden bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="p-4 sm:p-6">
                    <div class="grid grid-cols-1 gap-y-4 gap-x-4 sm:grid-cols-6">
                        <div class="sm:col-span-3">
                            <label for="education_level" class="block text-sm font-medium text-gray-700 mb-1">Education Level</label>
                            <select id="education_level" name="education_level" 
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
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

                        <div class="sm:col-span-3">
                            <label for="experience_years" class="block text-sm font-medium text-gray-700 mb-1">Years of Experience</label>
                            <input type="number" name="experience_years" id="experience_years" min="0" max="30" 
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                                value="{{ old('experience_years') }}">
                            @error('experience_years')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Subject Expertise (Select 2-5)</label>
                            <p id="subjects-count" class="text-xs text-gray-500 mb-2">
                                Selected: <span id="selected-count">0</span>/5
                            </p>
                            <div class="mt-1 mb-2 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                                @foreach([
                                    'English Literature', 'History', 'Mathematics', 'Physics', 'Chemistry', 
                                    'Biology', 'Computer Science', 'Economics', 'Business Studies', 'Psychology', 
                                    'Sociology', 'Political Science', 'Philosophy', 'Law', 'Medicine', 
                                    'Engineering', 'Architecture', 'Art & Design', 'Music', 'Film Studies',
                                    'Media Studies', 'Communications', 'Journalism', 'Marketing', 'Management', 
                                    'Finance', 'Accounting', 'Nursing', 'Education', 'Social Work'
                                ] as $subject)
                                    <div class="relative flex items-start">
                                        <div class="flex items-center h-5">
                                            <input type="checkbox" id="subject-{{ Str::slug($subject) }}" name="subjects[]" value="{{ $subject }}" 
                                                {{ in_array($subject, old('subjects', [])) ? 'checked' : '' }} 
                                                class="subject-checkbox h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                                onclick="checkSubjectLimit(this)">
                                        </div>
                                        <div class="ml-2 text-sm">
                                            <label for="subject-{{ Str::slug($subject) }}" class="font-medium text-gray-700">{{ $subject }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('subjects')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p id="subjects-error" class="mt-1 text-sm text-red-600 hidden">Please select between 2 and 5 subjects</p>
                        </div>

                        <div class="sm:col-span-6">
                            <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">Professional Bio</label>

                            <div class="mt-1 relative">
                                <textarea id="bio" name="bio" rows="4" 
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                    placeholder="Tell us about your professional background, expertise, and writing experience...">{{ old('bio') }}</textarea>
                                <div class="absolute bottom-2 right-2 text-xs text-gray-500">
                                    <span id="bio-chars">0</span>/1000 characters (min 100)
                                </div>
                            </div>
                            @error('bio')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 bg-gray-50 flex justify-between sm:px-6">
                    <button type="button" id="prev-professional" class="inline-flex justify-center py-1.5 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Back
                    </button>
                    <button type="button" id="next-professional" class="inline-flex justify-center py-1.5 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Next: Availability & Payment
                    </button>
                </div>
            </div>

            <!-- Availability and Payment Tab Panel -->
            <div id="payment-content" class="tab-content hidden bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="p-4 sm:p-6">
                    <div class="grid grid-cols-1 gap-y-4 gap-x-4 sm:grid-cols-6">
                        <div class="sm:col-span-3">
                            <div class="bg-white rounded-md border border-gray-200 p-3">
                                <div class="relative flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="night_calls" name="night_calls" type="checkbox" value="yes" {{ old('night_calls') ? 'checked' : '' }}
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-2 text-sm">
                                        <label for="night_calls" class="font-medium text-gray-700">Available for night calls</label>
                                        <p class="text-gray-500 text-xs mt-0.5">Select if you're available to receive calls during night hours</p>
                                    </div>
                                </div>
                            </div>
                            @error('night_calls')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-3">
                            <div class="bg-white rounded-md border border-gray-200 p-3">
                                <div class="relative flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="force_assign" name="force_assign" type="checkbox" value="yes" {{ old('force_assign') ? 'checked' : '' }}
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-2 text-sm">
                                        <label for="force_assign" class="font-medium text-gray-700">Available for force-assign</label>
                                        <p class="text-gray-500 text-xs mt-0.5">Select if you're available for urgent order assignments</p>
                                    </div>
                                </div>
                            </div>
                            @error('force_assign')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-3">
                            <label for="linkedin" class="block text-sm font-medium text-gray-700 mb-1">LinkedIn Profile (Optional)</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                                    </svg>
                                </div>
                                <input type="url" id="linkedin" name="linkedin" 
                                    class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md" 
                                    placeholder="https://linkedin.com/in/yourprofile" value="{{ old('linkedin') }}">
                            </div>
                            @error('linkedin')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-3">
                            <label for="facebook" class="block text-sm font-medium text-gray-700 mb-1">Facebook Profile (Optional)</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M22.675 0h-21.35c-.732 0-1.325.593-1.325 1.325v21.351c0 .731.593 1.324 1.325 1.324h11.495v-9.294h-3.128v-3.622h3.128v-2.671c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12v9.293h6.116c.73 0 1.323-.593 1.323-1.325v-21.35c0-.732-.593-1.325-1.325-1.325z"/>
                                    </svg>
                                </div>
                                <input type="url" id="facebook" name="facebook" 
                                    class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md" 
                                    placeholder="https://facebook.com/yourprofile" value="{{ old('facebook') }}">
                            </div>
                            @error('facebook')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-6">
                            <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                            <div class="mt-1 grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="relative flex p-3 border border-gray-300 rounded-md cursor-pointer hover:border-indigo-300 focus-within:ring-1 focus-within:ring-indigo-500" for="mpesa">
                                        <input type="radio" id="mpesa" name="payment_method" value="mpesa" class="sr-only" {{ old('payment_method') == 'mpesa' ? 'checked' : '' }}>
                                        <div class="flex justify-between w-full">
                                            <span class="flex items-center">
                                                <span class="text-sm font-medium text-gray-900">M-Pesa</span>
                                            </span>
                                            <span class="h-5 w-5 flex items-center justify-center text-white rounded-full overflow-hidden" id="mpesa-indicator"></span>
                                        </div>
                                    </label>
                                </div>
                                <div>
                                    <label class="relative flex p-3 border border-gray-300 rounded-md cursor-pointer hover:border-indigo-300 focus-within:ring-1 focus-within:ring-indigo-500" for="bank">
                                        <input type="radio" id="bank" name="payment_method" value="bank" class="sr-only" {{ old('payment_method') == 'bank' ? 'checked' : '' }}>
                                        <div class="flex justify-between w-full">
                                            <span class="flex items-center">
                                                <span class="text-sm font-medium text-gray-900">Bank Transfer</span>
                                            </span>
                                            <span class="h-5 w-5 flex items-center justify-center text-white rounded-full overflow-hidden" id="bank-indicator"></span>
                                        </div>
                                    </label>
                                </div>
                                <div>
                                    <label class="relative flex p-3 border border-gray-300 rounded-md cursor-pointer hover:border-indigo-300 focus-within:ring-1 focus-within:ring-indigo-500" for="paypal">
                                        <input type="radio" id="paypal" name="payment_method" value="paypal" class="sr-only" {{ old('payment_method') == 'paypal' ? 'checked' : '' }}>
                                        <div class="flex justify-between w-full">
                                            <span class="flex items-center">
                                                <span class="text-sm font-medium text-gray-900">PayPal</span>
                                            </span>
                                            <span class="h-5 w-5 flex items-center justify-center text-white rounded-full overflow-hidden" id="paypal-indicator"></span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            @error('payment_method')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-6">
                            <label for="payment_details" class="block text-sm font-medium text-gray-700 mb-1">Payment Details</label>
                            <div class="mt-1">
                                <textarea id="payment_details" name="payment_details" rows="3" 
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                    placeholder="Enter your payment details (e.g., M-Pesa number, bank account details, or PayPal email)">{{ old('payment_details') }}</textarea>
                                <p class="text-xs text-gray-500 mt-1">This information will be used to process your payments</p>
                            </div>
                            @error('payment_details')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 bg-gray-50 flex justify-between sm:px-6">
                    <button type="button" id="prev-payment" class="inline-flex justify-center py-1.5 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Back
                    </button>
                    <button type="submit" class="inline-flex justify-center py-1.5 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Complete Profile Setup
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab navigation
        const personalTab = document.getElementById('personal-tab');
        const professionalTab = document.getElementById('professional-tab');
        const paymentTab = document.getElementById('payment-tab');
        
        const personalContent = document.getElementById('personal-content');
        const professionalContent = document.getElementById('professional-content');
        const paymentContent = document.getElementById('payment-content');
        
        const progressBar = document.getElementById('progress-bar');
        const progressPercentage = document.getElementById('progress-percentage');
        
        // Next and Previous buttons
        const nextPersonalBtn = document.getElementById('next-personal');
        const nextProfessionalBtn = document.getElementById('next-professional');
        const prevProfessionalBtn = document.getElementById('prev-professional');
        const prevPaymentBtn = document.getElementById('prev-payment');
        
        // Switch to personal tab
        function showPersonalTab() {
            personalContent.classList.remove('hidden');
            professionalContent.classList.add('hidden');
            paymentContent.classList.add('hidden');
            
            personalTab.classList.add('border-indigo-600', 'text-indigo-600');
            personalTab.classList.remove('border-transparent', 'hover:border-gray-300', 'hover:text-gray-600');
            
            professionalTab.classList.remove('border-indigo-600', 'text-indigo-600');
            professionalTab.classList.add('border-transparent', 'hover:border-gray-300', 'hover:text-gray-600');
            
            paymentTab.classList.remove('border-indigo-600', 'text-indigo-600');
            paymentTab.classList.add('border-transparent', 'hover:border-gray-300', 'hover:text-gray-600');
            
            progressBar.style.width = '0%';
            progressPercentage.textContent = '0%';
        }
        
        // Switch to professional tab
        function showProfessionalTab() {
            personalContent.classList.add('hidden');
            professionalContent.classList.remove('hidden');
            paymentContent.classList.add('hidden');
            
            personalTab.classList.remove('border-indigo-600', 'text-indigo-600');
            personalTab.classList.add('border-transparent', 'hover:border-gray-300', 'hover:text-gray-600');
            
            professionalTab.classList.add('border-indigo-600', 'text-indigo-600');
            professionalTab.classList.remove('border-transparent', 'hover:border-gray-300', 'hover:text-gray-600');
            
            paymentTab.classList.remove('border-indigo-600', 'text-indigo-600');
            paymentTab.classList.add('border-transparent', 'hover:border-gray-300', 'hover:text-gray-600');
            
            progressBar.style.width = '33%';
            progressPercentage.textContent = '33%';
        }
        
        // Switch to payment tab
        function showPaymentTab() {
            personalContent.classList.add('hidden');
            professionalContent.classList.add('hidden');
            paymentContent.classList.remove('hidden');
            
            personalTab.classList.remove('border-indigo-600', 'text-indigo-600');
            personalTab.classList.add('border-transparent', 'hover:border-gray-300', 'hover:text-gray-600');
            
            professionalTab.classList.remove('border-indigo-600', 'text-indigo-600');
            professionalTab.classList.add('border-transparent', 'hover:border-gray-300', 'hover:text-gray-600');
            
            paymentTab.classList.add('border-indigo-600', 'text-indigo-600');
            paymentTab.classList.remove('border-transparent', 'hover:border-gray-300', 'hover:text-gray-600');
            
            progressBar.style.width = '66%';
            progressPercentage.textContent = '66%';
        }
        
        // Tab click events
        personalTab.addEventListener('click', function() {
            showPersonalTab();
        });
        
        professionalTab.addEventListener('click', function() {
            if (validatePersonalInfo()) {
                showProfessionalTab();
            } else {
                showPersonalTab();
            }
        });
        
        paymentTab.addEventListener('click', function() {
            if (validatePersonalInfo() && validateProfessionalInfo()) {
                showPaymentTab();
            } else if (validatePersonalInfo()) {
                showProfessionalTab();
            } else {
                showPersonalTab();
            }
        });
        
        // Next button events
        nextPersonalBtn.addEventListener('click', function() {
            if (validatePersonalInfo()) {
                showProfessionalTab();
            }
        });
        
        nextProfessionalBtn.addEventListener('click', function() {
            if (validateProfessionalInfo()) {
                showPaymentTab();
            }
        });
        
        // Previous button events
        prevProfessionalBtn.addEventListener('click', function() {
            showPersonalTab();
        });
        
        prevPaymentBtn.addEventListener('click', function() {
            showProfessionalTab();
        });
        
        // Form validation functions
        function validatePersonalInfo() {
            const phoneNumber = document.getElementById('phone_number');
            const nationalId = document.getElementById('national_id');
            const country = document.getElementById('country');
            const county = document.getElementById('county');
            const nativeLanguage = document.getElementById('native_language');
            const nationalIdImage = document.getElementById('national_id_image');
            
            let isValid = true;
            const errorElements = document.querySelectorAll('.error-message');
            errorElements.forEach(el => el.remove());
            
            if (!phoneNumber.value) {
                addErrorMessage(phoneNumber, 'Phone number is required');
                isValid = false;
            }
            
            if (!nationalId.value) {
                addErrorMessage(nationalId, 'National ID is required');
                isValid = false;
            }
            
            if (!country.value) {
                addErrorMessage(country, 'Country is required');
                isValid = false;
            }
            
            if (!county.value) {
                addErrorMessage(county, 'County/State is required');
                isValid = false;
            }
            
            if (!nativeLanguage.value) {
                addErrorMessage(nativeLanguage, 'Native language is required');
                isValid = false;
            }
            
            if (nationalIdImage.files.length === 0 && !document.getElementById('id-preview').src) {
                addErrorMessage(nationalIdImage.parentElement, 'National ID image is required');
                isValid = false;
            }
            
            return isValid;
        }
        
        function validateProfessionalInfo() {
            const educationLevel = document.getElementById('education_level');
            const experienceYears = document.getElementById('experience_years');
            const bio = document.getElementById('bio');
            const subjectsSelected = document.querySelectorAll('.subject-checkbox:checked');
            
            let isValid = true;
            const errorElements = document.querySelectorAll('.error-message');
            errorElements.forEach(el => el.remove());
            
            if (!educationLevel.value) {
                addErrorMessage(educationLevel, 'Education level is required');
                isValid = false;
            }
            
            if (!experienceYears.value) {
                addErrorMessage(experienceYears, 'Years of experience is required');
                isValid = false;
            }
            
            if (!bio.value || bio.value.length < 100) {
                addErrorMessage(bio, 'Bio must be at least 100 characters');
                isValid = false;
            }
            
            if (subjectsSelected.length < 2 || subjectsSelected.length > 5) {
                const subjectsCount = document.getElementById('subjects-count');
                if (subjectsSelected.length < 2) {
                    addErrorMessage(subjectsCount, 'Please select at least 2 subjects');
                } else {
                    addErrorMessage(subjectsCount, 'Please select no more than 5 subjects');
                }
                isValid = false;
            }
            
            return isValid;
        }
        
        // Helper function to add error messages
        function addErrorMessage(element, message) {
            const errorDiv = document.createElement('p');
            errorDiv.classList.add('text-sm', 'text-red-600', 'mt-1', 'error-message');
            errorDiv.textContent = message;
            element.parentNode.appendChild(errorDiv);
        }
        
        // Bio character counter
        const bioField = document.getElementById('bio');
        const bioChars = document.getElementById('bio-chars');
        
        bioField.addEventListener('input', function() {
            bioChars.textContent = this.value.length;
            
            if (this.value.length < 100) {
                bioChars.classList.remove('text-green-600');
                bioChars.classList.add('text-red-600');
            } else {
                bioChars.classList.remove('text-red-600');
                bioChars.classList.add('text-green-600');
            }
        });
        
        // Initialize bio character count
        bioField.dispatchEvent(new Event('input'));
        
        // Initialize subject count
        updateSubjectCount();
        
        // Initialize payment method indicators
        const paymentMethods = ['mpesa', 'bank', 'paypal'];
        paymentMethods.forEach(method => {
            const radioButton = document.getElementById(method);
            const indicator = document.getElementById(`${method}-indicator`);
            
            radioButton.addEventListener('change', function() {
                updatePaymentMethodIndicators();
            });
        });
        
        updatePaymentMethodIndicators();
        
        function updatePaymentMethodIndicators() {
            paymentMethods.forEach(method => {
                const radioButton = document.getElementById(method);
                const indicator = document.getElementById(`${method}-indicator`);
                
                if (radioButton.checked) {
                    indicator.innerHTML = '<svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>';
                    indicator.classList.add('bg-indigo-600');
                } else {
                    indicator.innerHTML = '';
                    indicator.classList.remove('bg-indigo-600');
                }
            });
        }
        
        // Form submission
        const profileForm = document.getElementById('profileForm');
        profileForm.addEventListener('submit', function(event) {
            let isValid = true;
            
            if (!validatePersonalInfo()) {
                showPersonalTab();
                isValid = false;
            } else if (!validateProfessionalInfo()) {
                showProfessionalTab();
                isValid = false;
            } else {
                // Validate payment tab
                const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
                const paymentDetails = document.getElementById('payment_details');
                
                const errorElements = document.querySelectorAll('.error-message');
                errorElements.forEach(el => el.remove());
                
                if (!paymentMethod) {
                    addErrorMessage(document.querySelector('.grid.grid-cols-1.sm\\:grid-cols-3'), 'Please select a payment method');
                    isValid = false;
                }
                
                if (!paymentDetails.value) {
                    addErrorMessage(paymentDetails, 'Payment details are required');
                    isValid = false;
                }
            }
            
            if (!isValid) {
                event.preventDefault();
            } else {
                // Show completion state
                progressBar.style.width = '100%';
                progressPercentage.textContent = '100%';
                
                // Disable submit button to prevent double submission
                const submitButton = document.querySelector('button[type="submit"]');
                submitButton.disabled = true;
                submitButton.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
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
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
            };
            
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.classList.add('hidden');
            placeholder.classList.remove('hidden');
        }
    }
    
    // Update subject count
    function updateSubjectCount() {
        const checkboxes = document.querySelectorAll('.subject-checkbox:checked');
        const countDisplay = document.getElementById('selected-count');
        countDisplay.textContent = checkboxes.length;
    }
    
    // Check subject limit
    function checkSubjectLimit(checkbox) {
        const checkboxes = document.querySelectorAll('.subject-checkbox:checked');
        const errorMessage = document.getElementById('subjects-error');
        
        if (checkboxes.length > 5) {
            checkbox.checked = false;
            errorMessage.textContent = "You can only select up to 5 subjects";
            errorMessage.classList.remove('hidden');
            setTimeout(() => {
                errorMessage.classList.add('hidden');
            }, 3000);
        } else if (checkboxes.length < 2) {
            errorMessage.textContent = "Please select at least 2 subjects";
            errorMessage.classList.remove('hidden');
        } else {
            errorMessage.classList.add('hidden');
        }
        
        updateSubjectCount();
    }
</script>
@endsection