<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\AntiplagiatReportService
 *
 * @property integer $id
 * @property integer $antiplagiat_report_id
 * @property float $score_white
 * @property float $score_black
 * @property string $name
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AntiplagiatReportServiceSource[] $sources
 * @method static \Illuminate\Database\Query\Builder|\App\AntiplagiatReportService whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\AntiplagiatReportService whereAntiplagiatReportId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\AntiplagiatReportService whereScoreWhite($value)
 * @method static \Illuminate\Database\Query\Builder|\App\AntiplagiatReportService whereScoreBlack($value)
 * @method static \Illuminate\Database\Query\Builder|\App\AntiplagiatReportService whereName($value)
 * @mixin \Eloquent
 */
class AntiplagiatReportService extends Model
{
    protected $fillable = [
        'score_white',
        'score_black',
        'name',
    ];

    public $timestamps = false;

    public function sources()
    {
        return $this->hasMany(AntiplagiatReportServiceSource::class);
    }

}
