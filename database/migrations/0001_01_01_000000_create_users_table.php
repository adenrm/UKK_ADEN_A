<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('name');
            $table->string('password');
            $table->enum('level', ['admin', 'staff', 'student'])->default('student');
            $table->rememberToken();
            $table->foreignId('current_team_id')->nullable();
            $table->string('profile_photo_path', 2048)->nullable();
            $table->timestamps();
        });

        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('major');
            $table->timestamps();
        });

        Schema::create('spps', function (Blueprint $table) {
            $table->id();
            $table->string('tahun_ajaran', 9); // '2024/2025'
            $table->decimal('nominal_per_bulan', 10, 2);
            $table->integer('total_bulan')->default(12);
            $table->decimal('total_nominal_tahun', 10, 2)->virtualAs('nominal_per_bulan * total_bulan');
            $table->boolean('is_active')->default(true);
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            $table->index('tahun_ajaran');
            $table->index('is_active');
        });

         Schema::create('student_spp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('spp_id')->constrained('spps')->onDelete('restrict');
            $table->year('tahun_masuk');
            $table->enum('status', ['active', 'inactive', 'lulus', 'keluar'])->default('active');
            $table->timestamps();
            
            $table->unique(['user_id', 'spp_id']);
            $table->index('status');
            $table->index('tahun_masuk');
        });

         Schema::create('spp_bulan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_spp_id')->constrained('student_spp')->onDelete('cascade');
            $table->tinyInteger('bulan'); // 1-12
            $table->year('tahun');
            $table->decimal('nominal', 10, 2);
            $table->enum('status', ['unpaid', 'paid', 'partial', 'overpaid'])->default('unpaid');
            $table->date('tanggal_jatuh_tempo');
            $table->date('tanggal_dibayar')->nullable();
            $table->decimal('sisa_utang', 10, 2)->default(0);
            $table->timestamps();
            
            $table->unique(['student_spp_id', 'bulan', 'tahun'], 'unique_student_bulan_tahun');
            $table->index(['status', 'bulan']);
            $table->index('tanggal_jatuh_tempo');
        });

        Schema::create('user_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('nisn')->unique()->nullable()->length(10);
            $table->integer('nis')->unique()->nullable()->length(8);
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->string('rayon')->nullable();
            $table->integer('phone')->nullable()->length(20);
            $table->enum('program', ['unggulan', 'reguler']);
            $table->timestamps();
        });

         Schema::create('payments', function (Blueprint $table) {
             $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('student_spp_id')->constrained('student_spp')->onDelete('cascade');
            $table->decimal('nominal_bayar', 10, 2);
            $table->decimal('sisa_tagihan', 10, 2)->default(0);
            $table->enum('metode_pembayaran', ['tunai', 'transfer', 'virtual_account', 'qris'])->default('tunai');
            $table->enum('status', ['pending', 'success', 'failed'])->default('success');
            $table->text('keterangan')->nullable();
            $table->string('bukti_pembayaran')->nullable();
            $table->string('dibayar_oleh', 100)->nullable();
            $table->timestamp('tanggal_bayar')->useCurrent();
            $table->timestamps();
            
            $table->index('tanggal_bayar');
            $table->index('status');
            $table->index('metode_pembayaran');
        });

        Schema::create('payment_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('payments')->onDelete('cascade');
            $table->foreignId('spp_bulan_id')->constrained('spp_bulan')->onDelete('cascade');
            $table->decimal('nominal_dibayar', 10, 2);
            $table->timestamps();
            
            $table->unique(['payment_id', 'spp_bulan_id'], 'unique_payment_bulan');
        });

        Schema::create('overpayments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_spp_id')->constrained('student_spp')->onDelete('cascade');
            $table->foreignId('payment_id')->constrained('payments')->onDelete('cascade');
            $table->decimal('nominal', 10, 2);
            $table->enum('status', ['deposit', 'refunded', 'used'])->default('deposit');
            $table->decimal('nominal_terpakai', 10, 2)->default(0);
            $table->date('tanggal_refund')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            $table->index('status');
        });

         Schema::create('spp_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('student_spp_id')->nullable()->constrained('student_spp')->onDelete('set null');
            $table->string('aksi');
            $table->decimal('nominal_sebelum', 10, 2)->nullable();
            $table->decimal('nominal_sesudah', 10, 2)->nullable();
            $table->text('keterangan')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            
            $table->index('created_at');
            $table->index('aksi');
        });

        // Schema::create('savings', function (Blueprint $table) {
        //     $table->id('savings_id');
        //     $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        //     $table->integer('amount')->nullable();
        //     $table->timestamps();
        // });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staffs');
        Schema::dropIfExists('class');
        Schema::dropIfExists('spps');
        Schema::dropIfExists('users');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('user_data');
    }
};
