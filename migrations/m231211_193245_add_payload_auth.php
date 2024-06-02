<?php

use yii\db\Migration;

/**
 * Class m231211_193245_add_payload_auth
 */
class m231211_193245_add_payload_auth extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user_auth', 'payload', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231211_193245_add_payload_auth cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231211_193245_add_payload_auth cannot be reverted.\n";

        return false;
    }
    */
}
