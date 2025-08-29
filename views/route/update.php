<?php

use app\models\repository\Car;
use app\models\repository\Route;
use app\models\repository\Stop;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var Route $model */

// Все доступные остановки
$stopItems = ArrayHelper::map(Stop::find()->all(), 'id', 'name');

$this->title = 'Изменение маршрута: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Маршруты', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование маршрута';
?>
<div class="route-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="route-form">

        <?php $form = ActiveForm::begin(); ?>

        <?php if ($model->hasErrors()): ?>
            <div class="alert alert-danger">
                <?= $form->errorSummary($model); ?>
            </div>
        <?php endif; ?>

        <?= $form->field($model, 'code')->input('string', [
            'maxlength' => true,
            'value' => $model->code,
        ]) ?>

        <?= $form->field($model, 'type')->dropDownList(
            Route::getTypeLabels(),
            ['prompt' => 'Выберите тип маршрута']
        ) ?>

        <div class="stops-block">
            <label><b>Остановки по порядку следования:</b></label>
            <div class="row">
                <?php for ($i = 1; $i <= 10; $i++): ?>
                    <div class="col-md-6">
                        <?= $form->field($model, "stop_ids[$i]")
                            ->dropDownList($stopItems, ['prompt' => ""])
                            ->label("Остановка №$i"); ?>
                    </div>
                <?php endfor; ?>
            </div>
        </div>

        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
