<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{

    use HasFactory;
    
    protected $fillable = [
        'clock_in',
        'clock_out',
        'user_id',
    ];

    protected $casts = [
    'clock_in' => 'datetime',
    'clock_out' => 'datetime',
];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function breakTimes(){
        return $this->hasMany(BreakTime::class);
    }
}