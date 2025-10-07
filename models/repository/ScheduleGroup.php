<?php

namespace app\models\repository;

use yii\base\Model;
use Yii;

/**
 * Модель для группировки расписаний по маршрутам
 * Представляет сводную информацию о расписании маршрута на определенную дату
 */
class ScheduleGroup extends Model
{
    public $date;
    public $car_id;
    public $route_id;
    public $route_code;
    public $route_type;
    public $route_direction_label;
    public $car_name;
    public $stops_count;
    public $first_stop_time;
    public $last_stop_time;
    public $total_boarded;
    public $completed_stops; // остановки с фактическим временем
    public $planned_stops; // остановки с планируемым временем

    /**
     * @return array the validation rules.
     */
    public function rules(): array
    {
        return [
            [['date', 'car_id', 'route_id'], 'required'],
            [['car_id', 'route_id', 'stops_count', 'total_boarded', 'completed_stops', 'planned_stops'], 'integer'],
            [['date', 'first_stop_time', 'last_stop_time'], 'safe'],
            [['route_code', 'route_type', 'route_direction_label', 'car_name'], 'string'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels(): array
    {
        return [
            'date' => 'Дата',
            'car_id' => 'Машина',
            'route_id' => 'Маршрут',
            'route_code' => 'Номер маршрута',
            'route_type' => 'Тип маршрута',
            'route_direction_label' => 'Направление',
            'car_name' => 'Машина',
            'stops_count' => 'Остановок',
            'first_stop_time' => 'Первая остановка',
            'last_stop_time' => 'Последняя остановка',
            'total_boarded' => 'Всего вошло',
            'completed_stops' => 'Выполнено',
            'planned_stops' => 'Запланировано',
        ];
    }

    /**
     * Получает сгруппированные данные расписаний
     * @param array $filters
     * @return ScheduleGroup[]
     */
    public static function getGroupedSchedules(array $filters = []): array
    {
        $query = Schedule::find()
            ->select([
                'schedules.date',
                'schedules.car_id',
                'schedules.route_id',
                'routes.code as route_code',
                'routes.type as route_type',
                'COUNT(*) as stops_count',
                'MIN(schedules.planned_time) as first_stop_time',
                'MAX(schedules.planned_time) as last_stop_time',
                'SUM(schedules.boarded_count) as total_boarded',
                'SUM(CASE WHEN schedules.actual_time IS NOT NULL THEN 1 ELSE 0 END) as completed_stops',
                'SUM(CASE WHEN schedules.planned_time IS NOT NULL THEN 1 ELSE 0 END) as planned_stops',
            ])
            ->joinWith(['car', 'route'])
            ->groupBy(['schedules.date', 'schedules.car_id', 'schedules.route_id', 'route_code', 'route_type'])
            ->orderBy(['schedules.date' => SORT_DESC, 'routes.code' => SORT_ASC]);

        // Применяем фильтры
        if (!empty($filters['date'])) {
            $query->andWhere(['schedules.date' => $filters['date']]);
        }
        if (!empty($filters['car_id'])) {
            $query->andWhere(['schedules.car_id' => $filters['car_id']]);
        }
        if (!empty($filters['route_id'])) {
            $query->andWhere(['schedules.route_id' => $filters['route_id']]);
        }
        if (!empty($filters['route_type'])) {
            $query->andWhere(['routes.type' => $filters['route_type']]);
        }

        $results = [];
        $data = $query->asArray()->all();

        foreach ($data as $row) {
            $group = new self();
            $group->setAttributes($row, false);

            // Получаем дополнительные данные
            $car = Car::findOne($row['car_id']);
            $group->car_name = $car ? $car->publicName() : 'Машина #' . $row['car_id'];

            $group->route_direction_label = Route::getTypeLabels()[$row['route_type']] ?? $row['route_type'];

            $results[] = $group;
        }

        return $results;
    }

    /**
     * Получает детальную информацию о расписании группы
     * @return Schedule[]
     */
    public function getScheduleDetails(): array
    {
        return Schedule::find()
            ->where([
                'date' => $this->date,
                'car_id' => $this->car_id,
                'route_id' => $this->route_id,
            ])
            ->orderBy(['stop_number' => SORT_ASC])
            ->all();
    }

    /**
     * Возвращает публичное имя группы расписания
     * @return string
     */
    public function getPublicName(): string
    {
        return "Маршрут №{$this->route_code} ({$this->route_direction_label}) - " .
               Yii::$app->formatter->asDate($this->date, Schedule::DATE_FORMAT);
    }

    /**
     * Возвращает статус выполнения расписания
     * @return string
     */
    public function getCompletionStatus(): string
    {
        if ($this->completed_stops == 0) {
            return 'Не начато';
        } elseif ($this->completed_stops < $this->stops_count) {
            return 'В процессе';
        } else {
            return 'Завершено';
        }
    }

    /**
     * Возвращает CSS класс для статуса
     * @return string
     */
    public function getStatusClass(): string
    {
        if ($this->completed_stops == 0) {
            return 'badge-secondary';
        } elseif ($this->completed_stops < $this->stops_count) {
            return 'badge-warning';
        } else {
            return 'badge-success';
        }
    }

    /**
     * Возвращает процент выполнения
     * @return int
     */
    public function getCompletionPercent(): int
    {
        if ($this->stops_count == 0) {
            return 0;
        }
        return round(($this->completed_stops / $this->stops_count) * 100);
    }
}
