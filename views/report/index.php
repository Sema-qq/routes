<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = "Отчеты";
$this->params["breadcrumbs"][] = $this->title;
?>

<div class="report-index">
    <div class="row">
        <div class="col-md-12">
            <h1><?= Html::encode($this->title) ?></h1>

            <p class="lead">Система отчетов для управления маршрутками</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">
                        <i class="bi bi-wrench"></i>
                        Техническое состояние
                    </h3>
                </div>
                <div class="card-body">
                    <p>Отчеты по техническому состоянию транспортных средств</p>

                    <?= Html::a(
                        '<i class="bi bi-exclamation-triangle-fill"></i> Маршрутки, требующие замены',
                        ["cars-for-replacement"],
                        ["class" => "btn btn-danger w-100"],
                    ) ?>

                    <p class="text-muted mt-2">
                        <small>Показывает маршрутки, которые требуется заменить в текущем году по возрасту</small>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <h3 class="card-title mb-0">
                        <i class="bi bi-signpost-split"></i>
                        Анализ маршрутов
                    </h3>
                </div>
                <div class="card-body">
                    <p>Отчеты по маршрутам и их характеристикам</p>

                    <?= Html::a(
                        '<i class="bi bi-arrows-expand"></i> Длина маршрутов',
                        ["route-length"],
                        ["class" => "btn btn-info w-100"],
                    ) ?>

                    <p class="text-muted mt-2">
                        <small>Показывает самые длинные и короткие маршруты по количеству остановок</small>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h3 class="card-title mb-0">
                        <i class="bi bi-currency-exchange"></i>
                        Финансовые отчеты
                    </h3>
                </div>
                <div class="card-body">
                    <p>Отчеты по доходности и финансовым показателям</p>

                    <?= Html::a(
                        '<i class="bi bi-graph-up"></i> Средняя ежедневная выручка',
                        ["daily-revenue"],
                        ["class" => "btn btn-success w-100"],
                    ) ?>

                    <p class="text-muted mt-2">
                        <small>Анализ средней ежедневной выручки каждого маршрута за указанный период</small>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-warning">
                <div class="card-header bg-warning text-dark">
                    <h3 class="card-title mb-0">
                        <i class="bi bi-person-badge"></i>
                        Анализ персонала
                    </h3>
                </div>
                <div class="card-body">
                    <p>Отчеты по работе водителей и соблюдению расписания</p>

                    <?= Html::a(
                        '<i class="bi bi-clock-history"></i> Анализ опозданий водителей',
                        ["latest-driver"],
                        ["class" => "btn btn-warning w-100"],
                    ) ?>

                    <p class="text-muted mt-2">
                        <small>Поиск водителя с наибольшими опозданиями на конкретном маршруте</small>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-info">
                <h4><i class="bi bi-info-circle"></i> Информация</h4>
                <p>Все отчеты формируются на основе актуальных данных из базы данных системы.
                   Данные обновляются в режиме реального времени.</p>

                <p><strong>Доступные отчеты:</strong></p>
                <ul>
                    <li><strong>Маршрутки, требующие замены</strong> - анализ технического состояния транспорта по возрасту</li>
                    <li><strong>Длина маршрутов</strong> - сравнительный анализ маршрутов по количеству остановок</li>
                    <li><strong>Средняя ежедневная выручка</strong> - финансовый анализ доходности маршрутов</li>
                    <li><strong>Анализ опозданий водителей</strong> - контроль соблюдения расписания персоналом</li>
                </ul>
            </div>
        </div>
    </div>
</div>
