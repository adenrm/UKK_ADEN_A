<?php

namespace App\Http\Controllers;

use App\Models\SppBulan;
use App\Models\StudentSpp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        if(Auth::user()->level === 'admin') {
            $students = User::where('level', 'student');
            $totalstudents = User::where('level', 'student')->count();
            $lunas = SppBulan::where('status', 'paid')->count();
            $belumLunas = SppBulan::where('status', 'partial')->count();
            $belumBayar = SppBulan::where('status', 'unpaid')->count();
            return view('admin.dashboard.index', compact('students', 'totalstudents', 'lunas', 'belumLunas', 'belumBayar'));
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
