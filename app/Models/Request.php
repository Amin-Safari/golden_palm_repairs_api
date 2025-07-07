<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Request extends Model
{
    use HasFactory,Notifiable;
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
