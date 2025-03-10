<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AssessmentController extends Controller
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
     * Show the failed assessment page
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showFailedPage()
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to view this page.');
        }

        $user = Auth::user();
        
        // Only writers should see this page
        if ($user->usertype !== 'writer') {
            return redirect()->route('home');
        }
        
        // Get the latest assessment result
        $result = AssessmentResult::where('user_id', $user->id)
            ->where('assessment_type', 'grammar')
            ->where('passed', false)
            ->latest()
            ->first();
            
        // If no failed result exists and user is not in failed status, redirect
        if (!$result && $user->status !== 'failed') {
            return redirect()->route('home');
        }
        
        // If no result exists but status is failed, create a placeholder result object
        if (!$result) {
            // Create a dummy result object with basic properties
            $result = new \stdClass();
            $result->percentage = 0;
            $result->created_at = now()->subDays(6); // Assume 6 days ago to show 1 day remaining
        }
        
        // Calculate remaining time until retake
        $lastAttempt = Carbon::parse($result->created_at);
        $hoursRemaining = 168 - $lastAttempt->diffInHours(Carbon::now()); // 168 hours = 7 days
        $hoursRemaining = max(0, $hoursRemaining);
        
        // Calculate percentage if it comes from session flash data
        $percentage = session('percentage') ?? $result->percentage ?? 0;
        
        return view('writers.others.failed', [
            'result' => $result,
            'hoursRemaining' => $hoursRemaining,
            'percentage' => $percentage
        ]);
    }

    /**
     * Show the grammar assessment page
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showAssessment()
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to take the assessment.');
        }
        
        $user = Auth::user();
        Log::info('User accessed assessment: ' . $user->id . ' with status: ' . $user->status);
        
        // Only writers with pending status should access this page
        if ($user->usertype !== 'writer') {
            return redirect()->route('home')->with('error', 'Only writers can access this assessment.');
        }
        
        // If already active, redirect appropriately
        if ($user->status === 'active') {
            if (!$user->profile_completed) {
                return redirect()->route('profilesetup')->with('info', 'You have already passed the assessment. Please complete your profile.');
            }
            return redirect()->route('writer.available')->with('info', 'Your account is already active.');
        }
        
        // If status is not pending, redirect appropriately
        if ($user->status !== 'pending') {
            if ($user->status === 'failed') {
                return redirect()->route('failed')->with('error', 'You have failed the assessment.');
            }
            return redirect()->route('home')->with('error', 'You cannot take the assessment at this time.');
        }
        
        // Check if user has already completed the assessment
        $existingResult = AssessmentResult::where('user_id', $user->id)
            ->where('assessment_type', 'grammar')
            ->latest()
            ->first();
            
        if ($existingResult) {
            if ($existingResult->passed) {
                // Update user status to active if passed
                $user->status = 'active';
                $user->save();
                
                return redirect()->route('profilesetup')->with('success', 'You have already passed the grammar assessment.');
            } else {
                // If failed and 7 days have passed, allow retake
                $lastAttempt = Carbon::parse($existingResult->created_at);
                $daysSinceLastAttempt = $lastAttempt->diffInDays(Carbon::now());
                $hoursRemaining = 168 - $lastAttempt->diffInHours(Carbon::now()); // 168 hours = 7 days
                
                if ($daysSinceLastAttempt < 7) {
                    return redirect()->route('failed')->with([
                        'error' => 'You did not pass the grammar assessment. You can retake it after the waiting period.',
                        'percentage' => $existingResult->percentage,
                        'hoursRemaining' => $hoursRemaining
                    ]);
                }
                
                // Allow retake after 7 days
                $existingResult->delete();
            }
        }
        
        // Check for existing assessment session
        $existingSession = Assessment::where('user_id', $user->id)
            ->where('type', 'grammar')
            ->whereNull('completed_at')
            ->first();
            
        if ($existingSession) {
            // Use existing session but get fresh questions 
            Log::info('Using existing assessment session ID: ' . $existingSession->id);
            $assessment = $existingSession;
        } else {
            try {
                // Get 20 real grammar questions
                $questions = AssessmentQuestion::where('type', 'grammar')
                    ->where('question', 'NOT LIKE', '%placeholder%')
                    ->inRandomOrder()
                    ->limit(20)
                    ->get();
                    
                // Verify we have enough questions
                if ($questions->count() < 20) {
                    Log::error('Not enough grammar questions in database. Found: ' . $questions->count());
                    return redirect()->route('welcome')->with('error', 'Assessment system is being updated. Please try again later.');
                }
                
                // Ensure all questions have valid options
                foreach ($questions as $question) {
                    if (!is_array($question->options) || count($question->options) < 2) {
                        Log::error('Question ID ' . $question->id . ' has invalid options: ' . json_encode($question->options));
                        return redirect()->route('welcome')->with('error', 'Assessment system is being updated. Please try again later.');
                    }
                }
                
                // Generate a token for this assessment session
                $token = md5($user->id . time() . rand(1000, 9999));
                
                // Store assessment session
                DB::beginTransaction();
                try {
                    $assessment = Assessment::create([
                        'user_id' => $user->id,
                        'type' => 'grammar',
                        'token' => $token,
                        'started_at' => Carbon::now(),
                        'expires_at' => Carbon::now()->addMinutes(17), // 17 minute time limit
                        'question_count' => $questions->count()
                    ]);
                    
                    DB::commit();
                    Log::info('Created new assessment session ID: ' . $assessment->id);
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Failed to create assessment: ' . $e->getMessage());
                    return redirect()->route('welcome')->with('error', 'Assessment system is being updated. Please try again later.');
                }
                
                // Return the assessment view with questions
                try {
                    // First check if the view exists
                    if (!view()->exists('writers.others.assessment-setup')) {
                        Log::error('View writers.others.assessment-setup does not exist');
                        // Try fallback to assessment view
                        if (view()->exists('writers.others.assessment')) {
                            Log::info('Using fallback view writers.others.assessment');
                            return view('writers.others.assessment', [
                                'questions' => $questions,
                                'assessment' => $assessment
                            ]);
                        } else {
                            Log::error('Fallback view writers.others.assessment also does not exist');
                            return redirect()->route('welcome')->with('error', 'Assessment system is being updated. Please try again later.');
                        }
                    }
                    
                    // View exists, proceed normally
                    return view('writers.others.assessment-setup', [
                        'questions' => $questions,
                        'assessment' => $assessment
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error rendering assessment view: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
                    return redirect()->route('welcome')->with('error', 'Assessment system is being updated. Please try again later.');
                }
            } catch (\Exception $e) {
                Log::error('Error in showAssessment: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
                return redirect()->route('welcome')->with('error', 'Assessment system is being updated. Please try again later.');
            }
        }
    }

    
    
    /**
     * Submit the grammar assessment
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function submitAssessment(Request $request)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to submit an assessment.');
        }
        
        try {
            $request->validate([
                'assessment_id' => 'required|exists:assessments,id',
                'token' => 'required|string',
                'answers' => 'required|array',
                'answers.*' => 'required|string'
            ]);
            
            // Get the assessment
            $assessment = Assessment::findOrFail($request->assessment_id);
            
            // Verify user owns this assessment
            if ($assessment->user_id !== Auth::id()) {
                Log::warning('User ' . Auth::id() . ' attempted to submit assessment ' . $assessment->id . ' owned by user ' . $assessment->user_id);
                return redirect()->route('assessment.grammar')->with('error', 'Invalid assessment session.');
            }
            
            // Verify token and that assessment hasn't expired
            if ($assessment->token !== $request->token) {
                Log::warning('Invalid token provided for assessment ' . $assessment->id);
                return redirect()->route('assessment.grammar')->with('error', 'Invalid assessment token.');
            }
            
            // Check if assessment was already completed
            if ($assessment->completed_at) {
                Log::warning('Attempted to submit already completed assessment ' . $assessment->id);
                return redirect()->route('failed')->with('error', 'This assessment has already been submitted.');
            }
            
            if (Carbon::now()->gt($assessment->expires_at)) {
                DB::beginTransaction();
                try {
                    // Record as failed due to time expiry
                    $result = AssessmentResult::create([
                        'user_id' => $assessment->user_id,
                        'assessment_id' => $assessment->id,
                        'assessment_type' => $assessment->type,
                        'score' => 0,
                        'max_score' => $assessment->question_count,
                        'percentage' => 0,
                        'passed' => false,
                        'time_taken' => 17, // maxed out at 17 minutes
                        'completed_at' => Carbon::now()
                    ]);
                    
                    $user = User::find($assessment->user_id);
                    $user->status = 'failed';
                    $user->save();
                    
                    // Mark assessment as completed
                    $assessment->completed_at = Carbon::now();
                    $assessment->save();
                    
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Error recording failed assessment: ' . $e->getMessage());
                }
                
                return redirect()->route('failed')->with('error', 'Assessment time expired. You did not complete the assessment in time.');
            }
            
            // Calculate time taken
            $timeTaken = Carbon::parse($assessment->started_at)->diffInMinutes(Carbon::now());
            if ($timeTaken > 17) $timeTaken = 17; // Cap at 17 minutes
            
            // Get all questions used in this assessment
            $questionIds = array_keys($request->answers);
            $questions = AssessmentQuestion::whereIn('id', $questionIds)->get()->keyBy('id');
            
            // Calculate score
            $score = 0;
            $questionCount = count($questionIds);
            $answeredQuestions = 0;
            
            foreach ($request->answers as $questionId => $answer) {
                if (isset($questions[$questionId])) {
                    $answeredQuestions++;
                    $question = $questions[$questionId];
                    if (strtolower(trim($answer)) === strtolower(trim($question->correct_answer))) {
                        $score++;
                    }
                }
            }
            
            // Make sure they answered all questions
            if ($answeredQuestions < $questionCount) {
                Log::warning('User ' . Auth::id() . ' only answered ' . $answeredQuestions . ' of ' . $questionCount . ' questions');
            }
            
            // Calculate percentage
            $percentage = ($questionCount > 0) ? ($score / $questionCount) * 100 : 0;
            $passed = $percentage >= 80; // Pass threshold is 80%
            
            DB::beginTransaction();
            try {
                // Save the result
                $result = AssessmentResult::create([
                    'user_id' => $assessment->user_id,
                    'assessment_id' => $assessment->id,
                    'assessment_type' => $assessment->type,
                    'score' => $score,
                    'max_score' => $questionCount,
                    'percentage' => $percentage,
                    'passed' => $passed,
                    'time_taken' => $timeTaken,
                    'completed_at' => Carbon::now()
                ]);
                
                // Update user status
                $user = User::find($assessment->user_id);
                $user->status = $passed ? 'active' : 'failed';
                $user->save();
                
                // Mark assessment as completed
                $assessment->completed_at = Carbon::now();
                $assessment->save();
                
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error saving assessment result: ' . $e->getMessage());
                return redirect()->route('assessment.grammar')->with('error', 'An error occurred while submitting your assessment. Please try again.');
            }
            
            if ($passed) {
                return redirect()->route('profilesetup')->with('success', 
                    'Congratulations! You passed the grammar assessment with ' . round($percentage) . '%.');
            } else {
                return redirect()->route('failed')->with([
                    'error' => 'You did not pass the grammar assessment. You scored ' . round($percentage) . '%. You need 80% to pass. You can retake the assessment after 7 days.',
                    'percentage' => round($percentage)
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error in submitAssessment: ' . $e->getMessage());
            return redirect()->route('assessment.grammar')->with('error', 'An error occurred while submitting your assessment. Please try again.');
        }
    }
    
    /**
     * Auto-submit the assessment when timer expires via AJAX
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function autoSubmitAssessment(Request $request)
    {
        // Check if user is authenticated via AJAX
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Authentication required', 'redirect' => route('login')], 401);
        }
        
        try {
            $validatedData = $request->validate([
                'assessment_id' => 'required|exists:assessments,id',
                'token' => 'required|string',
                'answers' => 'required|array',
                'answers.*' => 'nullable|string'
            ]);
            
            // Process submission just like the regular submit
            $assessment = Assessment::findOrFail($validatedData['assessment_id']);
            
            // Verify user owns this assessment
            if ($assessment->user_id !== Auth::id()) {
                return response()->json(['success' => false, 'message' => 'Invalid assessment session'], 403);
            }
            
            if ($assessment->token !== $validatedData['token']) {
                return response()->json(['success' => false, 'message' => 'Invalid assessment token'], 400);
            }
            
            // Check if assessment was already completed
            if ($assessment->completed_at) {
                return response()->json(['success' => false, 'message' => 'This assessment has already been submitted'], 400);
            }
            
            // Calculate score, etc - same logic as submitAssessment but for AJAX
            $questionIds = array_keys($validatedData['answers']);
            $questions = AssessmentQuestion::whereIn('id', $questionIds)->get()->keyBy('id');
            
            $score = 0;
            $questionCount = count($questionIds);
            
            foreach ($validatedData['answers'] as $questionId => $answer) {
                if (isset($questions[$questionId]) && !empty($answer)) {
                    $question = $questions[$questionId];
                    if (strtolower(trim($answer)) === strtolower(trim($question->correct_answer))) {
                        $score++;
                    }
                }
            }
            
            $percentage = ($questionCount > 0) ? ($score / $questionCount) * 100 : 0;
            $passed = $percentage >= 80;
            
            DB::beginTransaction();
            try {
                // Save result
                $result = AssessmentResult::create([
                    'user_id' => $assessment->user_id,
                    'assessment_id' => $assessment->id,
                    'assessment_type' => $assessment->type,
                    'score' => $score,
                    'max_score' => $questionCount,
                    'percentage' => $percentage,
                    'passed' => $passed,
                    'time_taken' => 17, // maxed out
                    'completed_at' => Carbon::now()
                ]);
                
                $user = User::find($assessment->user_id);
                $user->status = $passed ? 'active' : 'failed';
                $user->save();
                
                $assessment->completed_at = Carbon::now();
                $assessment->save();
                
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error in autoSubmitAssessment: ' . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'Error saving assessment: ' . $e->getMessage()], 500);
            }
            
            $redirectUrl = $passed ? route('profilesetup') : route('failed');
            $message = $passed ? 
                'Congratulations! You passed the grammar assessment with ' . round($percentage) . '%.' :
                'You did not pass the grammar assessment. You scored ' . round($percentage) . 
                '%. You need 80% to pass. You can retake the assessment after 7 days.';
                
            return response()->json([
                'success' => true,
                'passed' => $passed,
                'percentage' => round($percentage),
                'redirect' => $redirectUrl,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            Log::error('Error in autoSubmitAssessment: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }
}