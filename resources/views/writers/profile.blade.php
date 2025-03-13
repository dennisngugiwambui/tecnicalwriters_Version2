@extends('writers.app')

@section('content')

<!-- Add in the head section -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<div class="flex flex-col lg:flex-row min-h-screen pb-8">
    <!-- Profile Content -->
    <div class="flex-1 p-8 lg:ml-64" style="padding-top:5%;">
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
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div class="flex flex-col">
                        <span class="text-gray-600">Your ID:</span>
                        <span class="font-medium">{{ Auth::user()->writerProfile->writer_id ?? 'N/A' }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-gray-600">Writer category:</span>
                        <span class="font-medium">{{ Auth::user()->writerProfile->education_level ?? 'Advanced' }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-gray-600">Writer level:</span>
                        <span class="font-medium">Expert</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-gray-600">Status:</span>
                        <select class="border rounded px-2 py-1" id="status-select">
                            <option value="active" {{ Auth::user()->status === 'active' ? 'selected' : '' }}>Looking for orders</option>
                            <option value="vacation" {{ Auth::user()->status === 'vacation' ? 'selected' : '' }}>On vacation</option>
                            <option value="inactive" {{ Auth::user()->status === 'inactive' ? 'selected' : '' }}>Not willing to work</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Basic Info -->
            <div>
                <h2 class="text-xl font-semibold mb-4">Basic Info</h2>
                <form class="space-y-4" id="basic-info-form">
                    @csrf
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="block text-gray-600 mb-1">Full name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" class="w-full border rounded px-3 py-2" value="{{ Auth::user()->name }}" readonly>
                        </div>
                        <div>
                            <label class="block text-gray-600 mb-1">Email address</label>
                            <input type="email" name="email" class="w-full border rounded px-3 py-2" value="{{ Auth::user()->email }}" readonly>
                        </div>
                        <div>
                            <label class="block text-gray-600 mb-1">Phone number</label>
                            <input type="text" name="phone_number" class="w-full border rounded px-3 py-2" value="{{ Auth::user()->writerProfile->phone_number ?? '' }}" placeholder="Enter your phone number">
                        </div>
                        <div>
                            <label class="block text-gray-600 mb-1">National ID</label>
                            <input type="text" name="national_id" class="w-full border rounded px-3 py-2" value="{{ Auth::user()->writerProfile->national_id ?? '' }}" {{ Auth::user()->writerProfile && Auth::user()->writerProfile->id_verification_status === 'verified' ? 'readonly' : '' }}>
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
                        <select name="native_language" class="w-full border rounded px-3 py-2">
                            @php
                                $nativeLanguage = Auth::user()->writerProfile->native_language ?? '';
                                $languages = ['English', 'Spanish', 'French', 'German', 'Swahili', 'Arabic', 'Chinese', 'Hindi', 'Japanese', 'Russian', 'Portuguese', 'Other'];
                            @endphp
                            @foreach($languages as $language)
                                <option value="{{ $language }}" {{ $nativeLanguage === $language ? 'selected' : '' }}>{{ $language }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-600 mb-2">Years of experience <span class="text-red-500">*</span></label>
                        <input type="number" name="experience_years" min="0" max="30" class="w-full border rounded px-3 py-2" value="{{ Auth::user()->writerProfile->experience_years ?? 0 }}">
                    </div>

                    <div>
                        <label class="block text-gray-600 mb-2">Subjects you are proficient in:</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                            @php
                                $userSubjects = Auth::user()->writerProfile->subjects ?? [];
                                if (!is_array($userSubjects)) {
                                    $userSubjects = [];
                                }
                                
                                $allSubjects = [
                                    'English Literature', 'History', 'Mathematics', 'Physics', 'Chemistry', 
                                    'Biology', 'Computer Science', 'Economics', 'Business Studies', 'Psychology', 
                                    'Sociology', 'Political Science', 'Philosophy', 'Law', 'Medicine', 
                                    'Engineering', 'Architecture', 'Art & Design', 'Music', 'Film Studies',
                                    'Media Studies', 'Communications', 'Journalism', 'Marketing', 'Management', 
                                    'Finance', 'Accounting', 'Nursing', 'Education', 'Social Work'
                                ];
                            @endphp
                            
                            @foreach($allSubjects as $subject)
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="subjects[]" value="{{ $subject }}" class="form-checkbox subject-checkbox" {{ in_array($subject, $userSubjects) ? 'checked' : '' }}>
                                    <span>{{ $subject }}</span>
                                </label>
                            @endforeach
                        </div>
                        <p id="subjects-error" class="mt-1 text-sm text-red-600 hidden">Please select between 2 and 5 subjects</p>
                    </div>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="mt-8">
                <h2 class="text-xl font-semibold mb-4">Contact Info</h2>
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-600 mb-2">Country <span class="text-red-500">*</span></label>
                            <select name="country" class="w-full border rounded px-3 py-2">
                                @php
                                    $userCountry = Auth::user()->writerProfile->country ?? '';
                                    $countries = ['United States', 'Canada', 'United Kingdom', 'Australia', 'Germany', 'France', 'Kenya', 'Nigeria', 'South Africa', 'India', 'China', 'Japan', 'Brazil', 'Other'];
                                @endphp
                                <option value="">Select a country</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country }}" {{ $userCountry === $country ? 'selected' : '' }}>{{ $country }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-600 mb-2">County/State <span class="text-red-500">*</span></label>
                            <input type="text" name="county" class="w-full border rounded px-3 py-2" value="{{ Auth::user()->writerProfile->county ?? '' }}">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-600 mb-2">Available for night calls <span class="text-red-500">*</span></label>
                            <div class="flex space-x-4">
                                <label class="flex items-center">
                                    <input type="radio" name="night_calls" value="1" class="mr-2" {{ Auth::user()->writerProfile && Auth::user()->writerProfile->night_calls ? 'checked' : '' }}> Yes
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="night_calls" value="0" class="mr-2" {{ Auth::user()->writerProfile && !Auth::user()->writerProfile->night_calls ? 'checked' : '' }}> No
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-600 mb-2">Available for force-assign <span class="text-red-500">*</span></label>
                            <div class="flex space-x-4">
                                <label class="flex items-center">
                                    <input type="radio" name="force_assign" value="1" class="mr-2" {{ Auth::user()->writerProfile && Auth::user()->writerProfile->force_assign ? 'checked' : '' }}> Yes
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="force_assign" value="0" class="mr-2" {{ Auth::user()->writerProfile && !Auth::user()->writerProfile->force_assign ? 'checked' : '' }}> No
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
                        <input type="url" name="facebook" class="w-full border rounded px-3 py-2" placeholder="https://facebook.com/username" value="{{ Auth::user()->writerProfile->facebook ?? '' }}">
                    </div>
                    <div>
                        <label class="block text-gray-600 mb-2">LinkedIn</label>
                        <input type="url" name="linkedin" class="w-full border rounded px-3 py-2" placeholder="https://linkedin.com/in/username" value="{{ Auth::user()->writerProfile->linkedin ?? '' }}">
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="mt-8">
                <h2 class="text-xl font-semibold mb-4">Payment Information</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-600 mb-2">Payment Method <span class="text-red-500">*</span></label>
                        <select name="payment_method" class="w-full border rounded px-3 py-2">
                            @php
                                $paymentMethod = Auth::user()->writerProfile->payment_method ?? '';
                            @endphp
                            <option value="">Select payment method</option>
                            <option value="mpesa" {{ $paymentMethod === 'mpesa' ? 'selected' : '' }}>M-Pesa</option>
                            <option value="bank" {{ $paymentMethod === 'bank' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="paypal" {{ $paymentMethod === 'paypal' ? 'selected' : '' }}>PayPal</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-600 mb-2">Payment Details <span class="text-red-500">*</span></label>
                        <textarea name="payment_details" class="w-full border rounded px-3 py-2 h-24" placeholder="Enter your payment details (e.g., M-Pesa number, bank account details, or PayPal email)">{{ Auth::user()->writerProfile->payment_details ?? '' }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Bio -->
            <div class="mt-8">
                <h2 class="text-xl font-semibold mb-4">Professional Bio</h2>
                <textarea name="bio" class="w-full border rounded px-3 py-2 h-32" placeholder="Tell us about your professional background, expertise, and writing experience...">{{ Auth::user()->writerProfile->bio ?? Auth::user()->bio ?? '' }}</textarea>
                <div class="mt-1 text-right text-sm text-gray-500">
                    <span id="bio-chars">0</span>/1000 characters (min 100)
                </div>
            </div>
        </div>
        
        <div class="flex justify-end mt-4 mb-4">
            <button class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition-colors" onclick="confirmUpdate()">
                Save Changes    
            </button>
        </div>
    </div>
</div>

<!-- Custom Alert Box -->
<div id="custom-alert" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 1000;">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Confirm Update</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Are you sure you want to update your profile information?
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <button id="ok-btn" class="px-4 py-2 bg-indigo-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-300" onclick="proceedWithUpdate()">
                    Yes, Update
                </button>
                <button class="mt-3 px-4 py-2 bg-white text-gray-700 text-base font-medium rounded-md w-full shadow-sm border border-gray-300 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-300" onclick="closeAlert()">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configure toastr options
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: "toast-bottom-left",
        timeOut: 5000,
        extendedTimeOut: 1000
    };
    
    // Bio character counter
    const bioField = document.querySelector('textarea[name="bio"]');
    const bioChars = document.getElementById('bio-chars');
    
    if (bioField && bioChars) {
        bioField.addEventListener('input', function() {
            bioChars.textContent = this.value.length;
            
            if (this.value.length < 100) {
                bioChars.classList.remove('text-green-500');
                bioChars.classList.add('text-red-500');
            } else {
                bioChars.classList.remove('text-red-500');
                bioChars.classList.add('text-green-500');
            }
        });
        
        // Trigger initial count
        bioField.dispatchEvent(new Event('input'));
    }
    
    // Subject checkbox limit
    const subjectCheckboxes = document.querySelectorAll('.subject-checkbox');
    subjectCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', checkSubjectLimit);
    });
    
    function checkSubjectLimit() {
        const checked = document.querySelectorAll('.subject-checkbox:checked');
        const errorElement = document.getElementById('subjects-error');
        
        if (checked.length > 5) {
            this.checked = false;
            errorElement.textContent = "You can only select up to 5 subjects";
            errorElement.classList.remove('hidden');
            setTimeout(() => {
                errorElement.classList.add('hidden');
            }, 3000);
        } else if (checked.length < 2) {
            errorElement.textContent = "Please select at least 2 subjects";
            errorElement.classList.remove('hidden');
        } else {
            errorElement.classList.add('hidden');
        }
    }
    
    // Initial check
    checkSubjectLimit();
    
    // Status select change
    const statusSelect = document.getElementById('status-select');
    if (statusSelect) {
        statusSelect.addEventListener('change', function() {
            const status = this.value;
            
            fetch('/profile/update-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify({ status: status })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    toastr.success('Status updated successfully');
                } else {
                    toastr.error('Error updating status: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                toastr.error('An error occurred while updating your status');
            });
        });
    }
});

