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

    protected $casts = [
    'clock_in' => 'datetime',
    'clock_out' => 'datetime',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function attendance(){
        return $this->belongsTo(Attendance::class);
    }

    public function breakCorrections(){
        return $this->hasMany(BreakCorrection::class);
    }

}