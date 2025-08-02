<?php

namespace app\models;

use app\models\repository\Car;
use app\models\repository\Route;
use app\models\repository\RouteStops;
use app\models\repository\Schedule;
use app\models\repository\Stop;
use Yii;
use yii\base\Model;

/**
 * Form-модель для пошагового мастера создания расписания
 */
class ScheduleCreateForm extends Model
{
    // Общие данные
    public $date;

    // Шаг 1: Выбор маршрутки
    public $car_id;

    // Шаг 2: Выбор маршрута
    public $route_id;

    // Шаг 3: Выбор остановки
    public $stop_id;
    public $stop_number;
    public $route_stop_key;

    // Шаг 4: Ввод времени и количества пассажиров
    public $planned_time;
    public $actual_time;
    public $boarded_count;

    // Текущий шаг мастера
    public $current_step = 1;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [["date"], "required"],
            [["date"], "date", "format" => "php:Y-m-d"],

            // Шаг 1
            [["car_id"], "integer"],
            [
                ["car_id"],
                "required",
                "when" => function ($model) {
                    return $model->current_step >= 1;
                },
            ],
            [
                ["car_id"],
                "exist",
                "targetClass" => Car::class,
                "targetAttribute" => "id",
            ],
            [["car_id"], "validateCarAvailability"],

            // Шаг 2
            [["route_id"], "integer"],
            [
                ["route_id"],
                "required",
                "when" => function ($model) {
                    return $model->current_step >= 2;
                },
            ],
            [
                ["route_id"],
                "exist",
                "targetClass" => Route::class,
                "targetAttribute" => "id",
            ],
            [["route_id"], "validateRouteBelongsToCar"],
            [["route_id"], "validateRouteAvailability"],

            // Шаг 3
            [
                ["route_stop_key"],
                "required",
                "when" => function ($model) {
                    return $model->current_step >= 3;
                },
            ],
            [["route_stop_key"], "validateRouteStopKey"],
            [["stop_id", "stop_number"], "integer"],
            [
                ["stop_id"],
                "exist",
                "targetClass" => Stop::class,
                "targetAttribute" => "id",
                "when" => function ($model) {
                    return !empty($model->stop_id);
                },
            ],
            [
                ["stop_number"],
                "in",
                "range" => array_keys(RouteStops::getStopNumberList()),
                "when" => function ($model) {
                    return !empty($model->stop_number);
                },
            ],
            [
                ["stop_id", "stop_number"],
                "validateStopBelongsToRoute",
                "when" => function ($model) {
                    return !empty($model->stop_id) &&
                        !empty($model->stop_number);
                },
            ],
            [
                ["stop_id", "stop_number"],
                "validateStopAvailability",
                "when" => function ($model) {
                    return !empty($model->stop_id) &&
                        !empty($model->stop_number);
                },
            ],

            // Шаг 4
            [["planned_time", "actual_time"], "time", "format" => Schedule::TIME_FORMAT],
            [["boarded_count"], "integer", "min" => 0],
            [["boarded_count"], "default", "value" => 0],
            [["planned_time", "actual_time"], "default", "value" => null],

            // Общая валидация дублирования
            [
                ["date", "car_id", "route_id", "stop_id", "stop_number"],
                "validateUniqueSchedule",
            ],

