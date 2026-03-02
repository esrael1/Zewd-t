<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'academy_name', 
        'bio', 
        'profile_image_path',
        'specialization',
        'phone_number',
        'social_links',
        'status', 
        'approved_by'
    ];

    protected $casts = [
        'social_links' => 'json'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
