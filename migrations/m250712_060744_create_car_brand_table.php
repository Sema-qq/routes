<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%car_brand}}`.
 */
class m250712_060744_create_car_brand_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%car_brand}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()->unique()->comment('Бренд (марка) автомобиля'),
            'country' => $this->string()->comment('Страна-производитель'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%car_brand}}');
    }
}
