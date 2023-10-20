<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\BelongsToManyRelationship;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feed extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $appends = ['liked'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }
    
    public function getLikedAttribute(): bool
    {
        return (bool) $this->likes()->where('feed_id', $this->id)
        ->where('user_id', auth()->id())
        ->exists();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
