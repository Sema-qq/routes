<?php

use app\models\repository\CarBrand;
use app\models\repository\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\repository\Car $model */

$this->title = 'Создание';
$this->params['breadcrumbs'][] = ['label' => 'Маршрутки', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Владельцы
$userItems = ArrayHelper::map(User::find()->all(), 'id', 'full_name');

// Водители
$driverItems = ArrayHelper::map(User::getDrivers(), 'id', 'full_name');

// Производители
$brandItems = ArrayHelper::map(CarBrand::find()->all(), 'id', 'name');
?>
<div class="car-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="car-form">

        <?php $form = ActiveForm::begin(); ?>

        <?php if ($model->hasErrors()): ?>
            <div class="alert alert-danger">
                <?= $form->errorSummary($model); ?>
            </div>
        <?php endif; ?>

        <?= $form->field($model, 'fare')->input('number') ?>

        <?= $form->field($model, 'production_year')->input('number', [
            'min' => 1900,
            'max' => date('Y'),
            'placeholder' => 'Год, например, 2022'
        ]) ?>

        <?= $form->field($model, 'owner_id')->dropDownList($userItems, ['prompt' => 'Выберите владельца']) ?>

        <?= $form->field($model, 'driver_id')->dropDownList($driverItems, ['prompt' => 'Выберите водителя']) ?>

        <?= $form->field($model, 'brand_id')->dropDownList($brandItems, ['prompt' => 'Выберите производителя']) ?>

        <?= $form->field($model, 'model')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
