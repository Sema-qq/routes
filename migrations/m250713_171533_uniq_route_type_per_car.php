<?php

use yii\db\Migration;

class m250713_171533_uniq_route_type_per_car extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex(
            'idx-unique-car-type',
            '{{%routes}}',
            ['car_id', 'type'],
            true
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx-unique-car-type', '{{%routes}}');
    }
}
