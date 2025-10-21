<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

class ReportsController extends Controller
{
    public function index() { return view('user.reports.index'); }
}
