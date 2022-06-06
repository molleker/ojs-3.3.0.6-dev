<?php
/**
 * Created by PhpStorm.
 * User: Машулик
 * Date: 21.05.2015
 * Time: 22:34
 */

namespace app\components;


use yii\base\Exception;

class FilesManager {
    private $rootDir;
    public function __construct(){
        $this->rootDir=$_SERVER["DOCUMENT_ROOT"];
    }
    public  function cleanFilesInDir($dir){
        if ($handle = opendir($this->rootDir.'/'.$dir)) {

            while (false !== ($entry = readdir($handle))) {
                if (!is_dir($this->rootDir.'/'.$dir.'/'.$entry)){
                     unlink($this->rootDir.'/'.$dir."/$entry");
                }

            }


            closedir($handle);

        
        } else {

            throw new \yii\base\Exception('Не могу открыть папку '.$this->rootDir.'/'.$dir);
        }
    }
}