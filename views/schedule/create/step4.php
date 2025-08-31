<?php

use app\models\repository\Route;
use app\models\repository\Schedule;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\forms\ScheduleCreateForm $model */

$this->title = "Создание расписания - Шаг 4: Время прибытия";
$this->registerCssFile("@web/css/create.css");
$this->params["breadcrumbs"][] = ["label" => "Расписание", "url" => ["index"]];
$this->params["breadcrumbs"][] = [
    "label" => "Создание расписания",
    "url" => ["create"],
];
$this->params["breadcrumbs"][] = "Шаг 4";

$selectDate = Yii::$app->formatter->asDate($model->date, Schedule::DATE_FORMAT);
$availableCars = $model->getAvailableCars();
$carName = isset($availableCars[$model->car_id])
    ? $availableCars[$model->car_id]
    : "Машина #" . $model->car_id;
?>

<div class="schedule-wizard-step4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clock"></i>
                        Шаг 4: Указание времени прибытия для всех остановок
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Прогресс-бар -->
                    <div class="wizard-progress mb-4">
                        <div class="progress">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 100%"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            <small class="text-success">1. Маршрут</small>
                            <small class="text-success">2. Направление</small>
                            <small class="text-success">3. Машина</small>
                            <small class="text-primary font-weight-bold">4. Время</small>
                        </div>
                    </div>

                    <!-- Навигация по шагам -->
                    <div class="wizard-navigation mb-4">
                        <nav aria-label="Навигация по шагам">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <?= Html::a("Дата: " . $selectDate, [
                                        "create",
                                    ]) ?>
                                </li>
                                <li class="breadcrumb-item">
                                    <?= Html::a(
                                        "Маршрут " . $model->route_code,
                                        ["create-step", "step" => 1],
                                    ) ?>
                                </li>
                                <li class="breadcrumb-item">
                                    <?= Html::a(
                                        Route::getTypeLabels()[
                                            $model->route_direction
                                        ],
                                        ["create-step", "step" => 2],
                                    ) ?>
                                </li>
                                <li class="breadcrumb-item">
                                    <?= Html::a(Html::encode($carName), [
                                        "create-step",
                                        "step" => 3,
                                    ]) ?>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Время</li>
                            </ol>
                        </nav>
                    </div>

                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle"></i>
                        <strong>Заполните время прибытия для всех 10 остановок</strong><br>
                        Планируемое время обязательно для заполнения. Фактическое время можно указать позже при необходимости.
                        Время указывается в формате ЧЧ:ММ (например, 14:30).
                    </div>

                    <?php $form = ActiveForm::begin([
                        "id" => "create-step4-form",
                        "options" => ["class" => "form-horizontal"],
                    ]); ?>

                    <!-- Сводная информация -->
                    <div class="card mb-4 border-info">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Дата:</strong><br>
                                    <span class="text-primary"><?= $selectDate ?></span>
                                </div>
                                <div class="col-md-3">
                                    <strong>Маршрут:</strong><br>
                                    <span class="text-primary"><?= Html::encode(
                                        $model->route_code,
                                    ) ?></span>
                                </div>
                                <div class="col-md-3">
                                    <strong>Направление:</strong><br>
                                    <span class="text-primary"><?= Html::encode(
                                        Route::getTypeLabels()[
                                            $model->route_direction
                                        ],
                                    ) ?></span>
                                </div>
                                <div class="col-md-3">
                                    <strong>Машина:</strong><br>
                                    <span class="text-primary"><?= Html::encode(
                                        $carName,
                                    ) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Таблица остановок -->
                    <?php
                    $routeStops = $model->getRouteStops();
                    if (empty($model->stops_data) && !empty($routeStops)) {
                        $model->initializeStopsData();
                    }
                    ?>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-list"></i>
                                Остановки маршрута (<?= count(
                                    $routeStops,
                                ) ?> шт.)
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="8%">Имя маршрута</th>
                                            <th width="30%">Название остановки</th>
                                            <th width="20%">Планируемое время <span class="text-danger">*</span></th>
                                            <th width="20%">Фактическое время</th>
                                            <th width="22%">Количество вошедших</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (
                                            $model->stops_data
                                            as $index => $stopData
                                        ): ?>
                                            <tr>
                                                <td class="align-middle">
                                                    <span class="badge badge-primary"><?= $stopData[
                                                        "stop_number"
                                                    ] ?></span>
                                                </td>
                                                <td class="align-middle">
                                                    <strong><?= Html::encode(
                                                        $stopData["stop_name"],
                                                    ) ?></strong>
                                                    <?= Html::hiddenInput(
                                                        "ScheduleCreateForm[stops_data][{$index}][stop_id]",
                                                        $stopData["stop_id"],
                                                    ) ?>
                                                    <?= Html::hiddenInput(
                                                        "ScheduleCreateForm[stops_data][{$index}][stop_number]",
                                                        $stopData[
                                                            "stop_number"
                                                        ],
                                                    ) ?>
                                                    <?= Html::hiddenInput(
                                                        "ScheduleCreateForm[stops_data][{$index}][stop_name]",
                                                        $stopData["stop_name"],
                                                    ) ?>
                                                </td>
                                                <td>
                                                    <?= Html::textInput(
                                                        "ScheduleCreateForm[stops_data][{$index}][planned_time]",
                                                        $stopData[
                                                            "planned_time"
                                                        ],
                                                        [
                                                            "class" =>
                                                                "form-control planned-time-input",
                                                            "placeholder" =>
                                                                "14:30",
                                                            "pattern" =>
                                                                '^([01]?[0-9]|2[0-3]):[0-5][0-9]$',
                                                            "title" =>
                                                                "Формат: ЧЧ:ММ (например, 14:30)",
                                                            "required" => true,
                                                            "data-stop-index" => $index,
                                                        ],
                                                    ) ?>
                                                </td>
                                                <td>
                                                    <?= Html::textInput(
                                                        "ScheduleCreateForm[stops_data][{$index}][actual_time]",
                                                        $stopData[
                                                            "actual_time"
                                                        ],
                                                        [
                                                            "class" =>
                                                                "form-control actual-time-input",
                                                            "placeholder" =>
                                                                "14:35",
                                                            "pattern" =>
                                                                '^([01]?[0-9]|2[0-3]):[0-5][0-9]$',
                                                            "title" =>
                                                                "Формат: ЧЧ:ММ (например, 14:35)",
                                                            "data-stop-index" => $index,
                                                        ],
                                                    ) ?>
                                                </td>
                                                <td>
                                                    <?= Html::textInput(
                                                        "ScheduleCreateForm[stops_data][{$index}][boarded_count]",
                                                        isset(
                                                            $stopData[
                                                                "boarded_count"
                                                            ],
                                                        )
                                                            ? $stopData[
                                                                "boarded_count"
                                                            ]
                                                            : 0,
                                                        [
                                                            "class" =>
                                                                "form-control boarded-count-input",
                                                            "type" => "number",
                                                            "min" => 0,
                                                            "max" => 999,
                                                            "placeholder" =>
                                                                "0",
                                                            "data-stop-index" => $index,
                                                        ],
                                                    ) ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                <div class="alert alert-light border">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Автозаполнение времени:</h6>
                                            <div class="form-row">
                                                <div class="col-md-6">
                                                    <?= Html::textInput(
                                                        "start_time",
                                                        "",
                                                        [
                                                            "class" =>
                                                                "form-control",
                                                            "placeholder" =>
                                                                "08:00",
                                                            "id" =>
                                                                "start-time-input",
                                                        ],
                                                    ) ?>
                                                    <small class="form-text text-muted">Время первой остановки</small>
                                                </div>
                                                <div class="col-md-6">
                                                    <?= Html::textInput(
                                                        "interval",
                                                        "5",
                                                        [
                                                            "class" =>
                                                                "form-control",
                                                            "placeholder" =>
                                                                "5",
                                                            "id" =>
                                                                "interval-input",
                                                            "type" => "number",
                                                            "min" => 1,
                                                            "max" => 30,
                                                        ],
                                                    ) ?>
                                                    <small class="form-text text-muted">Интервал в минутах</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 d-flex align-items-end">
                                            <button type="button" class="btn btn-outline-primary" id="auto-fill-btn">
                                                <i class="fas fa-magic"></i> Автозаполнить планируемое время
                                            </button>
                                        </div>
                                        <div class="col-md-2 d-flex align-items-end">
                                            <button type="button" class="btn btn-outline-secondary" id="clear-all-btn">
                                                <i class="fas fa-eraser"></i> Очистить
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Скрытые поля для сохранения данных -->
                    <?= $form
                        ->field($model, "date")
                        ->hiddenInput()
                        ->label(false) ?>
                    <?= $form
                        ->field($model, "route_code")
                        ->hiddenInput()
                        ->label(false) ?>
                    <?= $form
                        ->field($model, "route_direction")
                        ->hiddenInput()
                        ->label(false) ?>
                    <?= $form
                        ->field($model, "route_id")
                        ->hiddenInput()
                        ->label(false) ?>
                    <?= $form
                        ->field($model, "car_id")
                        ->hiddenInput()
                        ->label(false) ?>
                    <?= $form
                        ->field($model, "current_step")
                        ->hiddenInput()
                        ->label(false) ?>

                    <div class="form-group row mt-4">
                        <div class="col-sm-12">
                            <div class="btn-group">
                                <?= Html::submitButton(
                                    '<i class="fas fa-arrow-left"></i> Назад',
                                    [
                                        "class" => "btn btn-secondary",
                                        "name" => "previous",
                                    ],
                                ) ?>

                                <?= Html::submitButton(
                                    '<i class="fas fa-check"></i> Создать расписание',
                                    [
                                        "class" => "btn btn-success",
                                        "name" => "finish",
                                        "id" => "finish-btn",
                                        "data-confirm" =>
                                            "Создать расписание для всех 10 остановок?",
                                    ],
                                ) ?>

                                <?= Html::a(
                                    '<i class="fas fa-times"></i> Отменить',
                                    ["create-cancel"],
                                    [
                                        "class" => "btn btn-outline-danger",
                                        "data-confirm" =>
                                            "Вы уверены, что хотите отменить создание расписания?",
                                    ],
                                ) ?>
                            </div>

                            <div class="mt-2">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i>
                                    При создании будет создано <?= count(
                                        $model->stops_data,
                                    ) ?> записей расписания
                                </small>
                            </div>
                        </div>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->registerJs("
    // Автозаполнение времени
    $('#auto-fill-btn').on('click', function() {
        var startTime = $('#start-time-input').val();
        var interval = parseInt($('#interval-input').val()) || 5;

        if (!startTime || !startTime.match(/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/)) {
            alert('Укажите корректное время начала в формате ЧЧ:ММ');
            return;
        }

        var timeParts = startTime.split(':');
        var currentHour = parseInt(timeParts[0]);
        var currentMinute = parseInt(timeParts[1]);

        $('.planned-time-input').each(function(index) {
            var totalMinutes = currentHour * 60 + currentMinute + (index * interval);
            var newHour = Math.floor(totalMinutes / 60) % 24;
            var newMinute = totalMinutes % 60;

            var timeStr = String(newHour).padStart(2, '0') + ':' + String(newMinute).padStart(2, '0');
            $(this).val(timeStr);
        });

        // Проверяем валидность после автозаполнения
        validateAllTimes();
    });

    // Очистка всех полей времени
    $('#clear-all-btn').on('click', function() {
        if (confirm('Очистить все поля времени?')) {
            $('.planned-time-input, .actual-time-input').val('');
            validateAllTimes();
        }
    });

    // Валидация времени при вводе
    $('.planned-time-input, .actual-time-input').on('blur', function() {
        validateTime($(this));
    });

    function validateTime(input) {
        var timeValue = input.val();
        var isValid = true;

        if (timeValue && !timeValue.match(/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/)) {
            input.addClass('is-invalid');
            isValid = false;
        } else {
            input.removeClass('is-invalid');
        }

        return isValid;
    }

    function validateAllTimes() {
        var allValid = true;

        $('.planned-time-input').each(function() {
            if (!validateTime($(this))) {
                allValid = false;
            }
        });

        $('.actual-time-input').each(function() {
            if ($(this).val() && !validateTime($(this))) {
                allValid = false;
            }
        });

        // Проверяем, что все планируемые времена заполнены
        var allFilled = true;
        $('.planned-time-input').each(function() {
            if (!$(this).val()) {
                allFilled = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        $('#finish-btn').prop('disabled', !allValid || !allFilled);

        return allValid && allFilled;
    }

    // Проверка при загрузке страницы
    $(document).ready(function() {
        validateAllTimes();

        // Инициализация тултипов
        $('[data-toggle=\"tooltip\"]').tooltip();
    });

    // Проверка перед отправкой формы
    $('#create-step4-form').on('submit', function(e) {
        if (!validateAllTimes()) {
            e.preventDefault();
            alert('Пожалуйста, заполните корректно все обязательные поля времени.');
            return false;
        }
    });

    // Автоформатирование времени при вводе
    $('.planned-time-input, .actual-time-input').on('input', function() {
        var value = $(this).val().replace(/[^0-9]/g, '');

        if (value.length >= 3) {
            value = value.substring(0, 2) + ':' + value.substring(2, 4);
            $(this).val(value);
        }
    });
");
?>
