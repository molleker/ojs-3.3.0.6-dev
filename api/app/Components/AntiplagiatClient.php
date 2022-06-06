<?php


namespace App\Components;


use App\AntiplagiatLog;
use App\AntiplagiatReport;
use App\ArticleFile;
use App\Exceptions\AntiplagiatReportStatusFailedException;
use App\Exceptions\AntiplagiatFileUploadException;
use Artisaninweb\SoapWrapper\Extension\SoapService;
use File;
use Log;
use Psy\Exception\ErrorException;
use App\AntiplagiatUploadedFile;

class AntiplagiatClient extends SoapService
{
    protected $name = 'antiplagiat';
    /**
     * @var boolean
     */
    protected $trace = true;

    protected $cache = WSDL_CACHE_NONE;

    public function __construct()
    {
        $this->options([
            'login' => config('antiplagiat.login'),
            'password' => config('antiplagiat.password'),
            'soap_version' => SOAP_1_1,
            'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
        ]);
        $this->wsdl = config('antiplagiat.url');
        return parent::__construct();
    }

    public function getOptions()
    {
        return [
            'login' => config('antiplagiat.login'),
            'password' => config('antiplagiat.password'),
            'soap_version' => SOAP_1_1,
            'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
        ];
    }

    /**
     * @param ArticleFile $article_file
     * @return AntiplagiatUploadedFile
     * @throws AntiplagiatReportStatusFailedException
     */
    public function uploadFile(ArticleFile $article_file, $user_id)
    {

        if (! \File::exists($article_file->fullPath())) {
            throw new ErrorException('Не найден файл ' . $article_file->fullPath());
        }

        try {
            $uploadResult = $this->log($this->client->UploadDocument([
                'data' => [
                    'Data' => \File::get($article_file->fullPath()),
                    'FileName' => $article_file->file_name,
                    'FileType' => '.' . File::extension($article_file->fullPath()),
		    'ExternalUserID' => $user_id
                ]
            ]), $article_file->antiplagiatReport);
        } catch (\Exception $e) {
            throw new AntiplagiatFileUploadException($e->getMessage());
        }
        return AntiplagiatUploadedFile::createFromApi($article_file->antiplagiatReport, $uploadResult);
    }

    private function log($response, AntiplagiatReport $antiplagiat_report)
    {
        $data = json_encode($response);

        $log = AntiplagiatLog::create([
            'method' => debug_backtrace()[1]['function'],
            'antiplagiat_report_id' => $antiplagiat_report->id,
            'data' => '',
        ]);

        if (strlen($data) < 1500) {
            $log->data = $data;
            $log->save();
        } else {
            Log::info('antiplagiat-log-' . $log->id . ": \n" . $data);
        }

        return $response;
    }

    /**
     * @param AntiplagiatUploadedFile $uploaded_file
     * @return AntiplagiatUploadedFileStatus
     */
    public function getStatus(AntiplagiatUploadedFile $uploaded_file)
    {   
        return new AntiplagiatUploadedFileStatus(
            $this->log(
                $this->client->GetCheckStatus(['docId' => $uploaded_file->id()]),
                $uploaded_file->antiplagiatReport
            )
        );
    }

    public function requestReport(AntiplagiatUploadedFile $uploaded_file)
    {
        $this->log(
            $this->client->CheckDocument(['docId' => $uploaded_file->id()]),
            $uploaded_file->antiplagiatReport
        );
    }

    public function getReport(AntiplagiatUploadedFile $uploaded_file)
    {                                      
        return new AntiplagiatApiReport($this->log($this->client->GetReportView([
            'docId' => $uploaded_file->id(),
            'options' =>
                [
                    'FullReport' => true,
                    'NeedText' => true,
                    'NeedStats' => true,
                    'NeedAttributes' => true
                ]
        ]),
            $uploaded_file->antiplagiatReport
        ));
    }
}