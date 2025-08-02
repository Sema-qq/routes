<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\repository\Car $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Маршрутки', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);


?>
<div class="car-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены, что хотите удалить маршрутку?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'fare',
            'production_year',
            [
                'attribute' => 'owner_id',
                'value' => function ($model) {
                    return $model->owner->full_name;
                }
            ],
            [
                'attribute' => 'driver_id',
                'value' => function ($model) {
                    return $model->driver->full_name;
                }
            ],
            'created_at',
            'updated_at',
            [
                'attribute' => 'brand_id',
                'value' => function ($model) {
                    return $model->brand->name;
                }
            ],
            'model',
        ],
    ]) ?>

</div>
