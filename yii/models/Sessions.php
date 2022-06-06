<?php

namespace app\models;

class Sessions extends \yii\db\ActiveRecord
{
    public static function userId()
    {
        $session_id = $_COOKIE[parse_ini_file(\Yii::$app->basePath . '/../www/config.inc.php')['session_cookie_name']];
        return self::findOne(['session_id' => $session_id])->user_id;
    }
}
