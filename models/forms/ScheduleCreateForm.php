<?php

namespace app\models\forms;

use app\models\repository\Car;
use app\models\repository\Route;
use app\models\repository\RouteStops;
use app\models\repository\Schedule;
use Yii;
use yii\base\Model;

/**
 * Form-модель для пошагового мастера создания расписания
 *
 * Шаг 1: Выбор маршрута (по коду)
 * Шаг 2: Выбор направления маршрута (прямой/обратный)
 * Шаг 3: Выбор машины на маршрут
 * Шаг 4: Указание времени для всех 10 остановок
 */
class ScheduleCreateForm extends Model
{
    // Общие данные
    public $date;

    // Шаг 1: Выбор маршрута
    public $route_code;

    // Шаг 2: Выбор направления маршрута
    public $route_direction; // 'direct' или 'reverse'
    public $route_id; // будет заполнен автоматически после выбора направления

    // Шаг 3: Выбор машины
    public $car_id;

    // Шаг 4: Данные для всех 10 остановок
    public $stops_data = []; // массив с данными остановок

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
            [["route_code"], "string", "max" => 16],
            [["route_code"], "validateRouteCodeExists"],

            // Шаг 2
            [
                ["route_direction"],
                "in",
                "range" => [Route::TYPE_DIRECT, Route::TYPE_REVERSE],
            ],
            [["route_id"], "integer"],
            [["route_id"], "validateRouteExists"],

            // Шаг 3
            [["car_id"], "integer"],
            [
                ["car_id"],
                "exist",
                "targetClass" => Car::class,
                "targetAttribute" => "id",
            ],
            [["car_id"], "validateCarAvailability"],

            // Шаг 4
            [["stops_data"], "safe"],
            [["stops_data"], "validateStopsData"],

