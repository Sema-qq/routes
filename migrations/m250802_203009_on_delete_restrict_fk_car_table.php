<?php

use yii\db\Migration;

class m250802_203009_on_delete_restrict_fk_car_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Удаляем все старые ключи
        $this->dropForeignKey('fk-cars-brand_id', '{{%cars}}');
        $this->dropForeignKey('fk-cars-owner', '{{%cars}}');
        $this->dropForeignKey('fk-cars-driver', '{{%cars}}');

        // Создаём новые ключи с RESTRICT
        $this->addForeignKey(
            'fk-cars-brand_id',
            '{{%cars}}',
            'brand_id',
            '{{%car_brand}}',
            'id',
            'RESTRICT'
        );
        $this->addForeignKey(
            'fk-cars-owner',
            '{{%cars}}',
            'owner_id',
            '{{%users}}',
            'id',
            'RESTRICT'
        );
        $this->addForeignKey(
            'fk-cars-driver',
            '{{%cars}}',
            'driver_id',
            '{{%users}}',
            'id',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Откатываем обратно на CASCADE
        $this->dropForeignKey('fk-cars-brand_id', '{{%cars}}');
        $this->dropForeignKey('fk-cars-owner', '{{%cars}}');
        $this->dropForeignKey('fk-cars-driver', '{{%cars}}');

        $this->addForeignKey(
            'fk-cars-brand_id',
            '{{%cars}}',
            'brand_id',
            '{{%car_brand}}',
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-cars-owner',
            '{{%cars}}',
            'owner_id',
            '{{%users}}',
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-cars-driver',
            '{{%cars}}',
            'driver_id',
            '{{%users}}',
            'id',
            'CASCADE'
        );
    }
}
