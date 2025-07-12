<?php

use yii\db\Migration;

/**
 * Handles altering table `{{%cars}}`:
 *  - removes brand, manufacturer, country
 *  - adds brand_id (FK)
 *  - adds model (string)
 */
class m250712_060959_change_car_table extends Migration
{
    public function safeUp()
    {
        // Удаляем лишние поля
        $this->dropColumn('{{%cars}}', 'brand');
        $this->dropColumn('{{%cars}}', 'manufacturer');
        $this->dropColumn('{{%cars}}', 'country');

        // Добавляем brand_id и model
        $this->addColumn('{{%cars}}', 'brand_id', $this->integer()->notNull()->after('id'));
        $this->addColumn('{{%cars}}', 'model', $this->string()->notNull()->after('brand_id'));

        // Создаём внешний ключ на таблицу брендов
        $this->addForeignKey(
            'fk-cars-brand_id',
            '{{%cars}}',
            'brand_id',
            '{{%car_brand}}',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        // Откатываем изменения
        $this->dropForeignKey('fk-cars-brand_id', '{{%cars}}');
        $this->dropColumn('{{%cars}}', 'brand_id');
        $this->dropColumn('{{%cars}}', 'model');

        $this->addColumn('{{%cars}}', 'brand', $this->string());
        $this->addColumn('{{%cars}}', 'manufacturer', $this->string());
        $this->addColumn('{{%cars}}', 'country', $this->string());
    }
}
