<?php

use app\models\repository\Car;
use app\models\repository\Route;
use app\models\repository\Stop;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/** @var View $this */
/** @var Route $model */

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


$this->title = 'Добавление маршрута';
$this->params['breadcrumbs'][] = ['label' => 'Маршруты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="route-create">

    <h1><?= Html::encode($this->title) ?></h1>


    <?php if (empty($carItems)): ?>
        <div class="alert alert-warning">
            <b>Нет доступных маршруток для создания нового маршрута.</b><br>
            Возможно, все маршрутки уже привязаны к маршрутам или не подходят по условиям.<br>
            <a href="<?= \yii\helpers\Url::to(['/car/index']) ?>" class="btn btn-sm btn-primary mt-2">
                Перейти к списку маршруток
            </a>
        </div>
    <?php else: ?>

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

    <?php endif; ?>

</div>
