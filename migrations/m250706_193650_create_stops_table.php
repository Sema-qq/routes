<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%stops}}`.
 */
class m250706_193650_create_stops_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('stops', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()
        ]);

        $this->batchInsert('stops', ['id', 'name'], [
           [1, 'Нектар город'],
           [2, 'Мегасити'],
           [3, 'Двор Даймё'],
           [4, 'Доля Демона'],
           [5, 'Китайский квартал'],
           [6, 'Какие-то холмы'],
           [7, 'Педулище'],
           [8, 'ЧЮК'],
           [9, '28-ой магазин'],
           [10, 'Уралец'],
           [11, 'Клуб Мальчишек'],
           [12, 'пр-т Ленина'],
           [13, 'Танкистов'],
           [14, 'Героев Танкограда'],
           [15, 'улица Пушкина дом Колотушкина'],
           [16, 'Марченко'],
           [17, 'Чичерина'],
           [18, 'Куйбышева'],
           [19, 'усатый волосатый'],
           [20, 'UFC 320'],
        ]);

        $this->execute("SELECT setval(pg_get_serial_sequence('stops', 'id'), (SELECT MAX(id) FROM stops));");
    }

    public function safeDown(): void
    {
        $this->dropTable('stops');
    }
}
