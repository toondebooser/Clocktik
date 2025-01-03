
@extends('layout')

@section('login')
<div class="verificationMessage">
    <h2>Verify Your Email Address</h2>

    @if (session('message'))
        <div class="alert alert-warning">
            {{ session('message') }}
        </div>
    @endif

    <p>Please check your email and click on the verification link to confirm your email address.</p>

    <p class="resendVerificationMessage">If you did not receive the email, you can request a new verification link.</p>

    <form method="POST" action="{{ route('verification.resend') }}">
        @csrf
        <button type="submit" class="btn btn-primary">Resend Verification Email</button>
    </form>
</div>
@endsection
