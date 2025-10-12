<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\repository\Route as RouteAR;

/** @var array|null $longest */
/** @var array|null $shortest */

$this->title = 'Длина маршрутов (по времени)';
$this->params['breadcrumbs'][] = ['label' => 'Отчеты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

function formatDuration($seconds) {
    $seconds = (int)$seconds;
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $secs = $seconds % 60;
    return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
}
?>
<div class="route-length-report">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="alert alert-info">
        Самый длинный и самый короткий маршрут по <b>средней длительности прохождения</b>
        (разница между первой и последней остановкой по расписанию за день, усреднённая по дням).
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card border-success mb-3">
                <div class="card-header bg-success text-white">Самый длинный маршрут</div>
                <div class="card-body">
                    <?php if ($longest): ?>
                        <?= DetailView::widget([
                            'model' => $longest['route'],
                            'attributes' => [
                                [
                                    'label' => 'Маршрут',
                                    'value' => $longest['route']->code,
                                ],
                                [
                                    'label' => 'Направление',
                                    'value' => RouteAR::getTypeLabels()[$longest['route']->type] ?? $longest['route']->type,
                                ],
                                [
                                    'label' => 'Средняя длительность',
                                    'value' => formatDuration(round($longest['avg_duration'])),
                                ],
                            ],
                            'options' => ['class' => 'table table-bordered detail-view'],
                        ]) ?>
                    <?php else: ?>
                        <p class="text-muted mb-0">Нет данных.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-info mb-3">
                <div class="card-header bg-info text-white">Самый короткий маршрут</div>
                <div class="card-body">
                    <?php if ($shortest): ?>
                        <?= DetailView::widget([
                            'model' => $shortest['route'],
                            'attributes' => [
                                [
                                    'label' => 'Маршрут',
                                    'value' => '№' . $shortest['route']->code,
                                ],
                                [
                                    'label' => 'Направление',
                                    'value' => RouteAR::getTypeLabels()[$shortest['route']->type] ?? $shortest['route']->type,
                                ],
                                [
                                    'label' => 'Средняя длительность',
                                    'value' => formatDuration(round($shortest['avg_duration'])),
                                ],
                            ],
                            'options' => ['class' => 'table table-bordered detail-view'],
                        ]) ?>
                    <?php else: ?>
                        <p class="text-muted mb-0">Нет данных.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
