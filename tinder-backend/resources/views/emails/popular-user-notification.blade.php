<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Popular User Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .content {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }
        .user-info {
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .footer {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
        }
        .highlight {
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🚨 Popular User Alert</h1>
    </div>

    <div class="content">
        <p>Hello Admin,</p>

        <p>A user has become <strong class="highlight">popular</strong> on our platform!</p>

        <div class="user-info">
            <h3>User Details:</h3>
            <ul>
                <li><strong>Name:</strong> {{ $user->name }}</li>
                <li><strong>Email:</strong> {{ $user->email }}</li>
                <li><strong>Age:</strong> {{ $user->age }}</li>
                <li><strong>User ID:</strong> {{ $user->id }}</li>
            </ul>
        </div>

        <p><strong>Popularity Stats:</strong></p>
        <p>This user has received <strong class="highlight">{{ $user->likes_count }} likes</strong>, which exceeds our threshold of 50 likes.</p>

        <p>This notification was triggered because the user reached the popularity milestone and hadn't been flagged before.</p>

        <p>You may want to:</p>
        <ul>
            <li>Review the user's profile for any special attention needed</li>
            <li>Consider featuring this user in recommendations</li>
            <li>Check if this indicates any trending patterns</li>
        </ul>

        <p>Best regards,<br>Your Tinder-like App System</p>
    </div>

    <div class="footer">
        <p>This is an automated notification from your application.</p>
        <p>Notification sent at: {{ now()->format('Y-m-d H:i:s T') }}</p>
    </div>
</body>
</html>
