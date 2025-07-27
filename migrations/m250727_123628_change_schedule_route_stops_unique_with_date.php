<?php

use yii\db\Migration;

/**
 * Handles updating the unique index on the `schedules` table
 * to include `date`, `route_id`, and `stop_number`.
 */
class m250727_123628_change_schedule_route_stops_unique_with_date extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Удаляем старый индекс, если он существует
        $this->dropIndex('idx-unique-route-stop-number', 'schedules');

        // Добавляем новый уникальный индекс
        $this->createIndex(
            'idx-unique-schedule',
            'schedules',
            ['date', 'route_id', 'stop_number'],
            true // уникальный
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Удаляем новый индекс
        $this->dropIndex('idx-unique-schedule', 'schedules');

        // Восстанавливаем старый индекс
        $this->createIndex(
            'idx-unique-route-stop-number',
            'schedules',
            ['route_id', 'stop_number'],
            true // уникальный
        );
    }
}
