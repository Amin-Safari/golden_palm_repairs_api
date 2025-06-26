<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceDescription extends Model
{
    protected $table = 'device_descriptions';

    protected $fillable = [
        'description',
        'price',
        'category_id',
    ];
    public function category()
    {
        return $this->belongsTo(DeviceCategory::class);
    }
}
