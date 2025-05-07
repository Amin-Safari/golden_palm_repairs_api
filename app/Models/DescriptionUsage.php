<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DescriptionUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_description_id',
        'repair_id',
        'price_at_time'
    ];

    protected $casts = [
        'price_at_time' => 'decimal:2'
    ];

    public function description()
    {
        return $this->belongsTo(CategoryDescription::class, 'category_description_id');
    }

    public function repair()
    {
        return $this->belongsTo(Repair::class);
    }
}
