<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserData extends Model
{
     protected $fillable = [
        'user_id',
        'nisn',
        'nis',
        'class_id',
        'rayon',
        'phone',
        'spp_id',
        'program'
    ];
    protected $table = 'user_data';

    public function User()
    {
        return $this->belongsTo(User::class);
    }

    public function ClassGrade()
    {
        return $this->belongsTo(ClassGrade::class, 'class_id', 'id');
    }

    public function Spps()
    {
        return $this->belongsTo(Spps::class, 'spp_id', 'id');
    }
}
