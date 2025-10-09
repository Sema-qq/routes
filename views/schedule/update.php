<?php

use app\models\repository\Route;
use app\models\repository\Schedule;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\repository\Schedule $model */

$this->title = "Изменение расписания: " . $model->PublicName() . ", остановка №" . $model->stop_number;
$this->params["breadcrumbs"][] = ["label" => "Расписания", "url" => ["index"]];
$this->params["breadcrumbs"][] = [
    "label" => $model->id,
    "url" => ["view", "id" => $model->id],
];
$this->params["breadcrumbs"][] = "Редактирование";
?>
<div class="schedule-update">

    <h1><?= Html::encode($this->title) ?></h1>


    <div class="schedule-form">
        <?php $form = ActiveForm::begin(); ?>

        <?php if ($model->hasErrors()): ?>
            <div class="alert alert-danger">
                <?= $form->errorSummary($model); ?>
            </div>
        <?php endif; ?>

        <div class="form-group">
            <label><b>Дата</b></label>
            <div class="form-control-plaintext"><?= Html::encode(
                $model->date,
            ) ?></div>
        </div>

        <div class="form-group">
            <label><b>Маршрутка</b></label>
            <div class="form-control-plaintext"><?= Html::encode(
                $model->car ? $model->car->publicName() : $model->car_id,
            ) ?></div>
        </div>

        <div class="form-group">
            <label><b>Маршрут</b></label>
            <div class="form-control-plaintext"><?= Html::encode(
                $model->route
                    ? Route::getTypeLabels()[$model->route->type]
                    : $model->route_id,
            ) ?></div>
        </div>

        <div class="form-group">
            <label><b>Остановка</b></label>
            <div class="form-control-plaintext"><?= Html::encode(
                $model->stop ? $model->stop->name : $model->stop_id,
            ) ?></div>
        </div>

        <div class="form-group">
            <label><b>№ остановки</b></label>
            <div class="form-control-plaintext"><?= Html::encode(
                $model->stop_number,
            ) ?></div>
        </div>

        <?= $form->field($model, "planned_time")->input("time", [
            "value" => Yii::$app->formatter->asDate($model->planned_time, Schedule::TIME_FORMAT),
        ]) ?>

        <?= $form->field($model, "actual_time")->input("time", [
                "value" => Yii::$app->formatter->asDate($model->actual_time, Schedule::TIME_FORMAT),
        ]) ?>

        <?= $form->field($model, "boarded_count")->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton("Сохранить", [
                "class" => "btn btn-success",
            ]) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

</div>
