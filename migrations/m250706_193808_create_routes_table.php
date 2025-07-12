<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%routes}}`.
 */
class m250706_193808_create_routes_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('routes', [
            'id' => $this->primaryKey(),
            'car_id' => $this->integer()->notNull(),
            'type' => $this->string(16)->notNull(), // 'direct'/'reverse'
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')
        ]);
        $this->addForeignKey('fk-routes-cars', 'routes', 'car_id', 'cars', 'id', 'CASCADE');
    }

    public function safeDown(): void
    {
        $this->dropTable('routes');
    }
}
