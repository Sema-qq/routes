<?php

use app\models\repository\Route;
use app\models\repository\Schedule;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\repository\Schedule $model */

$this->title = $model->PublicName();
$this->params['breadcrumbs'][] = ['label' => 'Расписания', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="schedule-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы точно хотите удалить расписание?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'date',
                'value' => function ($model) {
                    return Yii::$app->formatter->asDate($model->date, Schedule::DATE_FORMAT);
                }
            ],
            [
                'attribute' => 'car_id',
                'value' => function ($model) {
                    return $model->car->publicName();
                }
            ],
            [
                'attribute' => 'route_id',
                'value' => function ($model) {
                    return Route::getTypeLabels()[$model->route->type];
                }
            ],
            [
                'attribute' => 'stop_id',
                'value' => function ($model) {
                    return $model->stop->name;
                }
            ],
            'stop_number',
            [
                'attribute' => 'planned_time',
                'value' => function ($model) {
                    return Yii::$app->formatter->asDate($model->planned_time, Schedule::TIME_FORMAT);
                }
            ],
            [
                'attribute' => 'actual_time',
                'value' => function ($model) {
                    return Yii::$app->formatter->asDate($model->actual_time, Schedule::TIME_FORMAT);
                }
            ],
            'boarded_count',
        ],
    ]) ?>

</div>
