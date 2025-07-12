<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\repository\CarSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="car-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'brand') ?>

    <?= $form->field($model, 'fare') ?>

    <?= $form->field($model, 'manufacturer') ?>

    <?= $form->field($model, 'country') ?>

    <?php // echo $form->field($model, 'production_year') ?>

    <?php // echo $form->field($model, 'owner_id') ?>

    <?php // echo $form->field($model, 'driver_id') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
