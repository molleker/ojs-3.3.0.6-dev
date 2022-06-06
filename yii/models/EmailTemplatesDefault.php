<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "email_templates_default".
 *
 * @property integer $email_id
 * @property string $email_key
 * @property integer $can_disable
 * @property integer $can_edit
 * @property integer $from_role_id
 * @property integer $to_role_id
 */
class EmailTemplatesDefault extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'email_templates_default';
    }

    public function getData()
    {
        return $this->hasOne(EmailTemplatesDefaultData::className(), ['email_key' => 'email_key'])->onCondition(EmailTemplatesDefaultData::tableName() . '.locale="ru_Ru"');
    }

    public function getAlldata()
    {
        return $this->hasMany(EmailTemplatesDefaultData::className(), ['email_key' => 'email_key'])->orderBy('locale DESC');
    }

    public function getJournalData()
    {
        return $this->hasMany(EmailTemplatesData::className(), ['email_key' => 'email_key']);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['can_disable', 'can_edit', 'from_role_id', 'to_role_id'], 'integer'],
            [['email_key'], 'string', 'max' => 64]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email_id' => 'Email ID',
            'email_key' => 'Код шаблона',
            'can_disable' => 'Can Disable',
            'can_edit' => 'Can Edit',
            'from_role_id' => 'From Role ID',
            'to_role_id' => 'To Role ID',
        ];
    }
}
