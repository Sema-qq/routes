<?php

namespace app\models\repository;

use Yii;

/**
 * This is the model class for table "routes".
 *
 * @property int $id
 * @property int $car_id
 * @property string $type
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Car $car
 * @property RouteStops[] $routeStops
 * @property Schedule[] $schedules
 */
class Route extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'routes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['car_id', 'type'], 'required'],
            [['car_id'], 'default', 'value' => null],
            [['car_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['type'], 'string', 'max' => 16],
            [['car_id'], 'exist', 'skipOnError' => true, 'targetClass' => Car::class, 'targetAttribute' => ['car_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'car_id' => 'Car ID',
            'type' => 'Type',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
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
     * Gets query for [[RouteStops]].
     *
     * @return \yii\db\ActiveQuery|RouteStopsQuery
     */
    public function getRouteStops()
    {
        return $this->hasMany(RouteStops::class, ['route_id' => 'id']);
    }

    /**
     * Gets query for [[Schedules]].
     *
     * @return \yii\db\ActiveQuery|ScheduleQuery
     */
    public function getSchedules()
    {
        return $this->hasMany(Schedule::class, ['route_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return RouteQuery the active query used by this AR class.
     */
    public static function find(): RouteQuery
    {
        return new RouteQuery(get_called_class());
    }

}
