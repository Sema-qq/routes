<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\ScheduleWizardForm $model */

$this->title = "Создание расписания - Шаг 1: Выбор маршрутки";
$this->registerCssFile("@web/css/wizard.css");
$this->params["breadcrumbs"][] = ["label" => "Расписание", "url" => ["index"]];
$this->params["breadcrumbs"][] = [
    "label" => "Создание расписания",
    "url" => ["wizard"],
];
$this->params["breadcrumbs"][] = "Шаг 1";

$selectDate = Yii::$app->formatter->asDate($model->date, 'php:d.m.Y')
?>

<div class="schedule-wizard-step1">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bus"></i>
                        Шаг 1: Выбор маршрутки
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Прогресс-бар -->
                    <div class="wizard-progress mb-4">
                        <div class="progress">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 25%"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            <small class="text-primary font-weight-bold">1. Маршрутка</small>
                            <small class="text-muted">2. Маршрут</small>
                            <small class="text-muted">3. Остановка</small>
                            <small class="text-muted">4. Данные</small>
                        </div>
                    </div>

                    <!-- Навигация по шагам -->
                    <div class="wizard-navigation mb-4">
                        <nav aria-label="Навигация по шагам">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <?= Html::a(
                                        "Дата: " . $selectDate,
                                        ["wizard"],
                                    ) ?>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Маршрутка</li>
                            </ol>
                        </nav>
                    </div>

                    <?php $form = ActiveForm::begin([
                        "id" => "wizard-step1-form",
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
                                <strong>Выберите маршрутку</strong><br>
                                Отображаются только те маршрутки, у которых есть маршруты и для которых можно создать расписание на выбранную дату
                                <strong><?= $selectDate ?></strong>.
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
                                    <strong>Нет доступных маршруток</strong><br>
                                    На выбранную дату <?= $selectDate ?> нет доступных маршруток для создания расписания.
                                    Возможно, для всех маршруток уже созданы расписания на все маршруты.
                                </div>

                                <div class="btn-group">
                                    <?= Html::a(
                                        '<i class="fas fa-arrow-left"></i> Изменить дату',
                                        ["wizard"],
                                        ["class" => "btn btn-primary"],
                                    ) ?>

                                    <?= Html::a(
                                        '<i class="fas fa-times"></i> Отменить',
                                        ["wizard-cancel"],
                                        ["class" => "btn btn-secondary"],
                                    ) ?>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <?= $form
                            ->field($model, "car_id")
                            ->dropDownList($availableCars, [
                                "prompt" => "Выберите маршрутку...",
                                "class" => "form-control",
                                "id" => "car-select",
                            ]) ?>

                        <!-- Скрытые поля для сохранения данных -->
                        <?= $form
                            ->field($model, "date")
                            ->hiddenInput()
                            ->label(false) ?>
                        <?= $form
                            ->field($model, "current_step")
                            ->hiddenInput()
                            ->label(false) ?>

                        <div class="form-group row">
                            <div class="col-sm-8 offset-sm-4">
                                <div class="btn-group">
                                    <?= Html::a(
                                        '<i class="fas fa-arrow-left"></i> Назад к выбору даты',
                                        ["wizard"],
                                        ["class" => "btn btn-secondary"],
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
                                        ["wizard-cancel"],
                                        [
                                            "class" => "btn btn-outline-danger",
                                            "data-confirm" =>
                                                "Вы уверены, что хотите отменить создание расписания?",
                                        ],
                                    ) ?>
                                </div>

                                <div class="mt-2">
                                    <small class="text-muted">
                                        Найдено доступных маршруток: <strong><?= count(
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
                    <h6>Шаг 1: Выбор маршрутки</h6>
                    <p class="small">
                        На этом шаге вы выбираете маршрутку, для которой хотите создать расписание.
                    </p>

                    <h6>Отображаются только:</h6>
                    <ul class="small">
                        <li>Маршрутки, у которых есть хотя бы один маршрут</li>
                        <li>Маршрутки, для которых не созданы расписания на все маршруты на выбранную дату</li>
                    </ul>

                    <?php if (!empty($model->date)): ?>
                        <hr class="my-3">
                        <h6>Выбранная дата:</h6>
                        <p class="text-primary font-weight-bold">
                            <?= $selectDate ?>
                        </p>
                    <?php endif; ?>

                    <?php if (!empty($availableCars)): ?>
                        <hr class="my-3">
                        <h6>Доступно маршруток:</h6>
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
                            Выбранная маршрутка
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="font-weight-bold text-success">
                            <?= Html::encode($availableCars[$model->car_id]) ?>
                        </p>
                    </div>
                </div>
            <?php endif; ?>
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
