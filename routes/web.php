<?php

use App\Http\Controllers\admin\BillController;
use App\Http\Controllers\admin\PaymentController;
use App\Http\Controllers\admin\SPPController;
use App\Http\Controllers\admin\StaffController;
use App\Http\Controllers\admin\StudentController;
use App\Http\Controllers\admin\TicketController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\user\SPPController as StudentSPPController;
use App\Http\Controllers\user\PaymentController as StudentPaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/payment/index', [StudentPaymentController::class, 'index'])->name('student.payment.index');
    Route::get('/bill', [SppController::class, 'billStudent'])->name('student.bill');
    Route::get('/payment', [StudentPaymentController::class, 'riwayatStudent'])->name('student.payment');

    Route::get('/student/bill', [StudentSPPController::class, 'bill'])->name('student.bill');
    Route::post('/spp/generate-bill', [StudentSPPController::class, 'generatebill'])->name('spp.generate');

    Route::get('/payment/{studentSppId}/create', [StudentPaymentController::class, 'create'])->name('payment.create');
    Route::get('/payment/{studentSppId}/riwayat', [StudentPaymentController::class, 'riwayat'])->name('payment.riwayat');

   

    

    Route::middleware('creator')->group(function () {
        Route::get('/bill', [BillController::class, 'index'])->name('bill.index');
        Route::get('/bill/payment/{id}', [PaymentController::class, 'create'])->name('payment.create');
        Route::get('/bill/register/{id}', [BillController::class, 'register'])->name('bill.register');
        Route::post('/bill/register/spp', [BillController::class, 'registerSpp'])->name('bill.register.spp');
        Route::get('/bill/{user}', [BillController::class, 'detail'])->name('bill.detail');
        Route::get('/bill/payment/{id}/detail', [PaymentController::class, 'detail'])->name('payment.detail');
        Route::post('/payment/{id}', [PaymentController::class, 'store'])->name('payment.store');
        Route::get('/bill/register/{id}', [BillController::class, 'register'])->name('staff.bill.register');


        Route::get('/log', [DashboardController::class, 'log'])->name('log');


    });
});


 Route::middleware('auth:sanctum',  config('jetstream.auth_session'),
    'verified','admin',)->group(function () {
        Route::get('/management', [DashboardController::class, 'management'])->name('admin.management');


        Route::get('/management/student', [StudentController::class, 'index'])->name('admin.student.index');
        Route::get('/management/student/create', [StudentController::class, 'create'])->name('admin.student.create');
        Route::post('/management/student', [StudentController::class, 'store'])->name('admin.student.store');
        Route::get('/management/student/{user}/edit', [StudentController::class, 'edit'])->name('admin.student.edit');
        Route::put('/management/student/{user}', [StudentController::class, 'update'])->name('admin.student.update');
        Route::delete('/management/student/{user}', [StudentController::class, 'destroy'])->name('admin.student.destroy');

        Route::get('/management/staff', [StaffController::class, 'index'])->name('admin.staff.index');
        Route::get('/management/staff/create', [StaffController::class, 'create'])->name('admin.staff.create');
        Route::post('/management/staff', [StaffController::class, 'store'])->name('admin.staff.store');
        Route::get('/management/staff/{user}/edit', [StaffController::class, 'edit'])->name('admin.staff.edit');
        Route::put('/management/staff/{user}', [StaffController::class, 'update'])->name('admin.staff.update');
        Route::delete('/management/staff/{user}', [StaffController::class, 'destroy'])->name('admin.staff.destroy');

        Route::get('/management/spp', [SPPController::class, 'index'])->name('admin.spp.index');
        Route::get('/management/spp/create', [SPPController::class, 'create'])->name('admin.spp.create');
        Route::post('/management/spp', [SPPController::class, 'store'])->name('admin.spp.store');
        Route::get('/management/spp/{spp}/edit', [SPPController::class, 'edit'])->name('admin.spp.edit');
        Route::put('/management/spp/{spp}', [SPPController::class, 'update'])->name('admin.spp.update');
        Route::delete('/management/spp/{spp}', [SPPController::class, 'destroy'])->name('admin.spp.destroy');
        Route::patch('/management/spp/{spp}/update-status', [SPPController::class, 'updateStatus'])->name('admin.spp.updateStatus');

         // bill
        Route::post('/bill/generate', [BillController::class, 'generateBill'])->name('admin.bill.generate');
        Route::post('/bill/generate-massal', [BillController::class, 'generateMassal'])->name('admin.bill.generate.massal');
        // Route::get('/bill/register/{id}', [BillController::class, 'register'])->name('admin.bill.register');
        Route::get('/bill/generate/index', [BillController::class, 'generate'])->name('admin.bill.generate.index');

        Route::get('/payment', [PaymentController::class, 'index'])->name('admin.payment.index');

        Route::get('/bill/payment/{id}', [PaymentController::class, 'create'])->name('admin.payment.create');
        
        // payment
        Route::get('/payment/create/{studentSppId}', [BillController::class, 'createPayment'])->name('admin.payment.create');
        Route::post('/payment/bulan', [PaymentController::class, 'bayarPerBulan'])->name('admin.payment.bulan');


        Route::get('/tickets', [TicketController::class, 'index'])->name('admin.ticket.index');
        Route::post('/tickets/update-status/{id}', [TicketController::class, 'updateStatus'])->name('admin.ticket.updateStatus');
    });


    Route::middleware('auth:sanctum',  config('jetstream.auth_session'),
    'verified','staff')->group(function () {
        Route::post('/bill/generate', [BillController::class, 'generateBill'])->name('staff.bill.generate');
        // Route::get('/bill/register/{id}', [BillController::class, 'register'])->name('staff.bill.register');

        Route::get('/bill/payment/{id}', [PaymentController::class, 'create'])->name('staff.payment.create');



    });