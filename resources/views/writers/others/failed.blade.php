@extends('writers.others.assessment')

@section('content')

<style>
    .failed-container {
        max-width: 800px;
        margin: 4rem auto;
        background: white;
        border-radius: 1rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    
    .failed-header {
        background: linear-gradient(to right, #e74c3c, #c0392b);
        color: white;
        padding: 2rem;
        position: relative;
    }
    
    .failed-header h1 {
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    
    .failed-header p {
        opacity: 0.9;
        font-size: 1rem;
        line-height: 1.5;
    }
    
    .failed-content {
        padding: 2rem;
    }
    
    .score-card {
        background: #f8f9fa;
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-bottom: 2rem;
        text-align: center;
    }
    
    .score-value {
        font-size: 3rem;
        font-weight: 700;
        color: #e74c3c;
        margin-bottom: 0.5rem;
    }
    
    .score-label {
        color: #7f8c8d;
        font-size: 0.9rem;
    }
    
    .retry-timer {
        background: #f8f9fa;
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-bottom: 2rem;
        text-align: center;
    }
    
    .timer-value {
        font-size: 2rem;
        font-weight: 700;
        color: #3498db;
        margin-bottom: 0.5rem;
    }
    
    .info-card {
        border-left: 4px solid #3498db;
        padding: 1rem;
        background: #f8f9fa;
        margin-bottom: 1.5rem;
    }
    
    .info-title {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }
    
    .btn {
        display: inline-block;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
        text-decoration: none;
    }
    
    .btn-primary {
        background-color: #3498db;
        color: white;
    }
    
    .btn-primary:hover {
        background-color: #2980b9;
    }
    
    .resources-list {
        list-style-type: none;
        padding: 0;
        margin: 1.5rem 0;
    }
    
    .resources-list li {
        padding: 0.75rem 0;
        border-bottom: 1px solid #eee;
    }
    
    .resources-list li a {
        color: #3498db;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .resources-list li a:hover {
        color: #2980b9;
    }
    
    @media (max-width: 768px) {
        .failed-container {
            margin: 2rem 1rem;
            border-radius: 0.75rem;
        }
        
        .failed-header {
            padding: 1.5rem;
        }
        
        .failed-content {
            padding: 1.5rem;
        }
    }
</style>

<div class="failed-container">
    <div class="failed-header">
        <h1>Assessment Not Passed</h1>
        <p>Unfortunately, you did not achieve the required score to proceed. Don't worry, you can try again after the waiting period.</p>
    </div>
    
    <div class="failed-content">
        <div class="score-card">
            <div class="score-value">{{ round($result->percentage) }}%</div>
            <div class="score-label">YOUR SCORE (80% REQUIRED TO PASS)</div>
        </div>
        
        <div class="retry-timer">
            <div class="timer-value" id="retryTimer">7 days</div>
            <div class="score-label">UNTIL YOU CAN RETRY</div>
        </div>
        
        <div class="info-card">
            <div class="info-title">Why this assessment?</div>
            <p>We require all writers to pass this grammar assessment to ensure high-quality content for our clients. This helps maintain our platform's standards and reputation.</p>
        </div>
        
        <div class="info-card">
            <div class="info-title">Preparation Resources</div>
            <p>Use these resources to improve your grammar skills before your next attempt:</p>
            <ul class="resources-list">
                <li>
                    <a href="https://owl.purdue.edu/owl/general_writing/grammar/" target="_blank">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-external-link"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                        Purdue Online Writing Lab
                    </a>
                </li>
                <li>
                    <a href="https://www.grammarly.com/blog/category/handbook/" target="_blank">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-external-link"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                        Grammarly Handbook
                    </a>
                </li>
                <li>
                    <a href="https://learnenglish.britishcouncil.org/grammar" target="_blank">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-external-link"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                        British Council Grammar Resources
                    </a>
                </li>
            </ul>
        </div>
        
        <div class="text-center mt-8">
            <a href="" class="btn btn-primary">Thanks {{ auth()->user()->name }}</a>
        </div>
    </div
</div>

<script>
    // Calculate days until retry is available
    const lastAttemptDate = new Date('{{ $result->created_at }}');
    const retryDate = new Date(lastAttemptDate);
    retryDate.setDate(retryDate.getDate() + 7);
    
    function updateRetryTimer() {
        const now = new Date();
        const diff = retryDate - now;
        
        // If time has elapsed
        if (diff <= 0) {
            document.getElementById('retryTimer').textContent = 'Available Now';
            return;
        }
        
        // Calculate days, hours, minutes
        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        
        let timerText = '';
        if (days > 0) {
            timerText = `${days} day${days !== 1 ? 's' : ''}`;
            if (hours > 0) timerText += `, ${hours} hr${hours !== 1 ? 's' : ''}`;
        } else if (hours > 0) {
            timerText = `${hours} hr${hours !== 1 ? 's' : ''}, ${minutes} min${minutes !== 1 ? 's' : ''}`;
        } else {
            timerText = `${minutes} minute${minutes !== 1 ? 's' : ''}`;
        }
        
        document.getElementById('retryTimer').textContent = timerText;
    }
    
    // Update timer immediately and then every minute
    updateRetryTimer();
    setInterval(updateRetryTimer, 60000);
</script>
@endsection