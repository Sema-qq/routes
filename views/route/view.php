<?php

use app\models\repository\Route;
use app\models\forms\CreateAutomaticRoute;
use yii\helpers\Html;
use yii\web\View;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/** @var View $this */
/** @var Route $model */

$this->title = $model->code;
$this->params["breadcrumbs"][] = ["label" => "Маршруты", "url" => ["index"]];
$this->params["breadcrumbs"][] = $this->title;
YiiAsset::register($this);

$reverseRoute = CreateAutomaticRoute::GetReverseRoute($model);
?>
<div class="route-view">

    <h1>Маршрут <?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(
            "Изменить",
            ["update", "id" => $model->id],
            ["class" => "btn btn-primary"],
        ) ?>
        <?= Html::a(
            "Удалить",
            ["delete", "id" => $model->id],
            [
                "class" => "btn btn-danger",
                "data" => [
                    "confirm" => "Вы уверены, что хотите удалить маршрут?",
                    "method" => "post",
                ],
            ],
        ) ?>
        <?php if ($reverseRoute): ?>
            <?= Html::a(
                Route::getTypeLabels()[$reverseRoute->type] . " маршрут",
                ["view", "id" => $reverseRoute->id],
                ["class" => "btn btn-light"],
            ) ?>
        <?php else: ?>
            <?= Html::a(
                CreateAutomaticRoute::getButtonText($model),
                ["create-automatic", "id" => $model->id],
                [
                    "class" => "btn btn-warning",
                    "data" => [
                        "method" => "post",
                        "confirm" =>
                            "Создать автоматический маршрут на основе текущего?",
                    ],
                ],
            ) ?>
        <?php endif; ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'code',
            [
                'attribute' => 'type',
                'value' => function($model) {
                    return Route::getTypeLabels()[$model->type] ?? $model->type;
                },
            ],
            'created_at',
            'updated_at',
            [
                'label' => 'Остановки маршрута',
                'format' => 'raw',
                'value' => function(Route $model) {
                    $routeStops = $model->getRouteStops()
                        ->orderBy(['stop_number' => SORT_ASC])
                        ->all();

                    $list = [];
                    foreach ($routeStops as $routeStop) {
                        $num = $routeStop->stop_number;
                        $name = Html::encode($routeStop->stop->name ?? '—');
                        $list[] = "{$num}. {$name}";
                    }
                    return $list ? '<ul><li>' . implode('</li><li>', $list) . '</li></ul>' : '—';
                }
            ],
        ],
    ]) ?>

</div>
