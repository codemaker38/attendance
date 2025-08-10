<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = ['user_id', 'date', 'clock_in', 'clock_out', 'ip_address'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}