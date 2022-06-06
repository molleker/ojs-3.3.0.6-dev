<?php


namespace App\Components;


class AntiplagiatUploadedFileStatus
{

    private $data;

    /**
     * AntiplagiatReportStatus constructor.
     * @param $data
     * {#220
     * +"GetCheckStatusResult": {#210
     * +"DocId": {#227
     * +"Id": 667
     * +"External": null
     * }
     * +"Status": "InProgress"
     * +"FailDetails": null
     * +"Summary": null
     * +"EstimatedWaitTime": 11
     * }
     * }
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function inProgress()
    {
        return $this->data->GetCheckStatusResult->Status === 'InProgress';
    }

    public function waitTime()
    {
        return $this->data->GetCheckStatusResult->EstimatedWaitTime;
    }

    public function isFailed()
    {
        return $this->data->GetCheckStatusResult->Status === 'Failed';
    }

    public function reason()
    {

    }

}