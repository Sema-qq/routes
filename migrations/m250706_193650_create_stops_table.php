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
    }

    public function safeDown(): void
    {
        $this->dropTable('stops');
    }
}
