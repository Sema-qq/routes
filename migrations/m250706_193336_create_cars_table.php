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
        // foreign keys
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
