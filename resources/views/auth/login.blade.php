@extends('layouts.auth')

@section('content')
<div class="row justify-content-center">
    <!-- Mengatur lebar kotak login agar pas di tengah (mirip layar HP) -->
    <div class="col-md-5 col-lg-4">
        
        <div class="ios-card p-4 p-md-5 fade-in-up">
            
            <!-- Bagian Header / Logo -->
            <div class="text-center mb-4">
                <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                    <i class="bi bi-person-bounding-box fs-1 text-primary"></i>
                </div>
                <h3 class="fw-bold text-dark mb-1">CASHIER APPS</h3>
                <p class="text-muted" style="font-size: 0.9rem;">Sign in to Cashier Apps</p>
            </div>

            <!-- Form Login -->
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Input Email -->
                <div class="mb-3">
                    <input id="email" type="email" class="form-control ios-input @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Email Address">

                    @error('email')
                        <span class="invalid-feedback ms-2" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <!-- Input Password -->
                <div class="mb-4">
                    <input id="password" type="password" class="form-control ios-input @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Password">

                    @error('password')
                        <span class="invalid-feedback ms-2" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <!-- Tombol Login -->
                <button type="submit" class="btn ios-btn w-100 mb-3 shadow-sm">
                    Sign In
                </button>
                
            </form>

        </div>
        
    </div>
</div>
@endsection