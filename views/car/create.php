<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\repository\Car $model */

$this->title = 'Создание';
$this->params['breadcrumbs'][] = ['label' => 'Маршрутки', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="car-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
