<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'description',
        'date',
        'heure',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
