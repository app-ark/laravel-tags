<?php

namespace Spatie\Tags;

use Illuminate\Database\Eloquent\Model;

trait HasSlug
{
    public static function bootHasSlug()
    {
        static::saving(function (Model $model) {
        });
    }
    protected function generateSlug($locale)
    {
        $slugger = config('tags.slugger');
        $slugger = $slugger ?: '\\Illuminate\\Support\\Str::slug';
        return call_user_func($slugger, $this->getTranslation('name', $locale));
    }
}
