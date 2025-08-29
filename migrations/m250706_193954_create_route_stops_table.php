<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%route_stops}}`.
 */
class m250706_193954_create_route_stops_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('route_stops', [
            'id' => $this->primaryKey(),
            'route_id' => $this->integer()->notNull(),
            'stop_id' => $this->integer()->notNull(),
            'stop_number' => $this->integer()->notNull() // 1..10
        ]);

        // foreign keys
        $this->addForeignKey(
            'fk-route_stops-route',
            'route_stops',
            'route_id',
            'routes',
            'id',
            'CASCADE');

        $this->addForeignKey(
            'fk-route_stops-stop',
            'route_stops',
            'stop_id',
            'stops',
            'id',
            'RESTRICT'
        );

        // 1. stop_number только 1–10
        $this->execute('ALTER TABLE {{%route_stops}} ADD CONSTRAINT chk_route_stops_stop_number_range CHECK (stop_number BETWEEN 1 AND 10)');

        // 2. Уникальность пары (route_id, stop_number)
        $this->createIndex(
            'idx-route_stops-unique-route-stop-number',
            'route_stops',
            ['route_id', 'stop_number'],
            true // уникальный
        );
    }

    public function safeDown(): void
    {
        $this->dropTable('route_stops');
    }
}
