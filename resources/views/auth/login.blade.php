@extends('components.layouts.auth', [
    'title' => 'Login',
])

@section('content')
    <div class="row h-100">
        <div class="col-lg-5 col-12">
            <div id="auth-left">
                <div class="auth-logo">
                    <a href="{{ route('dashboard') }}"><img class="bg-light rounded-3 w-50 h-25 p-4"
                            src="{{ url('assets/images/logo/logo.svg') }}" alt="Logo"></a>
                </div>

                <h1 class="text-bold text-primary">Log in.</h1>
                <p class="auth-subtitle mb-5">Sistem Informasi HRGA</p>

                <x-alert.validation />
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group position-relative has-icon-left mb-4">
                        <input type="text" class="form-control form-control-xl @error('email') is-invalid @enderror"
                            placeholder="Email / Username" name="email" value="{{ old('email') }}">
                        <div class="form-control-icon">
                            <i class="bi bi-person"></i>
                        </div>
                        @error('email')
                            <x-forms.error message="{{ $message }}" />
                        @enderror
                    </div>
                    <div class="form-group position-relative has-icon-left mb-4">
                        <input type="password" class="form-control form-control-xl @error('email') is-invalid @enderror"
                            placeholder="Password" name="password">
                        <div class="form-control-icon">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                        @error('password')
                            <x-forms.error message="{{ $message }}" />
                        @enderror
                    </div>

                    <div class="row mb-4">
                        <div class="col d-flex justify-content-center">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="form2Example31"
                                    checked />
                                <label class="form-check-label" for="form2Example31"> Remember me </label>
                            </div>
                        </div>

                        <div class="col">
                            <a href="/password/reset">Forgot password?</a>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-lg mt-3 shadow-lg">Log in</button>
                </form>

                <div class="fs-4 mt-3 text-center text-lg">
                    <p class="text-gray-600">Don't have an account? <a href="{{ route('register') }}" class="font-bold">Sign
                            up</a>.</p>

                </div>
            </div>
        </div>
        <div class="col-lg-7 d-none d-lg-block">
            {{-- <div class="position-fixed m-5">
                <h1 class="auth-title">Gerak Sedekah Cilacap adalah <strong class="bg-primary text-white">Lembaga
                        Filantropi<strong></h1>
                <div class="text-black">
                    <h4 class="text-bold text-black">Dibawah ini merupakah pembangunan yang dilakukan Gerak Sedekah Cilacap, dan
                        merupakan program terbaru yang sedang berjalan yaitu rumah sholeh sebagai :</h4>
                    <li> Baitul Mal</li>
                    <li> Baituz Zakkah</li>
                    <li> Dakwah</li>
                </div>

            </div> --}}

            <div id="auth-right">
                <img src="{{ url('assets/images/bg/bg.jpg') }}" alt="Rumah Sholeh GSC">
            </div>

        </div>
    </div>
@endsection
