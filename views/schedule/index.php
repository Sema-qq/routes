<?php

use app\models\repository\Car;
use app\models\repository\Route;
use app\models\repository\Schedule;
use app\models\repository\ScheduleGroup;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\repository\ScheduleGroupSearch $searchModel */
/** @var yii\data\ArrayDataProvider $dataProvider */

// Все машины, у которых есть расписания
$carItems = ArrayHelper::map(
    Car::find()->joinWith("schedules")->groupBy("cars.id")->all(),
    "id",
    function (Car $car) {
        return $car->publicName();
    },
);

// Все маршруты, у которых есть расписания
$routeItems = ArrayHelper::map(
    Route::find()->joinWith("schedules")->groupBy("routes.id")->all(),
    "id",
    function (Route $route) {
        return "№{$route->code} ({$route->getTypeLabels()[$route->type]})";
    },
);

$this->title = "Расписания маршрутов";
$this->params["breadcrumbs"][] = $this->title;
?>
<div class="schedule-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(
            '<i class="fas fa-plus"></i> Создать расписание',
            ["create"],
            ["class" => "btn btn-success"],
        ) ?>
        <?= Html::a(
            "Сбросить фильтры",
            [Yii::$app->controller->id . "/index"],
            ["class" => "btn btn-outline-secondary"],
        ) ?>
    </p>

    <?= GridView::widget([
        "dataProvider" => $dataProvider,
        "filterModel" => $searchModel,
        "tableOptions" => ["class" => "table table-striped table-bordered"],
        "columns" => [
            ["class" => "yii\grid\SerialColumn"],

            [
                "attribute" => "date",
                "format" => ["date", Schedule::DATE_FORMAT],
                "filter" => Html::input(
                    "date",
                    $searchModel->formName() . "[date]",
                    $searchModel->date,
                    ["class" => "form-control"],
                ),
            ],
            [
                "attribute" => "route_code",
                "label" => "Маршрут",
                "value" => function (ScheduleGroup $model) {
                    return $model->route_code;
                },
                "filter" => Html::textInput(
                    $searchModel->formName() . "[route_code]",
                    $searchModel->route_code,
                    [
                        "class" => "form-control",
                        "placeholder" => "Номер маршрута",
                    ],
                ),
            ],
            [
                "attribute" => "route_type",
                "label" => "Направление",
                "value" => function (ScheduleGroup $model) {
                    return $model->route_direction_label;
                },
                "filter" => Html::activeDropDownList(
                    $searchModel,
                    "route_type",
                    Route::getTypeLabels(),
                    ["prompt" => "Все", "class" => "form-control"],
                ),
            ],
            [
                "attribute" => "car_id",
                "label" => "Машина",
                "value" => function (ScheduleGroup $model) {
                    return $model->car_name;
                },
                "filter" => Html::activeDropDownList(
                    $searchModel,
                    "car_id",
                    $carItems,
                    ["prompt" => "Все", "class" => "form-control"],
                ),
            ],
            [
                "label" => "Время работы",
                "format" => "raw",
                "value" => function (ScheduleGroup $model) {
                    $first = $model->first_stop_time
                        ? Yii::$app->formatter->asTime(
                            $model->first_stop_time,
                            "php:H:i",
                        )
                        : "—";
                    $last = $model->last_stop_time
                        ? Yii::$app->formatter->asTime(
                            $model->last_stop_time,
                            "php:H:i",
                        )
                        : "—";
                    return "<small class='text-muted'>с</small> <strong>{$first}</strong><br><small class='text-muted'>по</small> <strong>{$last}</strong>";
                },
            ],
            [
                "label" => "Пассажиры",
                "format" => "raw",
                "value" => function (ScheduleGroup $model) {
                    if ($model->total_boarded > 0) {
                        return $model->total_boarded;
                    }
                    return "—";
                },
            ],
            [
                'class' => ActionColumn::className(),
                'template' => '{view} {delete}', // update исключён
                'urlCreator' => function ($action, ScheduleGroup $model, $key, $index, $column) {
                    return Url::toRoute([
                        $action,
                        "date" => $model->date,
                        "car_id" => $model->car_id,
                        "route_id" => $model->route_id,
                    ]);
                }
            ],
        ],
    ]) ?>

</div>
