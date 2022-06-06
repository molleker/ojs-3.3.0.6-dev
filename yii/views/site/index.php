<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'Административная панель';
?>
<div class="site-index">

    <h1>Административная панель OJS</h1>

    <div class="body-content" style="min-height: 700px;">

        <div class="row">
            <?php foreach (Yii::$app->params['navigationMenu'] as $item) { ?>

                <div class="col-lg-4">
                    <h2><?php if (! isset($item['items'])) { ?>
                            <?php echo(Html::a($item['label'], $item['url'])); ?>
                        <?php } else { ?>
                            <?php echo $item['label']; ?>
                        <?php } ?></h2>
                    <p><?php echo ($item['description']); ?></p>
                    <?php if (isset($item['items'])) { ?>

                        <ul class="list-group">
                            <?php foreach ($item['items'] as $sub) { ?>
                                <li class="list-group-item"><?php echo (Html::a($sub['label'], $sub['url'])) ?></li>
                            <?php } ?>
                        </ul>
                    <?php } ?>
                </div>
                <?php

            }
            ?>

        </div>

    </div>
