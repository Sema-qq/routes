<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\repository\User $model */

$this->title = 'Создание Владельца/Водителя';
$this->params['breadcrumbs'][] = ['label' => 'Владельцы/водители', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
