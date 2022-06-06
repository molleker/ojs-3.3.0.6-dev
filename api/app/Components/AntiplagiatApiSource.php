<?php


namespace App\Components;


class AntiplagiatApiSource
{

    private $data;

    /**
     * AntiplagiatApiSource constructor.
     * @param $data
     * {#239
     * +"SrcHash": 72341212203333969
     * +"DocId": null
     * +"Name": "Огурец обыкновенный"
     * +"Url": "http://ru.wikipedia.org/wiki/Огурец обыкновенный"
     * +"Author": null
     * +"ScoreBySource": 9.591704
     * +"ScoreByReport": 9.591704
     * +"TimeStamp": null
     * }
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function hash()
    {
        return $this->data->SrcHash;
    }

    public function docId()
    {
        return $this->data->docId;
    }

    public function name()
    {
        return $this->data->Name;
    }

    public function url()
    {
        return $this->data->Url;
    }

    public function author()
    {
        return $this->data->Author;
    }

    public function scoreBySource()
    {
        return $this->data->ScoreBySource;
    }

    public function scoreByReport()
    {
        return $this->data->ScoreByReport;
    }

}