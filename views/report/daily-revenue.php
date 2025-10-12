<?php
/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var string $startDate */
/** @var string $endDate */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use app\models\repository\Route;

$this->title = 'Средняя ежедневная выручка по маршрутам';
$this->params['breadcrumbs'][] = ['label' => 'Отчеты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="report-daily-revenue">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="card mb-3">
        <div class="card-header"><i class="fas fa-filter"></i> Период</div>
        <div class="card-body">
            <?php $form = ActiveForm::begin([
                'method' => 'get',
                'action' => Url::to(['report/daily-revenue']),
                'options' => ['class' => 'form-inline'],
            ]); ?>
            <div class="form-group mr-2" style="margin-right:12px;">
                <label for="start_date" class="mr-2">С:</label>
                <input type="date" id="start_date" name="start_date" class="form-control" value="<?= Html::encode($startDate) ?>">
            </div>
            <div class="form-group mr-2" style="margin-right:12px;">
                <label for="end_date" class="mr-2">По:</label>
                <input type="date" id="end_date" name="end_date" class="form-control" value="<?= Html::encode($endDate) ?>">
            </div>
            <?= Html::submitButton('<i class="fas fa-search"></i> Показать', ['class' => 'btn btn-primary mr-2']) ?>
            <?= Html::a('<i class="fas fa-undo"></i> Сбросить', ['report/daily-revenue'], ['class' => 'btn btn-outline-secondary mr-2']) ?>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-striped table-bordered'],
        'columns' => array_filter([
            [
                'label' => 'Маршрут',
                'value' => function($row){ return ($row['code'] ?? $row['code']); },
            ],
            [
                'label' => 'Дней с рейсами',
                'attribute' => 'days_count',
                'contentOptions' => ['class' => 'text-center'],
                'headerOptions' => ['width' => '140'],
            ],
            [
                'label' => 'Итого за период',
                'attribute' => 'total_revenue',
                'format' => ['decimal', 0],
                'headerOptions' => ['width' => '180'],
            ],
            [
                'label' => 'Средняя выручка / день',
                'attribute' => 'avg_daily_revenue',
                'format' => ['decimal', 0],
                'headerOptions' => ['width' => '200'],
            ],
        ]),
    ]) ?>
</div>
