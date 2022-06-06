<?php

namespace app\controllers;
use app\models\LocaleContent;
use app\models\LocaleFiles;
use Yii;
use yii\base\Exception;
use app\components\EmptyLogger;

class ScriptController extends \yii\web\Controller
{
    public function actionLoadlocale()
    {
        echo "ok";
        Yii::setLogger(new EmptyLogger());
        function tryParseXml($file){

                $xml = @simplexml_load_file($file);

            if ($xml === false) {
                return;
            }
            if ($xml->message && $xml->message[0]['key']) {

                Yii::$app->db->createCommand()->insert('localeFiles', array(
                    'name' => str_replace($_SERVER['DOCUMENT_ROOT'], '', $file),

                ))->execute();
                $fileId= Yii::$app->db->lastInsertID;
                foreach ($xml->message as $message){

                    Yii::$app->db->createCommand()->insert('localeContent', array(
                        'fileId' =>$fileId,
                        'key'=>(string)$message['key'],
                        'content'=>(string)$message,

                    ))->execute();


                }

            }




        }

        function listDir($dir,$callback){
            if ($handle = opendir($dir)) {
                while (false !== ($entry = readdir($handle))) {
                    if ($entry=='.' || $entry=='..')
                        continue;

                    $fullPath=$dir.'/'.$entry;

                    if (is_dir($fullPath))
                        listDir($fullPath,$callback);
                    elseif(substr($fullPath,-3)=='xml' && strpos($fullPath,'ru_RU')!==false && strpos($fullPath,'locale_backup')===false && strpos($fullPath,'localebackup')===false)
                        $callback($fullPath);

                }

            }
        }


        function forceSaveFile($data,$path){
            $pathArr=explode('/',$path);
            $checkPath='/';
            foreach ($pathArr as $piece){
                if (strlen(trim($piece))==0)
                    continue;

                $checkPath.=$piece.'/';
                if (substr($piece,-4)!='.xml')
                if (!file_exists($checkPath)){
                    mkdir($checkPath);
                }
            }

            file_put_contents($path,$data);


        }
        Yii::$app->db->createCommand('TRUNCATE TABLE localeFiles')->execute();
        Yii::$app->db->createCommand('TRUNCATE TABLE localeContent')->execute();

        listDir($_SERVER["DOCUMENT_ROOT"],function($path){
            tryParseXml($path);
        });
        echo 'ok';

    }

    public function actionImprovedb(){
        exit;
        $sql='SELECT lc.key FROM localeContent lc   HAVING (SELECT COUNT(*) FROM localeContent com WHERE com.key = lc.key) > 1 ';
        $data=Yii::$app->db->createCommand($sql)->queryAll();
        foreach ($data as $row) {
            $keys[]=$row['key'];
        }

        $sql="SELECT lc.key , lc.id,lc.content, lf.name FROM localeContent lc LEFT JOIN localeFiles lf ON lf.id=lc.fileId  WHERE `key` IN ('".implode("','",$keys)."') AND lf.name LIKE '%custom%'";
        $data=Yii::$app->db->createCommand($sql)->queryAll();
        foreach ($data as $row){
            echo "ok";
            Yii::$app->db->createCommand()->update('localeContent',array('content'=>$row['content']),'`key`="'.$row['key'].'"')->execute();
        }
    }
    public function actionLoadfromorig(){
        $m=LocaleFiles::find()->all();
        $conn_id = ftp_connect('home.cochrane.ru');

// вход с именем пользователя и паролем
        $login_result = ftp_login($conn_id, 'mediasph_ojs@cochrane.ru', '76UiqkYb3');
        foreach ($m as $file){
            if (ftp_get($conn_id, $_SERVER['DOCUMENT_ROOT'].$file->name, '/www'.$file->name , FTP_BINARY)) {
                echo "Произведена запись в ";
            } else {
                echo "Не удалось завершить операцию $file->name<br>";
            }
        }






// попытка скачать $server_file и сохранить в $local_file


    }
    public function actionDecodedumps(){

        echo "OKJ";

        $files=[
            'part0',

        ];
        foreach ($files as $file){
            $data=file_get_contents($_SERVER['DOCUMENT_ROOT'].'/admin/dumps/'.$file.'.sql');
            file_put_contents($_SERVER['DOCUMENT_ROOT'].'/admin/dumps/'.$file.'-utf-to-win.sql',mb_convert_encoding($data,'windows-1251','UTF-8'));
            file_put_contents($_SERVER['DOCUMENT_ROOT'].'/admin/dumps/'.$file.'-win-to-utf.sql',mb_convert_encoding($data,'UTF-8','windows-1251'));
        }
        echo "OKJ";
    }

    public function actionRebuildlocale(){
        $files= LocaleFiles::find()->with('schema')->all();
        foreach ($files as $file){
            $data=array();
            if (file_exists($_SERVER['DOCUMENT_ROOT'].$file->name)){
                echo $file->name."<br>";

                foreach ($file->schema as $row){
                    $data[$row->key]=$row->content;
                }
                $xml = new \DOMDocument('1.0', 'utf-8');
                $xml->formatOutput = true;
                $xml->preserveWhiteSpace = false;
                $xml->load($_SERVER['DOCUMENT_ROOT'].$file->name);


                $elements = $xml->getElementsByTagName('message');
                foreach ($elements as $element){
                    if (isset($data[$element->getAttribute('key')])){
$element->nodeValue=$data[$element->getAttribute('key')];

                    };
                }


                $xml->save($_SERVER['DOCUMENT_ROOT'].$file->name);

            }

        }
    }


}
