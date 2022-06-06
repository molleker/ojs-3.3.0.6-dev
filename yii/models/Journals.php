<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "journals".
 *
 * @property integer $journal_id
 * @property string $path
 * @property double $seq
 * @property string $primary_locale
 * @property integer $enabled
 */
class Journals extends \yii\db\ActiveRecord
{
    public $data;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'journals';
    }

    public function getSettings()
    {
        return $this->hasMany(JournalSettings::className(), ['journal_id' => 'journal_id']);
    }

    public function getTitleSettings()
    {
        return $this->getSettings()->where(['setting_name' => 'title', 'locale' => 'ru_Ru']);
    }

    public function title()
    {
        return $this->titleSettings[0] ? $this->titleSettings[0]->setting_value : null;
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['seq'], 'number'],
            [['enabled'], 'integer'],
            [['path'], 'string', 'max' => 32],
            [['primary_locale'], 'string', 'max' => 5],
            [['path'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'journal_id' => 'Journal ID',
            'path' => 'Path',
            'seq' => 'Seq',
            'primary_locale' => 'Primary Locale',
            'enabled' => 'Enabled',
        ];
    }
}
