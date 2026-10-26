<?php

namespace App\Http\Controllers\LandingPage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LandingController extends Controller
{

    public function index()
    {

        return view('landing-page.index');
    }

    public function about()
    {

        return view('landing-page.about');
    }
    public function service()
    {

        return view('landing-page.service');
    }
}
