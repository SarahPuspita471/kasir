<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        // Nanti kita isi fitur admin (kelola user, dsb)
        return view('admin.dashboard');
    }
}
