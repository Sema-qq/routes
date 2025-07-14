<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\repository\Route $model */

$this->title = 'Добавление маршрута';
$this->params['breadcrumbs'][] = ['label' => 'Маршруты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="route-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
