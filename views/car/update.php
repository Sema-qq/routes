<?php

use app\models\repository\CarBrand;
use app\models\repository\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\repository\Car $model */

$this->title = 'Редактирование маршрутки №' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Cars', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';

// Владельцы
$userItems = ArrayHelper::map(User::find()->all(), 'id', 'full_name');

// Водители
$driverItems = ArrayHelper::map(User::getDrivers(), 'id', 'full_name');
?>
<div class="car-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="car-form">

        <?php $form = ActiveForm::begin(); ?>

        <?php if ($model->hasErrors()): ?>
            <div class="alert alert-danger">
                <?= $form->errorSummary($model); ?>
            </div>
        <?php endif; ?>

        <?= $form->field($model, 'fare')->input('number') ?>

        <?= $form->field($model, 'owner_id')->dropDownList($userItems, ['prompt' => 'Выберите владельца']) ?>

        <?= $form->field($model, 'driver_id')->dropDownList($driverItems, ['prompt' => 'Выберите водителя']) ?>

        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
