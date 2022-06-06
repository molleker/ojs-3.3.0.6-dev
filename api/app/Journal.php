<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Journal
 *
 * @property integer $journal_id
 * @property string $path
 * @property float $seq
 * @property string $primary_locale
 * @property boolean $enabled
 * @method static \Illuminate\Database\Query\Builder|\App\Journal whereJournalId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Journal wherePath($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Journal whereSeq($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Journal wherePrimaryLocale($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Journal whereEnabled($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\JournalSettings[] $settings
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Article[] $articles
 * @property-read mixed $title
 */
class Journal extends Model
{
    protected $primaryKey = 'journal_id';

    public function articles()
    {
        return $this->hasMany(Article::class);

    }
    public function settings()
    {
        return $this->hasMany(JournalSettings::class);
    }

    /**
     * @param $name
     * @return JournalSettings | null
     */
    public function setting($name)
    {
        $setting = $this->settings->first(function (JournalSettings $journal_setting) use ($name) {
            return $journal_setting->setting_name == $name;
        });

        return $setting ?: null;
    }

    public function getTitleAttribute()
    {
        return $this->setting('title')->setting_value;
    }

}
