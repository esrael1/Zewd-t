<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = ['teacher_id', 'plan_id', 'payment_screenshot', 'start_date', 'end_date', 'status', 'approved_by'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
