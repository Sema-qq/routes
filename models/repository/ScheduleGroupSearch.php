<?php

namespace app\models\repository;

use yii\base\Model;
use yii\data\ArrayDataProvider;

/**
 * ScheduleGroupSearch represents the model behind the search form for grouped schedules.
 */
class ScheduleGroupSearch extends Model
{
    public $date;
    public $car_id;
    public $route_id;
    public $route_code;
    public $route_type;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['car_id', 'route_id'], 'integer'],
            [['date', 'route_code'], 'safe'],
            [['route_type'], 'in', 'range' => [Route::TYPE_DIRECT, Route::TYPE_REVERSE]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'date' => 'Дата',
            'car_id' => 'Машина',
            'route_id' => 'Маршрут',
            'route_code' => 'Номер маршрута',
            'route_type' => 'Направление',
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @return ArrayDataProvider
     */
    public function search($params): ArrayDataProvider
    {
        $this->load($params);

        $filters = [];
        if (!empty($this->date)) {
            $filters['date'] = $this->date;
        }
        if (!empty($this->car_id)) {
            $filters['car_id'] = $this->car_id;
        }
        if (!empty($this->route_id)) {
            $filters['route_id'] = $this->route_id;
        }
        if (!empty($this->route_type)) {
            $filters['route_type'] = $this->route_type;
        }

        $data = ScheduleGroup::getGroupedSchedules($filters);

        // Дополнительная фильтрация по коду маршрута (поскольку это текстовое поле)
        if (!empty($this->route_code)) {
            $data = array_filter($data, function($item) {
                return stripos($item->route_code, $this->route_code) !== false;
            });
        }

        return new ArrayDataProvider([
            'allModels' => $data,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'attributes' => [
                    'date',
                    'route_code',
                    'route_type',
                    'car_name',
                    'stops_count',
                    'total_boarded',
                    'completed_stops',
                ],
                'defaultOrder' => [
                    'date' => SORT_DESC,
                    'route_code' => SORT_ASC,
                ],
            ],
        ]);
    }
}
