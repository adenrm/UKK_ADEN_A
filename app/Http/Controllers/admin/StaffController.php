<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index()
    {
        $staffs = User::where('level', 'staff')->get();
        return view('admin.management.staff.index', compact('staffs'));
    }

    public function create()
    {
        return view('admin.management.staff.create');
    }

    public function store(Request $request)
    {

        try {
            $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8',
            'password_confirmation' => 'required|string|min:8|same:password',
        ], [
            'name.required' => 'Nama staff wajib diisi',
            'email.required' => 'Email staff wajib diisi',
            'password.required' => 'Password staff wajib diisi',
            'password_confirmation.required' => 'Confirm Password staff wajib diisi',
            'password_confirmation.same' => 'Password dan Confirm Password tidak sama',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'level' => 'staff',
        ]);
        session()->flash('success', 'Data staff berhasil ditambahkan');
        
        return redirect()->route('admin.staff.index');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return redirect()->back();
        }
    }

    public function edit(User $user)
    {
        return view('admin.management.staff.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        try {
            $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users,email,' . $user->id,
            'password' => 'required|string|min:8',
        ], [
            'name.required' => 'Nama staff wajib diisi',
            'email.required' => 'Email staff wajib diisi',
            'password.required' => 'Password staff wajib diisi',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->password) {
            $user->password = bcrypt($request->password);
        }
        $user->save();
        session()->flash('success', 'Data staff berhasil diupdate');
        
        return redirect()->route('admin.staff.index') ->with('success', 'Data staff berhasil diupdate');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return redirect()->back();
        }
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.staff.index') ->with('success', 'Data staff berhasil dihapus');
    }
}
