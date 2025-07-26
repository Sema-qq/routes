<?php

use app\models\repository\Car;
use app\models\repository\Route;
use app\models\repository\Stop;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\repository\Route $model */
/** @var yii\widgets\ActiveForm $form */

// Все доступные маршрутки
$carItems = ArrayHelper::map(
    Car::find()->availableYear()->withLessThanTwoRoutes()->all(),
    'id',
    /**
     * @return string
     * @var Car $car
     */
    function(Car $car) {
        return $car->publicName();
    }
);

// Все доступные остановки
$stopItems = ArrayHelper::map(Stop::find()->all(), 'id', 'name');

// Все доступные остановки
$stopItems = ArrayHelper::map(Stop::find()->all(), 'id', 'name');
?>

<div class="route-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'car_id')->dropDownList(
        $carItems,
        ['prompt' => 'Выберите автомобиль']
    ) ?>

    <?= $form->field($model, 'type')->dropDownList(
        Route::getTypeLabels(),
        ['prompt' => 'Выберите тип маршрута']
    ) ?>

    <div class="stops-block">
        <label><b>Остановки по порядку следования:</b></label>
        <div class="row">
            <?php for ($i = 1; $i <= 10; $i++): ?>
                <div class="col-md-6">
                    <?= $form->field($model, "stop_ids[$i]")
                        ->dropDownList($stopItems, ['prompt' => ""])
                        ->label("Остановка №$i"); ?>
                </div>
            <?php endfor; ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
