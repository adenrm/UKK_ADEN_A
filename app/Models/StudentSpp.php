<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class StudentSpp extends Model
{
      use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()                    
            ->logOnlyDirty()          
            ->dontSubmitEmptyLogs();    
    }
    
      protected $table = 'student_spp';
    
    protected $fillable = [
        'user_id',
        'spp_id',
        'tahun_masuk',
        'status'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function spp()
    {
        return $this->belongsTo(Spps::class);
    }
    
    public function sppBulan()
    {
        return $this->hasMany(SppBulan::class);
    }
    
    public function pembayarans()
    {
        return $this->hasMany(Payment::class);
    }
    
    public function overpayments()
    {
        return $this->hasMany(Overpayment::class);
    }
    
    public function getTotalTagihanAttribute()
    {
        return $this->sppBulan()->sum('nominal');
    }
    
    public function getTotalTerbayarAttribute()
    {
        return $this->sppBulan()->where('status', 'paid')->sum('nominal');
    }
    
    public function getSisaTagihanAttribute()
    {
        return $this->total_tagihan - $this->total_terbayar;
    }
}
