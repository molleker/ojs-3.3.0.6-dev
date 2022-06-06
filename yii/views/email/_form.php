<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $model app\models\EmailTemplatesDefault */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $form = ActiveForm::begin(); ?>
    <div class="col-sm-<?= count($journals) ? '8' : '12'?>">
        <div class="panel panel-default">
            <div class="panel-heading">Описание</div>
            <div class="panel-body">
                <?= $model->data->description ?>
            </div>
        </div>
        <div class="email-templates-default-form">


            <?
            $items = [];
            $itemsTabName = [
                'ru_RU' => 'Русский',
                'en_US' => 'Английский',
            ];
            ?>
            <? foreach ($model->alldata as $one) {
                $items[] = [
                    'label' => $itemsTabName[$one->locale],
                    'content' =>
                        $form->field($one, '[' . $one->locale . ']subject')->textInput() .
                        $form->field($one, '[' . $one->locale . ']body')->textarea(['rows' => 15])
                    ,
                ];
                ?>
            <?php } ?>
            <?php echo Tabs::widget(['items' => $items]); ?>

            <div class="form-group">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-lg btn-success']) ?>
            </div>



        </div>
    </div>
<? if (count($journals)) : ?>
    <div class="col-sm-4">
        <div class="panel panel-default">
            <div class="panel-heading">Журналы, в котрых письмо будет изменено</div>
            <div class="panel-body">
                <? foreach ($journals as $journal) : ?>
                <div class="checkbox">
                    <label>
                        <input checked="checked" name="journal_update[]" type="checkbox" value="<?=$journal->journal_id ?>" >
                        <?=$journal->title() ?>
                    </label>
                </div>
                <? endforeach; ?>
            </div>
        </div>
    </div>
<? endif; ?>
<?php ActiveForm::end(); ?>
