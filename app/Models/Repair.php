<?php

// app/Models/Repair.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Morilog\Jalali\Jalalian;

class Repair extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'technician_id',
        'device_category_id',
        'device_name',
        'device_problem',
        'appearance_details',
        'repair_details',
        'price',
        'status',
        'verification_code',
        'received_at',
        'delivered_at'
    ];

    protected $casts = [
        'received_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    // روابط
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function category()
    {
        return $this->belongsTo(DeviceCategory::class, 'device_category_id');
    }

    // دسترسی‌های محاسبه شده
    public function getReceivedAtShamsiAttribute()
    {
        return Jalalian::fromDateTime($this->received_at)->format('Y/m/d H:i');
    }

    public function getDeliveredAtShamsiAttribute()
    {
        return $this->delivered_at 
            ? Jalalian::fromDateTime($this->delivered_at)->format('Y/m/d H:i')
            : null;
    }
}
