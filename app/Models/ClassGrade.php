<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ClassGrade extends Model
{
     use LogsActivity;
     
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()                    
            ->logOnlyDirty()          
            ->dontSubmitEmptyLogs();    
    }
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
