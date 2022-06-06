<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\AntiplagiatReportServiceSource
 *
 * @property integer $id
 * @property integer $antiplagiat_report_service_id
 * @property string $source_hash
 * @property float $score_by_report
 * @property float $score_by_source
 * @property string $author
 * @property string $url
 * @property string $name
 * @method static \Illuminate\Database\Query\Builder|\App\AntiplagiatReportServiceSource whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\AntiplagiatReportServiceSource whereAntiplagiatReportServiceId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\AntiplagiatReportServiceSource whereSourceHash($value)
 * @method static \Illuminate\Database\Query\Builder|\App\AntiplagiatReportServiceSource whereScoreByReport($value)
 * @method static \Illuminate\Database\Query\Builder|\App\AntiplagiatReportServiceSource whereScoreBySource($value)
 * @method static \Illuminate\Database\Query\Builder|\App\AntiplagiatReportServiceSource whereAuthor($value)
 * @method static \Illuminate\Database\Query\Builder|\App\AntiplagiatReportServiceSource whereUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\AntiplagiatReportServiceSource whereName($value)
 * @mixin \Eloquent
 */
class AntiplagiatReportServiceSource extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'source_hash',
        'score_by_report',
        'score_by_source',
        'author',
        'url',
        'name',
    ];
}
