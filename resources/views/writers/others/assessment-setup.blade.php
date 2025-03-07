@extends('writers.others.assessment')

@section('content')
<div class="container py-8">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-red-600 text-white px-6 py-4">
            <h2 class="text-xl font-semibold">Assessment Setup Required</h2>
        </div>
        
        <div class="p-6">
            <div class="mb-4 text-red-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                
                @if(session('error'))
                    <p class="text-center font-medium">{{ session('error') }}</p>
                @else
                    <p class="text-center font-medium">Assessment System Setup Required</p>
                @endif
            </div>
            
            <p class="text-gray-600 mb-6">
                The assessment system hasn't been properly configured yet. Our team has been notified and is working to resolve this issue.
            </p>
            
            <p class="text-gray-600 mb-6">
                Please try again later or contact support if you continue to experience this issue.
            </p>
            
            <div class="flex justify-center">
                <a href="{{ route('welcome') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md font-medium hover:bg-blue-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Return to Welcome Page
                </a>
            </div>
        </div>
    </div>
</div>
@endsection