            [["current_step"], "integer", "min" => 1, "max" => 4],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            "date" => "Дата",
            "car_id" => "Маршрутка",
            "route_id" => "Маршрут",
            "stop_id" => "Остановка",
            "stop_number" => "№ остановки",
            "planned_time" => "Планируемое время прибытия",
            "actual_time" => "Фактическое время прибытия",
            "boarded_count" => "Количество вошедших пассажиров",
        ];
    }

    /**
     * Проверяет, что у маршрутки есть маршруты и не все расписания созданы на выбранную дату
     */
    public function validateCarAvailability($attribute, $params)
    {
        if (empty($this->car_id) || empty($this->date)) {
            return;
        }

        $car = Car::findOne($this->car_id);
        if (!$car) {
            $this->addError($attribute, "Маршрутка не найдена.");
            return;
        }

        // Проверяем, что у маршрутки есть маршруты
        $routesCount = Route::find()
            ->where(["car_id" => $this->car_id])
            ->count();
        if ($routesCount == 0) {
            $this->addError($attribute, "У выбранной маршрутки нет маршрутов.");
            return;
        }

        // Проверяем, что не все расписания созданы на эту дату
        $availableRoutes = $this->getAvailableRoutes();
        if (empty($availableRoutes)) {
            $this->addError(
                $attribute,
                "Для выбранной маршрутки на эту дату уже созданы расписания для всех маршрутов.",
            );
        }
    }

    /**
     * Проверяет, что маршрут принадлежит выбранной маршрутке
     */
    public function validateRouteBelongsToCar($attribute, $params)
    {
        if (empty($this->route_id) || empty($this->car_id)) {
            return;
        }

        $route = Route::findOne([
            "id" => $this->route_id,
            "car_id" => $this->car_id,
        ]);
        if (!$route) {
            $this->addError(
                $attribute,
                "Выбранный маршрут не принадлежит выбранной маршрутке.",
            );
        }
    }

    /**
     * Проверяет, что для маршрута можно создать расписание на выбранную дату
     */
    public function validateRouteAvailability($attribute, $params)
    {
        if (empty($this->route_id) || empty($this->date)) {
            return;
        }

        $availableRoutes = $this->getAvailableRoutes();
        if (!in_array($this->route_id, array_keys($availableRoutes))) {
            $this->addError(
                $attribute,
                "Для выбранного маршрута на эту дату уже созданы все расписания.",
            );
        }
    }

    /**
     * Проверяет, что валидное значение пришло
     */
    public function validateRouteStopKey($attribute, $params)
    {
        if (empty($this->route_stop_key)) {
            $this->addError($attribute, "Остановка не выбрана.");
            return;
        }

        if (strpos($this->route_stop_key, "_") === false) {
            $this->addError($attribute, "Неверный формат ключа остановки.");
            return;
        }

        $parts = explode("_", $this->route_stop_key);
        if (count($parts) != 2 || empty($parts[0]) || empty($parts[1])) {
            $this->addError($attribute, "Неверный формат ключа остановки.");
            return;
        }

        $stopId = (int) $parts[0];
        $stopNumber = (int) $parts[1];

        if ($stopId <= 0 || $stopNumber <= 0) {
            $this->addError($attribute, "Некорректные данные остановки.");
            return;
        }

        // Если валидация прошла успешно, заполняем поля
        $this->stop_id = $stopId;
        $this->stop_number = $stopNumber;
    }

    /**
     * Проверяет, что остановка принадлежит выбранному маршруту
     */
    public function validateStopBelongsToRoute($attribute, $params)
    {
        // Если stop_id содержит комбинированный ключ, извлекаем данные
        if (!empty($this->stop_id) && strpos($this->stop_id, "_") !== false) {
            $parts = explode("_", $this->stop_id);
            if (count($parts) == 2) {
                $actualStopId = (int) $parts[0];
                $actualStopNumber = (int) $parts[1];
            } else {
                $this->addError(
                    $attribute,
                    "Неверный формат данных остановки.",
                );
                return;
            }
        } else {
            $actualStopId = (int) $this->stop_id;
            $actualStopNumber = (int) $this->stop_number;
        }

        if (
            empty($actualStopId) ||
            empty($actualStopNumber) ||
            empty($this->route_id)
        ) {
            return;
        }

        $routeStop = RouteStops::findOne([
            "route_id" => $this->route_id,
            "stop_id" => $actualStopId,
            "stop_number" => $actualStopNumber,
        ]);

        if (!$routeStop) {
            $this->addError(
                $attribute,
                "Выбранная остановка с указанным номером не принадлежит выбранному маршруту.",
            );
        }
    }

    /**
     * Проверяет, что для остановки можно создать расписание
     */
    public function validateStopAvailability($attribute, $params)
    {
        // Если stop_id содержит комбинированный ключ, используем его напрямую
        if (!empty($this->stop_id) && strpos($this->stop_id, "_") !== false) {
            $stopKey = $this->stop_id;
        } else {
            if (empty($this->stop_id) || empty($this->stop_number)) {
                return;
            }
            $stopKey = $this->stop_id . "_" . $this->stop_number;
        }

        if (
            empty($this->route_id) ||
            empty($this->car_id) ||
            empty($this->date)
        ) {
            return;
        }

        $availableStops = $this->getAvailableStops();

        if (!array_key_exists($stopKey, $availableStops)) {
            $this->addError(
                $attribute,
                "Для выбранной остановки на эту дату уже создано расписание.",
            );
        }
    }

    /**
     * Проверяет, что не создается дубликат расписания
     */
    public function validateUniqueSchedule($attribute, $params)
    {
        // Получаем актуальные ID остановки и номер
        $actualStopId = $this->getActualStopId();
        $actualStopNumber = $this->getActualStopNumber();

        if (
            empty($this->date) ||
            empty($this->car_id) ||
            empty($this->route_id) ||
            empty($actualStopId) ||
            empty($actualStopNumber)
        ) {
            return;
        }

        $existingSchedule = Schedule::findOne([
            "date" => $this->date,
            "car_id" => $this->car_id,
            "route_id" => $this->route_id,
            "stop_id" => $actualStopId,
            "stop_number" => $actualStopNumber,
        ]);

        if ($existingSchedule) {
            $this->addError(
                $attribute,
                "Расписание для данной комбинации уже существует.",
            );
        }
    }

    /**
     * Возвращает список доступных маршруток для выбранной даты
     */
    public function getAvailableCars()
    {
        if (empty($this->date)) {
            return [];
        }

        $cars = Car::find()
            ->availableYear()
            ->joinWith("routes")
            ->where(["not", ["routes.id" => null]])
            ->groupBy("cars.id")
            ->all();

        $availableCars = [];
        foreach ($cars as $car) {
            $availableRoutes = $this->getAvailableRoutesForCar($car->id);
            if (!empty($availableRoutes)) {
                $availableCars[$car->id] = $car->publicName();
            }
        }

        return $availableCars;
    }

    /**
     * Возвращает список доступных маршрутов для выбранной маршрутки и даты
     */
    public function getAvailableRoutes()
    {
        if (empty($this->car_id) || empty($this->date)) {
            return [];
        }

        return $this->getAvailableRoutesForCar($this->car_id);
    }

    /**
     * Возвращает список доступных маршрутов для указанной маршрутки
     */
    private function getAvailableRoutesForCar($carId)
    {
        if (empty($this->date)) {
            return [];
        }

        $allRoutes = Route::find()
            ->where(["car_id" => $carId])
            ->all();

        $availableRoutes = [];
        foreach ($allRoutes as $route) {
            // Проверяем, есть ли еще доступные остановки для этого маршрута
            $routeStopsCount = RouteStops::find()
                ->where(["route_id" => $route->id])
                ->count();
            $existingSchedulesCount = Schedule::find()
                ->where([
                    "date" => $this->date,
                    "car_id" => $carId,
                    "route_id" => $route->id,
                ])
                ->count();

            if ($existingSchedulesCount < $routeStopsCount) {
                $typeLabel =
                    Route::getTypeLabels()[$route->type] ?? $route->type;
                $availableRoutes[
                    $route->id
                ] = "Маршрут №{$route->id} ({$typeLabel})";
            }
        }

        return $availableRoutes;
    }

    /**
     * Возвращает список доступных остановок для выбранного маршрута
     */
    public function getAvailableStops()
    {
        if (
            empty($this->route_id) ||
            empty($this->car_id) ||
            empty($this->date)
        ) {
            return [];
        }

        $routeStops = RouteStops::find()
            ->joinWith("stop")
            ->where(["route_id" => $this->route_id])
            ->orderBy("stop_number")
            ->all();

        $availableStops = [];
        foreach ($routeStops as $routeStop) {
            // Проверяем, нет ли уже расписания для этой остановки
            $existingSchedule = Schedule::findOne([
                "date" => $this->date,
                "car_id" => $this->car_id,
                "route_id" => $this->route_id,
                "stop_id" => $routeStop->stop_id,
                "stop_number" => $routeStop->stop_number,
            ]);

            if (!$existingSchedule) {
                $stopKey = $routeStop->stop_id . "_" . $routeStop->stop_number;
                $availableStops[
                    $stopKey
                ] = "№{$routeStop->stop_number} — {$routeStop->stop->name}";
            }
        }

        return $availableStops;
    }

    /**
     * Возвращает список номеров остановок
     */
    public function getStopNumbersList()
    {
        return RouteStops::getStopNumberList();
    }

    /**
     * Сохраняет данные мастера в сессию
     */
    public function saveToSession()
    {
        $sessionKey = "schedule_wizard_data";
        Yii::$app->session->set($sessionKey, $this->toArray());
    }

    /**
     * Загружает данные мастера из сессии
     */
    public function loadFromSession()
    {
        $sessionKey = "schedule_wizard_data";
        $data = Yii::$app->session->get($sessionKey, []);

        if (!empty($data)) {
            $this->setAttributes($data, false);
        }

        return !empty($data);
    }

    /**
     * Очищает данные мастера из сессии
     */
    public function clearSession()
    {
        $sessionKey = "schedule_wizard_data";
        Yii::$app->session->remove($sessionKey);
    }

    /**
     * Создает и сохраняет модель Schedule на основе данных мастера
     */
    public function createSchedule()
    {
        if (!$this->validate()) {
            return false;
        }

        $schedule = new Schedule();
        $schedule->date = $this->date;
        $schedule->car_id = $this->car_id;
        $schedule->route_id = $this->route_id;
        $schedule->stop_id = $this->getActualStopId();
        $schedule->stop_number = $this->getActualStopNumber();
        $schedule->planned_time = $this->planned_time;
        $schedule->actual_time = $this->actual_time;
        $schedule->boarded_count = $this->boarded_count;

        if ($schedule->save()) {
            $this->clearSession();
            return $schedule;
        }

        // Переносим ошибки из модели Schedule в форму
        foreach ($schedule->errors as $attribute => $errors) {
            foreach ($errors as $error) {
                $this->addError($attribute, $error);
            }
        }

        return false;
    }

    /**
     * Переходит к следующему шагу
     */
    public function nextStep()
    {
        if ($this->current_step < 4) {
            $this->current_step++;
        }
    }

    /**
     * Переходит к предыдущему шагу
     */
    public function previousStep()
    {
        if ($this->current_step > 1) {
            $this->current_step--;
        }
    }

    /**
     * Устанавливает текущий шаг
     */
    public function setStep($step)
    {
        if ($step >= 1 && $step <= 4) {
            $this->current_step = $step;
        }
    }

    /**
     * Проверяет, можно ли перейти к указанному шагу
     */
    public function canGoToStep($step)
    {
        switch ($step) {
            case 1:
                return !empty($this->date);
            case 2:
                return !empty($this->date) && !empty($this->car_id);
            case 3:
                return !empty($this->date) &&
                    !empty($this->car_id) &&
                    !empty($this->route_id);
            case 4:
                // Проверяем либо route_stop_key, либо отдельные stop_id и stop_number
                $hasStopData = false;
                if (!empty($this->route_stop_key)) {
                    $parts = explode("_", $this->route_stop_key);
                    $hasStopData =
                        count($parts) == 2 &&
                        !empty($parts[0]) &&
                        !empty($parts[1]);
                } else {
                    $hasStopData =
                        !empty($this->stop_id) && !empty($this->stop_number);
                }

                return !empty($this->date) &&
                    !empty($this->car_id) &&
                    !empty($this->route_id) &&
                    $hasStopData;
            default:
                return false;
        }
    }

    /**
     * Возвращает название текущего шага
     */
    public function getStepTitle()
    {
        $titles = [
            1 => "Выбор маршрутки",
            2 => "Выбор маршрута",
            3 => "Выбор остановки",
            4 => "Ввод данных расписания",
        ];

        return $titles[$this->current_step] ?? "Неизвестный шаг";
    }

    /**
     * Получает актуальный ID остановки из комбинированного ключа или напрямую
     */
    public function getActualStopId()
    {
        if (!empty($this->stop_id) && strpos($this->stop_id, "_") !== false) {
            $parts = explode("_", $this->stop_id);
            return count($parts) == 2 ? (int) $parts[0] : null;
        }
        return (int) $this->stop_id;
    }

    /**
     * Получает актуальный номер остановки из комбинированного ключа или напрямую
     */
    public function getActualStopNumber()
    {
        if (!empty($this->stop_id) && strpos($this->stop_id, "_") !== false) {
            $parts = explode("_", $this->stop_id);
            return count($parts) == 2 ? (int) $parts[1] : null;
        }
        return (int) $this->stop_number;
    }
}
