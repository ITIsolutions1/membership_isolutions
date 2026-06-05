<x-guest-layout>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background: url('{{ asset('images/background3.jpg') }}') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background-color: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 25px rgba(183, 28, 28, 0.5);
        }

        .login-title {
            text-align: center;
            font-size: 1.4rem;
            font-weight: bold;
            color: #b71c1c;
            margin-bottom: 20px;
            letter-spacing: 1px;
        }

        .logo img {
            max-width: 300px;
            margin: 0 auto 20px;
            display: block;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            color: #b71c1c;
            font-size: 0.9rem;
        }

        input[type="email"],
        input[type="password"] {
            background-color: #fff;
            border: 1px solid #e53935;
            color: #333;
        }

        input::placeholder {
            color: #aaa;
        }

        .remember-me span {
            color: #555;
            font-size: 0.85rem;
        }

        .login-button {
            background-color: #6d0000;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            border: none;
        }

        .login-button:hover {
            background-color: #d32f2f;
        }

        .forgot-link {
            color: #b71c1c;
            text-decoration: none;
            font-size: 0.85rem;
        }

        .forgot-link:hover {
            color: #ff7961;
        }
    </style>

    <div class="login-card">
        <div class="logo">
            <img src="{{ asset('vendor/adminlte/dist/img/isolutions.png') }}" alt="Logo" />
        </div>

        <div class="login-title">Login</div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <input type="hidden" value="{{ $event }}" name="event">
            <!-- Email Address -->
            <div class="form-group">
                <label for="email">Email</label>
                <x-text-input id="email" class="block mt-1 w-full"
                              type="email"
                              name="email"
                              :value="old('email')"
                              required
                              autofocus
                              autocomplete="username"
                              placeholder="email@example.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="form-group">
                <label for="password">Password</label>
                <x-text-input id="password" class="block mt-1 w-full"
                              type="password"
                              name="password"
                              required
                              autocomplete="current-password"
                              placeholder="******" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="form-group remember-me">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox"
                           class="rounded border-gray-300 text-red-600 shadow-sm focus:ring focus:ring-red-500"
                           name="remember">
                    <span class="ms-2">{{ __('Remember me') }}</span>
                </label>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between mt-4">
                @if (Route::has('register'))
                    <a class="forgot-link" href="{{ route('register') }}">
                        {{ __('Dont Have Account?') }}
                    </a>
                @endif

                <x-primary-button class="login-button">
                    {{ __('Log in') }}
                </x-primary-button>
            </div>
            <div class="items-center flex">
                <a class="forgot-link" href="{{ route('password.request') }}">
                        {{ __('Forgot Password') }}
                    </a>
            </div>
        </form>
    </div>
</x-guest-layout>
