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

        $this->batchInsertBaseData();
    }

    public function safeDown(): void
    {
        $this->dropTable('schedules');
    }

    private function batchInsertBaseData()
    {
        $this->batchInsert('schedules', ['id', 'route_id', 'stop_id', 'stop_number'], [
            [11, 1, 1, 1],
            [12, 1, 6, 2],
            [13, 1, 2, 3],
            [14, 1, 7, 4],
            [15, 1, 3, 5],
            [16, 1, 8, 6],
            [17, 1, 4, 7],
            [18, 1, 9, 8],
            [19, 1, 5, 9],
            [20, 1, 10, 10],
            [21, 2, 1, 10],
            [22, 2, 6, 9],
            [23, 2, 2, 8],
            [24, 2, 7, 7],
            [25, 2, 3, 6],
            [26, 2, 8, 5],
            [27, 2, 4, 4],
            [28, 2, 9, 3],
            [29, 2, 5, 2],
            [30, 2, 10, 1],
            [31, 3, 20, 1],
            [32, 3, 19, 2],
            [33, 3, 18, 3],
            [34, 3, 17, 4],
            [35, 3, 16, 5],
            [36, 3, 15, 6],
            [37, 3, 14, 7],
            [38, 3, 14, 8],
            [39, 3, 13, 9],
            [40, 3, 12, 10],
            [41, 4, 20, 10],
            [42, 4, 19, 9],
            [43, 4, 18, 8],
            [44, 4, 17, 7],
            [45, 4, 16, 6],
            [46, 4, 15, 5],
            [47, 4, 14, 4],
            [48, 4, 14, 3],
            [49, 4, 13, 2],
            [50, 4, 12, 1],
            [51, 5, 15, 1],
            [52, 5, 15, 2],
            [53, 5, 15, 3],
            [54, 5, 15, 4],
            [55, 5, 15, 5],
            [56, 5, 15, 6],
            [57, 5, 15, 7],
            [58, 5, 15, 8],
            [59, 5, 15, 9],
            [60, 5, 15, 10],
            [61, 6, 19, 1],
            [62, 6, 19, 2],
            [63, 6, 19, 3],
            [64, 6, 19, 4],
            [65, 6, 19, 5],
            [66, 6, 19, 6],
            [67, 6, 19, 7],
            [68, 6, 19, 8],
            [69, 6, 19, 9],
            [70, 6, 19, 10],
            [71, 7, 1, 1],
            [72, 7, 2, 2],
            [73, 7, 1, 3],
            [74, 7, 2, 4],
            [75, 7, 1, 5],
            [76, 7, 2, 6],
            [77, 7, 1, 7],
            [78, 7, 2, 8],
            [79, 7, 1, 9],
            [80, 7, 2, 10],
            [81, 8, 1, 10],
            [82, 8, 2, 9],
            [83, 8, 1, 8],
            [84, 8, 2, 7],
            [85, 8, 1, 6],
            [86, 8, 2, 5],
            [87, 8, 1, 4],
            [88, 8, 2, 3],
            [89, 8, 1, 2],
            [90, 8, 2, 1],
        ]);

        $this->execute("SELECT setval(pg_get_serial_sequence('route_stops', 'id'), (SELECT MAX(id) FROM route_stops));");
    }
}
