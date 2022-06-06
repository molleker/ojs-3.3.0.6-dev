<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\EmailTemplatesDefault */

$this->title = 'Create Email Templates Default';
$this->params['breadcrumbs'][] = ['label' => 'Email Templates Defaults', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="email-templates-default-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
