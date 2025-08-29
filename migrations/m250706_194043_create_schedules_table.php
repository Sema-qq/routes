<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%schedules}}`.
 */
class m250706_194043_create_schedules_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('schedules', [
            'id' => $this->primaryKey(),
            'date' => $this->date()->notNull(),
            'car_id' => $this->integer()->notNull(),
            'route_id' => $this->integer()->notNull(),
            'stop_id' => $this->integer()->notNull(),
            'stop_number' => $this->integer()->notNull(),
            'planned_time' => $this->time(),
            'actual_time' => $this->time(),
            'boarded_count' => $this->integer()->defaultValue(0)
        ]);

        // foreign keys
        $this->addForeignKey('fk-schedules-cars',
            'schedules',
            'car_id',
            'cars',
            'id',
            'CASCADE');
        $this->addForeignKey('fk-schedules-route',
            'schedules',
            'route_id',
            'routes',
            'id',
            'CASCADE');
        $this->addForeignKey('fk-schedules-stop',
            'schedules',
            'stop_id',
            'stops',
            'id',
            'CASCADE');

        // 1. stop_number только 1–10
        $this->execute('ALTER TABLE {{%schedules}} ADD CONSTRAINT chk_stop_number_range CHECK (stop_number BETWEEN 1 AND 10)');

        // 2. Уникальность полей (date, route_id, stop_number)
        $this->createIndex(
            'idx-unique-schedule',
            'schedules',
            ['date', 'route_id', 'stop_number'],
            true // уникальный
        );
    }

    public function safeDown(): void
    {
        $this->dropTable('schedules');
    }
}
