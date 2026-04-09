<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
public function index() {
    $users = User::latest()->paginate(20);
    return view('admin.users.index', compact('users'));
}



}
