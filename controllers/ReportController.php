<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use app\models\repository\Car;
use app\models\repository\Route;
use app\models\repository\Schedule;
use app\models\repository\User;
use yii\db\Query;

class ReportController extends Controller
{
    /**
     * Главная страница отчетов
     * @return string
     */
    public function actionIndex()
    {
        return $this->render("index");
    }

    /**
     * Отчет: Маршрутки, которые требуется заменить в этом году
     * @return string
     */
    public function actionCarsForReplacement()
    {
        $currentYear = date("Y");
        $maxAge = 10; // Максимальный возраст маршрутки в годах
        $minYear = $currentYear - $maxAge;

        $query = Car::find()
            ->with(["brand", "owner", "driver"])
            ->where(["<=", "production_year", $minYear])
            ->orWhere(["is", "production_year", null]);

        $dataProvider = new ActiveDataProvider([
            "query" => $query,
            "pagination" => [
                "pageSize" => 20,
            ],
            "sort" => [
                "attributes" => ["production_year", "brand.name", "model"],
            ],
        ]);

        return $this->render("cars-for-replacement", [
            "dataProvider" => $dataProvider,
            "maxAge" => $maxAge,
            "currentYear" => $currentYear,
        ]);
    }

    /**
     * Отчет: Самый длинный и самый короткий маршрут
     * @return string
     */
    public function actionRouteLength(): string
    {
        // Для каждого маршрута считаем среднюю длительность (макс planned_time - мин planned_time)
        $routes = Route::find()->all();
        $routeDurations = [];

        foreach ($routes as $route) {
            $rows = (new \yii\db\Query())
                ->select([
                    "date",
                    "min_time" => "MIN(planned_time)",
                    "max_time" => "MAX(planned_time)",
                ])
                ->from('schedules')
                ->where(['route_id' => $route->id])
                ->groupBy('date')
                ->all();

            $totalSeconds = 0;
            $daysCount = 0;

            foreach ($rows as $row) {
                if ($row['min_time'] && $row['max_time']) {
                    // Преобразуем во времени (HH:MM:SS)
                    $minTime = strtotime($row['date'] . ' ' . $row['min_time']);
                    $maxTime = strtotime($row['date'] . ' ' . $row['max_time']);
                    if ($maxTime > $minTime) {
                        $totalSeconds += ($maxTime - $minTime);
                        $daysCount++;
                    }
                }
            }

            if ($daysCount > 0) {
                $avgDuration = $totalSeconds / $daysCount;
                $routeDurations[] = [
                    'route' => $route,
                    'avg_duration' => $avgDuration,
                ];
            }
        }

        // Сортируем по длительности
        usort($routeDurations, function($a, $b) {
            return $b['avg_duration'] <=> $a['avg_duration'];
        });

        $longest = $routeDurations[0] ?? null;
        $shortest = $routeDurations ? end($routeDurations) : null;

        return $this->render('route-length', [
            'longest' => $longest,
            'shortest' => $shortest,
        ]);
    }

    /**
     * Отчет: Средняя ежедневная выручка каждого маршрута за период
     * @return string
     */
    public function actionDailyRevenue()
    {
        $year = (int) date('Y');
        $defaultStart = sprintf('%d-01-01', $year);
        $defaultEnd   = date('Y-m-d');

        $startDate = Yii::$app->request->get('start_date', $defaultStart);
        $endDate   = Yii::$app->request->get('end_date', $defaultEnd);

        if ($endDate < $startDate) {
            $endDate = $startDate;
        }

        // 1) Сутки по (route_id, date): суммируем выручку всех машин на маршруте за день
        $dailyByRoute = (new \yii\db\Query())
            ->select([
                's.route_id',
                's.date',
                new \yii\db\Expression('SUM(s.boarded_count * c.fare) AS revenue'),
            ])
            ->from(['s' => 'schedules'])
            ->innerJoin(['c' => 'cars'], 'c.id = s.car_id')
            ->where(['between', 's.date', $startDate, $endDate])
            ->groupBy(['s.route_id', 's.date']);

        $dailyByCode = (new \yii\db\Query())
            ->select([
                'r.code',
                'd.date',
                new \yii\db\Expression('SUM(d.revenue) AS revenue'),
            ])
            ->from(['d' => $dailyByRoute])
            ->innerJoin(['r' => 'routes'], 'r.id = d.route_id')
            ->groupBy(['r.code', 'd.date']);

        $query = (new \yii\db\Query())
            ->select([
                'code',
                new \yii\db\Expression('AVG(revenue) AS avg_daily_revenue'),
                new \yii\db\Expression('SUM(revenue) AS total_revenue'),
                new \yii\db\Expression('COUNT(*)     AS days_count'),
            ])
            ->from(['x' => $dailyByCode])
            ->groupBy(['code'])
            ->orderBy(['avg_daily_revenue' => SORT_DESC]);

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('daily-revenue', [
            'dataProvider' => $dataProvider,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    /**
     * Отчет: Водитель, который больше всего опаздывает на определенном маршруте
     * @return string
     */
    public function actionLatestDriver()
    {
        $routeId = Yii::$app->request->get('route_id');

        // просто список маршрутов для селекта
        $routes = Route::find()->orderBy(['code' => SORT_ASC, 'type' => SORT_ASC])->all();

        $result = null;

        if ($routeId) {
            $row = (new \yii\db\Query())
                ->select([
                    'u.id',
                    'u.full_name',
                    // суммируем только положительные опоздания в секундах
                    'total_delay_seconds' => new \yii\db\Expression(
                        "SUM(GREATEST(EXTRACT(EPOCH FROM s.actual_time) - EXTRACT(EPOCH FROM s.planned_time), 0))"
                    ),
                ])
                ->from(['s' => 'schedules'])
                ->innerJoin(['c' => 'cars'], 's.car_id = c.id')
                ->innerJoin(['u' => 'users'], 'c.driver_id = u.id')
                ->where(['s.route_id' => $routeId])
                ->andWhere('s.actual_time IS NOT NULL AND s.planned_time IS NOT NULL')
                ->groupBy(['u.id', 'u.full_name'])
                ->orderBy(['total_delay_seconds' => SORT_DESC])
                ->limit(1)
                ->one();

            if ($row) {
                $result = [
                    'driver' => \app\models\repository\User::findOne($row['id']),
                    'total_delay_seconds' => (int) $row['total_delay_seconds'],
                    'total_delay_minutes' => round(((int) $row['total_delay_seconds']) / 60, 2),
                ];
            }
        }

        return $this->render('latest-driver', [
            'routes' => $routes,
            'selectedRouteId' => $routeId,
            'result' => $result,
        ]);
    }
}
