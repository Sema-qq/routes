<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\repository\ScheduleStopsSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="schedule-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'date') ?>

    <?= $form->field($model, 'car_id') ?>

    <?= $form->field($model, 'route_id') ?>

    <?= $form->field($model, 'stop_id') ?>

    <?php // echo $form->field($model, 'stop_number') ?>

    <?php // echo $form->field($model, 'planned_time') ?>

    <?php // echo $form->field($model, 'actual_time') ?>

    <?php // echo $form->field($model, 'boarded_count') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
