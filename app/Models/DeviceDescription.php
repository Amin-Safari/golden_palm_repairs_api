<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceDescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'price',
        'category_id'
    ];

    public function category()
    {
        return $this->belongsTo(DeviceCategory::class, 'category_id');
    }

    public function devices()
    {
        return $this->hasMany(Device::class, 'description_id');
    }
} 