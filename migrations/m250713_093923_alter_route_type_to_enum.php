<?php

use yii\db\Migration;

class m250713_093923_alter_route_type_to_enum extends Migration
{
    private string $enumTypeName = 'route_type_enum';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // 1. Создать новый ENUM-тип, если ещё не существует
        $this->execute("DO \$\$
            BEGIN
                IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = '{$this->enumTypeName}') THEN
                    CREATE TYPE {$this->enumTypeName} AS ENUM ('direct', 'reverse');
                END IF;
            END
        \$\$;");

        // 2. Изменить тип поля с явным указанием USING
        $this->alterColumn(
            '{{%routes}}',
            'type',
            "route_type_enum USING (type::route_type_enum) NOT NULL"
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // 1. Откатить тип поля обратно на строку
        $this->alterColumn('{{%routes}}', 'type', $this->string(16)->notNull());

        // 2. Удалить тип ENUM (если нужно, осторожно: удалит и если где-то ещё используется!)
        $this->execute("DROP TYPE IF EXISTS {$this->enumTypeName};");
    }
}
