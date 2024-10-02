@extends('backend.auth.auth_master')

@section('auth_title')
    Login | User Panel
@endsection

@section('auth-content')
<div class="login-area login-s2">
    <div class="container">
        <div class="login-box ptb--100">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="login-form-head">
                    <h4>Sign In</h4>
                    <p>Welcome! Sign in to access your server statistics and manage your monitoring dashboard.</p>
                    
                </div>
                <div class="login-form-body">
                    <div class="form-gp">
                        <label for="email">Email address</label>
                        <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autocomplete="email" autofocus>
                        <i class="ti-email"></i>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-gp">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="current-password">
                        <i class="ti-lock"></i>
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="row mb-4 rmber-area">
                        <div class="col-6">
                            <div class="custom-control custom-checkbox mr-sm-2">
                                <input type="checkbox" class="custom-control-input" id="customControlAutosizing" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="customControlAutosizing">Remember Me</label>
                            </div>
                        </div>
                    </div>
                    <div class="submit-btn-area">
                        <button id="form_submit" type="submit">Sign In <i class="ti-arrow-right"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
