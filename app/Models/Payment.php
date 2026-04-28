<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Payment extends Model
{
     use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()                    
            ->logOnlyDirty()          
            ->dontSubmitEmptyLogs();    
    }

    protected $table = 'payments';

    protected $fillable = [
        'user_id',
        'student_spp_id',
        'nominal_bayar',
        'sisa_tagihan',
        'metode_pembayaran',
        'status',
        'keterangan',
        'bukti_pembayaran',
        'dibayar_oleh',
        'tanggal_bayar'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    
}
