<?php

use app\models\repository\Route;
use app\models\repository\Schedule;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\forms\ScheduleCreateForm $model */

$this->title = "Создание расписания - Шаг 2: Выбор направления";
$this->registerCssFile("@web/css/create.css");
$this->params["breadcrumbs"][] = ["label" => "Расписание", "url" => ["index"]];
$this->params["breadcrumbs"][] = [
    "label" => "Создание расписания",
    "url" => ["create"],
];
$this->params["breadcrumbs"][] = "Шаг 2";

$selectDate = Yii::$app->formatter->asDate($model->date, Schedule::DATE_FORMAT);
?>

<div class="schedule-wizard-step2">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-directions"></i>
                        Шаг 2: Выбор направления маршрута
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Прогресс-бар -->
                    <div class="wizard-progress mb-4">
                        <div class="progress">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 50%"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            <small class="text-success">1. Маршрут</small>
                            <small class="text-primary font-weight-bold">2. Направление</small>
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
                                <li class="breadcrumb-item">
                                    <?= Html::a(
                                        "Маршрут " . $model->route_code,
                                        ["create-step", "step" => 1],
                                    ) ?>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Направление</li>
                            </ol>
                        </nav>
                    </div>

                    <?php $form = ActiveForm::begin([
                        "id" => "create-step2-form",
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
                                <strong>Выберите направление маршрута <?= Html::encode(
                                    $model->route_code,
                                ) ?></strong><br>
                                Для каждого маршрута может быть прямое и обратное направление.
                                Каждое направление имеет свой набор из 10 остановок.
                            </div>
                        </div>
                    </div>

                    <?php
                    $availableDirections = $model->getAvailableDirections();
                    if (empty($availableDirections)): ?>
                        <div class="form-group row">
                            <div class="col-sm-8 offset-sm-4">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Нет доступных направлений</strong><br>
                                    Для маршрута <?= Html::encode(
                                        $model->route_code,
                                    ) ?> не найдено направлений.
                                    Обратитесь к администратору.
                                </div>

                                <div class="btn-group">
                                    <?= Html::a(
                                        '<i class="fas fa-arrow-left"></i> Изменить маршрут',
                                        ["create-step", "step" => 1],
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
                        <div class="form-group row">
                            <div class="col-sm-8 offset-sm-4">
                                <div class="row">
                                    <?php foreach (
                                        $availableDirections
                                        as $direction => $label
                                    ): ?>
                                        <div class="col-md-6">
                                            <div class="card border-<?= $model->route_direction ===
                                            $direction
                                                ? "primary"
                                                : "light" ?> mb-3">
                                                <div class="card-body text-center">
                                                    <div class="form-check">
                                                        <?= Html::radio(
                                                            "ScheduleCreateForm[route_direction]",
                                                            $model->route_direction ===
                                                                $direction,
                                                            [
                                                                "value" => $direction,
                                                                "id" =>
                                                                    "direction_" .
                                                                    $direction,
                                                                "class" =>
                                                                    "form-check-input direction-radio",
                                                            ],
                                                        ) ?>
                                                        <label class="form-check-label" for="direction_<?= $direction ?>">
                                                            <h5><?= Html::encode(
                                                                $label,
                                                            ) ?></h5>
                                                            <p class="text-muted">
                                                                <?= $direction ===
                                                                Route::TYPE_DIRECT
                                                                    ? "Маршрут в прямом направлении"
                                                                    : "Маршрут в обратном направлении" ?>
                                                            </p>
                                                            <?php if (
                                                                $direction ===
                                                                Route::TYPE_DIRECT
                                                            ): ?>
                                                                <i class="fas fa-arrow-right fa-2x text-primary"></i>
                                                            <?php else: ?>
                                                                <i class="fas fa-arrow-left fa-2x text-warning"></i>
                                                            <?php endif; ?>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
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
                                            "disabled" => empty(
                                                $model->route_direction
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
                                        Найдено направлений: <strong><?= count(
                                            $availableDirections,
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
                    <h6>Шаг 2: Выбор направления</h6>
                    <p class="small">
                        На этом шаге вы выбираете направление маршрута: прямое или обратное.
                    </p>

                    <h6>Направления:</h6>
                    <ul class="small">
                        <li><strong>Прямое</strong> - основное направление маршрута</li>
                        <li><strong>Обратное</strong> - обратное направление с другим набором остановок</li>
                    </ul>

                    <?php if (!empty($model->route_code)): ?>
                        <hr class="my-3">
                        <h6>Выбранный маршрут:</h6>
                        <p class="text-primary font-weight-bold">
                            <?= Html::encode($model->route_code) ?>
                        </p>
                    <?php endif; ?>

                    <?php if (!empty($model->date)): ?>
                        <h6>Выбранная дата:</h6>
                        <p class="text-primary font-weight-bold">
                            <?= $selectDate ?>
                        </p>
                    <?php endif; ?>

                    <?php if (!empty($availableDirections)): ?>
                        <hr class="my-3">
                        <h6>Доступно направлений:</h6>
                        <span class="badge badge-info"><?= count(
                            $availableDirections,
                        ) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($model->route_direction)): ?>
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="fas fa-check-circle text-success"></i>
                            Выбранное направление
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="font-weight-bold text-success">
                            <?= Html::encode(
                                Route::getTypeLabels()[$model->route_direction],
                            ) ?>
                        </p>
                        <p class="small text-muted">
                            Маршрут №<?= Html::encode($model->route_code) ?>
                        </p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php $this->registerJs("
    // Обработка выбора направления
    $('.direction-radio').on('change', function() {
        var selected = $('input[name=\"ScheduleCreateForm[route_direction]\"]:checked').val();
        $('#next-btn').prop('disabled', !selected);

        // Обновляем стиль карточек
        $('.card').removeClass('border-primary').addClass('border-light');
        $(this).closest('.card').removeClass('border-light').addClass('border-primary');

        if (selected) {
            $('#next-btn').removeClass('btn-secondary').addClass('btn-primary');
        } else {
            $('#next-btn').removeClass('btn-primary').addClass('btn-secondary');
        }
    });

    // Инициализация состояния кнопки
    $(document).ready(function() {
        var selected = $('input[name=\"ScheduleCreateForm[route_direction]\"]:checked').val();
        $('#next-btn').prop('disabled', !selected);

        if (selected) {
            $('#next-btn').removeClass('btn-secondary').addClass('btn-primary');
            $('input[name=\"ScheduleCreateForm[route_direction]\"]:checked').closest('.card').removeClass('border-light').addClass('border-primary');
        }
    });
");
?>
