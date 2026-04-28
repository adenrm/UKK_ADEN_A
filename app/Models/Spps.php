<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Spps extends Model
{

 use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()                    
            ->logOnlyDirty()          
            ->dontSubmitEmptyLogs();    
    }
    protected $table = 'spps';

    protected $fillable = [
        'tahun_ajaran',
        'nominal_per_bulan',
        'total_bulan',
        'is_active',
        'keterangan'
    ];

     protected $casts = [
        'nominal_per_bulan' => 'decimal:2',
        'total_nominal_tahun' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    // public function UserData()
    // {
    //     return $this->hasMany(UserData::class, 'spp_id', 'id');
    // }

    public function StudentSpp()
    {
        return $this->hasMany(StudentSpp::class);
    }

    public function getNamaTahunAjaranAttribute()
    {
        return str_replace('/', '-', $this->tahun_ajaran);
    }
}
