<?php


namespace App\Components;


class AntiplagiatApiBlock
{
    private $data;

    /**
     * AntiplagiatApiBlock constructor.
     * @param $data
     * {#243
     * +"Offset": 208
     * +"Length": 139
     * +"SrcHash": 72341212203319914
     * +"Type": 1
     * }
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function offset()
    {
        return $this->data->Offset;
    }

    public function length()
    {
        return $this->data->Length;
    }

}