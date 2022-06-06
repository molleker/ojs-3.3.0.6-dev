<?php

namespace app\controllers;

use app\models\Sessions;
use app\models\Users;
use Yii;
use yii\web\Controller;
use yii\web\Response;


class TokenController extends Controller
{
    public function actionGet()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $user = Users::findOne(['user_id' => Sessions::userId()]);
        if (! $user->api_token) {
            $user->api_token = Yii::$app->security->generateRandomString();
            $user->save();
        }

        return ['token' => $user->api_token];
    }
}
