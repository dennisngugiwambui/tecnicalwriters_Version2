<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WriterProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProfileSetupController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the profile setup page
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showProfileSetup()
    {
        $user = Auth::user();
        
        // Check if the user is a writer
        if ($user->usertype !== 'writer') {
            return redirect()->route('home');
        }
        
        // If user hasn't passed the assessment yet, redirect to grammar assessment
        if ($user->status !== 'active') {
            if ($user->status === 'pending') {
                return redirect()->route('assessment.grammar')
                    ->with('warning', 'You need to complete the grammar assessment first.');
            } else if (in_array($user->status, ['failed', 'suspended', 'banned', 'terminated', 'locked']) || 
                      $user->is_suspended === 'yes') {
                return redirect()->route('failed')
                    ->with('error', 'Your account status does not allow profile setup.');
            }
        }
        
        // Check if profile already exists - this is to replace the profile_completed check
        $profileExists = WriterProfile::where('user_id', $user->id)->exists();
        if ($profileExists) {
            return redirect()->route('home')
                ->with('info', 'Your profile has already been set up.');
        }
        
        // Get existing profile data if any
        $profile = WriterProfile::where('user_id', $user->id)->first();
        
        // Get list of subjects for dropdown
        $subjects = $this->getSubjectsList();
        
        // Return the profile setup view
        return view('new.profilesetup', [
            'user' => $user,
            'profile' => $profile,
            'subjects' => $subjects
        ]);
    }
    
    /**
     * Save the profile setup information
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveProfileSetup(Request $request)
    {
        $user = Auth::user();
        
        // Check if the user is a writer
        if ($user->usertype !== 'writer') {
            return redirect()->route('home');
        }
        
        // If user hasn't passed the assessment yet, redirect to grammar assessment
        if ($user->status !== 'active') {
            if ($user->status === 'pending') {
                return redirect()->route('assessment.grammar')
                    ->with('warning', 'You need to complete the grammar assessment first.');
            } else if (in_array($user->status, ['failed', 'suspended', 'banned', 'terminated', 'locked']) || 
                      $user->is_suspended === 'yes') {
                return redirect()->route('failed')
                    ->with('error', 'Your account status does not allow profile setup.');
            }
        }
        
        // Check if profile already exists - this is to replace the profile_completed check
        $profileExists = WriterProfile::where('user_id', $user->id)->exists();
        if ($profileExists) {
            return redirect()->route('home')
                ->with('info', 'Your profile has already been set up.');
        }
        
        // Validate request data
        $validator = Validator::make($request->all(), [
            'education_level' => 'required|string|in:high_school,bachelor,master,phd',
            'experience_years' => 'required|integer|min:0|max:30',
            'subjects' => 'required|array|min:2|max:5',
            'subjects.*' => 'string',
            'bio' => 'required|string|min:100|max:1000',
            'phone_number' => 'required|string|max:15',
            'national_id' => 'required|string|max:20',
            'national_id_image' => 'required|image|max:5120', // 5MB max
            'country' => 'required|string|max:100',
            'county' => 'required|string|max:100',
            'native_language' => 'required|string',
            'profile_picture' => 'nullable|image|max:2048', // 2MB max
            'night_calls' => 'nullable',
            'force_assign' => 'nullable',
            'linkedin' => 'nullable|url|max:255',
            'facebook' => 'nullable|url|max:255',
            'payment_method' => 'required|string|in:mpesa,bank,paypal',
            'payment_details' => 'required|string|max:255',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        try {
            // Begin transaction for data consistency
            DB::beginTransaction();
            
            // Find or create writer profile
            $profile = WriterProfile::firstOrNew(['user_id' => $user->id]);
            
            // Generate writer_id if it doesn't exist
            if (!$profile->writer_id) {
                $profile->writer_id = $this->generateWriterId($user->id);
            }
            
            // Update profile data
            $profile->phone_number = $request->phone_number;
            $profile->national_id = $request->national_id;
            $profile->country = $request->country;
            $profile->county = $request->county;
            $profile->native_language = $request->native_language;
            $profile->education_level = $request->education_level;
            $profile->experience_years = $request->experience_years;
            $profile->subjects = $request->subjects;
            $profile->bio = $request->bio;
            $profile->night_calls = $request->has('night_calls') ? true : false;
            $profile->force_assign = $request->has('force_assign') ? true : false;
            $profile->linkedin = $request->linkedin;
            $profile->facebook = $request->facebook;
            $profile->payment_method = $request->payment_method;
            $profile->payment_details = $request->payment_details;
            $profile->id_verification_status = 'pending';
            
            // Handle national ID image upload
            if ($request->hasFile('national_id_image')) {
                $file = $request->file('national_id_image');
                $fileName = $user->id . '_national_id_' . time() . '.' . $file->getClientOriginalExtension();
                
                // Delete previous file if exists
                if ($profile->national_id_image && Storage::exists('private/national_ids/' . $profile->national_id_image)) {
                    Storage::delete('private/national_ids/' . $profile->national_id_image);
                }
                
                // Store new file in a private directory (not publicly accessible)
                $path = $file->storeAs('private/national_ids', $fileName);
                if (!$path) {
                    throw new \Exception('Failed to upload national ID image');
                }
                $profile->national_id_image = $fileName;
            }
            
            // Handle profile picture upload
            if ($request->hasFile('profile_picture')) {
                $file = $request->file('profile_picture');
                $fileName = uniqid('profile_') . '.' . $file->getClientOriginalExtension();
                
                // Delete previous file if exists
                if ($profile->profile_picture && Storage::exists('public/profiles/' . $profile->profile_picture)) {
                    Storage::delete('public/profiles/' . $profile->profile_picture);
                }
                
                // Store new file
                $path = $file->storeAs('public/profiles', $fileName);
                if (!$path) {
                    throw new \Exception('Failed to upload profile picture');
                }
                $profile->profile_picture = $fileName;
            }
            
            // Initialize statistics
            $profile->rating = 0.00;
            $profile->jobs_completed = 0;
            $profile->earnings = 0.00;
            
            // Save profile
            $profile->save();
            
            // Update user - we've removed the profile_completed field
            // Only update the fields that exist in the users table
            $user->status = 'active'; // Ensure status is active
            $user->bio = $request->bio; // Update bio in user table if needed
            
            // Update user's specialization from subjects if the field exists
            if (in_array('specialization', $user->getFillable())) {
                $user->specialization = implode(', ', array_slice($request->subjects, 0, 2));
            }
            
            // Add a verification timestamp if the field exists
            if (in_array('verified_at', $user->getFillable())) {
                $user->verified_at = Carbon::now();
            }
            
            // Save user changes
            $user->save();
            
            DB::commit();
            
            // Log successful profile setup
            Log::info('Writer #' . $user->id . ' completed profile setup with writer_id: ' . $profile->writer_id);
            
            // Redirect to index page
            return redirect()->route('home')
                ->with('success', 'Your profile has been set up successfully. Your ID is pending verification. You can now start accepting orders.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Profile setup error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'An error occurred while saving your profile: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Generate a unique writer ID based on user ID
     * 
     * @param int $userId
     * @return string
     */
    private function generateWriterId($userId)
    {
        // Create a writer ID with format WRT-XXXXX where XXXXX is padded user ID
        $prefix = 'WRT-';
        $paddedId = str_pad($userId, 5, '0', STR_PAD_LEFT);
        
        // Add a unique suffix to ensure uniqueness
        $uniqueSuffix = substr(uniqid(), -4);
        
        return $prefix . $paddedId . '-' . $uniqueSuffix;
    }
    
    /**
     * Get list of available subjects
     * 
     * @return array
     */
    private function getSubjectsList()
    {
        // This can be replaced with a database query if subjects are stored in the database
        return [
            'English Literature' => 'English Literature',
            'History' => 'History',
            'Mathematics' => 'Mathematics',
            'Physics' => 'Physics',
            'Chemistry' => 'Chemistry',
            'Biology' => 'Biology',
            'Computer Science' => 'Computer Science',
            'Economics' => 'Economics',
            'Business Studies' => 'Business Studies',
            'Psychology' => 'Psychology',
            'Sociology' => 'Sociology',
            'Political Science' => 'Political Science',
            'Philosophy' => 'Philosophy',
            'Law' => 'Law',
            'Medicine' => 'Medicine',
            'Engineering' => 'Engineering',
            'Architecture' => 'Architecture',
            'Art & Design' => 'Art & Design',
            'Music' => 'Music',
            'Film Studies' => 'Film Studies',
            'Media Studies' => 'Media Studies',
            'Communications' => 'Communications',
            'Journalism' => 'Journalism',
            'Marketing' => 'Marketing',
            'Management' => 'Management',
            'Finance' => 'Finance',
            'Accounting' => 'Accounting',
            'Nursing' => 'Nursing',
            'Education' => 'Education',
            'Social Work' => 'Social Work',
            'Other' => 'Other'
        ];
    }
}