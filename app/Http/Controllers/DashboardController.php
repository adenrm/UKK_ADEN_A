<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        if(Auth::user()->level === 'admin') {
            $students = User::all();
            return view('admin.dashboard.index');
        } elseif(Auth::user()->level === 'staff') {
            return view('staff.dashboard.index');
        } else {
            return view('student.dashboard.index');
        }
    }

    public function management()
    {
        // if(Auth::user()->level === 'admin') {
            return view('admin.management.index');
        // }
        // abort(403, 'Not Allowed');
    }
}
