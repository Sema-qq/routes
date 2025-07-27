<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\repository\Schedule $model */

$this->title = 'Добавление расписания';
$this->params['breadcrumbs'][] = ['label' => 'Расписания', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="schedule-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
