@extends('writers.others.assessment')
@section('content')
<style>
    /* Prevent text selection, copying, and right-clicking */
    body {
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -khtml-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        overflow-x: hidden;
    }
    
    /* Main container styles */
    .assessment-app {
        min-height: 100vh;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        padding: 1rem;
        position: relative;
    }
    
    /* Timer styles */
    .timer-container {
        position: fixed;
        top: 1rem;
        right: 1rem; 
        background: white;
        padding: 0.75rem 1.25rem;
        border-radius: 9999px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        display: flex;
        align-items: center;
        gap: 0.5rem;
        z-index: 50;
    }
    
    @media (max-width: 640px) {
        .timer-container {
            top: auto;
            bottom: 1rem;
            right: 50%;
            transform: translateX(50%);
        }
    }
    
    .timer {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1a202c;
        font-variant-numeric: tabular-nums;
        font-feature-settings: "tnum";
    }
    
    .timer.danger {
        color: #e53e3e;
        animation: pulse 1s infinite;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    
    /* Main card styles */
    .assessment-container {
        max-width: 800px;
        margin: 2rem auto;
        background: white;
        border-radius: 1rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    
    @media (max-width: 640px) {
        .assessment-container {
            margin: 0 auto 5rem auto;
            border-radius: 0.75rem;
        }
    }
    
    /* Header styles */
    .assessment-header {
        background: linear-gradient(120deg, #4299e1, #3182ce);
        color: white;
        padding: 2rem;
        position: relative;
    }
    
    @media (max-width: 640px) {
        .assessment-header {
            padding: 1.5rem;
        }
    }
    
    .assessment-header h1 {
        font-size: 1.875rem;
        font-weight: 700;
        margin-bottom: 0.75rem;
    }
    
    @media (max-width: 640px) {
        .assessment-header h1 {
            font-size: 1.5rem;
        }
    }
    
    .assessment-header p {
        font-size: 1rem;
        line-height: 1.5;
        opacity: 0.9;
    }
    
    /* Progress bar */
    .progress-container {
        height: 0.5rem;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 0.25rem;
        margin-top: 1.5rem;
        overflow: hidden;
    }
    
    .progress-bar {
        height: 100%;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 0.25rem;
        transition: width 0.3s ease;
    }
    
    /* Question styles */
    .question-container {
        padding: 2rem;
    }
    
    @media (max-width: 640px) {
        .question-container {
            padding: 1.5rem;
        }
    }
    
    .question {
        display: none;
        animation: fadeIn 0.5s ease forwards;
    }
    
    .question.active {
        display: block;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .question-number {
        display: flex;
        align-items: center;
        font-size: 0.875rem;
        color: #718096;
        margin-bottom: 0.75rem;
    }
    
    .question-number svg {
        margin-right: 0.375rem;
    }
    
    .question-text {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1a202c;
        margin-bottom: 1.5rem;
        line-height: 1.6;
    }
    
    @media (max-width: 640px) {
        .question-text {
            font-size: 1.125rem;
        }
    }
    
    /* Options styles */
    .options-container {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
    
    .option-label {
        padding: 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 0.5rem;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 1rem;
    }
    
    .option-label:hover {
        border-color: #4299e1;
        background-color: rgba(66, 153, 225, 0.05);
    }
    
    input[type="radio"] {
        display: none;
    }
    
    input[type="radio"]:checked + .option-label {
        border-color: #4299e1;
        background-color: rgba(66, 153, 225, 0.1);
    }
    
    /* Text input styles */
    .text-input {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 0.5rem;
        font-size: 1rem;
        transition: border-color 0.2s ease;
    }
    
    .text-input:focus {
        outline: none;
        border-color: #4299e1;
        box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
    }
    
    /* Navigation buttons */
    .nav-buttons {
        display: flex;
        justify-content: space-between;
        margin-top: 2rem;
    }
    
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        cursor: pointer;
        border: none;
    }
    
    @media (max-width: 640px) {
        .btn {
            padding: 0.625rem 1rem;
        }
    }
    
    .btn-prev {
        background-color: #edf2f7;
        color: #4a5568;
    }
    
    .btn-prev:hover {
        background-color: #e2e8f0;
    }
    
    .btn-next {
        background-color: #4299e1;
        color: white;
    }
    
    .btn-next:hover {
        background-color: #3182ce;
    }
    
    .btn-submit {
        background-color: #48bb78;
        color: white;
    }
    
    .btn-submit:hover {
        background-color: #38a169;
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
        border: 3px solid #f3f3f3;
        border-top: 3px solid #3182ce;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
        margin-bottom: 1rem;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 3rem 1.5rem;
    }
    
    .empty-state svg {
        height: 4rem;
        width: 4rem;
        color: #a0aec0;
        margin-bottom: 1rem;
        display: inline-block;
    }
    
    .empty-state-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0.75rem;
    }
    
    .empty-state-message {
        color: #718096;
        margin-bottom: 1.5rem;
        max-width: 24rem;
        margin-left: auto;
        margin-right: auto;
    }
    
    /* Start button */
    .start-btn {
        display: inline-flex;
        align-items: center;
        background-color: #4299e1;
        color: white;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        transition: background-color 0.2s;
        text-decoration: none;
    }
    
    .start-btn:hover {
        background-color: #3182ce;
    }
    
    .start-btn svg {
        height: 1.25rem;
        width: 1.25rem;
        margin-right: 0.5rem;
        color: white;
    }
    
    /* Warning box for user leaving page */
    .leave-warning {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.8);
        z-index: 9999;
        display: none;
        justify-content: center;
        align-items: center;
        padding: 1rem;
    }
    
    .leave-warning-content {
        background-color: white;
        border-radius: 0.5rem;
        padding: 2rem;
        max-width: 32rem;
        text-align: center;
    }
    
    .leave-warning-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #e53e3e;
        margin-bottom: 1rem;
    }
    
    .leave-warning-icon {
        color: #e53e3e;
        font-size: 3rem;
        margin-bottom: 1rem;
    }
    
    .leave-warning-text {
        margin-bottom: 1.5rem;
        color: #4a5568;
    }
    
    .leave-warning-buttons {
        display: flex;
        justify-content: center;
        gap: 1rem;
    }
</style>
<div class="assessment-app">
    @if(isset($assessment) && isset($questions) && $questions->count() > 0)
        <div class="timer-container">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-clock"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            <div class="timer" id="timer">17:00</div>
        </div>
        
        <div class="assessment-container">
            <div class="assessment-header">
                <h1>Grammar Assessment</h1>
                <p>Complete 20 grammar questions within 17 minutes. You need to score at least 80% to pass.</p>
                <div class="progress-container">
                    <div class="progress-bar" id="progressBar" style="width: 5%;"></div>
                </div>
            </div>
            
            <div class="question-container">
                <form id="assessmentForm" method="POST" action="{{ route('assessment.submit') }}">
                    @csrf
                    <input type="hidden" name="assessment_id" value="{{ $assessment->id }}">
                    <input type="hidden" name="token" value="{{ $assessment->token }}">
                    
                    @foreach($questions as $index => $question)
                        <div class="question {{ $index === 0 ? 'active' : '' }}" data-question="{{ $index + 1 }}">
                            <div class="question-number">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                                Question {{ $index + 1 }} of {{ $questions->count() }}
                            </div>
                            
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
                                <input type="text" class="text-input" 
                                    name="answers[{{ $question->id }}]" 
                                    placeholder="Type your answer here..." 
                                    autocomplete="off">
                            @endif
                            
                            <div class="nav-buttons">
                                <button type="button" class="btn btn-prev" {{ $index === 0 ? 'disabled' : '' }}>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                                    Previous
                                </button>
                                
                                @if($index === $questions->count() - 1)
                                    <button type="button" class="btn btn-submit" onclick="submitAssessment()">
                                        Submit
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    </button>
                                @else
                                    <button type="button" class="btn btn-next">
                                        Next
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </form>
            </div>
        </div>
        
        <!-- Loading overlay -->
        <div class="loading-overlay" id="loadingOverlay">
            <div class="spinner"></div>
            <div class="text-lg font-medium text-gray-700 mt-4">Submitting your assessment...</div>
        </div>
        
        <!-- Leave page warning -->
        <div class="leave-warning" id="leaveWarning">
            <div class="leave-warning-content">
                <div class="leave-warning-icon">⚠️</div>
                <h3 class="leave-warning-title">Warning! Don't Leave</h3>
                <p class="leave-warning-text">
                    Leaving this page will result in automatic failure. All your progress will be lost.
                </p>
                <div class="leave-warning-buttons">
                    <button class="btn btn-submit" id="stayButton">
                        Stay on this page
                    </button>
                </div>
            </div>
        </div>
    @else
        <div class="assessment-container">
            <div class="assessment-header">
                <h1>Writer Assessment</h1>
                <p>Complete a grammar assessment to verify your writing skills and start working on our platform.</p>
            </div>
            
            <div class="empty-state">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                
                <h3 class="empty-state-title">Ready to get started?</h3>
                <p class="empty-state-message">
                    You need to complete a grammar assessment to verify your skills. 
                    The assessment has 20 questions and must be completed within 17 minutes.
                </p>
                
                <a href="{{ route('assessment.grammar') }}" class="start-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    Start Assessment
                </a>
            </div>
        </div>
    @endif
</div>

<script>
    @if(isset($assessment) && isset($questions) && $questions->count() > 0)
        // Prevent text selection, copying, and right-clicking
        document.addEventListener('contextmenu', e => e.preventDefault());
        document.addEventListener('selectstart', e => e.preventDefault());
        document.addEventListener('copy', e => e.preventDefault());
        document.addEventListener('cut', e => e.preventDefault());
        document.addEventListener('paste', e => e.preventDefault());
        
        // Block keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Prevent F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+U (developer tools)
            if (
                e.key === 'F12' || 
                (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'J')) || 
                (e.ctrlKey && e.key === 'U')
            ) {
                e.preventDefault();
                return false;
            }
            
            // Prevent Ctrl+C, Ctrl+X (copy, cut)
            if ((e.ctrlKey || e.metaKey) && (e.key === 'c' || e.key === 'x' || e.key === 'C' || e.key === 'X')) {
                e.preventDefault();
                return false;
            }
            
            // Prevent Ctrl+P (print)
            if ((e.ctrlKey || e.metaKey) && (e.key === 'p' || e.key === 'P')) {
                e.preventDefault();
                return false;
            }
            
            // Prevent screen capture shortcuts
            if (
                (e.ctrlKey && e.shiftKey && (e.key === '3' || e.key === '4')) ||  // Mac
                (e.metaKey && e.shiftKey && (e.key === '3' || e.key === '4')) ||  // Mac
                (e.key === 'PrintScreen') ||  // Windows
                (e.altKey && e.key === 'PrintScreen')  // Windows
            ) {
                e.preventDefault();
                return false;
            }
        });
        
        // Variables
        const questions = document.querySelectorAll('.question');
        const progressBar = document.getElementById('progressBar');
        const timerDisplay = document.getElementById('timer');
        const loadingOverlay = document.getElementById('loadingOverlay');
        const form = document.getElementById('assessmentForm');
        const leaveWarning = document.getElementById('leaveWarning');
        const stayButton = document.getElementById('stayButton');
        
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
        
        // Stay button event listener
        stayButton.addEventListener('click', () => {
            leaveWarning.style.display = 'none';
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
            
            // Verify all questions have answers
            const answers = getFormAnswers();
            const answeredCount = Object.keys(answers).length;
            
            if (answeredCount < questions.length) {
                if (!confirm(`You've only answered ${answeredCount} of ${questions.length} questions. Are you sure you want to submit?`)) {
                    hideLoadingOverlay();
                    return false;
                }
            }
            
            form.submit();
        }
        
        // Function to auto-submit assessment when timer expires
        function autoSubmitAssessment() {
            showLoadingOverlay();
            
            const answers = getFormAnswers();
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Send AJAX request to submit
            fetch('{{ route("assessment.auto-submit") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    assessment_id: assessmentId,
                    token: assessmentToken,
                    answers: answers
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Show result and redirect
                    alert(data.message);
                    window.location.href = data.redirect;
                } else {
                    alert('There was an error submitting your assessment: ' + data.message);
                    hideLoadingOverlay();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('There was an error submitting your assessment. Please try again.');
                hideLoadingOverlay();
                
                // Fallback to form submission if AJAX fails
                form.submit();
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
        
        // Before unload warning
        window.addEventListener('beforeunload', function(e) {
            // If form is being submitted via submit button, don't show warning
            if (loadingOverlay.classList.contains('active')) {
                return undefined;
            }
            
            // Cancel the event and show confirmation dialog
            e.preventDefault();
            // Chrome requires returnValue to be set
            e.returnValue = '';
            
            // Show custom warning
            leaveWarning.style.display = 'flex';
            
            return "Leaving this page will result in automatic failure. Are you sure?";
        });
        
        // Detect when the page is not visible (tab switching)
        document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'hidden') {
                // Show warning when user returns
                document.addEventListener('visibilitychange', function onReturn() {
                    if (document.visibilityState === 'visible') {
                        leaveWarning.style.display = 'flex';
                        document.removeEventListener('visibilitychange', onReturn);
                    }
                });
            }
        });
        
        // Detect iframes and prevent embedding
        if (window.self !== window.top) {
            window.top.location.href = window.self.location.href;
        }
        
        // Detect browser developer tools
        const devToolsDetector = {
            isOpen: false,
            orientation: undefined
        };
        
        const threshold = 160;
        
        // Check for changes in window dimensions
        const checkDevTools = () => {
            const widthThreshold = window.outerWidth - window.innerWidth > threshold;
            const heightThreshold = window.outerHeight - window.innerHeight > threshold;
            
            if (widthThreshold || heightThreshold) {
                if (!devToolsDetector.isOpen) {
                    // Dev tools likely opened
                    leaveWarning.style.display = 'flex';
                }
                devToolsDetector.isOpen = true;
            } else {
                devToolsDetector.isOpen = false;
            }
        };
        
        window.addEventListener('resize', checkDevTools);
        setInterval(checkDevTools, 1000);
    @endif
    </script>


@endsection