<?php

use app\models\repository\Car;
use app\models\repository\Stop;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\repository\Schedule $model */
/** @var yii\widgets\ActiveForm $form */

// Все маршрутки, у которых есть хоть один маршрут
$carItems = ArrayHelper::map(
    Car::withRoutes(),
    'id',
    /**
     * @return string
     * @var Car $car
     */
    function(Car $car) {
        return $car->publicName();
    }
);

$stopItems = ArrayHelper::map(
    Stop::getUsedInRoutes(),
    'id',
    /**
     * @return string
     * @var Stop $stop
     */
    function(Stop $stop) {
        return $stop->name;
    }
);
?>

<div class="schedule-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'date')->textInput() ?>

    <?= $form->field($model, 'car_id')->dropDownList(
        $carItems,
        ['prompt' => 'Выберите автомобиль']
    ) ?>

    <?= $form->field($model, 'route_id')->textInput() ?>

    <?= $form->field($model, 'stop_id')->textInput() ?>

    <?= $form->field($model, 'stop_number')->input('number') ?>

    <?= $form->field($model, 'planned_time')->input('time') ?>

    <?= $form->field($model, 'actual_time')->input('time') ?>

    <?= $form->field($model, 'boarded_count')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
