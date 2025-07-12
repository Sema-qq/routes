<?php

namespace app\models\repository;

/**
 * This is the ActiveQuery class for [[RouteStops]].
 *
 * @see RouteStops
 */
class RouteStopsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return RouteStops[]|array
     */
    public function all($db = null): array
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return RouteStops|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
