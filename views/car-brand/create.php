<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\repository\CarBrand $model */

$this->title = 'Производитель';
$this->params['breadcrumbs'][] = ['label' => 'Производители', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="car-brand-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
