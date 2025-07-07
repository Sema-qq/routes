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
            'minibus_id' => $this->integer()->notNull(),
            'type' => $this->string(16)->notNull(), // 'direct'/'reverse'
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')
        ]);
        $this->addForeignKey('fk-routes-minibus', 'routes', 'minibus_id', 'minibuses', 'id', 'CASCADE');
    }

    public function safeDown(): void
    {
        $this->dropTable('routes');
    }
}
