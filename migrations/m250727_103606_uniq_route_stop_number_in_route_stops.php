<?php

use yii\db\Migration;

class m250727_103606_uniq_route_stop_number_in_route_stops extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // 1. stop_number только 1–10
        $this->execute('ALTER TABLE {{%route_stops}} ADD CONSTRAINT chk_route_stops_stop_number_range CHECK (stop_number BETWEEN 1 AND 10)');

        // 2. Уникальность пары (route_id, stop_number)
        $this->createIndex(
            'idx-route_stops-unique-route-stop-number',
            '{{%route_stops}}',
            ['route_id', 'stop_number'],
            true // уникальный
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('ALTER TABLE {{%route_stops}} DROP CONSTRAINT IF EXISTS chk_route_stops_stop_number_range');
        $this->dropIndex('idx-route_stops-unique-route-stop-number', '{{%route_stops}}');

    }
}
