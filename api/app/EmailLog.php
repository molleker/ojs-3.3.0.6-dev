<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\EmailLog
 *
 * @property integer $log_id
 * @property integer $assoc_type
 * @property integer $assoc_id
 * @property integer $sender_id
 * @property \Carbon\Carbon $date_sent
 * @property string $ip_address
 * @property integer $event_type
 * @property string $from_address
 * @property string $recipients
 * @property string $cc_recipients
 * @property string $bcc_recipients
 * @property string $subject
 * @property string $body
 * @method static \Illuminate\Database\Query\Builder|\App\EmailLog whereLogId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\EmailLog whereAssocType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\EmailLog whereAssocId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\EmailLog whereSenderId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\EmailLog whereDateSent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\EmailLog whereIpAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\EmailLog whereEventType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\EmailLog whereFromAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\EmailLog whereRecipients($value)
 * @method static \Illuminate\Database\Query\Builder|\App\EmailLog whereCcRecipients($value)
 * @method static \Illuminate\Database\Query\Builder|\App\EmailLog whereBccRecipients($value)
 * @method static \Illuminate\Database\Query\Builder|\App\EmailLog whereSubject($value)
 * @method static \Illuminate\Database\Query\Builder|\App\EmailLog whereBody($value)
 * @mixin \Eloquent
 */
class EmailLog extends Model
{
    protected $table = 'email_log';
    protected $primaryKey = 'log_id';
    public $timestamps = false;

    protected $fillable = [
        'assoc_type',
        'assoc_id',
        'from_address',
        'recipients',
        'subject',
        'body'
    ];

    protected $dates = [
        'date_sent'
    ];
}
