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

        $this->batchInsert('users', ['id', 'full_name', 'license_date'], [
            [1, 'Бетербиев Артур Асильбекович', '2007-04-12'],
            [2, 'Бивол Дмитрий Юрьевич', '2010-03-14'],
            [3, 'Турки Аль Шейх', null],
            [4, 'Семенов Арсений Кириллович', '2015-08-09'],
            [5, 'Семенов Роман Кириллович', '2017-08-21'],
            [6, 'Пашков Илья Сергеевич', '2024-08-21'],
            [7, 'Семенов Кирилл Вячеславович', null],
            [8, 'Трегубов Никита Дмитриевич', '2012-02-12'],
            [9, 'водила', '2020-02-20'],
        ]);

        $this->execute(
            "SELECT setval(pg_get_serial_sequence('users', 'id'), (SELECT MAX(id) FROM users));");
    }

    public function safeDown(): void
    {
        $this->dropTable('users');
    }
}
