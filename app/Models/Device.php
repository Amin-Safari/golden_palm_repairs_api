<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Notifications\Notifiable;


class Device extends Model
{
    use Notifiable;
    protected $fillable = [
        'customer_id',
        'technician_id',
        'device_category_id',
        'device_name',
        'device_problem',
        'appearance_details',
        'repair_details',
        'prepaid',
        'repair_price',
        'price',
        'status',
        'repair_status',
        'verification_code',
        'received_at',
        'delivered_at',
        'delivered_real_at'
    ];
    public function customer()
    {
        return $this->belongsTo(User::class);
    }
    public function technician()
    {
        return $this->belongsTo(User::class);
    }
    public function device_category()
    {
        return $this->belongsTo(DeviceCategory::class);
    }
}
