<?php

use yii\db\Migration;

class m250727_103349_uniq_route_stop_number extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // 1. stop_number только 1–10
        $this->execute('ALTER TABLE {{%schedules}} ADD CONSTRAINT chk_stop_number_range CHECK (stop_number BETWEEN 1 AND 10)');

        // 2. Уникальность пары (route_id, stop_number)
        $this->createIndex(
            'idx-unique-route-stop-number',
            '{{%schedules}}',
            ['route_id', 'stop_number'],
            true // уникальный
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Откатить оба ограничения
        $this->execute('ALTER TABLE {{%schedules}} DROP CONSTRAINT IF EXISTS chk_stop_number_range');
        $this->dropIndex('idx-unique-route-stop-number', '{{%schedules}}');
    }
}
