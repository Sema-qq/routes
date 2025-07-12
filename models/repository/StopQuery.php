<?php

namespace app\models\repository;

/**
 * This is the ActiveQuery class for [[Stop]].
 *
 * @see Stop
 */
class StopQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Stop[]|array
     */
    public function all($db = null): array
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Stop|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
