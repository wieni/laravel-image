<?php namespace Wieni\Image;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = ['filename'];

    public function owner()
    {
        return $this->morphTo();
    }
}
