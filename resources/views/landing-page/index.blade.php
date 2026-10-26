@extends('components.layouts.hlanding')

@section('landing')
    <x-effect.spinner />

    <x-landing.topbar />

    <x-landing.navbar />



    <!-- Carousel Start -->
    <div class="container-fluid header-carousel mb-5 px-0">
        <div id="header-carousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img class="w-100" src="{{ 'landing-page/img/carousel-1.jpg' }}" alt="Image">
                    <div class="carousel-caption">
                        <div class="container">
                            <div class="row justify-content-start">
                                <div class="col-lg-7 text-start">
                                    <h1 class="display-1 animated slideInRight mb-3 text-white">Award Winning Laboratory
                                        Center</h1>
                                    <p class="animated slideInRight mb-5">Lorem ipsum dolor sit amet, consectetur adipiscing
                                        elit. Curabitur tellus augue, iaculis id elit eget, ultrices pulvinar tortor.
                                        Quisque vel lorem porttitor, malesuada arcu quis, fringilla risus.</p>
                                    <a href="" class="btn btn-primary animated slideInRight px-5 py-3">Explore
                                        More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <img class="w-100" src="{{ 'landing-page/img/carousel-2.jpg' }}" alt="Image">
                    <div class="carousel-caption">
                        <div class="container">
                            <div class="row justify-content-end">
                                <div class="col-lg-7 text-end">
                                    <h1 class="display-1 animated slideInLeft mb-3 text-white">Expet Doctors & Lab
                                        Assistants</h1>
                                    <p class="animated slideInLeft mb-5">Lorem ipsum dolor sit amet, consectetur adipiscing
                                        elit. Curabitur tellus augue, iaculis id elit eget, ultrices pulvinar tortor.
                                        Quisque vel lorem porttitor, malesuada arcu quis, fringilla risus.</p>
                                    <a href="" class="btn btn-primary animated slideInLeft px-5 py-3">Explore
                                        More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#header-carousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#header-carousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>
    <!-- Carousel End -->


    <!-- About Start -->
    <div class="container-fluid py-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6 wow fadeIn" data-wow-delay="0.1s">
                    <div class="row g-0">
                        <div class="col-6">
                            <img class="img-fluid" src="{{ 'landing-page/img/about-1.jpg' }}">
                        </div>
                        <div class="col-6">
                            <img class="img-fluid" src="{{ 'landing-page/img/about-2.jpg' }}">
                        </div>
                        <div class="col-6">
                            <img class="img-fluid" src="i{{ 'landing-page/img/about-3.jpg' }}">
                        </div>
                        <div class="col-6">
                            <div
                                class="bg-primary w-100 h-100 mt-n5 ms-n5 d-flex flex-column align-items-center justify-content-center">
                                <div class="icon-box-light">
                                    <i class="bi bi-award text-dark"></i>
                                </div>
                                <h1 class="display-1 mb-0 text-white" data-toggle="counter-up">25</h1>
                                <small class="fs-5 text-white">Years Experience</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 wow fadeIn" data-wow-delay="0.5s">
                    <h1 class="display-6 mb-4">Trusted Lab Experts and Latest Lab Technologies</h1>
                    <p class="mb-4">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur tellus augue,
                        iaculis id elit eget, ultrices pulvinar tortor. Quisque vel lorem porttitor, malesuada arcu quis,
                        fringilla risus. Pellentesque eu consequat augue.</p>
                    <div class="row g-4 g-sm-5 justify-content-center">
                        <div class="col-sm-6">
                            <div class="about-fact btn-square flex-column rounded-circle bg-primary ms-sm-auto">
                                <p class="mb-0 text-white">Awards Winning</p>
                                <h1 class="mb-0 text-white" data-toggle="counter-up">9999</h1>
                            </div>
                        </div>
                        <div class="col-sm-6 text-start">
                            <div class="about-fact btn-square flex-column rounded-circle bg-secondary me-sm-auto">
                                <p class="mb-0 text-white">Complete Cases</p>
                                <h1 class="mb-0 text-white" data-toggle="counter-up">9999</h1>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="about-fact mt-n130 btn-square flex-column rounded-circle bg-dark mx-sm-auto">
                                <p class="mb-0 text-white">Happy Clients</p>
                                <h1 class="mb-0 text-white" data-toggle="counter-up">9999</h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- About End -->


    <!-- Features Start -->
    <div class="container-fluid py-5">
        <div class="container">
            <div class="row g-0 feature-row">
                <div class="col-md-6 col-lg-3 wow fadeIn" data-wow-delay="0.1s">
                    <div class="feature-item h-100 border p-5">
                        <div class="icon-box-primary mb-4">
                            <i class="bi bi-award text-dark"></i>
                        </div>
                        <h5 class="mb-3">Award Winning</h5>
                        <p class="mb-0">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur tellus augue.
                        </p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 wow fadeIn" data-wow-delay="0.3s">
                    <div class="feature-item h-100 border p-5">
                        <div class="icon-box-primary mb-4">
                            <i class="bi bi-people text-dark"></i>
                        </div>
                        <h5 class="mb-3">Expet Doctors</h5>
                        <p class="mb-0">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur tellus augue.
                        </p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 wow fadeIn" data-wow-delay="0.5s">
                    <div class="feature-item h-100 border p-5">
                        <div class="icon-box-primary mb-4">
                            <i class="bi bi-cash-coin text-dark"></i>
                        </div>
                        <h5 class="mb-3">Fair Prices</h5>
                        <p class="mb-0">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur tellus augue.
                        </p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 wow fadeIn" data-wow-delay="0.7s">
                    <div class="feature-item h-100 border p-5">
                        <div class="icon-box-primary mb-4">
                            <i class="bi bi-headphones text-dark"></i>
                        </div>
                        <h5 class="mb-3">24/7 Support</h5>
                        <p class="mb-0">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur tellus augue.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Features End -->


    <!-- Features Start -->
   <div class="container-fluid feature wow fadeInUp mt-5" data-wow-delay="0.1s">
    <div class="container">
        <div class="row g-0">
            <div class="col-lg-6 pt-lg-5">
                <div class="mt-lg-5 bg-white p-5">
                    <h1 class="display-6 wow fadeIn mb-4" data-wow-delay="0.3s">HRGA Information System: Empowering Your HR Management</h1>
                    <p class="wow fadeIn mb-4" data-wow-delay="0.4s">Explore the comprehensive features of our HRGA Information System designed to streamline and optimize your Human Resources and General Affairs processes. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur tellus augue, iaculis id elit eget, ultrices pulvinar tortor. Quisque vel lorem porttitor, malesuada arcu quis, fringilla risus. Pellentesque eu consequat augue.</p>
                    <div class="row g-5 mb-5 pt-2">
                        <div class="col-sm-6 wow fadeIn" data-wow-delay="0.3s">
                            <div class="icon-box-primary mb-4">
                                <i class="bi bi-person-plus text-dark"></i>
                            </div>
                            <h5 class="mb-3">Employee Management</h5>
                            <span>Effortlessly manage employee data and profiles with our user-friendly interface.</span>
                        </div>
                        <div class="col-sm-6 wow fadeIn" data-wow-delay="0.4s">
                            <div class="icon-box-primary mb-4">
                                <i class="bi bi-check-all text-dark"></i>
                            </div>
                            <h5 class="mb-3">Advanced Reporting</h5>
                            <span>Generate insightful reports and analytics for informed decision-making.</span>
                        </div>
                    </div>
                    <a class="btn btn-primary wow fadeIn px-5 py-3" data-wow-delay="0.5s" href="{{ route('landing-page.service') }}">Explore More</a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row h-100 align-items-end">
                    <div class="col-12 wow fadeIn" data-wow-delay="0.3s">
                        <div class="d-flex align-items-center justify-content-center" style="min-height: 300px;">
                            <button type="button" class="btn-play" data-bs-toggle="modal"
                                data-src="https://www.youtube.com/embed/tfoPsEC_stE?si=3NpeZ_VD77N5uPpU" data-bs-target="#videoModal">
                                <span></span>
                            </button>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="bg-primary p-5">
                            <div class="experience wow fadeIn mb-4" data-wow-delay="0.3s">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-white">User-Friendly Interface</span>
                                    <span class="text-white">90%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-dark" role="progressbar" aria-valuenow="90"
                                        aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                            <div class="experience wow fadeIn mb-4" data-wow-delay="0.4s">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-white">Insightful Analytics</span>
                                    <span class="text-white">95%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-dark" role="progressbar" aria-valuenow="95"
                                        aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                            <div class="experience wow fadeIn mb-0" data-wow-delay="0.5s">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-white">Efficient Reporting</span>
                                    <span class="text-white">90%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-dark" role="progressbar" aria-valuenow="90"
                                        aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Features End -->




    <!-- Video Modal Start -->
    <div class="modal modal-video fade" id="videoModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content rounded-0">
                <div class="modal-header">
                    <h3 class="modal-title" id="exampleModalLabel">Youtube Video</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- 16:9 aspect ratio -->
                    <div class="ratio ratio-16x9">
                        <iframe class="embed-responsive-item" src="" id="video" allowfullscreen
                            allowscriptaccess="always" allow="autoplay"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Video Modal End -->


    <!-- Service Start -->
 <div class="container-fluid container-service py-5">
    <div class="container pt-5">
        <div class="wow fadeInUp mx-auto text-center" data-wow-delay="0.1s" style="max-width: 600px;">
            <h1 class="display-6 mb-3">Comprehensive HRGA Information System Features</h1>
            <p class="mb-5">Explore the versatile features of our HRGA Information System designed to streamline your Human Resources and General Affairs operations. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur tellus augue, iaculis id elit eget, ultrices pulvinar tortor.</p>
        </div>
        <div class="row g-4">
            <!-- Adjusted content for HRGA Information System -->
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="service-item">
                    <div class="icon-box-primary mb-4">
                        <i class="bi bi-people text-dark"></i>
                    </div>
                    <h5 class="mb-3">Employee Management</h5>
                    <p class="mb-4">Effortlessly manage employee data and profiles with our user-friendly interface.</p>
                    <a class="btn btn-light px-3" href="#">Read More<i class="bi bi-chevron-double-right ms-1"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                <div class="service-item">
                    <div class="icon-box-primary mb-4">
                        <i class="bi bi-graph-up text-dark"></i>
                    </div>
                    <h5 class="mb-3">Analytics and Reporting</h5>
                    <p class="mb-4">Generate insightful reports and analytics for informed decision-making.</p>
                    <a class="btn btn-light px-3" href="#">Read More<i class="bi bi-chevron-double-right ms-1"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                <div class="service-item">
                    <div class="icon-box-primary mb-4">
                        <i class="bi bi-calendar2-event text-dark"></i>
                    </div>
                    <h5 class="mb-3">Attendance Management</h5>
                    <p class="mb-4">Efficiently track and manage employee attendance and work hours.</p>
                    <a class="btn btn-light px-3" href="#">Read More<i class="bi bi-chevron-double-right ms-1"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.7s">
                <div class="service-item">
                    <div class="icon-box-primary mb-4">
                        <i class="bi bi-building text-dark"></i>
                    </div>
                    <h5 class="mb-3">Company Asset Management</h5>
                    <p class="mb-4">Efficiently track and manage company assets and equipment.</p>
                    <a class="btn btn-light px-3" href="#">Read More<i class="bi bi-chevron-double-right ms-1"></i></a>
                </div>
            </div>
            <!-- Additional content -->
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="service-item">
                    <div class="icon-box-primary mb-4">
                        <i class="bi bi-file-text text-dark"></i>
                    </div>
                    <h5 class="mb-3">Document Management</h5>
                    <p class="mb-4">Effortlessly organize and manage HRGA-related documents and files.</p>
                    <a class="btn btn-light px-3" href="#">Read More<i class="bi bi-chevron-double-right ms-1"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                <div class="service-item">
                    <div class="icon-box-primary mb-4">
                        <i class="bi bi-shield-check text-dark"></i>
                    </div>
                    <h5 class="mb-3">Security and Access Control</h5>
                    <p class="mb-4">Ensure data security with robust access controls and permissions.</p>
                    <a class="btn btn-light px-3" href="#">Read More<i class="bi bi-chevron-double-right ms-1"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                <div class="service-item">
                    <div class="icon-box-primary mb-4">
                        <i class="bi bi-clock text-dark"></i>
                    </div>
                    <h5 class="mb-3">Time and Attendance Tracking</h5>
                    <p class="mb-4">Efficiently monitor and manage employee working hours and attendance.</p>
                    <a class="btn btn-light px-3" href="#">Read More<i class="bi bi-chevron-double-right ms-1"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.7s">
                <div class="service-item">
                    <div class="icon-box-primary mb-4">
                        <i class="bi bi-envelope text-dark"></i>
                    </div>
                    <h5 class="mb-3">Communication Hub</h5>
                    <p class="mb-4">Facilitate effective communication within your HR and General Affairs teams.</p>
                    <a class="btn btn-light px-3" href="#">Read More<i class="bi bi-chevron-double-right ms-1"></i></a>
                </div>
            </div>
            <!-- End of additional content -->
        </div>
    </div>
