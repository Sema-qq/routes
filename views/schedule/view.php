<?php

use app\models\repository\Route;
use app\models\repository\Schedule;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;

/** @var yii\web\View $this */
/** @var app\models\repository\Schedule $model */
/** @var app\models\repository\Schedule[] $scheduleDetails */

$this->title = "Расписание маршрута №{$model->route->code} ({$model->route->getTypeLabels()[$model->route->type]})";
$this->params["breadcrumbs"][] = ["label" => "Расписания", "url" => ["index"]];
$this->params["breadcrumbs"][] = $this->title;
\yii\web\YiiAsset::register($this);

// Вычисляем статистику
$totalStops = count($scheduleDetails);
$completedStops = 0;
$totalBoarded = 0;
foreach ($scheduleDetails as $detail) {
    if ($detail->actual_time) {
        $completedStops++;
    }
    $totalBoarded += $detail->boarded_count;
}
$completionPercent =
    $totalStops > 0 ? round(($completedStops / $totalStops) * 100) : 0;
?>
<div class="schedule-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-info-circle"></i>
                        Информация о расписании
                    </h5>
                </div>
                <div class="card-body">
                    <?= DetailView::widget([
                        "model" => $model,
                        "options" => [
                            "class" => "table table-bordered detail-view",
                        ],
                        "attributes" => [
                            [
                                "attribute" => "date",
                                "value" => function ($model) {
                                    return Yii::$app->formatter->asDate(
                                        $model->date,
                                        Schedule::DATE_FORMAT,
                                    );
                                },
                                "label" => "Дата",
                            ],
                            [
                                "attribute" => "route_id",
                                "value" => function ($model) {
                                    return "№{$model->route->code}";
                                },
                                "label" => "Номер маршрута",
                            ],
                            [
                                "attribute" => "route_id",
                                "value" => function ($model) {
                                    return Route::getTypeLabels()[
                                        $model->route->type
                                    ];
                                },
                                "label" => "Направление",
                            ],
                            [
                                "attribute" => "car_id",
                                "value" => function ($model) {
                                    return $model->car->publicName();
                                },
                                "label" => "Машина",
                            ],
                        ],
                    ]) ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-chart-pie"></i>
                        Статистика
                    </h5>
                </div>
                <div class="card-body text-center">
                    <div class="row">
                        <div class="col-6">
                            <h4 class="text-info mb-0"><?= $totalBoarded ?></h4>
                            <small class="text-muted">Всего вошло</small>
                        </div>
                        <div class="col-6">
                            <h4 class="text-warning mb-0"><?= $totalStops ?></h4>
                            <small class="text-muted">Остановок</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body text-center">
                    <?= Html::a(
                        '<i class="fas fa-trash"></i> Удалить расписание',
                        [
                            "delete",
                            "date" => $model->date,
                            "car_id" => $model->car_id,
                            "route_id" => $model->route_id,
                        ],
                        [
                            "class" => "btn btn-sm btn-danger btn-block",
                            "data" => [
                                "confirm" =>
                                    "Вы точно хотите удалить все расписание с " .
                                    $totalStops .
                                    " остановками?",
                                "method" => "post",
                            ],
                        ],
                    ) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Детализация по остановкам -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">
                <i class="fas fa-map-marker-alt"></i>
                Детализация по остановкам (<?= count($scheduleDetails) ?>)
            </h5>
        </div>
        <div class="card-body">
            <?php $dataProvider = new ArrayDataProvider([
                "allModels" => $scheduleDetails,
                "pagination" => false,
                "sort" => [
                    "attributes" => [
                        "stop_number",
                        "planned_time",
                        "actual_time",
                        "boarded_count",
                    ],
                ],
            ]); ?>

            <?= GridView::widget([
                "dataProvider" => $dataProvider,
                "tableOptions" => [
                    "class" => "table table-striped table-bordered",
                ],
                "columns" => [
                    [
                        "attribute" => "stop_number",
                        "label" => "№",
                        "format" => "raw",
                        "value" => function (Schedule $model) {
                            return $model->stop_number;
                        },
                    ],
                    [
                        "attribute" => "stop_id",
                        "label" => "Название остановки",
                        "value" => function (Schedule $model) {
                            return $model->stop->name;
                        },
                    ],
                    [
                        "attribute" => "planned_time",
                        "label" => "Планируемое время",
                        "format" => "raw",
                        "value" => function (Schedule $model) {
                            if ($model->planned_time) {
                                $time = Yii::$app->formatter->asTime(
                                    $model->planned_time,
                                    "php:H:i",
                                );
                                return "<strong class='text-primary'>{$time}</strong>";
                            }
                            return '<span class="text-muted">—</span>';
                        },
                    ],
                    [
                        "attribute" => "actual_time",
                        "label" => "Фактическое время",
                        "format" => "raw",
                        "value" => function (Schedule $model) {
                            if ($model->actual_time) {
                                $time = Yii::$app->formatter->asTime(
                                    $model->actual_time,
                                    "php:H:i",
                                );
                                $class = "text-success";

                                // Сравниваем с планируемым временем
                                if (
                                    $model->planned_time &&
                                    $model->actual_time > $model->planned_time
                                ) {
                                    $class = "text-danger";
                                    $icon =
                                        '<i class="fas fa-exclamation-triangle" title="Опоздание"></i> ';
                                } else {
                                    $icon =
                                        '<i class="fas fa-check" title="Вовремя"></i> ';
                                }

                                return "<strong class='{$class}'>{$icon}{$time}</strong>";
                            }
                            return '<span class="text-muted">—</span>';
                        },
                    ],
                    [
                        "attribute" => "boarded_count",
                        "label" => "Вошло пассажиров",
                        "format" => "raw",
                        "value" => function (Schedule $model) {
                            return $model->boarded_count;
                        },
                    ],
                    [
                        "class" => "yii\grid\ActionColumn",
                        "header" => "Действия",
                        "template" => "{update}",
                        "buttons" => [
                            "update" => function ($url, Schedule $model, $key) {
                                return Html::a(
                                    'Редактировать',
                                    ["update-stop", "id" => $model->id],
                                    [
                                        "class" => "btn btn-sm btn-outline-primary",
                                        'data-pjax' => '0',
                                    ],
                                );
                            },
                        ],
                    ],
                ],
            ]) ?>
        </div>
    </div>
</div>