<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\repository\Stop $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="stop-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php if ($model->hasErrors()): ?>
        <div class="alert alert-danger">
            <?= $form->errorSummary($model); ?>
        </div>
    <?php endif; ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
