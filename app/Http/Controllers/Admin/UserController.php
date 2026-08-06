<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

/**
 * Customer / user listing for the admin panel.
 *
 * NOTE: this file previously declared "class DashboardController", a
 * copy-paste slip that collided with Admin\DashboardController the moment
 * anything autoloaded it.
 */
class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(20);

        return view('admin.users.index', compact('users'));
    }
}
