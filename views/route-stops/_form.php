<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\repository\RouteStops $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="route-stops-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'route_id')->textInput() ?>

    <?= $form->field($model, 'stop_id')->textInput() ?>

    <?= $form->field($model, 'stop_number')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
