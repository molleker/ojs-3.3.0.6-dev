<?php


namespace App\Exceptions;


use App\AntiplagiatReportStatus;
use Exception;

class AntiplagiatFileUploadException extends \ErrorException
{
    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getFailStatus()
    {
        if ($this->getMessage() === 'NoText') {
            return AntiplagiatReportStatus::NO_TEXT_IN_FILE_FAILED;
        }

        if ($this->getMessage() === 'UnsupportedFormat') {
            return AntiplagiatReportStatus::UNSUPPORTED_FILE_FORMAT;
        }

        if ($this->getMessage() === 'Request Entity Too Large') {
            return AntiplagiatReportStatus::TOO_LARGE_FILE;
        }

        if (str_contains($this->getMessage(), 'повторно попытаться загрузить документ')) {
            return AntiplagiatReportStatus::DOUBLE_UPLOAD_FAILED_FILE;
        }

        if (str_contains($this->getMessage(), 'Данный тип файлов не поддерживается')) {
            return AntiplagiatReportStatus::UNSUPPORTED_FILE_FORMAT;
        }

        if (str_contains($this->getMessage(), 'Could not connect to host')) {
            return AntiplagiatReportStatus::SEVER_UNAVAILABLE;
        }

        if (str_contains($this->getMessage(), 'Failed Sending HTTP SOAP request')) {
            return AntiplagiatReportStatus::SEVER_UNAVAILABLE;
        }

        if (str_contains($this->getMessage(), 'Error Fetching http headers')) {
            return AntiplagiatReportStatus::SEVER_UNAVAILABLE;
        }

        if (str_contains($this->getMessage(), 'Forbidden')) {
            return AntiplagiatReportStatus::SEVER_UNAVAILABLE;
        }

        return AntiplagiatReportStatus::UNKNOWN_FAILED;
    }

    public function repeatable()
    {
        return $this->getFailStatus() === AntiplagiatReportStatus::SEVER_UNAVAILABLE;
    }
}