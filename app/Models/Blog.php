<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['title', 'image', 'description', 'content'])]
class Blog extends Model
{
    public function ratings():HasMany
    {
        return $this->hasMany(BlogRating::class);
    }

    public function comments():HasMany
    {
        return  $this->hasMany(BlogComment::class);
    }

}
