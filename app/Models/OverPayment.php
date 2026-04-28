<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OverPayment extends Model
{
    protected $table = 'overpayments';

    protected $fillable = [
        'student_spp_id',
        'payment_id',
        'nominal',
        'status',
        'nominal_terpakai',
        'tanggal_refund',
        'keterangan',
    ];
}
