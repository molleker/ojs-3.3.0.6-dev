<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\EmailTemplatesDefault */

$this->title = 'Редактирование Шаблона : ' . ' ' . $model->email_key;
$this->params['breadcrumbs'] = [['label' => 'Шаблоны писем', 'url' => ['index']]];

?>
<div class="email-templates-default-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'journals' => $journals,
    ]) ?>

</div>
