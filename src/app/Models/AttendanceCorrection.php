<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceCorrection extends Model
{
    protected $fillable = [
        'clock_in',
        'clock_out',
        'reason',
        'status',
        'admin_id',
        'user_id',
        'attendance_id',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function attendances(){
        return $this->belongsToMany(Attendance::class);
    }

    public function breakCorrections(){
        return $this->belongsToMany(BreakCorrection::class);
    }
}