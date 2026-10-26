@extends('components.layouts.auth', [
    'title' => 'Register',
])

@section('content')
    <div class="row h-100">
        <div class="col-lg-5 col-12">
            <div id="auth-left">
                <div class="auth-logo">
                    <a href="{{route('dashboard')}}"><img class=" bg-light p-4 rounded-3 w-50 h-25" src="{{url('assets/images/logo/logo.svg')}}" alt="Logo"></a>
                </div>
                <h1 class="text-bold text-primary">Sign up.</h1>
                <p class="auth-subtitle mb-5">Isi data untuk mengakses sistem ini</p>
                <x-alert.validation/>
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="form-group position-relative has-icon-left mb-2">
                        <input type="text" class="form-control form-control-xl @error('name') is-invalid @enderror" placeholder="Full Name" name="name">
                        <div class="form-control-icon">
                            <i class="bi bi-person"></i>
                        </div>
                        @error('name')
                        <x-forms.error message="{{$message}}"/>
                        @enderror
                    </div>
                    <div class="form-group position-relative has-icon-left mb-2">
                        <input type="text" class="form-control form-control-xl @error('email') is-invalid @enderror" placeholder="Email" name="email">
                        <div class="form-control-icon">
                            <i class="bi bi-person"></i>
                        </div>
                        @error('email')
                        <x-forms.error message="{{$message}}"/>
                        @enderror
                    </div>
                    <div class="form-group position-relative has-icon-left mb-2">
                        <input type="password" class="form-control form-control-xl @error('email') is-invalid @enderror" placeholder="Password"
                               name="password">
                        <div class="form-control-icon">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                        @error('password')
                        <x-forms.error message="{{$message}}"/>
                        @enderror
                    </div>
                    <div class="form-group position-relative has-icon-left mb-2">
                        <input type="password" class="form-control form-control-xl @error('password_confirmation') is-invalid @enderror" placeholder="{{ __('Confirm Password') }}"
                               name="password_confirmation">
                        <div class="form-control-icon">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                        @error('password_confirmation')
                        <x-forms.error message="{{$message}}"/>
                        @enderror
                    </div>
                      <div class="form-group position-relative has-icon-left mb-2">
                        <input type="text" class="form-control form-control-xl @error('no_wa') is-invalid @enderror" placeholder="Whatsapp Number" name="no_wa">
                        <div class="form-control-icon">
                            <i class="bi bi-person"></i>
                        </div>
                        @error('no_wa')
                        <x-forms.error message="{{$message}}"/>
                        @enderror
                    </div>
                     <div class="form-group position-relative has-icon-left mb-2">
                        <input type="text" class="form-control form-control-xl @error('address') is-invalid @enderror" placeholder="Address" name="address">
                        <div class="form-control-icon">
                            <i class="bi bi-person"></i>
                        </div>
                        @error('address')
                        <x-forms.error message="{{$message}}"/>
                        @enderror
                    </div>
                      <div class="form-group position-relative has-icon-left mb-2">
                        <input type="text" class="form-control form-control-xl @error('employee_id') is-invalid @enderror" placeholder="Employee Id" name="employee_id">
                        <div class="form-control-icon">
                            <i class="bi bi-person"></i>
                        </div>
                        @error('employee_id')
                        <x-forms.error message="{{$message}}"/>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-lg shadow-lg mt-3">Sign Up</button>
                </form>
                <div class="text-center mt-3 text-lg fs-4">
                    <p class="text-gray-600">Already have account? <a href="{{route('login')}}" class="font-bold">Sign
                            in</a>.</p>
                    {{--                    <p><a class="font-bold" href="auth-forgot-password.html">Forgot password?</a>.</p>--}}
                </div>
            </div>
        </div>
        <div class="col-lg-7 d-none d-lg-block">
            <div id="auth-right">
                <img src="{{url('assets/images/bg/bg.jpg')}}" alt="ID Farm Peternakan">
            </div>
        </div>
    </div>
@endsection
