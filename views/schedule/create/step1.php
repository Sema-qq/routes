<?php

use app\models\repository\Schedule;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\forms\ScheduleCreateForm $model */

$this->title = "Создание расписания - Шаг 1: Выбор маршрута";
$this->registerCssFile("@web/css/create.css");
$this->params["breadcrumbs"][] = ["label" => "Расписание", "url" => ["index"]];
$this->params["breadcrumbs"][] = [
    "label" => "Создание расписания",
    "url" => ["create"],
];
$this->params["breadcrumbs"][] = "Шаг 1";

$selectDate = Yii::$app->formatter->asDate($model->date, Schedule::DATE_FORMAT);
?>

<div class="schedule-wizard-step1">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-route"></i>
                        Шаг 1: Выбор маршрута
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Прогресс-бар -->
                    <div class="wizard-progress mb-4">
                        <div class="progress">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 25%"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            <small class="text-primary font-weight-bold">1. Маршрут</small>
                            <small class="text-muted">2. Направление</small>
                            <small class="text-muted">3. Машина</small>
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
                                <li class="breadcrumb-item active" aria-current="page">Маршрут</li>
                            </ol>
                        </nav>
                    </div>

                    <?php $form = ActiveForm::begin([
                        "id" => "create-step1-form",
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
                                <strong>Выберите номер маршрута</strong><br>
                                Отображаются все доступные маршруты в системе на дату
                                <strong><?= $selectDate ?></strong>.
                            </div>
                        </div>
                    </div>

                    <?php
                    $availableRouteCodes = $model->getAvailableRouteCodes();
                    if (empty($availableRouteCodes)): ?>
                        <div class="form-group row">
                            <div class="col-sm-8 offset-sm-4">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Нет доступных маршрутов</strong><br>
                                    В системе не найдено ни одного маршрута.
                                    Обратитесь к администратору для создания маршрутов.
                                </div>

                                <div class="btn-group">
                                    <?= Html::a(
                                        '<i class="fas fa-arrow-left"></i> Изменить дату',
                                        ["create"],
                                        ["class" => "btn btn-primary"],
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
                            ->field($model, "route_code")
                            ->dropDownList($availableRouteCodes, [
                                "prompt" => "Выберите номер маршрута...",
                                "class" => "form-control",
                                "id" => "route-code-select",
                                "required" => true,
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
                                        ["create"],
                                        ["class" => "btn btn-secondary"],
                                    ) ?>

                                    <?= Html::submitButton(
                                        '<i class="fas fa-arrow-right"></i> Далее',
                                        [
                                            "class" => "btn btn-primary",
                                            "name" => "next",
                                            "disabled" => empty(
                                                $model->route_code
                                            ),
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
                                        Найдено маршрутов: <strong><?= count(
                                            $availableRouteCodes,
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
                    <h6>Шаг 1: Выбор маршрута</h6>
                    <p class="small">
                        На этом шаге вы выбираете номер маршрута, для которого хотите создать расписание.
                    </p>

                    <h6>Что дальше:</h6>
                    <ul class="small">
                        <li>После выбора маршрута вы выберете направление (прямое/обратное)</li>
                        <li>Затем выберете машину на этот маршрут</li>
                        <li>И укажете время для всех 10 остановок</li>
                    </ul>

                    <?php if (!empty($model->date)): ?>
                        <hr class="my-3">
                        <h6>Выбранная дата:</h6>
                        <p class="text-primary font-weight-bold">
                            <?= $selectDate ?>
                        </p>
                    <?php endif; ?>

                    <?php if (!empty($availableRouteCodes)): ?>
                        <hr class="my-3">
                        <h6>Доступно маршрутов:</h6>
                        <span class="badge badge-info"><?= count(
                            $availableRouteCodes,
                        ) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (
                !empty($model->route_code) &&
                !empty($availableRouteCodes[$model->route_code])
            ): ?>
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="fas fa-check-circle text-success"></i>
                            Выбранный маршрут
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="font-weight-bold text-success">
                            Маршрут <?= Html::encode($model->route_code) ?>
                        </p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php $this->registerJs("
    // Включаем/отключаем кнопку 'Далее' в зависимости от выбора
    $('#route-code-select').on('change', function() {
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
        var selected = $('#route-code-select').val();
        $('#next-btn').prop('disabled', !selected);

        if (selected) {
            $('#next-btn').removeClass('btn-secondary').addClass('btn-primary');
        }
    });
");
?>
