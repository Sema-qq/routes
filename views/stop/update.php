<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\repository\Stop $model */

$this->title = 'Изменение остановки: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Остановки', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="stop-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
