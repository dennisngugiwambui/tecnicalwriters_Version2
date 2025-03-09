@extends('new.app')

@section('title', 'Complete Your Profile')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 to-blue-100 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Profile header card -->
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden mb-8 transform transition hover:scale-[1.01] duration-300">
            <div class="h-32 bg-gradient-to-r from-blue-500 to-indigo-600"></div>
            <div class="relative px-6 pb-8">
                <div class="absolute -top-16 left-6">
                    <div class="h-32 w-32 bg-white rounded-xl shadow-lg p-2">
                        <div class="h-full w-full rounded-lg bg-gray-200 flex items-center justify-center">
                            <svg class="h-20 w-20 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 14.25c4.14 0 7.5 3.36 7.5 7.5H4.5c0-4.14 3.36-7.5 7.5-7.5zM12 13.5c-2.9 0-5.25-2.35-5.25-5.25S9.1 3 12 3s5.25 2.35 5.25 5.25S14.9 13.5 12 13.5z" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="mt-16">
                    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">{{ Auth::user()->name }}</h2>
                            <p class="text-sm font-medium text-gray-500">Writer ID: {{ sprintf('%06d', Auth::id()) }}</p>
                        </div>
                        <div class="mt-4 sm:mt-0">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                {{ Auth::user()->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                <span class="flex-shrink-0 h-2 w-2 rounded-full 
                                    {{ Auth::user()->status === 'active' ? 'bg-green-500' : 'bg-yellow-500' }} mr-1.5"></span>
                                {{ Auth::user()->status === 'active' ? 'Looking for orders' : 'Pending' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
        <div class="mb-8 toaster-success hidden fixed top-4 right-4 z-50 max-w-md bg-green-50 border-l-4 border-green-500 rounded-lg shadow-lg p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
                <div class="ml-auto pl-3">
                    <div class="-mx-1.5 -my-1.5">
                        <button type="button" class="close-toaster inline-flex bg-green-50 rounded-md p-1.5 text-green-500 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-600">
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
        <div class="mb-8 toaster-error hidden fixed top-4 right-4 z-50 max-w-md bg-red-50 border-l-4 border-red-500 rounded-lg shadow-lg p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
                <div class="ml-auto pl-3">
                    <div class="-mx-1.5 -my-1.5">
                        <button type="button" class="close-toaster inline-flex bg-red-50 rounded-md p-1.5 text-red-500 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-600">
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

        <form method="POST" action="{{ route('profilesetup.submit') }}" enctype="multipart/form-data" class="space-y-8">
            @csrf
            
            <!-- Personal Information Card -->
            <div class="bg-white shadow-lg rounded-2xl overflow-hidden transition duration-300 hover:shadow-xl">
                <div class="px-6 py-5 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-800 flex items-center">
                        <svg class="mr-2 h-5 w-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Personal Information
                    </h3>
                </div>
                <div class="p-6 bg-white">
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                        <div class="sm:col-span-3">
                            <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                            <div class="mt-1">
                                <input type="text" name="name" id="name" value="{{ Auth::user()->name }}" disabled class="shadow-sm bg-gray-50 focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>

                        <div class="sm:col-span-3">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                            <div class="mt-1">
                                <input type="email" name="email" id="email" value="{{ Auth::user()->email }}" disabled class="shadow-sm bg-gray-50 focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>

                        <div class="sm:col-span-3">
                            <label for="writer_id" class="block text-sm font-medium text-gray-700">Writer ID</label>
                            <div class="mt-1">
                                <input type="text" name="writer_id" id="writer_id" value="{{ sprintf('%06d', Auth::id()) }}" disabled class="shadow-sm bg-gray-50 focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>

                        <div class="sm:col-span-3">
                            <label for="level" class="block text-sm font-medium text-gray-700">Writer Level</label>
                            <div class="mt-1">
                                <input type="text" name="level" id="level" value="Expert" disabled class="shadow-sm bg-gray-50 focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>

                        <div class="sm:col-span-3">
                            <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number</label>
                            <div class="mt-1">
                                <input type="text" name="phone_number" id="phone_number" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" value="{{ old('phone_number') }}">
                                @error('phone_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="sm:col-span-3">
                            <label for="national_id" class="block text-sm font-medium text-gray-700">National ID</label>
                            <div class="mt-1">
                                <input type="text" name="national_id" id="national_id" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" value="{{ old('national_id') }}">
                                @error('national_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="sm:col-span-3">
                            <label for="country" class="block text-sm font-medium text-gray-700">Country</label>
                            <div class="mt-1">
                                <select id="country" name="country" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
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
                            <label for="county" class="block text-sm font-medium text-gray-700">County/State</label>
                            <div class="mt-1">
                                <input type="text" name="county" id="county" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" value="{{ old('county') }}">
                                @error('county')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="sm:col-span-3">
                            <label for="native_language" class="block text-sm font-medium text-gray-700">Native Language</label>
                            <div class="mt-1">
                                <select id="native_language" name="native_language" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
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

                        <div class="sm:col-span-6">
                            <label for="profile_picture" class="block text-sm font-medium text-gray-700">Profile Picture</label>
                            <div class="mt-1 flex items-center">
                                <div class="flex-shrink-0 h-16 w-16 bg-gray-100 rounded-lg overflow-hidden">
                                    <img id="profile-preview" class="h-full w-full object-cover hidden">
                                    <div id="profile-placeholder" class="h-full w-full flex items-center justify-center text-gray-400">
                                        <svg class="h-8 w-8" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 14.25c4.14 0 7.5 3.36 7.5 7.5H4.5c0-4.14 3.36-7.5 7.5-7.5zM12 13.5c-2.9 0-5.25-2.35-5.25-5.25S9.1 3 12 3s5.25 2.35 5.25 5.25S14.9 13.5 12 13.5z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5">
                                    <div class="relative">
                                        <input id="profile_picture" name="profile_picture" type="file" class="sr-only" accept="image/*" onchange="previewImage(this, 'profile-preview', 'profile-placeholder')">
                                        <label for="profile_picture" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                            <span>Upload photo</span>
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
            </div>

            <!-- Professional Information Card -->
            <div class="bg-white shadow-lg rounded-2xl overflow-hidden transition duration-300 hover:shadow-xl">
                <div class="px-6 py-5 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-800 flex items-center">
                        <svg class="mr-2 h-5 w-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Professional Information
                    </h3>
                </div>
                <div class="p-6 bg-white">
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                        <div class="sm:col-span-3">
                            <label for="education_level" class="block text-sm font-medium text-gray-700">Education Level</label>
                            <div class="mt-1">
                                <select id="education_level" name="education_level" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
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
                            <label for="experience_years" class="block text-sm font-medium text-gray-700">Years of Experience</label>
                            <div class="mt-1">
                                <input type="number" name="experience_years" id="experience_years" min="0" max="30" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" value="{{ old('experience_years') }}">
                                @error('experience_years')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="sm:col-span-6">
                            <label for="subjects" class="block text-sm font-medium text-gray-700">Subject Expertise (Select 2-5)</label>
                            <div class="mt-1">
                                <select id="subjects" name="subjects[]" multiple class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" onchange="checkSubjectsLimit()">
                                    @foreach([
                                        'English Literature', 'History', 'Mathematics', 'Physics', 'Chemistry', 
                                        'Biology', 'Computer Science', 'Economics', 'Business Studies', 'Psychology', 
                                        'Sociology', 'Political Science', 'Philosophy', 'Law', 'Medicine', 
                                        'Engineering', 'Architecture', 'Art & Design', 'Music', 'Film Studies',
                                        'Media Studies', 'Communications', 'Journalism', 'Marketing', 'Management', 
                                        'Finance', 'Accounting', 'Nursing', 'Education', 'Social Work'
                                    ] as $subject)
                                        <option value="{{ $subject }}" {{ in_array($subject, old('subjects', [])) ? 'selected' : '' }}>{{ $subject }}</option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple subjects (min 2, max 5)</p>
                                @error('subjects')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p id="subjects-error" class="mt-1 text-sm text-red-600 hidden">Please select between 2 and 5 subjects</p>
                            </div>
                        </div>

                        <div class="sm:col-span-6">
                            <label for="bio" class="block text-sm font-medium text-gray-700">Professional Bio</label>
                            <div class="mt-1">
                                <textarea id="bio" name="bio" rows="4" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('bio') }}</textarea>
                                <p class="text-xs text-gray-500 mt-1"><span id="bio-chars">0</span>/1000 characters (min 100)</p>
                                @error('bio')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="sm:col-span-6">
                            <label for="resume" class="block text-sm font-medium text-gray-700">CV/Resume</label>
                            <div class="mt-1 flex items-center">
                                <div class="relative">
                                    <input id="resume" name="resume" type="file" class="sr-only" accept=".pdf,.doc,.docx">
                                    <label for="resume" class="relative cursor-pointer bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                        <span>Upload resume</span>
                                    </label>
                                    <span id="resume-name" class="ml-2 text-sm text-gray-500">No file selected</span>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">PDF, DOC, or DOCX up to 5MB</p>
                            @error('resume')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Availability and Social Media Card -->
            <div class="bg-white shadow-lg rounded-2xl overflow-hidden transition duration-300 hover:shadow-xl">
                <div class="px-6 py-5 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-800 flex items-center">
                        <svg class="mr-2 h-5 w-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Availability & Social Profiles
                    </h3>
                </div>
                <div class="p-6 bg-white">
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                        <div class="sm:col-span-3">
                            <div class="flex items-center">
                                <input id="night_calls" name="night_calls" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" {{ old('night_calls') ? 'checked' : '' }}>
                                <label for="night_calls" class="ml-2 block text-sm text-gray-700">Available for night calls</label>
                            </div>
                            @error('night_calls')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-3">
                            <div class="flex items-center">
                                <input id="force_assign" name="force_assign" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" {{ old('force_assign') ? 'checked' : '' }}>
                                <label for="force_assign" class="ml-2 block text-sm text-gray-700">Available for force-assign</label>
                            </div>
                            @error('force_assign')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-3">
                            <label for="linkedin" class="block text-sm font-medium text-gray-700">LinkedIn Profile</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                                    </svg>
                                </div>
                                <input type="url" id="linkedin" name="linkedin" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md" placeholder="https://linkedin.com/in/yourprofile" value="{{ old('linkedin') }}">
                            </div>
                            @error('linkedin')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-3">
                            <label for="facebook" class="block text-sm font-medium text-gray-700">Facebook Profile</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M22.675 0h-21.35c-.732 0-1.325.593-1.325 1.325v21.351c0 .731.593 1.324 1.325 1.324h11.495v-9.294h-3.128v-3.622h3.128v-2.671c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12v9.293h6.116c.73 0 1.323-.593 1.323-1.325v-21.35c0-.732-.593-1.325-1.325-1.325z"/>
                                    </svg>
                                </div>
                                <input type="url" id="facebook" name="facebook" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md" placeholder="https://facebook.com/yourprofile" value="{{ old('facebook') }}">
                            </div>
                            @error('facebook')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-6">
                            <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment Method</label>
                            <div class="mt-1">
                                <select id="payment_method" name="payment_method" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    <option value="">Select payment method</option>
                                    <option value="mpesa" {{ old('payment_method') == 'mpesa' ? 'selected' : '' }}>M-Pesa</option>
                                    <option value="bank" {{ old('payment_method') == 'bank' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="paypal" {{ old('payment_method') == 'paypal' ? 'selected' : '' }}>PayPal</option>
                                </select>
                                @error('payment_method')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="sm:col-span-6">
                            <label for="payment_details" class="block text-sm font-medium text-gray-700">Payment Details</label>
                            <div class="mt-1">
                                <textarea id="payment_details" name="payment_details" rows="2" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="E.g., M-Pesa phone number, bank account details, or PayPal email">{{ old('payment_details') }}</textarea>
                                <p class="text-xs text-gray-500 mt-1">This information will be used to process your payments</p>
                                @error('payment_details')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit" class="inline-flex justify-center py-3 px-6 border border-transparent shadow-sm text-base font-medium rounded-lg text-white bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transform transition hover:scale-105 duration-200">
                    <svg class="mr-2 -ml-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Complete Profile Setup
                </button>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript for form interactions -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Bio character counter
        const bioField = document.getElementById('bio');
        const bioChars = document.getElementById('bio-chars');
        
        bioField.addEventListener('input', function() {
            bioChars.textContent = this.value.length;
        });
        
        // Trigger initial count
        bioField.dispatchEvent(new Event('input'));
        
        // Resume file name display
        const resumeInput = document.getElementById('resume');
        const resumeName = document.getElementById('resume-name');
        
        resumeInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                resumeName.textContent = this.files[0].name;
            } else {
                resumeName.textContent = 'No file selected';
            }
        });
        
        // Toaster notifications
        const toasterSuccess = document.querySelector('.toaster-success');
        const toasterError = document.querySelector('.toaster-error');
        const closeButtons = document.querySelectorAll('.close-toaster');
        
        if (toasterSuccess) {
            setTimeout(function() {
                toasterSuccess.classList.remove('hidden');
                toasterSuccess.classList.add('transform', 'translate-x-0');
                
                setTimeout(function() {
                    toasterSuccess.classList.remove('translate-x-0');
                    toasterSuccess.classList.add('translate-x-full');
                }, 5000);
            }, 500);
        }
        
        if (toasterError) {
            setTimeout(function() {
                toasterError.classList.remove('hidden');
                toasterError.classList.add('transform', 'translate-x-0');
                
                setTimeout(function() {
                    toasterError.classList.remove('translate-x-0');
                    toasterError.classList.add('translate-x-full');
                }, 5000);
            }, 500);
        }
        
        closeButtons.forEach(button => {
            button.addEventListener('click', function() {
                const toaster = this.closest('.toaster-success, .toaster-error');
                toaster.classList.remove('translate-x-0');
                toaster.classList.add('translate-x-full');
            });
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
    
    // Check subjects limit
    function checkSubjectsLimit() {
        const subjects = document.getElementById('subjects');
        const error = document.getElementById('subjects-error');
        const selectedOptions = Array.from(subjects.selectedOptions);
        
        if (selectedOptions.length < 2 || selectedOptions.length > 5) {
            error.classList.remove('hidden');
            return false;
        } else {
            error.classList.add('hidden');
            return true;
        }
    }
    
    // Form validation before submit
    document.querySelector('form').addEventListener('submit', function(event) {
        if (!checkSubjectsLimit()) {
            event.preventDefault();
        }
    });
</script>
@endsection