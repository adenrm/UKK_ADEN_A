<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class SppBulan extends Model
{
     use LogsActivity;
       
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()                    
            ->logOnlyDirty()          
            ->dontSubmitEmptyLogs();    
    }
    
    protected $table = 'spp_bulan';
    
    protected $fillable = [
        'student_spp_id',
        'bulan',
        'tahun',
        'nominal',
        'status',
        'tanggal_jatuh_tempo',
        'tanggal_dibayar',
        'sisa_utang'
    ];
    
    protected $casts = [
        'nominal' => 'decimal:2',
        'sisa_utang' => 'decimal:2',
        'tanggal_jatuh_tempo' => 'date',
        'tanggal_dibayar' => 'date'
    ];
    
    public function studentSpp()
    {
        return $this->belongsTo(StudentSpp::class);
    }
    
    public function paymentDetails()
    {
        return $this->hasMany(PaymentDetail::class);
    }
    
    public function getNamaBulanAttribute()
    {
        $bulanNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        return $bulanNames[$this->bulan] ?? 'Unknown';
    }
    
    public function isLunas()
    {
        return $this->status === 'paid';
    }
}
    