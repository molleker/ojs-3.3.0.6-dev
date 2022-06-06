<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "antiplagiat_exception_logs".
 *
 * @property integer $id
 * @property integer $antiplagiat_report_id
 * @property string $exception
 * @property string $message
 * @property string $created_at
 * @property string $updated_at
 */
class AntiplagiatExceptionLogs extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'antiplagiat_exception_logs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['antiplagiat_report_id'], 'required'],
            [['antiplagiat_report_id'], 'integer'],
            [['exception', 'message'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'antiplagiat_report_id' => 'Antiplagiat Report ID',
            'exception' => 'Exception',
            'message' => 'Message',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
