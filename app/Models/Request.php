<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    protected $table = 'Request';

    protected $fillable = [
        'title',
        'request',
        'technician_id',
        'status'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
