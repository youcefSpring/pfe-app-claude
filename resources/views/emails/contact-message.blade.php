<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact Form Message</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: #007bff;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 30px;
        }
        .field {
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }
        .field:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        .label {
            font-weight: bold;
            color: #555;
            margin-bottom: 5px;
        }
        .value {
            color: #333;
        }
        .message-text {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #007bff;
        }
        .subject-type {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 12px;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>New Contact Form Message</h1>
        </div>

        <div class="content">
            <div class="field">
                <div class="label">Name:</div>
                <div class="value">{{ $contactData['name'] }}</div>
            </div>

            <div class="field">
                <div class="label">Email:</div>
                <div class="value">
                    <a href="mailto:{{ $contactData['email'] }}">{{ $contactData['email'] }}</a>
                </div>
            </div>

            @if(!empty($contactData['phone']))
            <div class="field">
                <div class="label">Phone:</div>
                <div class="value">{{ $contactData['phone'] }}</div>
            </div>
            @endif

            <div class="field">
                <div class="label">Subject Type:</div>
                <div class="value">
                    <span class="subject-type">{{ ucwords(str_replace('_', ' ', $contactData['subject_type'])) }}</span>
                </div>
            </div>

            <div class="field">
                <div class="label">Subject:</div>
                <div class="value"><strong>{{ $contactData['subject'] }}</strong></div>
            </div>

            <div class="field">
                <div class="label">Message:</div>
                <div class="value">
                    <div class="message-text">
                        {!! nl2br(e($contactData['message'])) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>