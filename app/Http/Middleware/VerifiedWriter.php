<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class VerifiedWriter
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
        // Check if user is authenticated
        if (Auth::check()) {
            $user = Auth::user();
            
            if ($user->usertype === 'writer') {
                if ($user->status === 'pending') {
                    return redirect()->route('assessment.grammar')
                        ->with('message', 'You need to complete the grammar assessment.');
                }
                
                if (in_array($user->status, ['failed', 'suspended', 'banned', 'terminated', 'locked']) || 
                    $user->is_suspended === 'yes') {
                    return redirect()->route('failed')
                        ->with('message', 'Your account has been ' . $user->status);
                }
            }
            
            // If user is active, proceed
            if ($user->status === 'active') {
                return $next($request);
            }
        }
        
        // If not authenticated, redirect to login
        return redirect()->route('login');
    }
}