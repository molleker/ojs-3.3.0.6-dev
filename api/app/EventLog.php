<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\EventLog
 *
 * @property integer $log_id
 * @property integer $assoc_type
 * @property integer $assoc_id
 * @property integer $user_id
 * @property string $date_logged
 * @property string $ip_address
 * @property integer $event_type
 * @property string $message
 * @property boolean $is_translated
 * @method static \Illuminate\Database\Query\Builder|\App\EventLog whereLogId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\EventLog whereAssocType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\EventLog whereAssocId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\EventLog whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\EventLog whereDateLogged($value)
 * @method static \Illuminate\Database\Query\Builder|\App\EventLog whereIpAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\EventLog whereEventType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\EventLog whereMessage($value)
 * @method static \Illuminate\Database\Query\Builder|\App\EventLog whereIsTranslated($value)
 * @mixin \Eloquent
 */
class EventLog extends Model
{
    public $fillable = [
        'assoc_type',
        'user_id',
        'date_logged',
        'message',
        'is_translated',
    ];
    protected $table = 'event_log';
    public $timestamps = false;
}
