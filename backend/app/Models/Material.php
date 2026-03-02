<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = ['teacher_id', 'subject_id', 'title', 'file_path', 'type'];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
