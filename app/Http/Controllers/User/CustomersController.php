<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

class CustomersController extends Controller
{
    public function index() { return view('user.customers.index'); }
}
