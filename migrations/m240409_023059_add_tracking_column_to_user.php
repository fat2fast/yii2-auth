<?php

use yii\db\Migration;

/**
 * Class m240409_023059_add_tracking_column_to_user
 */
class m240409_023059_add_tracking_column_to_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'failed_login_attempts', $this->integer()->defaultValue(0));
        $this->addColumn('user', 'login_lock_time', $this->integer());
        $this->addColumn('user', 'force_change_pwd', $this->tinyInteger(2)->defaultValue(0));
        $this->addColumn('user', 'last_updated_password', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240409_023059_add_tracking_column_to_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240409_023059_add_tracking_column_to_user cannot be reverted.\n";

        return false;
    }
    */
}
