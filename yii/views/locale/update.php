<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\LocaleContent */

$this->title = 'Название с ключом : ' . ' ' . $model->key;
$this->params['breadcrumbs'][] = ['label' => 'Системные названия', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->key];

?>
<div class="locale-content-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
