<?php

use yii\db\Migration;

/**
 * Class m231007_033659_add_token_expired_at
 */
class m231007_033659_add_token_expired_at extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'token_expired_at', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231007_033659_add_token_expired_at cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231007_033659_add_token_expired_at cannot be reverted.\n";

        return false;
    }
    */
}
