<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%routes}}`.
 */
class m250706_193808_create_routes_table extends Migration
{
    private string $enumTypeName = 'route_type_enum';

    public function safeUp(): void
    {
        // add enums
        $this->execute("DO \$\$
            BEGIN
                IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = '{$this->enumTypeName}') THEN
                    CREATE TYPE {$this->enumTypeName} AS ENUM ('direct', 'reverse');
                END IF;
            END
        \$\$;");

        $this->createTable('routes', [
            'id' => $this->primaryKey(),
            'code' => $this->string(16)->notNull(),
            'type' => "{$this->enumTypeName} NOT NULL", // 'direct'/'reverse'
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')
        ]);

        // indexes
        $this->createIndex(
            'idx-unique-car-type',
            'routes',
            ['code', 'type'],
            true
        );

        $this->batchInsert('routes', ['id', 'code', 'type'], [
            [1, '№9', 'reverse'],
            [2, '№9', 'direct'],
            [3, '№39', 'direct'],
            [4, '№39', 'reverse'],
            [5, 'Одинаковые', 'direct'],
            [6, 'Одинаковые', 'reverse'],
            [7, 'Фортик', 'direct'],
            [8, 'Фортик', 'reverse'],
        ]);

        $this->execute("SELECT setval(pg_get_serial_sequence('routes', 'id'), (SELECT MAX(id) FROM routes));");
    }

    public function safeDown(): void
    {
        $this->dropTable('routes');
    }
}
