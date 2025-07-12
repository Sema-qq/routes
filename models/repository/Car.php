<?php

namespace app\models\repository;

use Yii;

/**
 * This is the model class for table "cars".
 *
 * @property int $id
 * @property string|null $brand
 * @property int|null $fare
 * @property string|null $manufacturer
 * @property string|null $country
 * @property int|null $production_year
 * @property int $owner_id
 * @property int $driver_id
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Users $driver
 * @property Users $owner
 * @property Routes[] $routes
 * @property Schedules[] $schedules
 */
class Car extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'cars';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['brand', 'fare', 'manufacturer', 'country', 'production_year'], 'default', 'value' => null],
            [['fare', 'production_year', 'owner_id', 'driver_id'], 'default', 'value' => null],
            [['fare', 'production_year', 'owner_id', 'driver_id'], 'integer'],
            [['owner_id', 'driver_id'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['brand', 'manufacturer', 'country'], 'string', 'max' => 255],
            [['owner_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['owner_id' => 'id']],
            [['driver_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['driver_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'brand' => 'Brand',
            'fare' => 'Fare',
            'manufacturer' => 'Manufacturer',
            'country' => 'Country',
            'production_year' => 'Production Year',
            'owner_id' => 'Owner ID',
            'driver_id' => 'Driver ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Driver]].
     *
     * @return \yii\db\ActiveQuery|UserQuery
     */
    public function getDriver()
    {
        return $this->hasOne(User::class, ['id' => 'driver_id']);
    }

    /**
     * Gets query for [[Owner]].
     *
     * @return \yii\db\ActiveQuery|UserQuery
     */
    public function getOwner()
    {
        return $this->hasOne(User::class, ['id' => 'owner_id']);
    }

    /**
     * Gets query for [[Routes]].
     *
     * @return \yii\db\ActiveQuery|RoutesQuery
     */
    public function getRoutes()
    {
        return $this->hasMany(Route::class, ['car_id' => 'id']);
    }

    /**
     * Gets query for [[Schedules]].
     *
     * @return \yii\db\ActiveQuery|SchedulesQuery
     */
    public function getSchedules()
    {
        return $this->hasMany(Schedule::class, ['car_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return CarQuery the active query used by this AR class.
     */
    public static function find(): CarQuery
    {
        return new CarQuery(get_called_class());
    }

}
