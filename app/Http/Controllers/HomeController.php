<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the home view.
     *
     * @return \Inertia\Response
     */
    public function index() {
        return Inertia::render('Home.screen');
    }
}
