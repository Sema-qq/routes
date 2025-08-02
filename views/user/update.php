<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\repository\User $model */

$this->title = 'Редактирование: ' . $model->full_name;
$this->params['breadcrumbs'][] = ['label' => 'Владельцы/водители', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->full_name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Изменение';
?>
<div class="user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
