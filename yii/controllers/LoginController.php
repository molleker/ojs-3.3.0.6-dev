<?php

namespace app\controllers;
use Yii;
use app\models\LoginForm;
class LoginController extends \yii\web\Controller
{
    public $layout='empty';
    public function actionAuth()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('auth', [
                'model' => $model,
            ]);
        }
       // return $this->render('auth');
    }

}
