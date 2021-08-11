<?php

namespace App\Models;

use App\Scopes\TagScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable=[
        'name',
    ];

    public function posts(){
        return $this->morphedByMany(Post::class,'taggable');
    }

    public function videos(){
        return $this->morphedByMany(Video::class,'taggable');
    }

    public static function booted()
    {
        static::addGlobalScope(new TagScope());
    }
}
