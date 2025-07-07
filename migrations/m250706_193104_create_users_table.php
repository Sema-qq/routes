<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%users}}`.
 */
class m250706_193104_create_users_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('users', [
            'id' => $this->primaryKey(),
            'full_name' => $this->string()->notNull(),
            'license_date' => $this->date(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
    }

    public function safeDown(): void
    {
        $this->dropTable('users');
    }
}
