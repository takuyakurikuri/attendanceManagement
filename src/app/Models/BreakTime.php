<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BreakTime extends Model
{
    use HasFactory;
    protected $fillable = [
        'break_start',
        'break_end',
        'attendance_id',
    ];

    protected $casts = [
    'break_start' => 'datetime',
    'break_end' => 'datetime',
];

    public function attendance(){
        return $this->belongsTo(Attendance::class);
    }
}