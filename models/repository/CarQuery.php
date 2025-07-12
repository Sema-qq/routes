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
}
