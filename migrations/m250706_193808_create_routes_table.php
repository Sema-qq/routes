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
            'car_id' => $this->integer()->notNull(),
            'type' => "{$this->enumTypeName} NOT NULL", // 'direct'/'reverse'
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')
        ]);

        // add foreign keys
        $this->addForeignKey(
            'fk-routes-cars',
            'routes',
            'car_id',
            'cars',
            'id',
            'CASCADE');

        // indexes
        $this->createIndex(
            'idx-unique-car-type',
            'routes',
            ['car_id', 'type'],
            true
        );
    }

    public function safeDown(): void
    {
        $this->dropTable('routes');
    }
}
