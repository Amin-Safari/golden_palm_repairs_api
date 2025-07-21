<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class DeviceCategory extends Model
{
    use HasFactory,Notifiable;
    protected $table = 'device_categories';
    protected $guarded=[];
    public function description(){
        return $this->hasMany(DeviceDescription::class , 'category_id');
    }
}
