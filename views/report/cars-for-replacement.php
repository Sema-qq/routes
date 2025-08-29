<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $maxAge int */
/* @var $currentYear int */

$this->title = "Маршрутки, требующие замены в " . $currentYear . " году";
$this->params["breadcrumbs"][] = ["label" => "Отчеты", "url" => ["index"]];
$this->params["breadcrumbs"][] = $this->title;
?>

<div class="cars-for-replacement">
    <div class="row">
        <div class="col-md-12">
            <h1><?= Html::encode($this->title) ?></h1>

            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i>
                Отчет показывает маршрутки, которые требуется заменить в этом году.
                Критерий: возраст маршрутки более <?= $maxAge ?> лет (произведены до <?= $currentYear -
     $maxAge ?> года включительно)
                или год производства не указан.
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?= GridView::widget([
                "dataProvider" => $dataProvider,
                "tableOptions" => ["class" => "table table-striped table-bordered"],
                "columns" => [
                    ["class" => "yii\grid\SerialColumn"],

                    [
                        "attribute" => "id",
                        "label" => "№ маршрутки",
                        "value" => function ($model) {
                            return "№" . $model->id;
                        },
                    ],
                    [
                        "label" => "Марка и модель",
                        "value" => function ($model) {
                            return $model->brand->name . " " . $model->model;
                        },
                    ],
                    [
                        "attribute" => "production_year",
                        "label" => "Год производства",
                        "value" => function ($model) {
                            return $model->production_year ?: "Не указан";
                        },
                        "contentOptions" => function ($model) {
                            return $model->production_year ? [] : ["class" => "text-danger fw-bold"];
                        },
                    ],
                    [
                        "label" => "Возраст (лет)",
                        "value" => function ($model) use ($currentYear) {
                            if (!$model->production_year) {
                                return "Неизвестно";
                            }
                            return $currentYear - $model->production_year;
                        },
                        "contentOptions" => function ($model) use ($currentYear, $maxAge) {
                            if (!$model->production_year) {
                                return ["class" => "text-danger fw-bold"];
                            }
                            $age = $currentYear - $model->production_year;
                            return $age > $maxAge ? ["class" => "text-danger fw-bold"] : [];
                        },
                    ],
                    [
                        "label" => "Владелец",
                        "value" => function ($model) {
                            return $model->owner->full_name ?? null;
                        },
                    ],
                    [
                        "label" => "Водитель",
                        "value" => function ($model) {
                            return $model->driver->full_name ?? null;
                        },
                    ],
                    [
                        "attribute" => "fare",
                        "label" => "Стоимость проезда",
                        "value" => function ($model) {
                            return $model->fare ? $model->fare . " руб." : "Не указана";
                        },
                    ],
                ],
            ]) ?>

        </div>
    </div>
</div>
