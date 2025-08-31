<?php

use app\models\repository\Route;
use app\models\repository\Schedule;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\forms\ScheduleCreateForm $model */

$this->title = "Создание расписания - Шаг 3: Выбор машины";
$this->registerCssFile("@web/css/create.css");
$this->params["breadcrumbs"][] = ["label" => "Расписание", "url" => ["index"]];
$this->params["breadcrumbs"][] = [
    "label" => "Создание расписания",
    "url" => ["create"],
];
$this->params["breadcrumbs"][] = "Шаг 3";

$selectDate = Yii::$app->formatter->asDate($model->date, Schedule::DATE_FORMAT);
?>

<div class="schedule-wizard-step3">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bus"></i>
                        Шаг 3: Выбор машины
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Прогресс-бар -->
                    <div class="wizard-progress mb-4">
                        <div class="progress">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 75%"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            <small class="text-success">1. Маршрут</small>
                            <small class="text-success">2. Направление</small>
                            <small class="text-primary font-weight-bold">3. Машина</small>
                            <small class="text-muted">4. Время</small>
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
                                <li class="breadcrumb-item active" aria-current="page">Машина</li>
                            </ol>
                        </nav>
                    </div>

                    <?php $form = ActiveForm::begin([
                        "id" => "create-step3-form",
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
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Выберите машину для маршрута <?= Html::encode(
                                    $model->route_code,
                                ) ?></strong><br>
                                Отображаются только свободные машины, которые еще не назначены на этот маршрут на дату <?= $selectDate ?>.
                            </div>
                        </div>
                    </div>

                    <?php
                    $availableCars = $model->getAvailableCars();
                    if (empty($availableCars)): ?>
                        <div class="form-group row">
                            <div class="col-sm-8 offset-sm-4">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Нет доступных машин</strong><br>
                                    На дату <?= $selectDate ?> все машины уже назначены на маршрут <?= Html::encode(
     $model->route_code,
 ) ?> (<?= Html::encode(Route::getTypeLabels()[$model->route_direction]) ?>).
                                    Попробуйте выбрать другую дату или другое направление маршрута.
                                </div>

                                <div class="btn-group">
                                    <?= Html::a(
                                        '<i class="fas fa-arrow-left"></i> Изменить направление',
                                        ["create-step", "step" => 2],
                                        ["class" => "btn btn-primary"],
                                    ) ?>

                                    <?= Html::a(
                                        '<i class="fas fa-calendar"></i> Изменить дату',
                                        ["create"],
                                        ["class" => "btn btn-warning"],
                                    ) ?>

                                    <?= Html::a(
                                        '<i class="fas fa-times"></i> Отменить',
                                        ["create-cancel"],
                                        ["class" => "btn btn-secondary"],
                                    ) ?>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <?= $form
                            ->field($model, "car_id")
                            ->dropDownList($availableCars, [
                                "prompt" => "Выберите машину...",
                                "class" => "form-control",
                                "id" => "car-select",
                                "required" => true,
                            ]) ?>

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
                                        '<i class="fas fa-arrow-right"></i> Далее',
                                        [
                                            "class" => "btn btn-primary",
                                            "name" => "next",
                                            "disabled" => empty($model->car_id),
                                            "id" => "next-btn",
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
                                        Найдено доступных машин: <strong><?= count(
                                            $availableCars,
                                        ) ?></strong>
                                    </small>
                                </div>
                            </div>
                        </div>
                    <?php endif;
                    ?>

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
                    <h6>Шаг 3: Выбор машины</h6>
                    <p class="small">
                        На этом шаге вы выбираете конкретную машину, которая будет работать на выбранном маршруте.
                    </p>

                    <h6>Отображаются только:</h6>
                    <ul class="small">
                        <li>Машины, которые еще не назначены на этот маршрут в выбранную дату</li>
                        <li>Доступные для работы машины</li>
                    </ul>

                    <hr class="my-3">

                    <h6>Выбранные данные:</h6>
                    <dl class="small">
                        <dt>Дата:</dt>
                        <dd><?= $selectDate ?></dd>

                        <dt>Маршрут:</dt>
                        <dd class="text-success"><?= Html::encode(
                            $model->route_code,
                        ) ?></dd>

                        <dt>Направление:</dt>
                        <dd class="text-success"><?= Html::encode(
                            Route::getTypeLabels()[$model->route_direction],
                        ) ?></dd>
                    </dl>

                    <?php if (!empty($availableCars)): ?>
                        <hr class="my-3">
                        <h6>Доступно машин:</h6>
                        <span class="badge badge-info"><?= count(
                            $availableCars,
                        ) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (
                !empty($model->car_id) &&
                !empty($availableCars[$model->car_id])
            ): ?>
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="fas fa-check-circle text-success"></i>
                            Выбранная машина
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="font-weight-bold text-success">
                            <?= Html::encode($availableCars[$model->car_id]) ?>
                        </p>
                        <p class="small text-muted">
                            Будет работать на маршруте <?= Html::encode(
                                $model->route_code,
                            ) ?> (<?= Html::encode(
     Route::getTypeLabels()[$model->route_direction],
 ) ?>)
                        </p>
                    </div>
                </div>
            <?php endif; ?>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-lightbulb"></i>
                        Что дальше?
                    </h5>
                </div>
                <div class="card-body">
                    <p class="small text-muted">
                        После выбора машины вы перейдете к последнему шагу - указанию времени прибытия для всех 10 остановок маршрута.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->registerJs("
    // Включаем/отключаем кнопку 'Далее' в зависимости от выбора
    $('#car-select').on('change', function() {
        var selected = $(this).val();
        $('#next-btn').prop('disabled', !selected);

        if (selected) {
            $('#next-btn').removeClass('btn-secondary').addClass('btn-primary');
        } else {
            $('#next-btn').removeClass('btn-primary').addClass('btn-secondary');
        }
    });

    // Инициализация состояния кнопки
    $(document).ready(function() {
        var selected = $('#car-select').val();
        $('#next-btn').prop('disabled', !selected);

        if (selected) {
            $('#next-btn').removeClass('btn-secondary').addClass('btn-primary');
        }
    });
");
?>
