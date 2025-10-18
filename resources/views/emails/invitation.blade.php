<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background-color: #f9fafb;
            border-radius: 8px;
            padding: 32px;
            margin: 20px 0;
        }
        h1 {
            color: #111827;
            font-size: 24px;
            margin-bottom: 16px;
        }
        p {
            color: #6b7280;
            margin-bottom: 16px;
        }
        .button {
            display: inline-block;
            background-color: #10b981;
            color: white !important;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 20px 0;
        }
        .button:hover {
            background-color: #059669;
        }
        .footer {
            margin-top: 32px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 14px;
            color: #9ca3af;
        }
        .expires {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 12px;
            margin: 20px 0;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>You've been invited!</h1>

        <p>Hello!</p>

        <p>You've been invited to join <strong>{{ config('app.name') }}</strong>. Click the button below to create your account:</p>

        <a href="{{ $inviteUrl }}" class="button">Accept Invitation</a>

        <div class="expires">
            <strong>Note:</strong> This invitation expires on {{ $expiresAt }}.
        </div>

        <p>If the button doesn't work, you can copy and paste this link into your browser:</p>
        <p style="word-break: break-all; color: #6366f1;">{{ $inviteUrl }}</p>

        <div class="footer">
            <p>If you didn't expect this invitation, you can safely ignore this email.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
