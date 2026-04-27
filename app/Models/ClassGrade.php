<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassGrade extends Model
{
    protected $table = 'classes';

    public function UserData()
    {
        return $this->hasMany(UserData::class, 'class_id');
    }

    public function User()
    {
        return $this->hasManyThrough(User::class, UserData::class, 'class_id', 'id', 'id', 'id');
    }
}