function confirmUpdate() {
    document.getElementById('custom-alert').classList.remove('hidden');
}

function closeAlert() {
    document.getElementById('custom-alert').classList.add('hidden');
}

function proceedWithUpdate() {
    // Validate form
    const bioField = document.querySelector('textarea[name="bio"]');
    const checkedSubjects = document.querySelectorAll('.subject-checkbox:checked');
    let isValid = true;
    
    if (bioField && bioField.value.length < 100) {
        toastr.error('Bio must be at least 100 characters');
        isValid = false;
    }
    
    if (checkedSubjects.length < 2 || checkedSubjects.length > 5) {
        toastr.error('Please select between 2 and 5 subjects');
        isValid = false;
    }
    
    if (isValid) {
        // Collect form data
        const formData = new FormData();
        
        // Add form fields
        const nameInput = document.querySelector('input[name="name"]');
        if (nameInput) formData.append('name', nameInput.value);
        
        const phoneInput = document.querySelector('input[name="phone_number"]');
        if (phoneInput) formData.append('phone_number', phoneInput.value);
        
        const idInput = document.querySelector('input[name="national_id"]');
        if (idInput) formData.append('national_id', idInput.value);
        
        const langSelect = document.querySelector('select[name="native_language"]');
        if (langSelect) formData.append('native_language', langSelect.value);
        
        const expInput = document.querySelector('input[name="experience_years"]');
        if (expInput) formData.append('experience_years', expInput.value);
        
        const countrySelect = document.querySelector('select[name="country"]');
        if (countrySelect) formData.append('country', countrySelect.value);
        
        const countyInput = document.querySelector('input[name="county"]');
        if (countyInput) formData.append('county', countyInput.value);
        
        // Night calls and force assign
        const nightCalls = document.querySelector('input[name="night_calls"]:checked');
        formData.append('night_calls', nightCalls ? nightCalls.value : '0');
        
        const forceAssign = document.querySelector('input[name="force_assign"]:checked');
        formData.append('force_assign', forceAssign ? forceAssign.value : '0');
        
        // Social networks
        const fbInput = document.querySelector('input[name="facebook"]');
        if (fbInput) formData.append('facebook', fbInput.value);
        
        const liInput = document.querySelector('input[name="linkedin"]');
        if (liInput) formData.append('linkedin', liInput.value);
        
        // Payment information
        const payMethodSelect = document.querySelector('select[name="payment_method"]');
        if (payMethodSelect) formData.append('payment_method', payMethodSelect.value);
        
        const payDetailsInput = document.querySelector('textarea[name="payment_details"]');
        if (payDetailsInput) formData.append('payment_details', payDetailsInput.value);
        
        // Bio
        if (bioField) formData.append('bio', bioField.value);
        
        // Subjects
        checkedSubjects.forEach(checkbox => {
            formData.append('subjects[]', checkbox.value);
        });
        
        // Add CSRF token
        const csrfToken = document.querySelector('input[name="_token"]');
        if (csrfToken) formData.append('_token', csrfToken.value);
        
        // Submit form
        fetch('/profile/update', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                toastr.success('Profile updated successfully');
                setTimeout(() => {
                    window.location.reload();
                }, 1500); // Give time for the user to see the message
            } else {
                toastr.error('Error updating profile: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            toastr.error('An error occurred while updating your profile');
        });
        
        closeAlert();
    }
}
</script>
@endsection