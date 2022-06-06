<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\localeContentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Системные названия';
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .hightlighted{
        background-color: #528CE0;
    }
</style>
<div class="locale-content-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php /*echo "Hostinfo:".Yii::$app->request->hostInfo; */// echo $this->render('_search', ['model' => $searchModel]); ?>



    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
          //  ['class' => 'yii\grid\SerialColumn'],

          //  'id',

            [
                'attribute'=>'file.name',
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'file.name',
                    array_merge(array(0=>'Любой файл'),$filesArray),
                    ['class' => 'form-control']
                )
            ],
            'key',
            'content:ntext',


            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update}',
            ],
        ],
        'layout'=>'{pager}{items}{pager}'
    ]); ?>

</div>
