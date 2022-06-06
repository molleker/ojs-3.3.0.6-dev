<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\EditDecision
 *
 * @property int $edit_decision_id
 * @property int $article_id
 * @property bool $round
 * @property int $editor_id
 * @property bool $decision
 * @property string $date_decided
 * @method static \Illuminate\Database\Query\Builder|\App\EditDecision whereEditDecisionId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\EditDecision whereArticleId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\EditDecision whereRound($value)
 * @method static \Illuminate\Database\Query\Builder|\App\EditDecision whereEditorId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\EditDecision whereDecision($value)
 * @method static \Illuminate\Database\Query\Builder|\App\EditDecision whereDateDecided($value)
 * @mixin \Eloquent
 */
class EditDecision extends Model
{
    public $timestamps = false;
    const REJECT = 4;
    const ACCEPT = 1;
    protected $dates = [
        'date_decided'
    ];

    public function isFinally()
    {
        return in_array($this->decision, [self::REJECT, self::ACCEPT]);
    }
}
