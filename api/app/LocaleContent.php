<?php

namespace App;

/**
 * App\LocaleContent
 *
 * @property int $id
 * @property int $fileId
 * @property string $key
 * @property string $content
 * @method static \Illuminate\Database\Query\Builder|\App\LocaleContent whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\LocaleContent whereFileId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\LocaleContent whereKey($value)
 * @method static \Illuminate\Database\Query\Builder|\App\LocaleContent whereContent($value)
 * @mixin \Eloquent
 */
class LocaleContent extends \Eloquent
{
    public $timestamps = false;
    protected $table = 'localeContent';
    protected $fillable = [
        'key',
        'content',
    ];
}
