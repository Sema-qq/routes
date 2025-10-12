<?php
/** @var yii\web\View $this */
/** @var \app\models\repository\Route[] $routes */
/** @var int|string|null $selectedRouteId */
/** @var array|null $result */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\repository\Route as RouteAR;

$this->title = 'Кто больше всего опаздывает';
$this->params['breadcrumbs'][] = ['label' => 'Отчеты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Подготовим список для выпадашки "Маршрут"
$routeItems = [];
foreach ($routes as $r) {
    $label = $r->code . ' (' . (RouteAR::getTypeLabels()[$r->type] ?? $r->type) . ')';
    $routeItems[$r->id] = $label;
}

// Хелпер для форматирования секунд в ЧЧ:ММ:СС
$formatDuration = function(int $seconds): string {
    $h = floor($seconds / 3600);
    $m = floor(($seconds % 3600) / 60);
    $s = $seconds % 60;
    return sprintf('%02d:%02d:%02d', $h, $m, $s);
};
?>

<div class="report-latest-driver">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="alert alert-info">
        Отчет показывает водителя с <b>максимальной суммой опозданий</b> на выбранном маршруте
        (суммируются только положительные разницы между фактическим и плановым временем).
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <i class="fas fa-filter"></i> Фильтры
        </div>
        <div class="card-body">
            <?php $form = ActiveForm::begin([
                'method' => 'get',
                'action' => Url::to(['report/latest-driver']),
                'options' => ['class' => 'form-inline'],
            ]); ?>

            <div class="form-group mr-2" style="margin-right:12px;">
                <label for="route_id" class="mr-2">Маршрут:</label>
                <?= Html::dropDownList(
                    'route_id',
                    $selectedRouteId,
                    $routeItems,
                    ['class' => 'form-control', 'id' => 'route_id', 'prompt' => '— выберите маршрут —']
                ) ?>
            </div>

            <?= Html::submitButton('<i class="fas fa-search"></i> Показать', ['class' => 'btn btn-primary']) ?>
            <?= Html::a('<i class="fas fa-undo"></i> Сбросить', ['report/latest-driver'], ['class' => 'btn btn-outline-secondary mr-2']) ?>


            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <?php if (!$selectedRouteId): ?>
        <div class="alert alert-light border">
            Сначала выберите маршрут и нажмите «Показать».
        </div>
    <?php else: ?>
        <div class="card">
            <div class="card-header">
                <i class="fas fa-user-clock"></i> Результат
            </div>
            <div class="card-body">
                <?php if ($result && $result['driver']): ?>
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3">Водитель</h5>
                            <p class="lead mb-1"><?= Html::encode($result['driver']->full_name) ?></p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">Суммарное опоздание</h5>
                            <p class="mb-1">
                                <span class="badge badge-danger" style="font-size: 100%;">
                                    <?= $formatDuration((int)$result['total_delay_seconds']) ?>
                                </span>
                                <small class="text-muted ml-2">
                                    (≈ <?= Html::encode(number_format($result['total_delay_minutes'], 2)) ?> мин)
                                </small>
                            </p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-success mb-0">
                        По выбранному маршруту опозданий не найдено — все вовремя, либо нет данных.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
    /* легкая правка для inline-формы под BS4/5 */
    .form-inline .form-group label { margin-right: 8px; }
</style>
