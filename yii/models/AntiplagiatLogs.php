<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "antiplagiat_logs".
 *
 * @property integer $id
 * @property integer $antiplagiat_report_id
 * @property string $method
 * @property string $created_at
 * @property string $updated_at
 * @property string $data
 */
class AntiplagiatLogs extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'antiplagiat_logs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['antiplagiat_report_id', 'method', 'data'], 'required'],
            [['antiplagiat_report_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['data'], 'string'],
            [['method'], 'string', 'max' => 255],
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
            'method' => 'Method',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'data' => 'Data',
        ];
    }
}
