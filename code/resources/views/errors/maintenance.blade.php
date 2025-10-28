<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('app.maintenance_mode') }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .maintenance-container {
            max-width: 600px;
            padding: 40px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .maintenance-icon {
            font-size: 100px;
            color: #667eea;
            margin-bottom: 30px;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
        }

        h1 {
            color: #333;
            font-size: 2.5rem;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .maintenance-message {
            color: #666;
            font-size: 1.1rem;
            line-height: 1.8;
            margin-bottom: 30px;
        }

        .back-link {
            display: inline-block;
            padding: 12px 30px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 50px;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .back-link:hover {
            background: #764ba2;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .admin-notice {
            margin-top: 20px;
            padding: 15px;
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 10px;
            color: #856404;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="maintenance-container">
        <div class="maintenance-icon">
            <i class="bi bi-tools"></i>
        </div>

        <h1>{{ __('app.system_maintenance') }}</h1>

        <div class="maintenance-message">
            <p>{{ $message ?? __('app.default_maintenance_message') }}</p>
        </div>

        <p class="text-muted">
            <i class="bi bi-clock"></i>
            {{ __('app.maintenance_expected_back_soon') }}
        </p>

        <div class="mt-4">
            <a href="{{ route('login') }}" class="back-link">
                <i class="bi bi-arrow-left"></i> {{ __('app.back_to_login') }}
            </a>
        </div>

        @if(auth()->check() && auth()->user()->role === 'admin')
            <div class="admin-notice">
                <i class="bi bi-shield-check"></i>
                <strong>{{ __('app.admin_notice') }}:</strong>
                {{ __('app.admin_can_access_during_maintenance') }}
                <br>
                <a href="{{ route('admin.settings') }}" class="text-decoration-none">
                    {{ __('app.manage_maintenance_mode') }}
                </a>
            </div>
        @endif
    </div>
</body>
</html>
