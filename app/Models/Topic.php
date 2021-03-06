<?php

namespace Coyote;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Topic extends Model
{
    use SoftDeletes, Sortable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['subject', 'path', 'forum_id', 'is_sticky', 'is_announcement'];

    public function tags()
    {
        return $this->hasMany('Coyote\Topic\Tag')->join('tags', 'tags.id', '=', 'tag_id');
    }
}
