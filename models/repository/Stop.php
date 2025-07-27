<?php

namespace app\models\repository;

use Yii;

/**
 * This is the model class for table "stops".
 *
 * @property int $id
 * @property string $name
 *
 * @property RouteStops[] $routeStops
 * @property Schedule[] $schedules
 */
class Stop extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'stops';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
        ];
    }

    /**
     * Gets query for [[RouteStops]].
     *
     * @return \yii\db\ActiveQuery|RouteStopsQuery
     */
    public function getRouteStops()
    {
        return $this->hasMany(RouteStops::class, ['stop_id' => 'id']);
    }

    /**
     * Gets query for [[Schedules]].
     *
     * @return \yii\db\ActiveQuery|ScheduleQuery
     */
    public function getSchedules()
    {
        return $this->hasMany(Schedule::class, ['stop_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return StopQuery the active query used by this AR class.
     */
    public static function find(): StopQuery
    {
        return new StopQuery(get_called_class());
    }

    /**
     * Возвращает все используемые в маршрутах остановки
     * @return Stop[]
     */
    public static function getUsedInRoutes(): array
    {
        return self::find()->usedInRoutes()->all();
    }
}
