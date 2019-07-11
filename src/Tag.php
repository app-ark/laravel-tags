<?php

namespace Spatie\Tags;

use Spatie\EloquentSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\EloquentSortable\SortableTrait;
use Illuminate\Database\Eloquent\Collection as DbCollection;

class Tag extends Model implements Sortable
{
    use SortableTrait, HasSlug;

    public $guarded = [];

    public function scopeWithType(Builder $query, $type = null)
    {
        if (is_null($type)) {
            return $query;
        }
        return $query->where('type', $type)->ordered();
    }

    public function scopeContaining(Builder $query, $name, $locale = null)
    {
        $locale = isset($locale) ? $locale : app()->getLocale();
        $locale = '"' . $locale . '"';
        return $query->whereRaw("LOWER(JSON_EXTRACT(name, '\$." . $locale . "')) like ?", ['"%' . mb_strtolower($name) . '%"']);
    }
    /**
     * @param array|\ArrayAccess $values
     * @param string|null $type
     * @param string|null $locale
     *
     * @return \Spatie\Tags\Tag|static
     */

     public static function findOrCreate($values, $type = null, $locale = null)
    {
        $tags = collect($values)->map(function ($value) use ($type, $locale) {
            if ($value instanceof Tag) {
                return $value;
            }
            return static::findOrCreateFromString($value, $type, $locale);
        });
        return is_string($values) ? $tags->first() : $tags;
    }

    public static function getWithType($type)
    {
        return static::withType($type)->ordered()->get();
    }

    public static function findFromString($name, $type = null, $locale = null)
    {
        return static::query()->where("name", $name)->where('type', $type)->first();
    }

    public static function findFromStringOfAnyType($name, $locale = null)
    {
        return static::query()->where("name", $name)->first();
    }

    protected static function findOrCreateFromString($name, $type = null, $locale = null)
    {
        $tag = static::findFromString($name, $type, $locale);
        if (!$tag) {
            $tag = static::create(['name' => $name, 'type' => $type]);
        }
        return $tag;
    }
}
