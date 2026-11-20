@extends('layouts.auth')

@section('title', 'Sign in')

@section('content')
    <main class="auth">
        {{-- Brand panel. The logo is public/images/cvsu-logo.png: replace that FILE with the official
             CvSU logo (no code change needed). object-fit: contain keeps its original aspect ratio. --}}
        <section class="auth__brand" aria-label="About DocuSecure">
            <div class="auth__logos">
                <img class="auth__logo-img" src="{{ asset('images/cvsu-logo.png') }}" alt="Cavite State University logo">
                <div>
                    <div class="auth__wordmark"><x-icon name="shield-check" /> DocuSecure</div>
                    <div class="auth__institution">Cavite State University</div>
                </div>
            </div>

            <div>
                <div class="auth__eyebrow">Security-first design</div>
                <h1 class="auth__headline">Your documents.<br>Protected at every step.</h1>
                <p class="auth__lead">One trusted workspace to organize your institution's knowledge and control who can access it.</p>
            </div>

            <svg class="auth__art" viewBox="0 0 200 200" fill="none" aria-hidden="true" focusable="false">
                <rect x="24" y="66" width="124" height="94" rx="10" fill="#1d5a3f" stroke="#2f7d58" stroke-width="2"/>
                <rect x="42" y="40" width="84" height="64" rx="6" fill="#f4f8f5"/>
                <path d="M58 60h52M58 72h52M58 84h34" stroke="#9db8a9" stroke-width="4" stroke-linecap="round"/>
                <path d="M138 92l36 13v30c0 22-16 35-36 42-20-7-36-20-36-42v-30z" fill="#eabf55"/>
                <circle cx="138" cy="130" r="7" fill="#14452f"/>
                <rect x="135" y="133" width="6" height="16" rx="3" fill="#14452f"/>
            </svg>

            <div class="auth__goals"><strong>Security goals:</strong> encrypted storage · role-based access · full audit trail</div>
        </section>

        <section class="auth__panel" aria-labelledby="login-title">
            <div class="auth__card">
                <h2 class="auth__title" id="login-title">Welcome back</h2>
                <p class="auth__subtitle" id="login-subtitle">Sign in to your secure workspace.</p>

                {{-- Status messages (session expired, locked, failed sign-in…) are written here by login.js --}}
                <div id="auth-alert" aria-live="polite"></div>

                {{-- Step 1: credentials. method="post" + @csrf so that, when the backend exists, nothing
                     changes here; without JavaScript the browser POSTs (never puts a password in the URL). --}}
                <form id="login-form" class="auth__form" method="post" action="{{ route('login') }}" novalidate data-redirect="{{ route('dashboard') }}">
                    @csrf
                    <div class="field">
                        <label for="login-email">Email address</label>
                        <input class="input" id="login-email" name="email" type="email" inputmode="email"
                               autocomplete="username" autocapitalize="none" spellcheck="false" maxlength="254"
                               placeholder="name@cvsu.edu.ph" required>
                    </div>
                    <div class="field">
                        <label for="login-password">Password</label>
                        <div class="input-group">
                            <input class="input" id="login-password" name="password" type="password"
                                   autocomplete="current-password" maxlength="128" placeholder="Enter your password" required>
                            <button type="button" class="icon-btn" data-toggle-password="#login-password" aria-label="Show password" aria-pressed="false"><x-icon name="eye" /></button>
                        </div>
                    </div>
                    <div class="auth__row">
                        <label class="auth__check"><input type="checkbox" id="login-remember" name="remember"> Remember me</label>
                        <button type="button" class="text-link-btn" data-modal-open="#forgot-modal">Forgot password?</button>
                    </div>
                    <button type="submit" class="btn btn--primary" id="login-submit">Sign in</button>
                </form>

                {{-- Step 2: multi-factor code (interface only; verification must happen on the server) --}}
                <form id="mfa-form" class="auth__form" method="post" action="{{ route('login') }}" novalidate hidden>
                    @csrf
                    <div class="field">
                        <label for="mfa-code">Verification code</label>
                        <input class="input auth__code" id="mfa-code" name="code" type="text" inputmode="numeric"
                               autocomplete="one-time-code" maxlength="6" pattern="[0-9]{6}" placeholder="••••••">
                        <span class="field__hint">Enter the 6-digit code from your authenticator app.</span>
                    </div>
                    <div class="auth__step-actions">
                        <button type="submit" class="btn btn--primary" id="mfa-submit">Verify and continue</button>
                        <button type="button" class="text-link-btn" id="mfa-back">Back to sign in</button>
                    </div>
                </form>

                <p class="auth__assurance"><x-icon name="lock" /> For authorized CvSU users only.</p>
                <p class="auth__note">Need an account? Contact your administrator.</p>

                <details class="demo">
                    <summary>Demo mode (frontend only)</summary>
                    <div class="demo__body">
                        <p>No real authentication happens in this prototype. These buttons simulate server responses so each interface state can be reviewed. Lockout, MFA and session expiry must be enforced by the Laravel backend.</p>
                        <div class="demo__buttons">
                            <button type="button" class="btn btn--secondary btn--sm" data-demo="fail">Failed sign-in</button>
                            <button type="button" class="btn btn--secondary btn--sm" data-demo="lock">Locked account</button>
                            <button type="button" class="btn btn--secondary btn--sm" data-demo="disabled">Disabled account</button>
                            <button type="button" class="btn btn--secondary btn--sm" data-demo="expired">Session expired</button>
                            <button type="button" class="btn btn--secondary btn--sm" data-demo="reset">Reset demo</button>
                        </div>
                    </div>
                </details>

                <p class="auth__footer">© 2026 DocuSecure · Cavite State University</p>
            </div>
        </section>
    </main>

    <x-modal id="forgot-modal" title="Reset your password">
        <form id="forgot-form" class="form-stack" novalidate>
            <p class="muted" style="font-size: var(--fs-sm);">Enter your CvSU email address. If an account exists, we'll send reset instructions.</p>
            <div class="field">
                <label for="forgot-email">Email address</label>
                <input class="input" id="forgot-email" type="email" inputmode="email" autocomplete="email" autocapitalize="none" spellcheck="false" maxlength="254" placeholder="name@cvsu.edu.ph">
            </div>
        </form>
        <x-slot:footer>
            <x-button variant="secondary" data-modal-close>Cancel</x-button>
            <x-button variant="primary" type="submit" form="forgot-form">Send reset link</x-button>
        </x-slot:footer>
    </x-modal>
@endsection

@push('scripts')
    <script src="{{ asset('js/pages/login.js') }}"></script>
@endpush
