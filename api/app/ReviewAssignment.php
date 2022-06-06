<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\ReviewAssignment
 *
 * @property int $review_id
 * @property int $submission_id
 * @property int $reviewer_id
 * @property string $competing_interests
 * @property string $regret_message
 * @property bool $recommendation
 * @property string $date_assigned
 * @property string $date_notified
 * @property string $date_confirmed
 * @property string $date_completed
 * @property string $date_acknowledged
 * @property string $date_due
 * @property string $date_response_due
 * @property string $last_modified
 * @property bool $reminder_was_automatic
 * @property bool $declined
 * @property bool $replaced
 * @property bool $cancelled
 * @property int $reviewer_file_id
 * @property string $date_rated
 * @property string $date_reminded
 * @property bool $quality
 * @property int $review_round_id
 * @property bool $stage_id
 * @property bool $review_method
 * @property bool $round
 * @property bool $step
 * @property int $review_form_id
 * @property bool $unconsidered
 * @method static \Illuminate\Database\Query\Builder|\App\ReviewAssignment whereReviewId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ReviewAssignment whereSubmissionId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ReviewAssignment whereReviewerId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ReviewAssignment whereCompetingInterests($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ReviewAssignment whereRegretMessage($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ReviewAssignment whereRecommendation($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ReviewAssignment whereDateAssigned($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ReviewAssignment whereDateNotified($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ReviewAssignment whereDateConfirmed($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ReviewAssignment whereDateCompleted($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ReviewAssignment whereDateAcknowledged($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ReviewAssignment whereDateDue($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ReviewAssignment whereDateResponseDue($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ReviewAssignment whereLastModified($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ReviewAssignment whereReminderWasAutomatic($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ReviewAssignment whereDeclined($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ReviewAssignment whereReplaced($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ReviewAssignment whereCancelled($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ReviewAssignment whereReviewerFileId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ReviewAssignment whereDateRated($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ReviewAssignment whereDateReminded($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ReviewAssignment whereQuality($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ReviewAssignment whereReviewRoundId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ReviewAssignment whereStageId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ReviewAssignment whereReviewMethod($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ReviewAssignment whereRound($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ReviewAssignment whereStep($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ReviewAssignment whereReviewFormId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ReviewAssignment whereUnconsidered($value)
 * @mixin \Eloquent
 */
class ReviewAssignment extends Model
{
    public $timestamps = false;

    protected $dates = [
        'date_completed',
        'date_confirmed'
    ];
}
