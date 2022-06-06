<?php
namespace app\components;
use Yii;
class checkAuthorization extends \yii\base\Component {
    public function init(){
        parent::init();
        if (!Yii::$app->user->isGuest)
            return;
        $parsedUrl=Yii::$app->urlManager->parseRequest(Yii::$app->request);
        if ($parsedUrl[0]=='login/auth')
            return;

        if ($parsedUrl[0]=='token/get')
            return;

        if (strpos($parsedUrl[0],'gii')!==false && strpos($parsedUrl[0],'gii')==0)
            return;

        Yii::$app->getResponse()->redirect(array('login/auth'));



    }
}