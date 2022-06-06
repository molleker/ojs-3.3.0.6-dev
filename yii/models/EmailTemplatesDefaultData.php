<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "email_templates_default_data".
 *
 * @property string $email_key
 * @property string $locale
 * @property string $subject
 * @property string $body
 * @property string $description
 */
class EmailTemplatesDefaultData extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'email_templates_default_data';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email_key', 'locale'], 'required'],
            [['body', 'description'], 'string'],
            [['email_key'], 'string', 'max' => 64],
            [['locale'], 'string', 'max' => 5],
            [['subject'], 'string', 'max' => 120],
            [['email_key', 'locale'], 'unique', 'targetAttribute' => ['email_key', 'locale'], 'message' => 'The combination of Email Key and Locale has already been taken.']
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
            'subject' => 'Тема',
            'body' => 'Текст письма',
            'description' => 'Описание',
        ];
    }
}
