<?php

namespace app;

class loginController extends \yii\web\Controller
{
    public function actionAuth()
    {
        return $this->render('auth');
    }

}