            [["current_step"], "integer", "min" => 1, "max" => 4],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            "date" => "Дата",
            "route_code" => "Номер маршрута",
            "route_direction" => "Направление маршрута",
            "car_id" => "Машина",
            "stops_data" => "Данные остановок",
        ];
    }

    /**
     * Валидация существования маршрута по коду
     */
    public function validateRouteCodeExists($attribute, $params)
    {
        if (!empty($this->route_code)) {
            $exists = Route::find()
                ->where(["code" => $this->route_code])
                ->exists();

            if (!$exists) {
                $this->addError(
                    $attribute,
                    "Маршрут с таким номером не существует.",
                );
            }
        }
    }

    /**
     * Валидация существования конкретного маршрута
     */
    public function validateRouteExists($attribute, $params)
    {
        if (!empty($this->route_id)) {
            $route = Route::findOne($this->route_id);
            if (!$route) {
                $this->addError($attribute, "Выбранный маршрут не найден.");
            }
        }
    }

    /**
     * Валидация доступности машины на выбранную дату
     */
    public function validateCarAvailability($attribute, $params)
    {
        if (
            !empty($this->car_id) &&
            !empty($this->date) &&
            !empty($this->route_id)
        ) {
            // Проверяем, что на эту дату и маршрут машина еще не назначена
            $exists = Schedule::find()
                ->where([
                    "date" => $this->date,
                    "car_id" => $this->car_id,
                    "route_id" => $this->route_id,
                ])
                ->exists();

            if ($exists) {
                $this->addError(
                    $attribute,
                    "На выбранную дату эта машина уже назначена на данный маршрут.",
                );
            }
        }
    }

    /**
     * Валидация данных остановок
     */
    public function validateStopsData($attribute, $params)
    {
        if (!is_array($this->stops_data) || count($this->stops_data) !== 10) {
            $this->addError(
                $attribute,
                "Должно быть указано время для всех 10 остановок.",
            );
            return;
        }

        foreach ($this->stops_data as $index => $stopData) {
            if (
                !isset($stopData["planned_time"]) ||
                empty($stopData["planned_time"])
            ) {
                $this->addError(
                    $attribute,
                    "Не указано планируемое время для остановки №" .
                        ($index + 1),
                );
            }

            // Валидация формата времени
            if (
                !empty($stopData["planned_time"]) &&
                !$this->isValidTime($stopData["planned_time"])
            ) {
                $this->addError(
                    $attribute,
                    "Неверный формат планируемого времени для остановки №" .
                        ($index + 1),
                );
            }

            if (
                !empty($stopData["actual_time"]) &&
                !$this->isValidTime($stopData["actual_time"])
            ) {
                $this->addError(
                    $attribute,
                    "Неверный формат фактического времени для остановки №" .
                        ($index + 1),
                );
            }

            // Валидация количества вошедших
            if (
                isset($stopData["boarded_count"]) &&
                (!is_numeric($stopData["boarded_count"]) ||
                    (int) $stopData["boarded_count"] < 0)
            ) {
                $this->addError(
                    $attribute,
                    "Количество вошедших для остановки №" .
                        ($index + 1) .
                        " должно быть положительным числом",
                );
            }
        }
    }

    /**
     * Проверка корректности формата времени
     */
    private function isValidTime($time)
    {
        return (bool) preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $time);
    }

    /**
     * Получение доступных маршрутов (группировка по коду)
     * @return array
     */
    public function getAvailableRouteCodes(): array
    {
        $routes = Route::find()
            ->select(["code"])
            ->distinct()
            ->orderBy(["code" => SORT_ASC])
            ->asArray()
            ->all();

        $result = [];
        foreach ($routes as $route) {
            $result[$route["code"]] = $route["code"];
        }

        return $result;
    }

    /**
     * Получение доступных направлений для выбранного маршрута
     * @return array
     */
    public function getAvailableDirections(): array
    {
        if (empty($this->route_code)) {
            return [];
        }

        $routes = Route::find()
            ->withoutSchedules()
            ->byCode($this->route_code)
            ->all();

        $directions = [];
        foreach ($routes as $route) {
            $directions[$route->type] = Route::getTypeLabels()[$route->type];
        }

        return $directions;
    }

    /**
     * Получение доступных машин
     * @return array
     */
    public function getAvailableCars(): array
    {
        if (empty($this->date) || empty($this->route_id)) {
            return [];
        }

        // Получаем все машины, которые еще не назначены на этот маршрут в эту дату
        $busyCars = Schedule::find()
            ->select(["car_id"])
            ->where([
                "date" => $this->date,
                "route_id" => $this->route_id,
            ])
            ->distinct()
            ->column();

        $query = Car::find()->availableYear();
        if (!empty($busyCars)) {
            $query->andWhere(["not in", "id", $busyCars]);
        }

        $cars = $query->all();

        $result = [];
        foreach ($cars as $car) {
            $result[$car->id] = $car->publicName();
        }

        return $result;
    }

    /**
     * Получение остановок для выбранного маршрута
     * @return array
     */
    public function getRouteStops(): array
    {
        if (empty($this->route_id)) {
            return [];
        }

        return RouteStops::find()
            ->where(["route_id" => $this->route_id])
            ->orderBy(["stop_number" => SORT_ASC])
            ->with("stop")
            ->all();
    }

    /**
     * Инициализация данных остановок
     */
    public function initializeStopsData()
    {
        if (empty($this->route_id)) {
            return;
        }

        $routeStops = $this->getRouteStops();
        $this->stops_data = [];

        foreach ($routeStops as $routeStop) {
            $this->stops_data[] = [
                "stop_id" => $routeStop->stop_id,
                "stop_number" => $routeStop->stop_number,
                "stop_name" => $routeStop->stop->name,
                "planned_time" => "",
                "actual_time" => "",
                "boarded_count" => 0,
            ];
        }
    }

    /**
     * Сохранение данных в сессию
     */
    public function saveToSession(): void
    {
        Yii::$app->session->set("schedule_create_form", $this->attributes);
    }

    /**
     * Загрузка данных из сессии
     * @return bool
     */
    public function loadFromSession(): bool
    {
        $data = Yii::$app->session->get("schedule_create_form");
        if ($data) {
            $this->setAttributes($data, false);
            return true;
        }
        return false;
    }

    /**
     * Очистка сессии
     */
    public function clearSession(): void
    {
        Yii::$app->session->remove("schedule_create_form");
    }

    /**
     * Создание расписания
     * @return bool
     */
    public function createSchedule(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach ($this->stops_data as $stopData) {
                $schedule = new Schedule();
                $schedule->date = $this->date;
                $schedule->car_id = $this->car_id;
                $schedule->route_id = $this->route_id;
                $schedule->stop_id = $stopData["stop_id"];
                $schedule->stop_number = $stopData["stop_number"];
                $schedule->planned_time = !empty($stopData["planned_time"])
                    ? $stopData["planned_time"]
                    : null;
                $schedule->actual_time = !empty($stopData["actual_time"])
                    ? $stopData["actual_time"]
                    : null;
                $schedule->boarded_count = isset($stopData["boarded_count"])
                    ? (int) $stopData["boarded_count"]
                    : 0;

                if (!$schedule->save()) {
                    throw new \Exception(
                        "Не удалось сохранить расписание для остановки №" .
                            $stopData["stop_number"],
                    );
                }
            }

            $transaction->commit();
            $this->clearSession();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->addError("stops_data", $e->getMessage());
            return false;
        }
    }

    /**
     * Переход к следующему шагу
     */
    public function nextStep(): void
    {
        if ($this->current_step < 4) {
            $this->current_step++;
        }
    }

    /**
     * Переход к предыдущему шагу
     */
    public function previousStep(): void
    {
        if ($this->current_step > 1) {
            $this->current_step--;
        }
    }

    /**
     * Установка конкретного шага
     * @param int $step
     */
    public function setStep(int $step): void
    {
        if ($step >= 1 && $step <= 4) {
            $this->current_step = $step;
        }
    }

    /**
     * Проверка возможности перехода к шагу
     * @param int $step
     * @return bool
     */
    public function canGoToStep(int $step): bool
    {
        switch ($step) {
            case 1:
                return !empty($this->date);
            case 2:
                return !empty($this->date) && !empty($this->route_code);
            case 3:
                return !empty($this->date) &&
                    !empty($this->route_code) &&
                    !empty($this->route_direction) &&
                    !empty($this->route_id);
            case 4:
                return !empty($this->date) &&
                    !empty($this->route_code) &&
                    !empty($this->route_direction) &&
                    !empty($this->route_id) &&
                    !empty($this->car_id);
            default:
                return false;
        }
    }

    /**
     * Получение названия шага
     * @param int $step
     * @return string
     */
    public function getStepTitle(int $step): string
    {
        $titles = [
            1 => "Выбор маршрута",
            2 => "Выбор направления",
            3 => "Выбор машины",
            4 => "Время прибытия",
        ];

        return $titles[$step] ?? "";
    }

    /**
     * Установка route_id после выбора направления
     */
    public function setRouteId()
    {
        if (!empty($this->route_code) && !empty($this->route_direction)) {
            $route = Route::find()
                ->where([
                    "code" => $this->route_code,
                    "type" => $this->route_direction,
                ])
                ->one();

            if ($route) {
                $this->route_id = $route->id;
            }
        }
    }
}
