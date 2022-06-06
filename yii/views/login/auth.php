<?php
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
$this->title='Авторизация';

?>
        <h1 class="text-center">Авторизация</h1>
<div class="row justify-content-center">
    <!--<div class="col-md-6 col-md-offset-3">-->
        <?php $form = ActiveForm::begin([
            'id' => 'login-form',
            'options' => ['class' => 'form-horizontal'],
            'fieldConfig' => [
                'template' => "{label}\n<div class=\"col-md-12 \">{input}</div>\n<div class=\" offset-md-5 col-md-3\">{error}</div>",
                'labelOptions' => ['class' => 'col-md-1 offset-md-4 form-control-label'],
            ],
        ]); ?>
        <?= $form->field($model, 'username'); ?>

        <?= $form->field($model, 'password')->passwordInput() ?>

        <?= $form->field($model, 'rememberMe')->checkbox([
            'template' => "<div class=\"offset-md-5 col-md-3 checkbox\"><label>{input}{label}</label></div>\n<div class=\"offset-md-5 col-md-3\">{error}</div>",
            //'labelOptions' => ['class' => 'col-md-1 col-md-offset-4 control-label'],
        ]) ?>

        <div class="form-group">
            <div class="offset-md-5 col-md-3">
                <?= Html::submitButton('Войти', ['class' => 'btn btn-success btn-lg', 'name' => 'login-button']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
   <!-- </div> -->

</div>
