<?php

namespace App;

use App\Exceptions\AntiplagiatFileUploadException;
use Illuminate\Database\Eloquent\Model;

/**
 * App\AntiplagiatUploadedFile
 *
 * @property int $id
 * @property int $antiplagiat_report_id
 * @property int $uploaded_file_id
 * @property string $data
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\AntiplagiatReport $antiplagiatReport
 * @method static \Illuminate\Database\Query\Builder|\App\AntiplagiatUploadedFile whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\AntiplagiatUploadedFile whereAntiplagiatReportId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\AntiplagiatUploadedFile whereUploadedFileId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\AntiplagiatUploadedFile whereData($value)
 * @method static \Illuminate\Database\Query\Builder|\App\AntiplagiatUploadedFile whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\AntiplagiatUploadedFile whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AntiplagiatUploadedFile extends Model
{
    /**
     * AntiplagiatUploadedFile constructor.
     * @param $data
     * {#211
     * +"UploadDocumentResult": {#212
     * +"Uploaded": array:1 [
     * 0 => {#213
     * +"Id": {#221
     * +"Id": 662
     * +"External": null
     * }
     * +"FileName": "685-3114-1-SM.txt"
     * +"Reason": "NoError"
     * +"FailDetails": null
     * }
     * ]
     * }
     * }
     */

    public $fillable = [
        'uploaded_file_id',
        'antiplagiat_report_id',
        'data',
    ];

    public function antiplagiatReport()
    {
        return $this->belongsTo(AntiplagiatReport::class);
    }

    public static function createFromApi(AntiplagiatReport $antiplagiat_report, $data)
    {
        if (! $data->UploadDocumentResult->Uploaded[0]->Id) {
            throw new AntiplagiatFileUploadException($data->UploadDocumentResult->Uploaded[0]->Reason);
        }

        return self::create([
            'uploaded_file_id' => $data->UploadDocumentResult->Uploaded[0]->Id->Id,
            'antiplagiat_report_id' => $antiplagiat_report->id,
            'data' => json_encode($data),
        ]);
    }

    public function id()
    {
        return json_decode($this->data)->UploadDocumentResult->Uploaded[0]->Id;
    }
}
