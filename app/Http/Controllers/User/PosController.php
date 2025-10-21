<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

class PosController extends Controller
{
    public function index() { return view('user.pos.index'); }
}
