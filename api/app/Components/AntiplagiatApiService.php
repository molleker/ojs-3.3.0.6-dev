<?php


namespace App\Components;


class AntiplagiatApiService
{
    private $data;

    /**
     * AntiplagiatApiService constructor.
     * @param $data
     * {#233
     * +"CheckServiceName": "testapi3"
     * +"CollectionDescription": null
     * +"ScoreByReport": null
     * +"ScoreByCollection": {#234
     * +"Plagiarism": 0.0
     * +"Legal": 0.0
     * }
     * +"CloseDate": "2016-11-05T16:11:38.69103"
     */
    public function __construct($data)
    {

        $this->data = $data;
    }

    public function name()
    {
        return $this->data->CheckServiceName;
    }

    public function whiteScore()
    {
        return $this->data->ScoreByReport->Legal;
    }

    public function blackScore()
    {
        return $this->data->ScoreByReport->Plagiarism;
    }

    /**
     * @return AntiplagiatApiSource[]
     */
    public function sources()
    {
        $sources = collect();
        if (isset($this->data->Sources)) {
            foreach ($this->data->Sources as $source) {
                $sources->push(new AntiplagiatApiSource($source));
            }
        }
        return $sources;
    }
}