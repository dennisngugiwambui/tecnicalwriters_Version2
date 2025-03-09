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
        
        // Check if the user is a writer and has passed the assessment
        if ($user->usertype !== 'writer' || $user->status !== 'active') {
            if ($user->usertype === 'writer' && $user->status === 'pending') {
                return redirect()->route('assessment.grammar')
                    ->with('warning', 'You need to complete the grammar assessment first.');
            } else if ($user->usertype === 'writer' && 
                      (in_array($user->status, ['failed', 'suspended', 'banned', 'terminated', 'locked']) || 
                       $user->is_suspended === 'yes')) {
                return redirect()->route('failed')
                    ->with('error', 'Your account status does not allow profile setup.');
            } else {
                return redirect()->route('home');
            }
        }
        
        // Check if profile is already completed
        if ($user->profile_completed) {
            return redirect()->route('writer.available')
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
        
        // Check if the user is a writer and has passed the assessment
        if ($user->usertype !== 'writer' || $user->status !== 'active') {
            if ($user->usertype === 'writer' && $user->status === 'pending') {
                return redirect()->route('assessment.grammar')
                    ->with('warning', 'You need to complete the grammar assessment first.');
            } else if ($user->usertype === 'writer' && 
                      (in_array($user->status, ['failed', 'suspended', 'banned', 'terminated', 'locked']) || 
                       $user->is_suspended === 'yes')) {
                return redirect()->route('failed')
                    ->with('error', 'Your account status does not allow profile setup.');
            } else {
                return redirect()->route('home');
            }
        }
        
        // Check if profile is already completed
        if ($user->profile_completed) {
            return redirect()->route('writer.available')
                ->with('info', 'Your profile has already been set up.');
        }
        
        // Validate request data
        $validator = Validator::make($request->all(), [
            'education_level' => 'required|string|in:high_school,bachelor,master,phd',
            'experience_years' => 'required|integer|min:0|max:30',
            'subjects' => 'required|array|min:1',
            'subjects.*' => 'string',
            'bio' => 'required|string|min:100|max:1000',
            'phone_number' => 'required|string|max:15',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'profile_picture' => 'nullable|image|max:2048', // 2MB max
            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:5120', // 5MB max
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
            
            // Update profile data
            $profile->education_level = $request->education_level;
            $profile->experience_years = $request->experience_years;
            $profile->subjects = $request->subjects;
            $profile->bio = $request->bio;
            $profile->phone_number = $request->phone_number;
            $profile->address = $request->address;
            $profile->city = $request->city;
            $profile->country = $request->country;
            $profile->payment_method = $request->payment_method;
            $profile->payment_details = $request->payment_details;
            
            // Handle profile picture upload
            if ($request->hasFile('profile_picture')) {
                $file = $request->file('profile_picture');
                $fileName = uniqid('profile_') . '.' . $file->getClientOriginalExtension();
                
                // Delete previous file if exists
                if ($profile->profile_picture && Storage::exists('public/profiles/' . $profile->profile_picture)) {
                    Storage::delete('public/profiles/' . $profile->profile_picture);
                }
                
                // Store new file
                $file->storeAs('public/profiles', $fileName);
                $profile->profile_picture = $fileName;
            }
            
            // Handle resume upload
            if ($request->hasFile('resume')) {
                $file = $request->file('resume');
                $fileName = uniqid('resume_') . '.' . $file->getClientOriginalExtension();
                
                // Delete previous file if exists
                if ($profile->resume && Storage::exists('public/resumes/' . $profile->resume)) {
                    Storage::delete('public/resumes/' . $profile->resume);
                }
                
                // Store new file
                $file->storeAs('public/resumes', $fileName);
                $profile->resume = $fileName;
            }
            
            // Save profile
            $profile->save();
            
            // Mark user profile as completed
            $user->profile_completed = true;
            $user->verified_at = Carbon::now(); // Add a timestamp for verification
            $user->save();
            
            DB::commit();
            
            // Log successful profile setup
            Log::info('Writer #' . $user->id . ' completed profile setup');
            
            // Redirect to available orders page
            return redirect()->route('writer.available')
                ->with('success', 'Your profile has been set up successfully. You can now start accepting orders.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Profile setup error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'An error occurred while saving your profile. Please try again.')
                ->withInput();
        }
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