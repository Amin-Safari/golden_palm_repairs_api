<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryDescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_category_id',
        'description',
        'price',
        'is_visible_to_owner',
        'is_visible_to_technician'
    ];

    protected $casts = [
        'is_visible_to_owner' => 'boolean',
        'is_visible_to_technician' => 'boolean',
        'price' => 'decimal:2'
    ];

    public function category()
    {
        return $this->belongsTo(DeviceCategory::class, 'device_category_id');
    }

    public function usages()
    {
        return $this->hasMany(DescriptionUsage::class);
    }

    public function getTotalUsageCount()
    {
        return $this->usages()->count();
    }

    public function getTotalRevenue()
    {
        return $this->usages()->sum('price_at_time');
    }
}
