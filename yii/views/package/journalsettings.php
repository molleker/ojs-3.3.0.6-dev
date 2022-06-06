<?php
/* @var $this yii\web\View */

use \yii\helpers;
use \yii\helpers\Html;

?>
<h1>Копирование настроек</h1>
<?
$jm=$journals[0];
foreach ($journals as $journal) {
    $selectList[$journal->journal_id]=$journal->data->title;
}
$error=Yii::$app->getSession()->getFlash('error');
if ($error){
    ?>
    <div class="alert alert-danger">
        <?=$error ?>
    </div>
<?
}

$success=Yii::$app->getSession()->getFlash('success');

if ($success){
    ?>
    <div class="alert alert-success">
        <?=$success ?>
    </div>
<?
}
?>
<?php $form = yii\bootstrap\ActiveForm::begin([
    'id' => 'copy',
    'options' => [
        'class' => 'form-horizontal form-copy',
        //'onsubmit'=>'if (!confirm("Внимание, настройки из журнала будут удалены и заменены на настройки выбранного")){return false} ',
    ],
    'fieldConfig' => [
        'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
        'labelOptions' => ['class' => 'col-lg-1 control-label'],
    ],
]);
?>
<div class="alert alert-info">
    При копирование настроек настроки одного журнала безвозвартно удаляются и заменяются настройками другого
</div>
<div class="form-group">
    <label>Из</label>
<?=Html::dropDownList('from',Yii::$app->getRequest()->post('from'),$selectList,['class'=>'form-control'])?>
</div>
    <div class="form-group">
        <label>В</label>
        <?=Html::dropDownList('to',Yii::$app->getRequest()->post('to'),$selectList,['class'=>'form-control'])?>
    </div>
    <div class="form-group">
        <button class="btn btn-lg btn-success">Копировать</button>
    </div>
<?
yii\bootstrap\ActiveForm::end();
?>

