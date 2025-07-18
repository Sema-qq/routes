<?php

namespace app\models\repository;

/**
 * This is the ActiveQuery class for [[CarBrand]].
 *
 * @see CarBrand
 */
class CarBrandQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return CarBrand[]|array
     */
    public function all($db = null): array
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return CarBrand|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
