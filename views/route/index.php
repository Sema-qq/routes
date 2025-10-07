<?php

use app\models\repository\Route;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\repository\RouteSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Маршруты';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="route-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить маршрут', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a(
            "Сбросить фильтры",
            [Yii::$app->controller->id . "/index"],
            ["class" => "btn btn-outline-secondary"],
        ) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'code',
            [
                'attribute' => 'type',
                'value' => function($model) {
                    return Route::getTypeLabels()[$model->type] ?? $model->type;
                },
                'filter' => Route::getTypeLabels(),
            ],
            'created_at',
            'updated_at',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Route $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
