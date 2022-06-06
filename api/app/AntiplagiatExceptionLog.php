<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\AntiplagiatExceptionLog
 *
 * @property int $id
 * @property int $antiplagiat_report_id
 * @property string $exception
 * @property string $message
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\AntiplagiatExceptionLog whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\AntiplagiatExceptionLog whereAntiplagiatReportId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\AntiplagiatExceptionLog whereException($value)
 * @method static \Illuminate\Database\Query\Builder|\App\AntiplagiatExceptionLog whereMessage($value)
 * @method static \Illuminate\Database\Query\Builder|\App\AntiplagiatExceptionLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\AntiplagiatExceptionLog whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AntiplagiatExceptionLog extends Model
{
    protected $fillable = [
        'exception',
        'message',
    ];
}
