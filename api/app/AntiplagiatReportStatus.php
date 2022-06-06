<?php

namespace App;


/**
 * App\AntiplagiatReportsStatus
 *
 * @property integer $id
 * @property string $title
 * @method static \Illuminate\Database\Query\Builder|\App\AntiplagiatReportStatus whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\AntiplagiatReportStatus whereTitle($value)
 * @mixin \Eloquent
 */
class AntiplagiatReportStatus extends \Eloquent
{
    public $timestamps = false;

    const TREATMENT = 1;
    const READY = 2;
    const UNKNOWN_FAILED = 0;
    const NO_TEXT_IN_FILE_FAILED = 3;
    const DOUBLE_UPLOAD_FAILED_FILE = 4;
    const UNSUPPORTED_FILE_FORMAT = 5;
    const TOO_LARGE_FILE = 6;
    const SEVER_UNAVAILABLE = 7;

    protected $fillable = [
        'id'
    ];

    public function canRetry()
    {
        return (($this->id === self::SEVER_UNAVAILABLE) || ($this->id === self::READY));
    }
}
