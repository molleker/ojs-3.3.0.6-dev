<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\AntiplagiatLog
 *
 * @property int $id
 * @property int $antiplagiat_report_id
 * @property string $method
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $data
 * @method static \Illuminate\Database\Query\Builder|\App\AntiplagiatLog whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\AntiplagiatLog whereAntiplagiatReportId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\AntiplagiatLog whereMethod($value)
 * @method static \Illuminate\Database\Query\Builder|\App\AntiplagiatLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\AntiplagiatLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\AntiplagiatLog whereData($value)
 * @mixin \Eloquent
 */
class AntiplagiatLog extends Model
{
    protected $fillable = [
        'method',
        'antiplagiat_report_id',
        'data'
    ];
}
