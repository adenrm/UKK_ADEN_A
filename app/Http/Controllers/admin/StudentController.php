<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ClassGrade;
use App\Models\User;
use App\Models\UserData;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    //
    public function index()
    {
        $students = User::where('level', 'student')->get();
        return view('admin.management.student.index', compact('students'));
    }

    public function create()
    {
        $classes = ClassGrade::all();
        return view('admin.management.student.create', compact('classes'));
    }

    public function store(Request $request)
    {
        try {
        $request->validate([
        'name' => 'required',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6',
        'nisn' => 'required|integer|unique:user_data,nisn', // Cek duplikat NISN
        'nis' => 'required|integer|unique:user_data,nis',   // Cek duplikat NIS
        'class_id' => 'required|exists:classes,id',
        'rayon' => 'required',
        'phone' => 'required|integer',
        'program' => 'required|in:unggulan,reguler',
    ], [
        // Custom pesan error
        'nisn.unique' => 'NISN sudah terdaftar! Silakan gunakan NISN yang berbeda.',
        'nis.unique' => 'NIS sudah terdaftar! Silakan gunakan NIS yang berbeda.',
        'email.unique' => 'Email sudah digunakan! Silakan gunakan email yang berbeda.',
        'nisn.required' => 'NISN wajib diisi.',
        'nis.required' => 'NIS wajib diisi.',
        'nisn.integer' => 'NISN harus berupa angka.',
        'nis.integer' => 'NIS harus berupa angka.',
    ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'level' => 'student',
        ]);

        $user->UserData()->create([
            'nisn' => $request->nisn,
            'nis' => $request->nis,
            'class_id' => $request->class_id,
            'rayon' => $request->rayon,
            'phone' => $request->phone,
            'program' => $request->program,
        ]);
        return redirect()->route('admin.student.index')->with('success', 'Siswa berhasil ditambahkan');

        // ... kode Anda ...
        
    } catch (\Exception $e) {
        dd($e->getMessage()); // Akan menampilkan pesan error
    }
    }

    public function edit(User $user)
    {
        $classes = ClassGrade::all();
        return view('admin.management.student.edit', compact('user', 'classes'));
    }

    public function update(Request $request, User $user)
    {

   $userData = UserData::where('user_id', $user->id)->first();
    
    $request->validate([
        'name' => 'required',
        'email' => 'required|email|unique:users,email,' . $user->id,
        'password' => 'nullable|min:6',
        'nisn' => 'required|integer|unique:user_data,nisn,' . ($userData->id ?? 'NULL'),
        'nis' => 'required|integer|unique:user_data,nis,' . ($userData->id ?? 'NULL'),
        'class_id' => 'required|exists:classes,id',
        'rayon' => 'required',
        'phone' => 'required|regex:/^[0-9]+$/',
        'program' => 'required|in:unggulan,reguler',
    ], [
        'nisn.unique' => 'NISN sudah terdaftar! Silakan gunakan NISN yang berbeda.',
        'nis.unique' => 'NIS sudah terdaftar! Silakan gunakan NIS yang berbeda.',
        'email.unique' => 'Email sudah digunakan! Silakan gunakan email yang berbeda.',
        'nisn.required' => 'NISN wajib diisi.',
        'nis.required' => 'NIS wajib diisi.',
        'nisn.integer' => 'NISN harus berupa angka.',
        'nis.integer' => 'NIS harus berupa angka.',
    ]);
    
    $user->name = $request->name;
    $user->email = $request->email;
    
    if ($request->filled('password')) {
        $user->password = bcrypt($request->password);
    }
    
    $user->level = $request->level ?? 'student'; 
    $user->save();
    

    $spp_id = $request->program == 'unggulan' ? 1 : 2;
    
    if ($userData) {
        $userData->nisn = $request->nisn;
        $userData->nis = $request->nis;
        $userData->class_id = $request->class_id;
        $userData->rayon = $request->rayon;
        $userData->phone = $request->phone;
        $userData->program = $request->program;
        $userData->save();
    } else {
        UserData::create([
            'user_id' => $user->id,
            'nisn' => $request->nisn,
            'nis' => $request->nis,
            'class_id' => $request->class_id,
            'rayon' => $request->rayon,
            'phone' => $request->phone,
            'program' => $request->program,
        ]);
    }
    
    return redirect()->route('admin.student.index')
        ->with('success', 'Data siswa berhasil diupdate');
        
}

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.student.index');
    }
}
