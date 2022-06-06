<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\AntiplagiatReportBlock
 *
 * @property integer $id
 * @property integer $antiplagiat_report_id
 * @property integer $offset
 * @property integer $length
 * @method static \Illuminate\Database\Query\Builder|\App\AntiplagiatReportBlock whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\AntiplagiatReportBlock whereAntiplagiatReportId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\AntiplagiatReportBlock whereOffset($value)
 * @method static \Illuminate\Database\Query\Builder|\App\AntiplagiatReportBlock whereLength($value)
 * @mixin \Eloquent
 */
class AntiplagiatReportBlock extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'offset',
        'length'
    ];
}
