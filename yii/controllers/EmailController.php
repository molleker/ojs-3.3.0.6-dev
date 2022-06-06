<?php

namespace app\controllers;

use app\models\EmailTemplatesData;
use app\models\EmailTemplatesDefaultData;
use app\models\Journals;
use Yii;
use app\models\EmailTemplatesDefault;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\components\OJSroles;

/**
 * EmailController implements the CRUD actions for EmailTemplatesDefault model.
 */
class EmailController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all EmailTemplatesDefault models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => EmailTemplatesDefault::find()->where(array('locale' => 'ru_Ru'))->joinWith('data')->orderBy('email_key'),
            'pagination' => [
                'pageSize' => 30,
            ]
        ]);


        $roles = new OJSroles;


        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'roles' => $roles,
        ]);
    }

    /**
     * Displays a single EmailTemplatesDefault model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new EmailTemplatesDefault model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new EmailTemplatesDefault();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->email_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing EmailTemplatesDefault model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $journals = Journals::find()
            ->where(['journal_id' => ArrayHelper::getColumn($model->journalData, 'assoc_id')])
            ->with('titleSettings')
            ->all();

        if (Yii::$app->request->isPost) {
            foreach (Yii::$app->request->post('EmailTemplatesDefaultData') as $locale => $arr) {
                $m = (new EmailTemplatesDefaultData())->find()->where(array('locale' => $locale, 'email_key' => $model->email_key))->one();
                $m->body = $arr['body'];
                $m->subject = $arr['subject'];
                $m->save();
                if (Yii::$app->request->post('journal_update')) {
                    foreach (Yii::$app->request->post('journal_update') as $journal_id) {
                        EmailTemplatesData::updateAll([
                            'body' => $arr['body'],
                            'subject' => $arr['subject'],
                        ], [
                            'assoc_id' => Yii::$app->request->post('journal_update'),
                            'email_key' => $m->email_key,
                            'locale' => $locale,
                        ]);
                    }
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
            'journals' => $journals,
        ]);
    }

    /**
     * Deletes an existing EmailTemplatesDefault model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the EmailTemplatesDefault model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EmailTemplatesDefault the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EmailTemplatesDefault::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
