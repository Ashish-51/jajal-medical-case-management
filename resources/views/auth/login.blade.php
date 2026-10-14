<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Sign In - Jajal Medical Case Management</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="h-full flex items-center justify-center p-4 antialiased selection:bg-teal-500 selection:text-white bg-slate-900">
    <div class="w-full max-w-md">
        <!-- Logo and Brand -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-teal-600 text-white shadow-xl shadow-teal-500/20 mb-4">
                <i data-lucide="activity" class="w-8 h-8"></i>
            </div>
            <h1 class="text-2xl font-bold text-white tracking-tight">JAJAL MEDICAL</h1>
            <p class="text-sm text-slate-400 mt-1">Implant Case Management Portal</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200/80 p-8 sm:p-10">
            <div class="mb-6">
                <h2 class="text-xl font-bold text-slate-900">Sign in to your account</h2>
                <p class="text-xs text-slate-500 mt-1">Access the administrative case tracking portal</p>
            </div>

            @if(session('status'))
                <div class="mb-5 p-3.5 rounded-lg bg-teal-50 border border-teal-200 text-teal-800 text-xs font-medium flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4 text-teal-600"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-5 p-3.5 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium">
                    <div class="flex items-center gap-2 mb-1">
                        <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-600 shrink-0"></i>
                        <span class="font-semibold">Authentication failed</span>
                    </div>
                    <ul class="list-disc list-inside space-y-0.5 text-rose-700">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" id="login-form" class="space-y-4">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Email Address
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                            <i data-lucide="mail" class="w-4 h-4"></i>
                        </span>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               value="{{ old('email') }}"
                               required 
                               autofocus 
                               autocomplete="username"
                               placeholder="user@jajalmedical.com"
                               class="w-full pl-10 pr-4 py-2.5 rounded-lg border {{ $errors->has('email') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }} text-slate-900 placeholder:text-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all">
                    </div>
                    @error('email')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Password
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </span>
                        <input type="password" 
                               name="password" 
                               id="password" 
                               required 
                               autocomplete="current-password"
                               placeholder="••••••••••••"
                               class="w-full pl-10 pr-11 py-2.5 rounded-lg border {{ $errors->has('password') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }} text-slate-900 placeholder:text-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all">
                        <button type="button" 
                                id="toggle-password" 
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none">
                            <i data-lucide="eye" id="eye-icon" class="w-4 h-4"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" 
                               name="remember" 
                               class="w-4 h-4 rounded text-teal-600 border-slate-300 focus:ring-teal-500">
                        <span class="text-xs text-slate-600">Remember this device</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" 
                            id="submit-btn"
                            class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-lg bg-teal-600 hover:bg-teal-500 text-white font-semibold text-sm shadow-md shadow-teal-500/20 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition-all">
                        <span>Sign In</span>
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </button>
                </div>
            </form>

            <!-- Quick Credentials Helper Box -->
            <div class="mt-6 pt-5 border-t border-slate-100 text-xs text-slate-500">
                <span class="font-semibold text-slate-700 block mb-1">Demo Evaluation Accounts:</span>
                <div class="grid grid-cols-2 gap-2 mt-2">
                    <button type="button" onclick="fillCreds('admin@jajalmedical.com', 'AdminPass123!')" class="p-2 text-left rounded bg-slate-50 hover:bg-teal-50 border border-slate-200 hover:border-teal-300 transition-colors">
                        <span class="font-bold text-slate-800 block">Admin</span>
                        <span class="text-[11px] text-slate-500 block truncate">admin@jajalmedical.com</span>
                    </button>
                    <button type="button" onclick="fillCreds('viewer@jajalmedical.com', 'ViewerPass123!')" class="p-2 text-left rounded bg-slate-50 hover:bg-teal-50 border border-slate-200 hover:border-teal-300 transition-colors">
                        <span class="font-bold text-slate-800 block">Viewer</span>
                        <span class="text-[11px] text-slate-500 block truncate">viewer@jajalmedical.com</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.lucide) {
                window.lucide.createIcons();
            }

            // Show / Hide Password toggle
            const toggleBtn = document.getElementById('toggle-password');
            const passwordInput = document.getElementById('password');

            if (toggleBtn && passwordInput) {
                toggleBtn.addEventListener('click', function () {
                    const isPassword = passwordInput.getAttribute('type') === 'password';
                    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                    
                    const eyeIcon = document.getElementById('eye-icon');
                    if (eyeIcon) {
                        eyeIcon.setAttribute('data-lucide', isPassword ? 'eye-off' : 'eye');
                        if (window.lucide) {
                            window.lucide.createIcons();
                        }
                    }
                });
            }
        });

        function fillCreds(email, pass) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = pass;
        }
    </script>
</body>
</html>
