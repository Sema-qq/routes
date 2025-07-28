<?php

use app\models\repository\Schedule;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\ScheduleCreateForm $model */

$this->title = "Создание расписания - Шаг 3: Выбор остановки";
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
                        <i class="fas fa-map-marker-alt"></i>
                        Шаг 3: Выбор остановки
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Прогресс-бар -->
                    <div class="wizard-progress mb-4">
                        <div class="progress">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 75%"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            <small class="text-success">
                                <i class="fas fa-check"></i> 1. Маршрутка
                            </small>
                            <small class="text-success">
                                <i class="fas fa-check"></i> 2. Маршрут
                            </small>
                            <small class="text-primary font-weight-bold">3. Остановка</small>
                            <small class="text-muted">4. Данные</small>
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
                                <li class="breadcrumb-item active" aria-current="page">Остановка</li>
                            </ol>
                        </nav>
                    </div>

                    <?php
                    // Предзаполняем route_stop_key из существующих данных
                    if (
                        empty($model->route_stop_key) &&
                        !empty($model->stop_id) &&
                        !empty($model->stop_number)
                    ) {
                        $model->route_stop_key =
                            $model->stop_id . "_" . $model->stop_number;
                    }

                    $form = ActiveForm::begin([
                        "id" => "create-step3-form",
                        "options" => ["class" => "form-horizontal"],
                        "fieldConfig" => [
                            "template" =>
                                '{label}<div class="col-sm-8">{input}{error}</div>',
                            "labelOptions" => [
                                "class" => "col-sm-4 control-label",
                            ],
                        ],
                    ]);
                    ?>

                    <div class="form-group row">
                        <div class="col-sm-8 offset-sm-4">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Выберите остановку</strong><br>
                                Отображаются только те остановки выбранного маршрута, для которых еще нет расписания на выбранную дату.
                                В списке указан номер остановки и её название.
                            </div>
                        </div>
                    </div>

                    <?php
                    $availableStops = $model->getAvailableStops();
                    if (empty($availableStops)): ?>
                        <div class="form-group row">
                            <div class="col-sm-8 offset-sm-4">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Нет доступных остановок</strong><br>
                                    Для выбранного маршрута на дату <?= $selectDate ?> нет доступных остановок для создания расписания.
                                    Для всех остановок уже созданы расписания.
                                </div>

                                <div class="btn-group">
                                    <?= Html::a(
                                        '<i class="fas fa-arrow-left"></i> Выбрать другой маршрут',
                                        ["create-go-to-step", "step" => 2],
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
                    <?php
                        // Создаем специальное поле для выбора остановки с комбинированным значением

                        else: ?>
                        <?php echo $form
                            ->field($model, "route_stop_key", [
                                "template" =>
                                    '{label}<div class="col-sm-8">{input}{error}<div class="form-text">Формат: № остановки — название остановки</div></div>',
                            ])
                            ->dropDownList($availableStops, [
                                "prompt" => "Выберите остановку...",
                                "class" => "form-control",
                                "id" => "stop-select",
                                "onchange" => "updateStopData(this.value)",
                            ])
                            ->label("Остановка и номер"); ?>

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
                                        '<i class="fas fa-arrow-right"></i> Далее',
                                        [
                                            "class" => "btn btn-primary",
                                            "name" => "next",
                                            "disabled" => empty(
                                                $model->stop_id
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
                                        Найдено доступных остановок: <strong><?= count(
                                            $availableStops,
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
                    <h6>Шаг 3: Выбор остановки</h6>
                    <p class="small">
                        На этом шаге вы выбираете остановку выбранного маршрута, для которой хотите создать расписание.
                    </p>

                    <h6>Отображаются только:</h6>
                    <ul class="small">
                        <li>Остановки выбранного маршрута</li>
                        <li>Остановки, для которых еще нет расписания на выбранную дату</li>
                        <li>Формат: "№ остановки — название остановки"</li>
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

                        <dt>Маршрут:</dt>
                        <dd class="text-success"><?= Html::encode(
                            $routeName,
                        ) ?></dd>
                    </dl>

                    <?php if (!empty($availableStops)): ?>
                        <hr class="my-3">
                        <h6>Доступно остановок:</h6>
                        <span class="badge badge-info"><?= count(
                            $availableStops,
                        ) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (
                !empty($model->stop_id) &&
                !empty($model->stop_number)
            ): ?>
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="fas fa-check-circle text-success"></i>
                            Выбранная остановка
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $stopKey = $model->stop_id . "_" . $model->stop_number;
                        if (isset($availableStops[$stopKey])): ?>
                            <p class="font-weight-bold text-success">
                                <?= Html::encode($availableStops[$stopKey]) ?>
                            </p>
                        <?php endif;
                        ?>
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
                        Номер остановки определяется порядком следования по маршруту.
                        Если нужной остановки нет в списке, значит для неё уже создано расписание на эту дату.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Создаем массив для JavaScript с данными об остановках
$stopDataJson = [];
foreach ($availableStops as $key => $label) {
    $parts = explode("_", $key);
    if (count($parts) == 2) {
        $stopDataJson[$key] = [
            "stop_id" => (int) $parts[0],
            "stop_number" => (int) $parts[1],
            "label" => $label,
        ];
    }
}

$this->registerJs(
    "
    var stopData = " .
        json_encode($stopDataJson) .
        ";

    function updateStopData(selectedKey) {
        if (selectedKey && stopData[selectedKey]) {
            // Заполняем скрытые поля
            $('#schedulewizardform-stop_id').val(stopData[selectedKey].stop_id);
            $('#schedulewizardform-stop_number').val(stopData[selectedKey].stop_number);

            // Активируем кнопку 'Далее'
            $('#next-btn').prop('disabled', false).removeClass('btn-secondary').addClass('btn-primary');
        } else {
            // Очищаем скрытые поля
            $('#schedulewizardform-stop_id').val('');
            $('#schedulewizardform-stop_number').val('');

            $('#next-btn').prop('disabled', true).removeClass('btn-primary').addClass('btn-secondary');
        }
    }

    // Включаем/отключаем кнопку 'Далее' в зависимости от выбора
    $('#stop-select').on('change', function() {
        updateStopData($(this).val());
    });

    // Инициализация состояния при загрузке страницы
    $(document).ready(function() {
        var selected = $('#stop-select').val();
        if (selected) {
            updateStopData(selected);
        } else {
            // Проверяем, возможно данные уже есть в скрытых полях
            var stopId = $('#schedulewizardform-stop_id').val();
            var stopNumber = $('#schedulewizardform-stop_number').val();

            if (stopId && stopNumber) {
                // Формируем ключ и выбираем соответствующий элемент в dropdown
                var key = stopId + '_' + stopNumber;
                $('#stop-select').val(key);
                updateStopData(key);
            } else {
                $('#next-btn').prop('disabled', true).removeClass('btn-primary').addClass('btn-secondary');
            }
        }
    });
",
);


?>
