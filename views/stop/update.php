<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\repository\Stop $model */

$this->title = 'Update Stop: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Stops', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="stop-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
