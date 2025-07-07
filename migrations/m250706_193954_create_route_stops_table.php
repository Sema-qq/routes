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
        $this->addForeignKey('fk-route_stops-route', 'route_stops', 'route_id', 'routes', 'id', 'CASCADE');
        $this->addForeignKey('fk-route_stops-stop', 'route_stops', 'stop_id', 'stops', 'id', 'CASCADE');
    }

    public function safeDown(): void
    {
        $this->dropTable('route_stops');
    }
}
