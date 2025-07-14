<?php

namespace app\models\repository;

use Yii;

/**
 * This is the model class for table "cars".
 *
 * @property int $id
 * @property int|null $fare
 * @property int|null $production_year
 * @property int $owner_id
 * @property int $driver_id
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property int $brand_id
 * @property string $model
 *
 * @property CarBrand $brand
 * @property User $driver
 * @property User $owner
 * @property Route[] $routes
 * @property Schedule[] $schedules
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
            [['fare', 'production_year'], 'default', 'value' => null],
            [['fare', 'production_year', 'owner_id', 'driver_id', 'brand_id'], 'default', 'value' => null],
            [['fare', 'production_year', 'owner_id', 'driver_id', 'brand_id'], 'integer'],
            [['owner_id', 'driver_id', 'brand_id', 'model'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['model'], 'string', 'max' => 255],
            [['brand_id'], 'exist', 'skipOnError' => true, 'targetClass' => CarBrand::class, 'targetAttribute' => ['brand_id' => 'id']],
            [['owner_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['owner_id' => 'id']],
            [['driver_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['driver_id' => 'id']],
            [['driver_id'], 'validateDriverExperience'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'fare' => 'Стоимость проезда',
            'production_year' => 'Год производства',
            'owner_id' => 'Владелец',
            'driver_id' => 'Водитель',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления',
            'brand_id' => 'Производитель',
            'model' => 'Марка',
        ];
    }
    public function beforeSave($insert): bool
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->created_at = date('Y-m-d H:i:s');
            }
            $this->updated_at = date('Y-m-d H:i:s');
            return true;
        }
        return false;
    }

    /**
     * Gets query for [[Brand]].
     *
     * @return \yii\db\ActiveQuery|CarBrandQuery
     */
    public function getBrand()
    {
        return $this->hasOne(CarBrand::class, ['id' => 'brand_id']);
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
     * @return \yii\db\ActiveQuery|RouteQuery
     */
    public function getRoutes()
    {
        return $this->hasMany(Route::class, ['car_id' => 'id']);
    }

    /**
     * Gets query for [[Schedules]].
     *
     * @return \yii\db\ActiveQuery|ScheduleQuery
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

    /**
     * Проверяет, что водитель имеет стаж более 3 лет.
     */
    public function validateDriverExperience($attribute, $params)
    {
        $driver = User::findOne($this->driver_id);
        if (!$driver || !$driver->license_date) {
            $this->addError($attribute, 'Не удалось определить дату получения прав водителя.');
            return;
        }
        $threeYearsAgo = strtotime('-3 years');
        $licenseDate = strtotime($driver->license_date);
        if ($licenseDate > $threeYearsAgo) {
            $this->addError($attribute, 'Водитель должен иметь стаж не менее 3 лет.');
        }
    }

    public function publicName(): string
    {
        // например: "№1 Sprinter Mercedes 2017 г.в."
        return "№{$this->id} {$this->model} {$this->brand->name} {$this->production_year} г.в.";
    }

    /**
     * Возвращает массив доступных маршруток
     * @param array $carIds
     * @return Car[]
     */
    public static function getAvailableForTransport(array $carIds = []): array
    {
        $minYear = date('Y') - 10;
        $query = self::find()->where(['>=', 'production_year', $minYear]);

        if (!empty($carIds)) {
            $query->andWhere(['id' => $carIds]);
        }

        return $query->orderBy('id')->all();
    }
}
