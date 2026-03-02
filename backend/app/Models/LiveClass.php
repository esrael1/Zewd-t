<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveClass extends Model
{
    protected $fillable = ['teacher_id', 'grade_id', 'subject_id', 'title', 'meeting_id', 'start_time', 'status'];

    protected $casts = [
        'start_time' => 'datetime',
    ];

    /**
     * Get the dynamic meeting link based on Jitsi URL in config.
     */
    public function getMeetingLinkAttribute()
    {
        $baseUrl = config('services.jitsi.url', 'https://meet.jit.si');
        return rtrim($baseUrl, '/') . '/' . $this->meeting_id;
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
