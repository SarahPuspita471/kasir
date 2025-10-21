<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

class HistoryController extends Controller
{
    public function index() { return view('user.history.index'); }
}
