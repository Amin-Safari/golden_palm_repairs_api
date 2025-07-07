<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class DeviceDescription extends Model
{
    use HasFactory,Notifiable;
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
