<?php

use yii\db\Migration;

/**
 * Class m240416_022226_add_column_secret_totp_and_use_totp_to_table_user
 */
class m240416_022226_add_column_secret_totp_and_use_totp_to_table_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'use_totp', $this->integer()
            ->defaultValue(0)
            ->comment("Enable TOTP or not"));
        $this->addColumn('user','secret_totp', $this->string()->comment("Secret Generate TOTP"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240416_022226_add_column_secret_totp_and_use_totp_to_table_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240416_022226_add_column_secret_totp_and_use_totp_to_table_user cannot be reverted.\n";

        return false;
    }
    */
}
