@extends('layouts.assessment')

@section('title', 'Grammar Assessment')

@section('styles')
<style>
    /* Prevent text selection */
    body {
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        overflow-x: hidden;
    }
    
    /* Prevent screenshot notifications on some browsers */
    html {
        -webkit-user-select: none;
        -webkit-touch-callout: none;
    }
    
    /* Timer styling */
    .timer-container {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        padding: 0.5rem 1rem;
        background-color: #ffffff;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        z-index: 100;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .timer {
        font-size: 1.5rem;
        font-weight: bold;
        color: #2c3e50;
    }
    
    .timer.danger {
        color: #e74c3c;
        animation: pulse 1s infinite;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    
    /* Progress bar styling */
    .progress-container {
        height: 6px;
        width: 100%;
        background-color: #ecf0f1;
        position: fixed;
        top: 60px; /* Below timer */
        left: 0;
        z-index: 90;
    }
    
    .progress-bar {
        height: 100%;
        background-color: #3498db;
        transition: width 0.3s ease;
    }
    
    /* Question container styling */
    .question-container {
        max-width: 800px;
        margin: 100px auto 2rem;
        padding: 2rem;
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
    }
    
    /* Question styling */
    .question {
        margin-bottom: 2rem;
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 0.5s ease, transform 0.5s ease;
    }
    
    .question.active {
        opacity: 1;
        transform: translateY(0);
    }
    
    .question-number {
        font-size: 1rem;
        color: #7f8c8d;
        margin-bottom: 0.5rem;
    }
    
    .question-text {
        font-size: 1.25rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 1.5rem;
        line-height: 1.6;
    }
    
    /* Answer input styling */
    .answer-input {
        width: 100%;
        padding: 1rem;
        border: 2px solid #dfe6e9;
        border-radius: 5px;
        font-size: 1rem;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }
    
    .answer-input:focus {
        border-color: #3498db;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        outline: none;
    }
    
    /* Multiple choice styling */
    .options-container {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
    
    .option-label {
        display: block;
        padding: 1rem;
        border: 2px solid #dfe6e9;
        border-radius: 5px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .option-label:hover {
        border-color: #bdc3c7;
        background-color: #f8f9fa;
    }
    
    input[type="radio"] {
        display: none;
    }
    
    input[type="radio"]:checked + .option-label {
        border-color: #3498db;
        background-color: rgba(52, 152, 219, 0.1);
    }
    
    /* Navigation buttons */
    .nav-buttons {
        display: flex;
        justify-content: space-between;
        margin-top: 2rem;
    }
    
    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 5px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
    }
    
    .btn-prev {
        background-color: #e0e0e0;
        color: #333;
    }
    
    .btn-prev:hover {
        background-color: #d0d0d0;
    }
    
    .btn-next {
        background-color: #3498db;
        color: white;
    }
    
    .btn-next:hover {
        background-color: #2980b9;
    }
    
    .btn-submit {
        background-color: #2ecc71;
        color: white;
    }
    
    .btn-submit:hover {
        background-color: #27ae60;
    }
    
    .btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    /* Loading overlay */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(255, 255, 255, 0.9);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        z-index: 1000;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
    }
    
    .loading-overlay.active {
        opacity: 1;
        pointer-events: all;
    }
    
    .spinner {
        width: 50px;
        height: 50px;
        border: 5px solid #f3f3f3;
        border-top: 5px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-bottom: 1rem;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .loading-text {
        font-size: 1.25rem;
        color: #2c3e50;
    }
    
    /* For smaller screens */
    @media (max-width: 768px) {
        .timer {
            font-size: 1.25rem;
        }
        
        .question-container {
            margin: 90px 1rem 2rem;
            padding: 1.5rem;
        }
        
        .question-text {
            font-size: 1.1rem;
        }
        
        .btn {
            padding: 0.6rem 1.2rem;
            font-size: 0.9rem;
        }
    }
</style>
@endsection

@section('content')
<!-- Timer bar -->
<div class="timer-container">
    <div>
        <h1 class="text-lg font-bold">Grammar Assessment</h1>
    </div>
    <div class="timer" id="timer">17:00</div>
</div>

<!-- Progress bar -->
<div class="progress-container">
    <div class="progress-bar" id="progressBar" style="width: 5%;"></div>
</div>

<!-- Questions container -->
<div class="question-container">
    <form id="assessmentForm" method="POST" action="{{ route('assessment.submit') }}">
        @csrf
        <input type="hidden" name="assessment_id" value="{{ $assessment->id }}">
        <input type="hidden" name="token" value="{{ $assessment->token }}">
        
        <!-- Questions will be generated here -->
        @foreach($questions as $index => $question)
            <div class="question {{ $index === 0 ? 'active' : '' }}" data-question="{{ $index + 1 }}">
                <div class="question-number">Question {{ $index + 1 }} of {{ $questions->count() }}</div>
                <div class="question-text">{!! $question->question !!}</div>
                
                @if(isset($question->options) && is_array($question->options) && count($question->options) > 0)
                    <!-- Multiple choice question -->
                    <div class="options-container">
                        @foreach($question->options as $optionKey => $option)
                            <div>
                                <input type="radio" id="q{{ $question->id }}_{{ $optionKey }}" 
                                       name="answers[{{ $question->id }}]" value="{{ $option }}"
                                       class="option-input">
                                <label for="q{{ $question->id }}_{{ $optionKey }}" class="option-label">
                                    {{ $option }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                @else
                    <!-- Text input question -->
                    <input type="text" class="answer-input" 
                           name="answers[{{ $question->id }}]" 
                           placeholder="Type your answer here..." 
                           autocomplete="off">
                @endif
                
                <div class="nav-buttons">
                    <button type="button" class="btn btn-prev" {{ $index === 0 ? 'disabled' : '' }}>Previous</button>
                    
                    @if($index === $questions->count() - 1)
                        <button type="button" class="btn btn-submit" onclick="submitAssessment()">Submit</button>
                    @else
                        <button type="button" class="btn btn-next">Next</button>
                    @endif
                </div>
            </div>
        @endforeach
    </form>
</div>

<!-- Loading overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner"></div>
    <div class="loading-text">Submitting your assessment...</div>
</div>
@endsection

@section('scripts')
<script>
    // Prevent F12, right-click, and other ways to access dev tools
    document.addEventListener('keydown', function(e) {
        // Prevent F12
        if (e.key === 'F12' || e.keyCode === 123) {
            e.preventDefault();
            return false;
        }
        
        // Prevent Ctrl+Shift+I
        if (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.keyCode === 73)) {
            e.preventDefault();
            return false;
        }
        
        // Prevent Ctrl+Shift+J
        if (e.ctrlKey && e.shiftKey && (e.key === 'J' || e.keyCode === 74)) {
            e.preventDefault();
            return false;
        }
        
        // Prevent Ctrl+Shift+C
        if (e.ctrlKey && e.shiftKey && (e.key === 'C' || e.keyCode === 67)) {
            e.preventDefault();
            return false;
        }
        
        // Prevent Ctrl+U (view source)
        if (e.ctrlKey && (e.key === 'U' || e.keyCode === 85)) {
            e.preventDefault();
            return false;
        }
        
        // Prevent Ctrl+C (copy)
        if (e.ctrlKey && (e.key === 'C' || e.keyCode === 67)) {
            if (window.getSelection().toString()) {
                e.preventDefault();
                return false;
            }
        }
    });
    
    // Prevent right-click menu
    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
        return false;
    });
    
    // Detect when the page is not visible (tab switching)
    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'hidden') {
            // Optionally implement behavior for when the user switches tabs
            console.log('Tab switching detected');
        }
    });
    
    // Timer and assessment functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Variables
        const questions = document.querySelectorAll('.question');
        const progressBar = document.getElementById('progressBar');
        const timerDisplay = document.getElementById('timer');
        const loadingOverlay = document.getElementById('loadingOverlay');
        const form = document.getElementById('assessmentForm');
        
        // Set initial progress bar
        updateProgressBar(1, questions.length);
        
        // Navigation buttons functionality
        const prevButtons = document.querySelectorAll('.btn-prev');
        const nextButtons = document.querySelectorAll('.btn-next');
        
        // Current question index
        let currentIndex = 0;
        
        // Add event listeners to navigation buttons
        prevButtons.forEach(button => {
            button.addEventListener('click', () => {
                if (currentIndex > 0) {
                    goToQuestion(currentIndex - 1);
                }
            });
        });
        
        nextButtons.forEach(button => {
            button.addEventListener('click', () => {
                if (currentIndex < questions.length - 1) {
                    goToQuestion(currentIndex + 1);
                }
            });
        });
        
        function goToQuestion(index) {
            // Hide current question
            questions[currentIndex].classList.remove('active');
            
            // Show new question
            questions[index].classList.add('active');
            
            // Update current index
            currentIndex = index;
            
            // Update progress bar
            updateProgressBar(index + 1, questions.length);
            
            // Scroll to top of question container
            document.querySelector('.question-container').scrollIntoView({ behavior: 'smooth' });
        }
        
        function updateProgressBar(current, total) {
            const percentage = (current / total) * 100;
            progressBar.style.width = `${percentage}%`;
        }
        
        // Function to collect all answers
        function getFormAnswers() {
            const formData = new FormData(form);
            const answers = {};
            
            for (const [key, value] of formData.entries()) {
                if (key.startsWith('answers[')) {
                    // Extract question ID from the key (answers[123] -> 123)
                    const questionId = key.match(/\[(\d+)\]/)[1];
                    answers[questionId] = value;
                }
            }
            
            return answers;
        }
        
        // Function to submit assessment
        window.submitAssessment = function() {
            if (!confirm('Are you sure you want to submit your assessment? You cannot make changes after submission.')) {
                return false;
            }
            
            showLoadingOverlay();
            form.submit();
        }
        
        // Function to auto-submit assessment when timer expires
        function autoSubmitAssessment() {
            showLoadingOverlay();
            
            const answers = getFormAnswers();
            
            // Send AJAX request to submit
            fetch('{{ route("assessment.auto-submit") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    assessment_id: assessmentId,
                    token: assessmentToken,
                    answers: answers
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show result and redirect
                    alert(data.message);
                    window.location.href = data.redirect;
                } else {
                    alert('There was an error submitting your assessment. Please try again.');
                    hideLoadingOverlay();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('There was an error submitting your assessment. Please try again.');
                hideLoadingOverlay();
            });
        }
        
        // Show loading overlay
        function showLoadingOverlay() {
            loadingOverlay.classList.add('active');
        }
        
        // Hide loading overlay
        function hideLoadingOverlay() {
            loadingOverlay.classList.remove('active');
        }
        
        // Timer functionality
        let timeLeft = 17 * 60; // 17 minutes in seconds
        const assessmentId = document.querySelector('input[name="assessment_id"]').value;
        const assessmentToken = document.querySelector('input[name="token"]').value;
        
        const timerInterval = setInterval(() => {
            timeLeft--;
            
            // Format time as MM:SS
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerDisplay.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            
            // Add warning class when less than 2 minutes left
            if (timeLeft <= 120) {
                timerDisplay.classList.add('danger');
            }
            
            // Auto-submit when timer expires
            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                autoSubmitAssessment();
            }
        }, 1000);
        
        </script>
        </