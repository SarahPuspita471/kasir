<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

class StockController extends Controller
{
    public function index() { return view('user.stock.index'); }
}
