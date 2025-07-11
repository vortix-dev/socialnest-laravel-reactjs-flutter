<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'description', 'date', 'heure', 'status'
    ];

    public function gallery()
    {
        return $this->hasMany(GalleryPost::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
