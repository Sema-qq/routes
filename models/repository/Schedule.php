<?php

namespace app\models\repository;

use Yii;

/**
 * This is the model class for table "schedules".
 *
 * @property int $id
 * @property string $date
 * @property int $car_id
 * @property int $route_id
 * @property int $stop_id
 * @property int $stop_number
 * @property string|null $planned_time
 * @property string|null $actual_time
 * @property int|null $boarded_count
 *
 * @property Car $car
 * @property Route $route
 * @property Stop $stop
 */
class Schedule extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'schedules';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['date'], 'date', 'format' => 'php:Y-m-d'],
            [['planned_time', 'actual_time'], 'time', 'format' => 'php:H:i'],
            [['stop_number'], 'integer', 'min' => RouteStops::STOP_NUMBER_MIN, 'max' => RouteStops::STOP_NUMBER_MAX],

            [['planned_time', 'actual_time'], 'default', 'value' => null],
            [['boarded_count'], 'default', 'value' => 0],
            [['date', 'car_id', 'route_id', 'stop_id', 'stop_number'], 'required'],
            [['date', 'planned_time', 'actual_time'], 'safe'],
            [['car_id', 'route_id', 'stop_id', 'stop_number', 'boarded_count'], 'default', 'value' => null],
            [['car_id', 'route_id', 'stop_id', 'stop_number', 'boarded_count'], 'integer'],
            [['car_id'], 'exist', 'skipOnError' => true, 'targetClass' => Car::class, 'targetAttribute' => ['car_id' => 'id']],
            [['route_id'], 'exist', 'skipOnError' => true, 'targetClass' => Route::class, 'targetAttribute' => ['route_id' => 'id']],
            [['stop_id'], 'exist', 'skipOnError' => true, 'targetClass' => Stop::class, 'targetAttribute' => ['stop_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => '№',
            'date' => 'Дата',
            'car_id' => 'Маршрутка',
            'route_id' => 'Маршрут',
            'stop_id' => 'Остановка',
            'stop_number' => '№ остановки',
            'planned_time' => 'Планируемое время прибытия',
            'actual_time' => 'Фактическое время прибытия',
            'boarded_count' => 'Количество вошедших',
        ];
    }

    /**
     * Gets query for [[Car]].
     *
     * @return \yii\db\ActiveQuery|CarQuery
     */
    public function getCar()
    {
        return $this->hasOne(Car::class, ['id' => 'car_id']);
    }

    /**
     * Gets query for [[Route]].
     *
     * @return \yii\db\ActiveQuery|RouteQuery
     */
    public function getRoute()
    {
        return $this->hasOne(Route::class, ['id' => 'route_id']);
    }

    /**
     * Gets query for [[Stop]].
     *
     * @return \yii\db\ActiveQuery|StopQuery
     */
    public function getStop()
    {
        return $this->hasOne(Stop::class, ['id' => 'stop_id']);
    }

    /**
     * {@inheritdoc}
     * @return ScheduleQuery the active query used by this AR class.
     */
    public static function find(): ScheduleQuery
    {
        return new ScheduleQuery(get_called_class());
    }

}
