<?php

use app\models\repository\Schedule;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;

/** @var yii\web\View $this */
/** @var app\models\ScheduleCreateForm $model */

$this->title = "Создание расписания - Выбор даты";
$this->registerCssFile("@web/css/create.css");
$this->params["breadcrumbs"][] = ["label" => "Расписание", "url" => ["index"]];
$this->params["breadcrumbs"][] = $this->title;
?>

<div class="schedule-wizard-start">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-plus"></i>
                        Создание расписания поездки
                    </h3>
                </div>
                <div class="card-body">
                    <div class="wizard-progress mb-4">
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            <small class="text-muted">Выбор даты</small>
                            <small class="text-muted">Маршрутка</small>
                            <small class="text-muted">Маршрут</small>
                            <small class="text-muted">Остановка</small>
                            <small class="text-muted">Данные</small>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Добро пожаловать в мастер создания расписания!</strong><br>
                        Этот мастер поможет вам пошагово создать расписание поездки для маршрутки.
                        Начните с выбора даты, для которой хотите создать расписание.
                    </div>

                    <?php $form = ActiveForm::begin([
                        "id" => "create-start-form",
                        "options" => ["class" => "form-horizontal"],
                        "fieldConfig" => [
                            "template" =>
                                '{label}<div class="col-sm-8">{input}{error}</div>',
                            "labelOptions" => [
                                "class" => "col-sm-4 control-label",
                            ],
                        ],
                    ]); ?>

                    <div class="form-group row">
                        <div class="col-sm-8 offset-sm-4">
                            <h4>Шаг 1: Выберите дату</h4>
                            <p class="text-muted">
                                Выберите дату, для которой хотите создать расписание.
                                Будут показаны только доступные маршрутки и маршруты.
                            </p>
                        </div>
                    </div>

                    <?= $form->field($model, "date")->input("date", [
                        "class" => "form-control",
                        "required" => true,
                        "format" => Schedule::DATE_FORMAT,
                    ]) ?>

                    <div class="form-group row">
                        <div class="col-sm-8 offset-sm-4">
                            <div class="btn-group">
                                <?= Html::submitButton(
                                    '<i class="fas fa-arrow-right"></i> Начать создание',
                                    [
                                        "class" => "btn btn-primary",
                                        "id" => "start-create-btn",
                                    ],
                                ) ?>

                                <?= Html::a(
                                    '<i class="fas fa-times"></i> Отмена',
                                    ["index"],
                                    ["class" => "btn btn-secondary"],
                                ) ?>
                            </div>
                        </div>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-question-circle"></i>
                        Справка
                    </h5>
                </div>
                <div class="card-body">
                    <h6>Как работает мастер:</h6>
                    <ol class="small">
                        <li><strong>Выбор даты</strong> - укажите дату для расписания</li>
                        <li><strong>Выбор маршрутки</strong> - выберите из доступных маршруток</li>
                        <li><strong>Выбор маршрута</strong> - выберите маршрут выбранной маршрутки</li>
                        <li><strong>Выбор остановки</strong> - выберите остановку из маршрута</li>
                        <li><strong>Ввод данных</strong> - укажите время и количество пассажиров</li>
                    </ol>

                    <hr class="my-3">

                    <h6>Важные моменты:</h6>
                    <ul class="small">
                        <li>Показываются только доступные варианты</li>
                        <li>Нельзя создать дубликаты расписания</li>
                        <li>Можно вернуться к предыдущим шагам</li>
                        <li>Данные сохраняются между шагами</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
