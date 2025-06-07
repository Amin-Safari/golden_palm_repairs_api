<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function repairs()
    {
        return $this->hasMany(Repair::class);
    }

    public function descriptions()
    {
        return $this->hasMany(DeviceDescription::class, 'category_id');
    }
}
