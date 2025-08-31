<?php

use app\models\repository\Car;
use app\models\repository\Route;
use app\models\repository\RouteStops;
use app\models\repository\Schedule;
use app\models\repository\Stop;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\repository\ScheduleSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

// Все маршрутки, у которых есть хоть один маршрут
$carItems = ArrayHelper::map(
    Car::withSchedules(),
    "id",
    /**
     * @return string
     * @var Car $car
     */
    function (Car $car) {
        return $car->publicName();
    },
);

$routeNames = ArrayHelper::map(
    Route::withSchedules(),
    "id",
    /**
     * @return string
     * @var Route $route
     */
    function (Route $route) {
        return $route->code;
    },
);

$stopItems = ArrayHelper::map(
    Stop::getUsedInRoutes(),
    "id",
    /**
     * @return string
     * @var Stop $stop
     */
    function (Stop $stop) {
        return $stop->name;
    },
);

$this->title = "Расписания";
$this->params["breadcrumbs"][] = $this->title;
?>
<div class="schedule-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(
            '<i class="fas fa-magic"></i> Добавить',
            ["create"],
            ["class" => "btn btn-success"],
        ) ?>
        <?= Html::a(
            "Сбросить фильтры",
            [Yii::$app->controller->id . "/index"],
            ["class" => "btn btn-outline-secondary"],
        ) ?>
    </p>

    <?php
    // echo $this->render('_search', ['model' => $searchModel]);
    ?>

    <?= GridView::widget([
        "dataProvider" => $dataProvider,
        "filterModel" => $searchModel,
        "columns" => [
            ["class" => "yii\grid\SerialColumn"],

            "id",
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
                "attribute" => "car_id",
                "value" => function (Schedule $model) {
                    return $model->car->publicName();
                },
                "filter" => $carItems,
            ],
            [
                "attribute" => "route_id",
                "value" => function (Schedule $model) {
                    return $model->route->code;
                },
                "filter" => $routeNames,
            ],
            [
                "attribute" => "route_id",
                "value" => function (Schedule $model) {
                    return Route::getTypeLabels()[$model->route->type];
                },
                "filter" => Html::activeDropDownList(
                    $searchModel,
                    "route_type",
                    Route::getTypeLabels(),
                    ["prompt" => "", "class" => "form-control"],
                ),
                "encodeLabel" => false,
            ],
            [
                "attribute" => "stop_id",
                "value" => function ($model) {
                    return $model->stop->name;
                },
                "filter" => $stopItems,
            ],
            [
                "attribute" => "stop_number",
                "filter" => Html::activeDropDownList(
                    $searchModel,
                    "stop_number",
                    RouteStops::getStopNumberList(),
                    ["prompt" => "", "class" => "form-control"],
                ),
            ],
            [
                "attribute" => "planned_time",
                "format" => ["time", Schedule::TIME_FORMAT],
                "label" => "Планируемое<br>время<br>прибытия",
                "encodeLabel" => false,
                "filter" => Html::input(
                    "time",
                    $searchModel->formName() . "[planned_time]",
                    $searchModel->planned_time,
                    ["class" => "form-control"],
                ),
            ],
            [
                "attribute" => "actual_time",
                "format" => ["time", Schedule::TIME_FORMAT],
                "label" => "Фактическое<br>время<br>прибытия",
                "encodeLabel" => false,
                "filter" => Html::input(
                    "time",
                    $searchModel->formName() . "[actual_time]",
                    $searchModel->actual_time,
                    ["class" => "form-control"],
                ),
            ],
            [
                "attribute" => "boarded_count",
                "format" => "raw",
                "label" => "Количество<br>вошедших",
                "encodeLabel" => false,
                "filter" => Html::input(
                    "number",
                    $searchModel->formName() . "[boarded_count]",
                    $searchModel->boarded_count,
                    ["class" => "form-control", "min" => 0],
                ),
            ],
            [
                "class" => ActionColumn::className(),
                "urlCreator" => function (
                    $action,
                    Schedule $model,
                    $key,
                    $index,
                    $column
                ) {
                    return Url::toRoute([$action, "id" => $model->id]);
                },
            ],
        ],
    ]) ?>


</div>
