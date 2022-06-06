<?php

namespace app\models;

use Faker\Provider\File;
use Yii;
use app\components\FilesManager;

/**
 * This is the model class for table "localeContent".
 *
 * @property integer $id
 * @property integer $fileId
 * @property string $key
 * @property string $content
 */
class LocaleContent extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */

    public static function tableName()
    {
        return 'localeContent';
    }

    public function getFile(){
        return $this->hasOne(LocaleFiles::className(),['id'=>'fileId']);
    }
    public function getFilename(){
        return $this->file->name;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fileId', 'key', 'content'], 'required'],
            [['fileId'], 'integer'],
            [['content'], 'string'],
            [['key'], 'string', 'max' => 100]
        ];
    }
    public function afterSave($insert, $changedAttributes){


        $cp1251=LocaleContent::find()->where(['id'=>$this->id])->one();

        $file=$_SERVER['DOCUMENT_ROOT'].$this->file->name;
        $xml = new \DOMDocument('1.0', 'utf-8');
        $xml->formatOutput = true;
        $xml->preserveWhiteSpace = false;
        $xml->load($file);


        $elements = $xml->getElementsByTagName('message');
        foreach ($elements as $element){
            if ($element->getAttribute('key')==$this->key){
                $element->nodeValue=$cp1251->content;
            };
        }


        htmlentities($xml->save($file));

        $filesManager=new FilesManager();
        $filesManager->cleanFilesInDir('cache');
        $filesManager->cleanFilesInDir('cache/t_compile');


    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fileId' => 'Файл',
            'key' => 'Ключ',
            'content' => 'Содержимое',
            'file.name'=>'Имя файла',
        ];
    }
}
