<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "email_templates_data".
 *
 * @property string $email_key
 * @property string $locale
 * @property integer $assoc_type
 * @property integer $assoc_id
 * @property string $subject
 * @property string $body
 */
class EmailTemplatesData extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'email_templates_data';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['assoc_type', 'assoc_id'], 'integer'],
            [['body'], 'string'],
            [['email_key'], 'string', 'max' => 64],
            [['locale'], 'string', 'max' => 5],
            [['subject'], 'string', 'max' => 120],
            [['email_key', 'locale', 'assoc_type', 'assoc_id'], 'unique', 'targetAttribute' => ['email_key', 'locale', 'assoc_type', 'assoc_id'], 'message' => 'The combination of Email Key, Locale, Assoc Type and Assoc ID has already been taken.'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email_key' => 'Email Key',
            'locale' => 'Locale',
            'assoc_type' => 'Assoc Type',
            'assoc_id' => 'Assoc ID',
            'subject' => 'Subject',
            'body' => 'Body',
        ];
    }
}