</div>
<!-- Service End -->


    <!-- Appoinment Start -->
    <div class="container-fluid py-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                    <h1 class="display-6 mb-4">We Ensure You Will Always Get The Best Result</h1>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur tellus augue, iaculis id elit
                        eget, ultrices pulvinar tortor. Quisque vel lorem porttitor, malesuada arcu quis, fringilla risus.
                        Pellentesque eu consequat augue.</p>
                    <p class="mb-4">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur tellus augue,
                        iaculis id elit eget, ultrices pulvinar tortor.</p>
                    <div class="d-flex align-items-start wow fadeIn" data-wow-delay="0.3s">
                        <div class="icon-box-primary">
                            <i class="bi bi-geo-alt text-dark fs-1"></i>
                        </div>
                        <div class="ms-3">
                            <h5>Office Address</h5>
                            <span>123 Street, New York, USA</span>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex align-items-start wow fadeIn" data-wow-delay="0.4s">
                        <div class="icon-box-primary">
                            <i class="bi bi-clock text-dark fs-1"></i>
                        </div>
                        <div class="ms-3">
                            <h5>Office Time</h5>
                            <span>Mon-Sat 09am-5pm, Sun Closed</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.5s">
                    <h2 class="mb-4">Online Appoinment</h2>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="name" placeholder="Your Name">
                                <label for="name">Your Name</label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-floating">
                                <input type="email" class="form-control" id="mail" placeholder="Your Email">
                                <label for="mail">Your Email</label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="mobile" placeholder="Your Mobile">
                                <label for="mobile">Your Mobile</label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-floating">
                                <select class="form-select" id="service">
                                    <option selected>Pathology Testing</option>
                                    <option value="">Microbiology Tests</option>
                                    <option value="">Biochemistry Tests</option>
                                    <option value="">Histopatology Tests</option>
                                </select>
                                <label for="service">Choose A Service</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating">
                                <textarea class="form-control" placeholder="Leave a message here" id="message" style="height: 130px"></textarea>
                                <label for="message">Message</label>
                            </div>
                        </div>
                        <div class="col-12 text-center">
                            <button class="btn btn-primary w-100 py-3" type="submit">Submit Now</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Appoinment Start -->


    <!-- Team Start -->
    <div class="container-fluid container-team py-5">
        <div class="container pb-5">
            <div class="row g-5 align-items-center mb-5">
                <div class="col-md-6 wow fadeIn" data-wow-delay="0.3s">
                    <img class="img-fluid w-100" src="{{ 'landing-page/img/team-1.jpg' }}" alt="">
                </div>
                <div class="col-md-6 wow fadeIn" data-wow-delay="0.5s">
                    <h1 class="display-6 mb-3">Dr. John Martin</h1>
                    <p class="mb-1">CEO & Founder</p>
                    <p class="mb-5">Labsky, New York, USA</p>
                    <h3 class="mb-3">Biography</h3>
                    <p class="mb-4">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur tellus augue,
                        iaculis id elit eget, ultrices pulvinar tortor. Quisque vel lorem porttitor, malesuada arcu quis,
                        fringilla risus. Pellentesque eu consequat augue.</p>
                    <p class="mb-4">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur tellus augue,
                        iaculis id elit eget, ultrices pulvinar tortor. Quisque vel lorem porttitor, malesuada arcu quis,
                        fringilla risus. Pellentesque eu consequat augue.</p>
                    <div class="d-flex">
                        <a class="btn btn-lg-square btn-primary me-2" href=""><i
                                class="fab fa-facebook-f"></i></a>
                        <a class="btn btn-lg-square btn-primary me-2" href=""><i class="fab fa-twitter"></i></a>
                        <a class="btn btn-lg-square btn-primary me-2" href=""><i
                                class="fab fa-linkedin-in"></i></a>
                        <a class="btn btn-lg-square btn-primary me-2" href=""><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="team-item">
                        <div class="position-relative overflow-hidden">
                            <img class="img-fluid w-100" src="{{ 'landing-page/img/team-2.jpg' }}" alt="">
                            <div class="team-social">
                                <a class="btn btn-square btn-light mx-1" href=""><i
                                        class="fab fa-facebook-f"></i></a>
                                <a class="btn btn-square btn-light mx-1" href=""><i
                                        class="fab fa-twitter"></i></a>
                                <a class="btn btn-square btn-light mx-1" href=""><i
                                        class="fab fa-linkedin-in"></i></a>
                                <a class="btn btn-square btn-light mx-1" href=""><i
                                        class="fab fa-youtube"></i></a>
                            </div>
                        </div>
                        <div class="p-4 text-center">
                            <h5 class="mb-1">Alex Robin</h5>
                            <span>Lab Assistant</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="team-item">
                        <div class="position-relative overflow-hidden">
                            <img class="img-fluid w-100" src="{{ 'landing-page/img/team-3.jpg' }}" alt="">
                            <div class="team-social">
                                <a class="btn btn-square btn-light mx-1" href=""><i
                                        class="fab fa-facebook-f"></i></a>
                                <a class="btn btn-square btn-light mx-1" href=""><i
                                        class="fab fa-twitter"></i></a>
                                <a class="btn btn-square btn-light mx-1" href=""><i
                                        class="fab fa-linkedin-in"></i></a>
                                <a class="btn btn-square btn-light mx-1" href=""><i
                                        class="fab fa-youtube"></i></a>
                            </div>
                        </div>
                        <div class="p-4 text-center">
                            <h5 class="mb-1">Andrew Bon</h5>
                            <span>Lab Assistant</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="team-item">
                        <div class="position-relative overflow-hidden">
                            <img class="img-fluid w-100" src="{{ 'landing-page/img/team-4.jpg' }}" alt="">
                            <div class="team-social">
                                <a class="btn btn-square btn-light mx-1" href=""><i
                                        class="fab fa-facebook-f"></i></a>
                                <a class="btn btn-square btn-light mx-1" href=""><i
                                        class="fab fa-twitter"></i></a>
                                <a class="btn btn-square btn-light mx-1" href=""><i
                                        class="fab fa-linkedin-in"></i></a>
                                <a class="btn btn-square btn-light mx-1" href=""><i
                                        class="fab fa-youtube"></i></a>
                            </div>
                        </div>
                        <div class="p-4 text-center">
                            <h5 class="mb-1">Martin Tompson</h5>
                            <span>Lab Assistant</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.7s">
                    <div class="team-item">
                        <div class="position-relative overflow-hidden">
                            <img class="img-fluid w-100" src="{{ 'landing-page/img/team-5.jpg' }}" alt="">
                            <div class="team-social">
                                <a class="btn btn-square btn-light mx-1" href=""><i
                                        class="fab fa-facebook-f"></i></a>
                                <a class="btn btn-square btn-light mx-1" href=""><i
                                        class="fab fa-twitter"></i></a>
                                <a class="btn btn-square btn-light mx-1" href=""><i
                                        class="fab fa-linkedin-in"></i></a>
                                <a class="btn btn-square btn-light mx-1" href=""><i
                                        class="fab fa-youtube"></i></a>
                            </div>
                        </div>
                        <div class="p-4 text-center">
                            <h5 class="mb-1">Clarabelle Samber</h5>
                            <span>Lab Assistant</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Team End -->


    <!-- Testimonial Start -->
    <div class="container-fluid testimonial py-5">
        <div class="container pt-5">
            <div class="row gy-5 gx-0">
                <div class="col-lg-6 pe-lg-5 wow fadeIn" data-wow-delay="0.3s">
                    <h1 class="display-6 mb-4 text-white">What Clients Say About Our Lab Services!</h1>
                    <p class="mb-5 text-white">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur tellus
                        augue, iaculis id elit eget, ultrices pulvinar tortor.</p>
                    <a href="" class="btn btn-primary px-5 py-3">More Testimonials</a>
                </div>
                <div class="col-lg-6 mb-n5 wow fadeIn" data-wow-delay="0.5s">
                    <div class="bg-white p-5">
                        <div class="owl-carousel testimonial-carousel wow fadeIn" data-wow-delay="0.1s">
                            <div class="testimonial-item">
                                <div class="icon-box-primary mb-4">
                                    <i class="bi bi-chat-left-quote text-dark"></i>
                                </div>
                                <p class="fs-5 mb-4">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur
                                    tellus augue, iaculis id elit eget, ultrices pulvinar tortor. Quisque vel lorem
                                    porttitor, malesuada arcu quis, fringilla risus. Pellentesque eu consequat augue.</p>
                                <div class="d-flex align-items-center">
                                    <img class="flex-shrink-0" src="{{ 'landing-page/img/testimonial-1.jpg' }}"
                                        alt="">
                                    <div class="ps-3">
                                        <h5 class="mb-1">Client Name</h5>
                                        <span class="text-primary">Profession</span>
                                    </div>
                                </div>
                            </div>
                            <div class="testimonial-item">
                                <div class="icon-box-primary mb-4">
                                    <i class="bi bi-chat-left-quote text-dark"></i>
                                </div>
                                <p class="fs-5 mb-4">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur
                                    tellus augue, iaculis id elit eget, ultrices pulvinar tortor. Quisque vel lorem
                                    porttitor, malesuada arcu quis, fringilla risus. Pellentesque eu consequat augue.</p>
                                <div class="d-flex align-items-center">
                                    <img class="flex-shrink-0" src="{{ 'landing-page/img/testimonial-2.jpg' }}"
                                        alt="">
                                    <div class="ps-3">
                                        <h5 class="mb-1">Client Name</h5>
                                        <span class="text-primary">Profession</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Testimonial End -->
@endsection
