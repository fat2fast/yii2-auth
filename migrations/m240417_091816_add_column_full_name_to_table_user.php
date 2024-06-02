<?php

use yii\db\Migration;

/**
 * Class m240417_091816_add_column_full_name_to_table_user
 */
class m240417_091816_add_column_full_name_to_table_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'full_name', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240417_091816_add_column_full_name_to_table_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240417_091816_add_column_full_name_to_table_user cannot be reverted.\n";

        return false;
    }
    */
}
