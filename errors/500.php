<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Error - EWU Lost & Found</title>
    <link rel="stylesheet" href="/EWU-LostFound/assets/css/style.css">
    <style>
        .error-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 2rem;
        }
        .error-content {
            max-width: 500px;
        }
        .error-code {
            font-size: 8rem;
            font-weight: 700;
            color: var(--status-lost-bg);
            line-height: 1;
            margin-bottom: 1rem;
            opacity: 0.15;
        }
        .error-title {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text-dark);
        }
        .error-message {
            color: var(--text-secondary);
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        .error-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        .error-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="error-page">
        <div class="error-content">
            <div class="error-code">500</div>
            <div class="error-icon">⚠️</div>
            <h1 class="error-title">Something Went Wrong</h1>
            <p class="error-message">
                We're experiencing some technical difficulties. 
                Our team has been notified and is working to fix the issue. 
                Please try again in a few moments.
            </p>
            <div class="error-actions">
                <a href="/EWU-LostFound/" class="btn-pill">Go Home</a>
                <a href="javascript:location.reload()" class="btn-pill btn-outline">Try Again</a>
            </div>
        </div>
    </div>
</body>
</html>
