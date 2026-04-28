<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\SppBulan;
use App\Models\StudentSpp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;

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
            $payments = Payment::where('user_id', Auth::user()->id)->get();
            return view('student.dashboard.index', compact('payments'));
        }
    }

    public function management()
    {
            return view('admin.management.index');
    }

    public function log()
    {
        $log = Activity::orderBy('created_at', 'desc')->get();
        return view('admin.log', compact('log'));
    }
}
