<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\repository\RouteStops $model */

$this->title = 'Update Route Stops: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Route Stops', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="route-stops-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
