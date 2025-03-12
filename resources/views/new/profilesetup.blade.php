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