<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class UserMessage extends Model
{
    use Notifiable,HasFactory;
    protected $table = 'user_messages';
    protected $guarded = [];
}
