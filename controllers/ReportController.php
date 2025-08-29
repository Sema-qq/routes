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
        $startDate = Yii::$app->request->get("start_date", date("Y-m-01"));
        $endDate = Yii::$app->request->get("end_date", date("Y-m-d"));

        $query = (new Query())
            ->select([
                "c.id as car_id",
                "c.model",
                "cb.name as brand_name",
                "r.id as route_id",
                "r.type",
                "AVG(daily_revenue.revenue) as avg_daily_revenue",
            ])
            ->from([
                "daily_revenue" => (new Query())
                    ->select([
                        "s.car_id",
                        "s.route_id",
                        "s.date",
                        "SUM(s.boarded_count * c.fare) as revenue",
                    ])
                    ->from("schedules s")
                    ->leftJoin("cars c", "s.car_id = c.id")
                    ->where(["between", "s.date", $startDate, $endDate])
                    ->groupBy(["s.car_id", "s.route_id", "s.date"]),
            ])
            ->leftJoin("cars c", "daily_revenue.car_id = c.id")
            ->leftJoin("car_brands cb", "c.brand_id = cb.id")
            ->leftJoin("routes r", "daily_revenue.route_id = r.id")
            ->groupBy(["c.id", "c.model", "cb.name", "r.id", "r.type"])
            ->orderBy(["avg_daily_revenue" => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            "query" => $query,
            "pagination" => [
                "pageSize" => 20,
            ],
        ]);

        return $this->render("daily-revenue", [
            "dataProvider" => $dataProvider,
            "startDate" => $startDate,
            "endDate" => $endDate,
        ]);
    }

    /**
     * Отчет: Водитель, который больше всего опаздывает на определенном маршруте
     * @return string
     */
    public function actionLatestDriver()
    {
        $routeId = Yii::$app->request->get("route_id");
        $routes = Route::find()->with("car")->all();

        $result = null;

        if ($routeId) {
            $query = (new Query())
                ->select([
                    "u.id",
                    "u.first_name",
                    "u.last_name",
                    "u.middle_name",
                    "SUM(TIME_TO_SEC(s.actual_time) - TIME_TO_SEC(s.planned_time)) as total_delay_seconds",
                ])
                ->from("schedules s")
                ->leftJoin("cars c", "s.car_id = c.id")
                ->leftJoin("users u", "c.driver_id = u.id")
                ->where([
                    "s.route_id" => $routeId,
                    "and",
                    ["not", ["s.actual_time" => null]],
                    ["not", ["s.planned_time" => null]],
                    "TIME_TO_SEC(s.actual_time) > TIME_TO_SEC(s.planned_time)",
                ])
                ->groupBy([
                    "u.id",
                    "u.first_name",
                    "u.last_name",
                    "u.middle_name",
                ])
                ->orderBy(["total_delay_seconds" => SORT_DESC])
                ->limit(1)
                ->one();

            if ($query) {
                $result = [
                    "driver" => User::findOne($query["id"]),
                    "total_delay_seconds" => $query["total_delay_seconds"],
                    "total_delay_minutes" => round(
                        $query["total_delay_seconds"] / 60,
                        2,
                    ),
                ];
            }
        }

        return $this->render("latest-driver", [
            "routes" => $routes,
            "selectedRouteId" => $routeId,
            "result" => $result,
        ]);
    }
}
