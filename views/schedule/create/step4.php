<?php

use app\models\repository\Schedule;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\ScheduleCreateForm $model */

$this->title = "Создание расписания - Шаг 4: Ввод данных";
$this->registerCssFile("@web/css/create.css");
$this->params["breadcrumbs"][] = ["label" => "Расписание", "url" => ["index"]];
$this->params["breadcrumbs"][] = [
    "label" => "Создание расписания",
    "url" => ["create"],
];
$this->params["breadcrumbs"][] = "Шаг 4";

$selectDate = Yii::$app->formatter->asDate($model->date, Schedule::DATE_FORMAT);
?>

<div class="schedule-wizard-step4">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clock"></i>
                        Шаг 4: Ввод данных расписания
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Прогресс-бар -->
                    <div class="wizard-progress mb-4">
                        <div class="progress">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 100%"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            <small class="text-success">
                                <i class="fas fa-check"></i> 1. Маршрутка
                            </small>
                            <small class="text-success">
                                <i class="fas fa-check"></i> 2. Маршрут
                            </small>
                            <small class="text-success">
                                <i class="fas fa-check"></i> 3. Остановка
                            </small>
                            <small class="text-success font-weight-bold">4. Данные</small>
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
                                    <?php
                                    $availableCars = $model->getAvailableCars();
                                    $carName = isset(
                                        $availableCars[$model->car_id],
                                    )
                                        ? $availableCars[$model->car_id]
                                        : "Маршрутка #" . $model->car_id;
                                    ?>
                                    <?= Html::a(
                                        "Маршрутка: " . Html::encode($carName),
                                        ["create-go-to-step", "step" => 1],
                                    ) ?>
                                </li>
                                <li class="breadcrumb-item">
                                    <?php
                                    $availableRoutes = $model->getAvailableRoutes();
                                    $routeName = isset(
                                        $availableRoutes[$model->route_id],
                                    )
                                        ? $availableRoutes[$model->route_id]
                                        : "Маршрут #" . $model->route_id;
                                    ?>
                                    <?= Html::a(
                                        "Маршрут: " . Html::encode($routeName),
                                        ["create-go-to-step", "step" => 2],
                                    ) ?>
                                </li>
                                <li class="breadcrumb-item">
                                    <?php
                                    $availableStops = $model->getAvailableStops();
                                    $stopKey =
                                        $model->stop_id .
                                        "_" .
                                        $model->stop_number;
                                    $stopName = isset($availableStops[$stopKey])
                                        ? $availableStops[$stopKey]
                                        : "Остановка #" . $model->stop_id;
                                    ?>
                                    <?= Html::a(
                                        "Остановка: " . Html::encode($stopName),
                                        ["create-go-to-step", "step" => 3],
                                    ) ?>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Данные</li>
                            </ol>
                        </nav>
                    </div>

                    <?php $form = ActiveForm::begin([
                        "id" => "create-step4-form",
                        "options" => ["class" => "form-horizontal"],
                        "fieldConfig" => [
                            "template" =>
                                '{label}<div class="col-sm-8">{input}{error}{hint}</div>',
                            "labelOptions" => [
                                "class" => "col-sm-4 control-label",
                            ],
                        ],
                    ]); ?>

                    <div class="form-group row">
                        <div class="col-sm-8 offset-sm-4">
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i>
                                <strong>Финальный шаг!</strong><br>
                                Введите данные о времени и количестве пассажиров для создания расписания.
                                Все поля являются обязательными.
                            </div>
                        </div>
                    </div>

                    <?= $form
                        ->field($model, "planned_time")
                        ->input("time", [
                            "class" => "form-control",
                            "step" => "60", // шаг в секундах (1 минута)
                            "placeholder" => "ЧЧ:ММ",
                            "required" => true,
                        ])
                        ->hint("Время в формате ЧЧ:ММ (например, 14:30)") ?>

                    <?= $form
                        ->field($model, "actual_time")
                        ->input("time", [
                            "class" => "form-control",
                            "step" => "60",
                            "placeholder" => "ЧЧ:ММ",
                            "required" => true,
                        ])
                        ->hint(
                            "Фактическое время прибытия (заполняется позже)",
                        ) ?>

                    <?= $form
                        ->field($model, "boarded_count")
                        ->input("number", [
                            "class" => "form-control",
                            "min" => 0,
                            "max" => 999,
                            "placeholder" => "0",
                            "required" => true,
                        ])
                        ->hint(
                            "Количество пассажиров, которые вошли на этой остановке",
                        ) ?>

                    <!-- Скрытые поля для сохранения данных -->
                    <?= $form
                        ->field($model, "date")
                        ->hiddenInput()
                        ->label(false) ?>
                    <?= $form
                        ->field($model, "car_id")
                        ->hiddenInput()
                        ->label(false) ?>
                    <?= $form
                        ->field($model, "route_id")
                        ->hiddenInput()
                        ->label(false) ?>
                    <?= $form
                        ->field($model, "stop_id")
                        ->hiddenInput()
                        ->label(false) ?>
                    <?= $form
                        ->field($model, "stop_number")
                        ->hiddenInput()
                        ->label(false) ?>
                    <?= $form
                        ->field($model, "current_step")
                        ->hiddenInput()
                        ->label(false) ?>

                    <div class="form-group row">
                        <div class="col-sm-8 offset-sm-4">
                            <div class="btn-group">
                                <?= Html::submitButton(
                                    '<i class="fas fa-arrow-left"></i> Назад',
                                    [
                                        "class" => "btn btn-secondary",
                                        "name" => "previous",
                                    ],
                                ) ?>

                                <?= Html::submitButton(
                                    '<i class="fas fa-save"></i> Создать расписание',
                                    [
                                        "class" => "btn btn-success btn-lg",
                                        "name" => "finish",
                                        "id" => "finish-btn",
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

                            <div class="mt-3">
                                <small class="text-success">
                                    <i class="fas fa-info-circle"></i>
                                    После создания расписания вы будете перенаправлены на страницу просмотра.
                                </small>
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
                        <i class="fas fa-info-circle"></i>
                        Информация о шаге
                    </h5>
                </div>
                <div class="card-body">
                    <h6>Шаг 4: Ввод данных</h6>
                    <p class="small">
                        На финальном шаге введите данные о времени прибытия и количестве пассажиров.
                    </p>

                    <h6>Поля формы:</h6>
                    <ul class="small">
                        <li><strong>Планируемое время</strong> - ожидаемое время прибытия</li>
                        <li><strong>Фактическое время</strong> - реальное время прибытия</li>
                        <li><strong>Количество вошедших</strong> - число пассажиров</li>
                    </ul>

                    <div class="alert alert-info small mt-3">
                        <i class="fas fa-lightbulb"></i>
                        Все поля обязательные.
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-list-check"></i>
                        Сводка создаваемого расписания
                    </h5>
                </div>
                <div class="card-body">
                    <dl class="small">
                        <dt>Дата:</dt>
                        <dd class="text-primary font-weight-bold">
                            <?= $selectDate ?>
                        </dd>

                        <dt>Маршрутка:</dt>
                        <dd class="text-success">
                            <?= Html::encode($carName) ?>
                        </dd>

                        <dt>Маршрут:</dt>
                        <dd class="text-success">
                            <?= Html::encode($routeName) ?>
                        </dd>

                        <dt>Остановка:</dt>
                        <dd class="text-success">
                            <?= Html::encode($stopName) ?>
                        </dd>

                        <?php if (!empty($model->planned_time)): ?>
                            <dt>Планируемое время:</dt>
                            <dd class="text-info font-weight-bold">
                                <?= Html::encode($model->planned_time) ?>
                            </dd>
                        <?php endif; ?>

                        <?php if (!empty($model->actual_time)): ?>
                            <dt>Фактическое время:</dt>
                            <dd class="text-info font-weight-bold">
                                <?= Html::encode($model->actual_time) ?>
                            </dd>
                        <?php endif; ?>

                        <?php if (!empty($model->boarded_count)): ?>
                            <dt>Количество вошедших:</dt>
                            <dd class="text-info font-weight-bold">
                                <?= Html::encode($model->boarded_count) ?>
                            </dd>
                        <?php endif; ?>
                    </dl>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-shield-alt"></i>
                        Проверки системы
                    </h5>
                </div>
                <div class="card-body">
                    <div class="small">
                        <div class="text-success">
                            <i class="fas fa-check"></i> Маршрут принадлежит маршрутке
                        </div>
                        <div class="text-success">
                            <i class="fas fa-check"></i> Остановка принадлежит маршруту
                        </div>
                        <div class="text-success">
                            <i class="fas fa-check"></i> Нет дубликатов расписания
                        </div>
                        <div class="text-success">
                            <i class="fas fa-check"></i> Корректные номера остановок
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->registerJs("
    // Автоматическое обновление сводки при изменении полей
    $('#scheduleWizardform-planned_time, #scheduleWizardform-actual_time, #scheduleWizardform-boarded_count').on('change input', function() {
        updateSummary();
    });

    function updateSummary() {
        var plannedTime = $('#scheduleWizardform-planned_time').val();
        var actualTime = $('#scheduleWizardform-actual_time').val();
        var boardedCount = $('#scheduleWizardform-boarded_count').val();

        // Обновляем сводку в реальном времени
        // Здесь можно добавить код для динамического обновления сводки
    }

    // Валидация времени
    $('#scheduleWizardform-planned_time, #scheduleWizardform-actual_time').on('blur', function() {
        var timeValue = $(this).val();
        if (timeValue && !/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/.test(timeValue)) {
            $(this).addClass('is-invalid');
            $(this).after('<div class=\"invalid-feedback\">Неверный формат времени. Используйте ЧЧ:ММ</div>');
        } else {
            $(this).removeClass('is-invalid');
            $(this).siblings('.invalid-feedback').remove();
        }
    });
");
?>
