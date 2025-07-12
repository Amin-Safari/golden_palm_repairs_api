<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Blog extends Model
{
    use Notifiable,HasFactory;
    protected $table = 'blogs';
    protected $guarded= [];
    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }
    public function auther()
    {
        return $this->belongsTo(Author::class, 'author_id');
    }
}
