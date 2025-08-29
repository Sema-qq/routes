<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%car_brand}}`.
 */
class m250706_193120_create_car_brand_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('car_brand', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()->unique()->comment('Бренд (марка) автомобиля'),
            'country' => $this->string()->notNull()->comment('Страна-производитель'),
        ]);

        $this->batchInsert('car_brand', ['id', 'name', 'country'], [
            [1, 'GAZ', 'Россия'],
            [2, 'Ford', 'США'],
            [3, 'Volkswagen', 'Германия'],
            [4, 'Peugeot', 'Франция'],
            [5, 'Citroen', 'Франция'],
            [6, 'Toyota', 'Япония'],
        ]);

        $this->execute("SELECT setval(pg_get_serial_sequence('car_brand', 'id'), (SELECT MAX(id) FROM car_brand));");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('car_brand');
    }
}
