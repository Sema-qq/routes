<?php

namespace app\models\repository;

use Yii;

/**
 * This is the model class for table "route_stops".
 *
 * @property int $id
 * @property int $route_id
 * @property int $stop_id
 * @property int $stop_number
 *
 * @property Route $route
 * @property Stop $stop
 */
class RouteStops extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'route_stops';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['route_id', 'stop_id', 'stop_number'], 'required'],
            [['route_id', 'stop_id', 'stop_number'], 'default', 'value' => null],
            [['route_id', 'stop_id', 'stop_number'], 'integer'],
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
            'id' => 'ID',
            'route_id' => 'Route ID',
            'stop_id' => 'Stop ID',
            'stop_number' => 'Stop Number',
        ];
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
     * @return RouteStopsQuery the active query used by this AR class.
     */
    public static function find(): RouteStopsQuery
    {
        return new RouteStopsQuery(get_called_class());
    }

}
