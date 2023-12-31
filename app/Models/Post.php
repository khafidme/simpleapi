<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    /**
     * Fillable
     * 
     * @var array
     */
    protected $fillable = ['image', 'title', 'content'];

    /**
     * Image
     * 
     * @return attribute
     */
    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn($image) => asset('/storage/posts/'.$image),
        );
    }
}
