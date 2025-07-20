<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Device extends Model
{
    use HasFactory, Notifiable;
    protected $table = 'devices';
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
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(DeviceCategory::class, 'device_category_id');
    }
}
