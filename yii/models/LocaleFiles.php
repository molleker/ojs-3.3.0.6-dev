<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "localeFiles".
 *
 * @property integer $id
 * @property string $name
 */
class LocaleFiles extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function getSchema(){
        return $this->hasMany(LocaleContent::className(),['fileId'=>'id']);
    }
    public static function tableName()
    {
        return 'localeFiles';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }
}
