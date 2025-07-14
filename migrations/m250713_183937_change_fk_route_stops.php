<?php

use yii\db\Migration;

class m250713_183937_change_fk_route_stops extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('fk-route_stops-stop', 'route_stops');
        $this->addForeignKey(
            'fk-route_stops-stop',
            'route_stops',
            'stop_id',
            'stops',
            'id',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-route_stops-stop', 'route_stops');
        $this->addForeignKey(
            'fk-route_stops-stop',
            'route_stops',
            'stop_id',
            'stops',
            'id',
            'CASCADE'
        );
    }
}
