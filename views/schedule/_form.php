<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\repository\Schedule $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="schedule-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'date')->textInput() ?>

    <?= $form->field($model, 'car_id')->textInput() ?>

    <?= $form->field($model, 'route_id')->textInput() ?>

    <?= $form->field($model, 'stop_id')->textInput() ?>

    <?= $form->field($model, 'stop_number')->textInput() ?>

    <?= $form->field($model, 'planned_time')->textInput() ?>

    <?= $form->field($model, 'actual_time')->textInput() ?>

    <?= $form->field($model, 'boarded_count')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
