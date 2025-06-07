<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'description_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function description()
    {
        return $this->belongsTo(DeviceDescription::class, 'description_id');
    }
} 