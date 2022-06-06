<?php

use app\models\AntiplagiatReports;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\AntiplagiatReportsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Отчеты Антиплагиата';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="antiplagiat-reports-index">

    <h1><?php echo(Html::encode($this->title)); ?></h1>

    <?php echo(GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'summary' => '',
        'columns' => [            
            [
                'header' => 'Статус отчета',
                'attribute' => 'status_id',                
                'format' => 'raw',
                'value' => function (AntiplagiatReports $report) {
                    return '<span style="color: ' . $report->statusColor() . '">' . $report->statusName() . '</span>';
                },
            ],
            [
                //'header' => 'ID отчета',
                'attribute' => 'id',
                'format' => 'raw',
                'value' => function (AntiplagiatReports $report) {
                    return Html::a($report->id, ['view', 'id' => $report->id], ['target' => '_blank']);
                },
            ],
            [
                'header' => 'ID статьи',
                'attribute' => 'submission_id',
                'format' => 'raw',
                'value' => function (AntiplagiatReports $report) {
                    return Html::a($report->file->article->submission_id, $report->articleUrl(), ['target' => '_blank']);
                },
            ],
            [
                'header' => 'Название статьи',
                'attribute' => 'article_name',
                'format' => 'raw',
                'value' => function (AntiplagiatReports $report) {
                    return Html::a($report->articleTitle(), $report->articleUrl(), ['target' => '_blank']);
                },
            ],
            [
                'header' => 'Отчет создан',
                'filter' => false,
                'format' => 'raw',
                'value' => function (AntiplagiatReports $report) {
                    return Html::a($report->created_at, ['view', 'id' => $report->id], ['target' => '_blank']);
                },
            ],
            [
                'class' => ActionColumn::className(),
                'visibleButtons' => [
                    'update' => false,
                    'delete' => true,
                ]
            ],
        ],
    ])); ?>
</div>
