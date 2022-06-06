<?php

namespace app\models;

use App\AntiplagiatExceptionLog;
use Yii;
use yii\helpers\Html;

/**
 * This is the model class for table "antiplagiat_reports".
 *
 * @property integer $id
 * @property integer $file_id
 * @property integer $status_id
 * @property string $text
 * @property string $created_at
 * @property string $updated_at
 * @property string $link
 * @property double $score
 */
class AntiplagiatReports extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'antiplagiat_reports';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['file_id', 'status_id'], 'required'],
            [['file_id', 'status_id'], 'integer'],
            [['text'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['score'], 'number'],
            [['link'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID отчета',
            'file_id' => 'File ID',
            'status_id' => 'Статус отчета',
            'text' => 'Текст',
            'created_at' => 'Отчет создан',
            'updated_at' => 'Обновлено',
            'link' => 'Ссылка',
            'score' => 'Score',
        ];
    }

    public function getFile()
    {
        return $this->hasOne(ArticleFiles::className(), ['file_id' => 'file_id']);
    }

    public function getExceptions()
    {
        return $this->hasMany(AntiplagiatExceptionLogs::className(), ['antiplagiat_report_id' => 'id']);
    }

    public function getLogs()
    {
        return $this->hasMany(AntiplagiatLogs::className(), ['antiplagiat_report_id' => 'id']);
    }

    public function articleUrl()
    {
        if (! $this->file || ! $this->file->article) {
            return '';
        }

        return '/index.php/' . $this->file->article->journal->path . '/editor/submission/' . $this->file->submission_id;
    }

    public function articleTitle()
    {
        if (! $this->file->article) {
            return '';
        }

        return $this->file->article->titleSetting->setting_value;
    }

    public function statusName()
    {
        $statuses = [
            1 => '1 - Отчет в процессе получения',
            2 => '2 - Отчет готов',
            0 => '0 - Произошла ошибка во время получения отчета',
            3 => '3 - Не найден текст в файле',
            4 => '4 - Нельзя повторно загрузить такой же файл',
            5 => '5 - Формат файла не поддерживается',
            6 => '6 - Файл слишком большой для загрузки на антиплагиат',
            7 => '7 - Сервер перегружен. Попробуйте отправить запрос позднее',
        ];

        return isset($statuses[$this->status_id]) ? $statuses[$this->status_id] : '';
    }

    public function statusColor()
    {
        $failed_statuses = [0, 3, 4, 5, 6, 7];

        if (in_array($this->status_id, $failed_statuses)) {

            return '#d9534f';
        }

        return '#000';
    }
}
