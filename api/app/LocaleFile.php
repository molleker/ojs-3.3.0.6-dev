<?php

namespace App;

/**
 * App\LocaleFile
 *
 * @property int $id
 * @property string $name
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\LocaleContent[] $content
 * @method static \Illuminate\Database\Query\Builder|\App\LocaleFile whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\LocaleFile whereName($value)
 * @mixin \Eloquent
 */
class LocaleFile extends \Eloquent
{
    public $timestamps = false;
    protected $table = 'localeFiles';

    public function content()
    {
        return $this->hasMany(LocaleContent::class, 'fileId');
    }
}
