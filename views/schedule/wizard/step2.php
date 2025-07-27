<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\ScheduleWizardForm $model */

$this->title = "Создание расписания - Шаг 2: Выбор маршрута";
$this->registerCssFile("@web/css/wizard.css");
$this->params["breadcrumbs"][] = ["label" => "Расписание", "url" => ["index"]];
$this->params["breadcrumbs"][] = [
    "label" => "Создание расписания",
    "url" => ["wizard"],
];
$this->params["breadcrumbs"][] = "Шаг 2";

$selectDate = Yii::$app->formatter->asDate($model->date, 'php:d.m.Y')
?>

<div class="schedule-wizard-step2">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-route"></i>
                        Шаг 2: Выбор маршрута
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Прогресс-бар -->
                    <div class="wizard-progress mb-4">
                        <div class="progress">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 50%"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            <small class="text-success">
                                <i class="fas fa-check"></i> 1. Маршрутка
                            </small>
                            <small class="text-primary font-weight-bold">2. Маршрут</small>
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
                                        ["wizard-go-to-step", "step" => 1],
                                    ) ?>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Маршрут</li>
                            </ol>
                        </nav>
                    </div>

                    <?php $form = ActiveForm::begin([
                        "id" => "wizard-step2-form",
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
                                <strong>Выберите маршрут</strong><br>
                                Отображаются только те маршруты выбранной маршрутки, для которых можно создать расписание на выбранную дату.
                            </div>
                        </div>
                    </div>

                    <?php
                    $availableRoutes = $model->getAvailableRoutes();
                    if (empty($availableRoutes)): ?>
                        <div class="form-group row">
                            <div class="col-sm-8 offset-sm-4">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Нет доступных маршрутов</strong><br>
                                    Для выбранной маршрутки на дату <?= $selectDate ?> нет доступных маршрутов для создания расписания.
                                    Возможно, для всех маршрутов уже созданы расписания на все остановки.
                                </div>

                                <div class="btn-group">
                                    <?= Html::a(
                                        '<i class="fas fa-arrow-left"></i> Выбрать другую маршрутку',
                                        ["wizard-go-to-step", "step" => 1],
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
                            ->field($model, "route_id")
                            ->dropDownList($availableRoutes, [
                                "prompt" => "Выберите маршрут...",
                                "class" => "form-control",
                                "id" => "route-select",
                            ]) ?>

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
                                                $model->route_id
                                            ),
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
                                        Найдено доступных маршрутов: <strong><?= count(
                                            $availableRoutes,
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
                    <h6>Шаг 2: Выбор маршрута</h6>
                    <p class="small">
                        На этом шаге вы выбираете маршрут выбранной маршрутки, для которого хотите создать расписание.
                    </p>

                    <h6>Отображаются только:</h6>
                    <ul class="small">
                        <li>Маршруты выбранной маршрутки</li>
                        <li>Маршруты, для которых не созданы расписания на все остановки на выбранную дату</li>
                    </ul>

                    <hr class="my-3">

                    <h6>Выбранные данные:</h6>
                    <dl class="small">
                        <dt>Дата:</dt>
                        <dd><?= $selectDate ?></dd>

                        <dt>Маршрутка:</dt>
                        <dd class="text-success"><?= Html::encode(
                            $carName,
                        ) ?></dd>
                    </dl>

                    <?php if (!empty($availableRoutes)): ?>
                        <hr class="my-3">
                        <h6>Доступно маршрутов:</h6>
                        <span class="badge badge-info"><?= count(
                            $availableRoutes,
                        ) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (
                !empty($model->route_id) &&
                !empty($availableRoutes[$model->route_id])
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
                            <?= Html::encode(
                                $availableRoutes[$model->route_id],
                            ) ?>
                        </p>
                    </div>
                </div>
            <?php endif; ?>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-lightbulb"></i>
                        Подсказка
                    </h5>
                </div>
                <div class="card-body">
                    <p class="small text-muted">
                        Если нужного маршрута нет в списке, это означает, что для него уже созданы расписания на все остановки на выбранную дату.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->registerJs("
    // Включаем/отключаем кнопку 'Далее' в зависимости от выбора
    $('#route-select').on('change', function() {
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
        var selected = $('#route-select').val();
        $('#next-btn').prop('disabled', !selected);

        if (selected) {
            $('#next-btn').removeClass('btn-secondary').addClass('btn-primary');
        }
    });
");
?>
