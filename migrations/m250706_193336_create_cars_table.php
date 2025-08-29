<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%cars}}`.
 */
class m250706_193336_create_cars_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('cars', [
            'id' => $this->primaryKey(),
            'brand_id' => $this->integer()->notNull(),
            'model' => $this->string()->notNull(),
            'fare' => $this->integer(),
            'production_year' => $this->integer(),
            'owner_id' => $this->integer()->notNull(),
            'driver_id' => $this->integer()->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->batchInsert(
            'cars',
            ['id', 'brand_id', 'model', 'fare', 'production_year', 'owner_id', 'driver_id'],
            [
                [1, 3, 'ID.Buzz', 750, 2025, 7, 4],
                [2, 3, 'Caravelle', 500, 2024, 7, 5],
                [3, 2, 'Transit', 18, 2011, 7, 8],
                [4, 1, 'NEXT', 45, 2019, 3, 1],
                [5, 1, 'Соболь', 60, 2018, 3, 2],
                [6, 5, 'Jumper', 22, 2020, 9, 9],
        ]);

        $this->execute("SELECT setval(pg_get_serial_sequence('cars', 'id'), (SELECT MAX(id) FROM cars));");

        $this->addForeignKey(
            'fk-cars-brand_id',
            '{{%cars}}',
            'brand_id',
            '{{%car_brand}}',
            'id',
            'RESTRICT'
        );
        $this->addForeignKey(
            'fk-cars-owner',
            '{{%cars}}',
            'owner_id',
            '{{%users}}',
            'id',
            'RESTRICT'
        );
        $this->addForeignKey(
            'fk-cars-driver',
            '{{%cars}}',
            'driver_id',
            '{{%users}}',
            'id',
            'RESTRICT'
        );
    }

    public function safeDown(): void
    {
        $this->dropTable('cars');
    }
}
