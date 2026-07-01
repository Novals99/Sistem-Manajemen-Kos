<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register — {{ config('app.name', 'KosManager') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-surface flex items-center justify-center p-4"
      x-data="{ theme: localStorage.getItem('theme') || 'light' }"
      :class="{ 'dark': theme === 'dark' }">

    <div class="w-full max-w-md">
        {{-- Logo --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-primary-500 to-primary-600 shadow-lg shadow-primary-500/25 mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-charcoal">Create an account</h1>
            <p class="text-sm text-cool-gray mt-1">Join KosManager today</p>
        </div>

        {{-- Register Card --}}
        <div class="card">
            @if($errors->any())
                <div class="mb-4 p-3 rounded-lg bg-danger-50 border border-danger-500/20 text-danger-600 text-sm">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                {{-- Name --}}
                <div>
                    <label for="name" class="label">Full Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" class="input" placeholder="John Doe" required autofocus />
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="label">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" class="input" placeholder="you@example.com" required />
                </div>

                {{-- Phone --}}
                <div>
                    <label for="phone" class="label">Phone Number <span class="text-cool-gray font-normal">(optional)</span></label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}" class="input" placeholder="08123456789" />
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="label">Password</label>
                    <input type="password" id="password" name="password" class="input" placeholder="Min. 8 characters" required />
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label for="password_confirmation" class="label">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="input" placeholder="Repeat your password" required />
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn-primary w-full">
                    Create Account
                </button>
            </form>

            {{-- Login Link --}}
            <p class="mt-6 text-center text-sm text-cool-gray">
                Already have an account?
                <a href="{{ route('login') }}" class="text-primary-500 hover:text-primary-600 font-semibold">Sign In</a>
            </p>
        </div>

        <p class="mt-6 text-center text-xs text-cool-gray">
            &copy; {{ date('Y') }} KosManager. Built for PBPSI — Universitas Budi Luhur.
        </p>
    </div>

</body>
</html>
