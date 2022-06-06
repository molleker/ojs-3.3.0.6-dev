<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\LocaleContent */

$this->title = 'Create Locale Content';
$this->params['breadcrumbs'][] = ['label' => 'Locale Contents', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="locale-content-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
