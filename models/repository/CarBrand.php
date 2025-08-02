<?php

namespace app\models\repository;

use Yii;

/**
 * This is the model class for table "car_brand".
 *
 * @property int $id
 * @property string $name Бренд (марка) автомобиля
 * @property string|null $country Страна-производитель
 *
 * @property Car[] $cars
 */
class CarBrand extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'car_brand';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name', 'country'], 'required'],
            [['name', 'country'], 'string', 'max' => 255],
            [['name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Наименование',
            'country' => 'Страна',
        ];
    }

    /**
     * Gets query for [[Cars]].
     *
     * @return \yii\db\ActiveQuery|CarQuery
     */
    public function getCars()
    {
        return $this->hasMany(Car::class, ['brand_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return CarBrandQuery the active query used by this AR class.
     */
    public static function find(): CarBrandQuery
    {
        return new CarBrandQuery(get_called_class());
    }

    public function cantBeDeleted(): bool
    {
        return $this->getCars()->exists();
    }
}
