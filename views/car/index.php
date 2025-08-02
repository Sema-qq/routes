<?php

use app\models\repository\Car;
use app\models\repository\CarBrand;
use app\models\repository\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\repository\CarSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Маршрутки';
$this->params['breadcrumbs'][] = $this->title;

// Владельцы
$ownerItems = ArrayHelper::map(
    User::find()
        ->innerJoin('cars', 'cars.owner_id = users.id')
        ->distinct()
        ->all(),
    'id',
    'full_name'
);

// Водители
$driverItems = ArrayHelper::map(
    User::find()
        ->innerJoin('cars', 'cars.driver_id = users.id')
        ->distinct()
        ->all(),
    'id',
    'full_name'
);

// Производители
$brandItems = ArrayHelper::map(CarBrand::find()->all(), 'id', 'name');
?>
<div class="car-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить', ['create'], ['class' => 'btn btn-success']) ?>
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
            'fare',
            [
                'attribute' => 'owner_id',
                'value' => function ($model) {
                    return $model->owner->full_name ?? null;
                },
                'filter' => $ownerItems, // выпадающий список
            ],
            [
                'attribute' => 'driver_id',
                'value' => function ($model) {
                    return $model->driver->full_name ?? null;
                },
                'filter' => $driverItems,
            ],
            'production_year',
            'model',
            [
                'attribute' => 'brand_id',
                'value' => function ($model) {
                    return $model->brand->name ?? null;
                },
                'filter' => $brandItems,
            ],
            'created_at',
            'updated_at',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Car $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
