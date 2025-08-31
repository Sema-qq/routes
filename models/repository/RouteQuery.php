<?php

namespace app\models\repository;

/**
 * This is the ActiveQuery class for [[Route]].
 *
 * @see Route
 */
class RouteQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Route[]|array
     */
    public function all($db = null): array
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Route|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * Фильтр: есть хотя бы одно расписание
     * @return RouteQuery
     */
    public function withSchedules(): RouteQuery
    {
        return $this
            ->joinWith('schedules', false)
            ->groupBy('routes.id')
            ->having(['>', 'COUNT(schedules.id)', 0]);
    }

    /**
     * Фильтр: нет ни одного расписание
     * @return RouteQuery
     */
    public function withoutSchedules(): RouteQuery
    {
        return $this
            ->joinWith('schedules', false)
            ->groupBy('routes.id')
            ->having(['=', 'COUNT(schedules.id)', 0]);
    }

    /**
     * Фильтр: нет ни одного расписание
     * @return RouteQuery
     */
    public function byCode(string $code): RouteQuery
    {
        return $this->where(["code" => $code]);
    }
}
