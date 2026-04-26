<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HelloWorldController extends Controller
{
    public function home()
    {
        return 'Hello world from home';
    }

    public function helloWorld()
    {
        return 'Hello world';
    }
}