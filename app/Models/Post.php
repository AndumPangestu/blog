<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'content'
    ];

    // Relasi ke User (many-to-one)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }

    // Relasi many-to-many dengan kategori
    public function categories()
    {
        return $this->belongsToMany(PostCategory::class, 'post_category_post');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($post) {
            // Menghapus semua media terkait saat Post dihapus
            $post->clearMediaCollection('images');
        });
    }
}
