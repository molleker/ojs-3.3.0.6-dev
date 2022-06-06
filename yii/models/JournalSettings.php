<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "journal_settings".
 *
 * @property integer $journal_id
 * @property string $locale
 * @property string $setting_name
 * @property string $setting_value
 * @property string $setting_type
 */
class JournalSettings extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'journal_settings';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['journal_id', 'setting_name'], 'required'],
            [['journal_id'], 'integer'],
            [['setting_value'], 'string'],
            [['locale'], 'string', 'max' => 5],
            [['setting_name'], 'string', 'max' => 255],
            [['setting_type'], 'string', 'max' => 6],
            [['journal_id', 'setting_name'], 'unique', 'targetAttribute' => ['journal_id', 'setting_name'], 'message' => 'The combination of Journal ID and Setting Name has already been taken.']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'journal_id' => 'Journal ID',
            'locale' => 'Locale',
            'setting_name' => 'Setting Name',
            'setting_value' => 'Setting Value',
            'setting_type' => 'Setting Type',
        ];
    }
}
