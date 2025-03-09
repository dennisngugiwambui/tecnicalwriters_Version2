<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileCompletionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Skip middleware if user is not authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $path = $request->path();
        
        // If user is not a writer, allow access
        if ($user->usertype !== 'writer') {
            return $next($request);
        }
        
        // Define allowed routes for different states
        $assessmentRoutes = [
            'assessment/grammar',
            'assessment/submit',
            'assessment/auto-submit',
            'failed',
            'logout'
        ];
        
        $profileSetupRoutes = [
            'profilesetup',
            'profilesetup/submit',
            'logout'
        ];
        
        // Check user status and restrict access accordingly
        if ($user->status === 'pending') {
            // User needs to take the assessment
            if (!in_array($path, $assessmentRoutes)) {
                return redirect()->route('assessment.grammar')
                    ->with('warning', 'You need to complete the grammar assessment first.');
            }
        } else if ($user->status === 'failed') {
            // User failed the assessment
            if (!in_array($path, $assessmentRoutes)) {
                return redirect()->route('failed')
                    ->with('error', 'You did not pass the grammar assessment. Please wait until the waiting period ends to retake it.');
            }
        } else if ($user->status === 'active' && !$user->profile_completed) {
            // User passed assessment but needs to complete profile
            if (!in_array($path, $profileSetupRoutes)) {
                return redirect()->route('profilesetup')
                    ->with('warning', 'Please complete your profile setup before accessing other pages.');
            }
        }
        
        return $next($request);
    }
}