<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\repository\Stop $model */

$this->title = 'Добавление остановки';
$this->params['breadcrumbs'][] = ['label' => 'Остановки', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stop-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
