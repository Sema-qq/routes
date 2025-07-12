<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\repository\RouteStops $model */

$this->title = 'Create Route Stops';
$this->params['breadcrumbs'][] = ['label' => 'Route Stops', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="route-stops-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
