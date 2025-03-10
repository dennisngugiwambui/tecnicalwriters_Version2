<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title') - Writer Assessment</title>
    
    <!-- Meta tags to prevent caching -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/jpeg" href="writers/technicalwriters2.jpg">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }
        
        /* Disable right-click menu */
        body {
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
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
    
    @yield('styles')
</head>
<body>
    <div id="app">
        @yield('content')
    </div>
    
    <script>
        // Block browser's ability to print
        window.addEventListener('keydown', function(e) {
            // Detect Control+P or Command+P (print)
            if ((e.ctrlKey || e.metaKey) && e.keyCode === 80) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
            
            // Detect F12, right-click, and other ways to access dev tools
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
            
            // Detect screen capture shortcuts (may vary by OS)
            // Windows: Win+Shift+S, Alt+PrtScn
            // Mac: Cmd+Shift+3, Cmd+Shift+4
            if (
                (e.shiftKey && (e.keyCode === 51 || e.keyCode === 52) && (e.ctrlKey || e.metaKey)) ||  // Cmd+Shift+3/4
                (e.shiftKey && e.keyCode === 83 && e.metaKey)  // Win+Shift+S
            ) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        });
        
        // Prevent right-click menu
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            return false;
        });
        
        // Detect when the page is not visible (tab switching)
        let warningShown = false;
        document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'hidden' && !warningShown) {
                warningShown = true;
                setTimeout(function() {
                    alert("Warning: Leaving the assessment page may result in automatic failure. Please stay on this page until you complete the assessment.");
                    warningShown = false;
                }, 500);
            }
        });
        
        // Detect devtools (may not work in all browsers)
        const devtools = {
            isOpen: false,
            orientation: undefined
        };
        
        // Check for changes in size or position
        const threshold = 160;
        const emitEvent = (isOpen, orientation) => {
            window.dispatchEvent(new CustomEvent('devtoolschange', {
                detail: {
                    isOpen,
                    orientation
                }
            }));
        };
        
        // Function to check if devtools is open
        const checkDevTools = () => {
            const widthThreshold = window.outerWidth - window.innerWidth > threshold;
            const heightThreshold = window.outerHeight - window.innerHeight > threshold;
            const orientation = widthThreshold ? 'vertical' : 'horizontal';
            
            if (
                !(heightThreshold && widthThreshold) &&
                ((window.Firebug && window.Firebug.chrome && window.Firebug.chrome.isInitialized) || widthThreshold || heightThreshold)
            ) {
                if (!devtools.isOpen || devtools.orientation !== orientation) {
                    emitEvent(true, orientation);
                }
                
                devtools.isOpen = true;
                devtools.orientation = orientation;
            } else {
                if (devtools.isOpen) {
                    emitEvent(false, undefined);
                }
                
                devtools.isOpen = false;
                devtools.orientation = undefined;
            }
        };
        
        window.addEventListener('resize', checkDevTools);
        setInterval(checkDevTools, 1000);
        
        // Listen for devtools change event
        window.addEventListener('devtoolschange', function(e) {
            if (e.detail.isOpen) {
                console.log('%cStop!', 'color: red; font-size: 30px; font-weight: bold;');
                console.log('%cThis is a secure assessment environment. Using developer tools is not allowed.', 'font-size: 16px;');
            }
        });
    </script>

            <!-- Prevent back navigation during assessment -->
        <script type="text/javascript">
            window.history.pushState(null, "", window.location.href);
            window.onpopstate = function() {
                window.history.pushState(null, "", window.location.href);
            };

            // Prevent leaving the page accidentally during assessment
        window.addEventListener('beforeunload', function(e) {
            // If form is being submitted, don't show warning
            if (document.getElementById('loadingOverlay').classList.contains('active')) {
                return undefined;
            }
            
            // Show warning when trying to leave
            e.preventDefault();
            e.returnValue = 'If you leave this page, your assessment will be automatically failed. Are you sure?';
            
            return e.returnValue;
        });
        </script>



    
    @yield('scripts')
</body>
</html>