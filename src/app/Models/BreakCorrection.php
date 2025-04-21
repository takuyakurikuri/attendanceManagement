<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BreakCorrection extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'break_start',
        'break_end',
        'attendance_correction_id'
    ];
    
    public function attendance_correction(){
        return $this->belongsTo(AttendanceCorrection::class);
    }
}