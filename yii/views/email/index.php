<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Шаблоны писем по умолчанию';
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="email-templates-default-index">

    <h1><?= Html::encode($this->title) ?></h1>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layout'=>"{pager}{items}{pager}",
        'tableOptions'=>['class'=>'table table-hover'],

        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

          //  'email_id:email',
            'email_key',


            'data.subject'=>[
                'attribute' => '<Тема>',
                'content' => function($data) { return Html::a($data->data->subject,['email/update','id'=>$data->email_id]); },
            ],

          //  'can_disable',
          //  'can_edit',
            'from_role_id'=>[
                'attribute' => '<Отправитель>',
                //'htmlOptions'=>['style'=>'min-width:200px'],
                'value'=>function($model) use ($roles) {
                    return $roles->getRoleName($model->from_role_id);
                }
            ],

            'to_role_id'=>[
                 'attribute' => '<Получатель>',
      //  'htmlOptions'=>['style'=>'min-width:200px'],
                 'value'=>function($model) use ($roles) {
                     return $roles->getRoleName($model->to_role_id);
                 }
             ],

            ['class' => 'yii\grid\ActionColumn',
                'template' => '{update}',],
        ],
    ]); ?>

</div>
