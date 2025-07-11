<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GalleryPost extends Model
{
    use HasFactory;

    protected $fillable = ['post_id', 'img'];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
