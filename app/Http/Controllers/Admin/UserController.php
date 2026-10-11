<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function profile()
    {
        return view('admin.user.profile', [
            'user' => auth()->user(),
        ]);
    }
}
