@extends('components.layouts.auth', [
    'title' => 'Forgot-Password',
])

@section('content')
    <div class="row h-100">
        <div class="col-lg-5 col-12">
            <div id="auth-left">
                <div class="auth-logo">
                    <a href="{{ route('dashboard') }}"><img class="bg-light rounded-3 w-50 h-25 p-4"
                            src="{{ url('assets/images/logo/logo.svg') }}" alt="Logo"></a>
                </div>
                <x-layouts.card>
                    <x-slot:header>
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                    </x-slot:header>
                    <x-slot:body>
                        <h1 class="bold text-danger">Forgot Password</h1>
                        <p class="auth-subtitle mb-3">Sistem Informasi HRGA</p>

                        <x-alert.validation />
                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf
                            <div class="form-group position-relative has-icon-left mb-4">
                                <input type="text"
                                    class="form-control form-control-xl @error('email') is-invalid @enderror"
                                    placeholder="Email" name="email">
                                <div class="form-control-icon">
                                    <i class="bi bi-person"></i>
                                </div>
                                @error('email')
                                    <x-forms.error message="{{ $message }}" />
                                @enderror
                            </div>


                            <button type="submit" class="btn btn-primary btn-block btn-lg mt-5 shadow-lg">Send
                                Email</button>
                        </form>

                    </x-slot:body>

                </x-layouts.card>

            </div>
        </div>

        <div class="col-lg-7 d-none d-lg-block">
            <div id="auth-right">
                <img src="{{ url('assets/images/bg/bg.jpg') }}" alt="Rumah Sholeh">
            </div>
        </div>
    </div>
@endsection
