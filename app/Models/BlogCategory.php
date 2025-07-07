<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class BlogCategory extends Model
{
    use Notifiable,HasFactory;
    protected $table = 'blog_categories';
    protected $guarded =[];
}
