<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "article_settings".
 *
 * @property integer $submission_id
 * @property string $locale
 * @property string $setting_name
 * @property string $setting_value
 * @property string $setting_type
 */
class ArticleSettings extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'submission_settings';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['submission_id', 'locale', 'setting_name'], 'required'],
            [['submission_id'], 'integer'],
            [['setting_value'], 'string'],
            [['locale'], 'string', 'max' => 5],
            [['setting_name'], 'string', 'max' => 255],
            [['setting_type'], 'string', 'max' => 6],
            [['submission_id', 'locale', 'setting_name'], 'unique', 'targetAttribute' => ['submission_id', 'locale', 'setting_name'], 'message' => 'The combination of Article ID, Locale and Setting Name has already been taken.'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'submission_id' => 'Article ID',
            'locale' => 'Locale',
            'setting_name' => 'Setting Name',
            'setting_value' => 'Setting Value',
            'setting_type' => 'Setting Type',
        ];
    }
}
