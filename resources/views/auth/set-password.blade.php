<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Your Password</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f9fafb 0%, #eef2ff 100%);
        }

        .card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease-in-out;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.07);
        }

        .btn-primary {
            background: linear-gradient(90deg, #2563eb, #1d4ed8);
            color: white;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.2s ease-in-out;
        }

        .btn-primary:hover {
            background: linear-gradient(90deg, #1d4ed8, #1e3a8a);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.25);
        }

        .input-field {
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            width: 100%;
            transition: all 0.2s;
        }

        .input-field:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.2);
            outline: none;
        }

        .title {
            font-size: 1.875rem;
            font-weight: 700;
            color: #111827;
        }

        .subtitle {
            color: #6b7280;
            font-size: 0.95rem;
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen px-4">
    <div class="card w-full max-w-md p-8">
        <div class="text-center mb-6">
            <img src="{{ asset('logo2.png') }}" alt="Logo" class="mx-auto h-8 mb-4">
            <h1 class="title mb-2">Create a Password</h1>
            <p class="subtitle">Secure your account by setting a strong password.</p>
        </div>

        <form method="POST" action="{{ route('user.set-password.update', $user->id) }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                <input
                    type="password"
                    name="password"
                    class="input-field"
                    placeholder="Enter new password"
                    required
                >
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                <input
                    type="password"
                    name="password_confirmation"
                    class="input-field"
                    placeholder="Confirm password"
                    required
                >
            </div>

            <button type="submit" class="btn-primary w-full py-2.5">
                Set Password
            </button>
        </form>

    </div>
</body>
</html>
