<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CafeOwner;

class HomeController extends Controller
{
    public function index()
    {
        $owners = CafeOwner::all();
        return view('home', compact('owners'));
    }
}
