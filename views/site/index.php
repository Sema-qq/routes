<?php

/** @var yii\web\View $this */

$this->title = 'My Yii Application';
?>
<div class="site-index">

    <div class="jumbotron text-center bg-transparent mt-5 mb-5">
        <h1 class="display-4">Расписание маршруток</h1>

        <p class="lead">Учебный проект. Вариант задания №9.</p>

        <p><a class="btn btn-lg btn-success" href="/report/index">Перейти к отчетам</a></p>
    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-4 mb-3">
                <h2>Маршрутки</h2>

                <p>Транспортные средства для перевозки пассажиров.
                    В рамках проекта имеют владельца и водителя.
                    Могут быть назначены на разные маршруты. Не могут иметь более одного владельца.</p>

                <p><a class="btn btn-outline-secondary" href="/car/index">Маршрутки</a></p>
            </div>
            <div class="col-lg-4 mb-3">
                <h2>Маршруты</h2>

                <p>Маршрут следования состоит из 10 остановок. Может быть прямой и обратный.
                    На маршруте должен быть водитель с опытом более трёх лет и автомобилем не старше 10 лет.</p>

                <p><a class="btn btn-outline-secondary" href="/route/index">Маршруты</a></p>
            </div>
            <div class="col-lg-4">
                <h2>Расписания</h2>

                <p>Расписания маршрутов содержат информацию маршруте,
                    водителе, автомобиле, запланированном и фактическом времени остановки,
                    а так же о количестве вошедших пассажиров.</p>

                <p><a class="btn btn-outline-secondary" href="/schedule/index">Расписания</a></p>
            </div>
        </div>

    </div>
</div>
