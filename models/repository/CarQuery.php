<?php

namespace app\models\repository;

/**
 * This is the ActiveQuery class for [[Car]].
 *
 * @see Car
 */
class CarQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Car[]|array
     */
    public function all($db = null): array
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Car|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * Добавляет условие с маршрутками не старше 10 лет
     * @return CarQuery
     */
    public function availableYear(): CarQuery
    {
        $minYear = date('Y') - 10;
        return $this->andWhere(['>=', 'production_year', $minYear]);
    }

    /**
     * Фильтр: у маршрутки меньше двух маршрутов
     * @return CarQuery
     */
    public function withLessThanTwoRoutes(): CarQuery
    {
        return $this
            ->joinWith('routes', false)
            ->groupBy('cars.id')
            ->having(['<', 'COUNT(routes.id)', 2]);
    }

    /**
     * Фильтр: есть хотя бы один маршрут
     * @return CarQuery
     */
    public function withRoutes(): CarQuery
    {
        return $this
            ->joinWith('routes', false)
            ->groupBy('cars.id')
            ->having(['>', 'COUNT(routes.id)', 0]);
    }
}
