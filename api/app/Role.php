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
 * @property int $user_id
 * @property int $role_id
 * @property-read \App\Journal $journal
 * @method static \Illuminate\Database\Query\Builder|\App\Role whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Role whereRoleId($value)
 */
class Role extends Model
{
    const EDITOR_ROLE = 256;

    public $timestamps = false;

    public function journal()
    {
        return $this->belongsTo(Journal::class);
    }
}
