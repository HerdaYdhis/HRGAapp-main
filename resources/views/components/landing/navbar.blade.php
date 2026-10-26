
<!-- Brand Start -->
<div class="container-fluid bg-primary d-lg-flex pb-5 pt-4 text-white">
    <div class="container pb-2">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex">
                <img src="{{ url('assets/images/logo/fav-2.svg') }}" alt="Logo" style=" height:60px"
                    class="p-2 shadow-lg">
            </div>
            {{-- <marquee behavior="" direction=""><a href="/" class="h1 text-white mb-0">HRGA<span class="text-dark">IS</span></a></marquee> --}}
            <div class="d-flex border">
                <a href="{{ route('login') }}"> <button class="btn btn-primary hover"><i
                            class="bi bi-box-arrow-in-right"></i> Login</button></a>
            </div>
        </div>
    </div>
</div>
<!-- Brand End -->
<!-- Navbar Start -->
<div class="container-fluid sticky-top">
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light py-lg-0 px-lg-3 bg-white">
            <a href="{{ route('landing-page') }}" class="navbar-brand d-lg-none">
                <h1 class="text-primary m-0">HRGA<span class="text-dark">IS</span></h1>
            </a>
            <button type="button" class="navbar-toggler me-0" data-bs-toggle="collapse"
                data-bs-target="#navbarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="navbar-collapse collapse" id="navbarCollapse">
                <ul class="navbar-nav">
                    <li class="nav-item {{ request()->segment(1) == '' ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('landing-page') }}">Home</a>
                    </li>
                    <li class="nav-item {{ request()->segment(1) == 'about' ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('landing-page.about') }}">About</a>
                    </li>
                    <li class="nav-item {{ request()->segment(1) == 'service' ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('landing-page.service') }}">Services</a>
                    </li>
                </ul>

                <div class="d-none d-lg-flex ms-auto">
                    <a class="btn btn-sm-square btn-primary ms-2" href="https://saf-indonesia.com"
                        aria-label="Visit Website"><i class="fa fa-globe"></i></a>
                    <a class="btn btn-sm-square btn-primary ms-2" href="https://www.instagram.com/saf.karir/"
                        aria-label="Visit Instagram"><i class="fab fa-instagram"></i></a>
                    <a class="btn btn-sm-square btn-primary ms-2"
                        href="https://www.linkedin.com/company/pt-sukses-abadi-farmindo/mycompany/"
                        aria-label="Visit LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
        </nav>
    </div>
</div>
<!-- Navbar End -->
