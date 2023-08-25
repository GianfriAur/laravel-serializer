<?php

namespace Gianfriaur\Serializer\Tests\Service\ArrayEngineTest;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    protected $fillable = [
        'title',
        'content'
    ];

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}