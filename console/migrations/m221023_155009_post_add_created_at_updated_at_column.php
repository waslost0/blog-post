<?php

use yii\db\Migration;

/**
 * Class m221023_155009_post_add_created_at_updated_at_column
 */
class m221023_155009_post_add_created_at_updated_at_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        //time() -> new Expression('NOW()') new Expression('CURRENT_TIMESTAMP')
        $this->addColumn('post', 'createdAt', $this->integer()->defaultValue(time()));
        $this->addColumn('post', 'updatedAt', $this->integer()->defaultValue(time()));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('post', 'createdAt');
        $this->dropColumn('post', 'updatedAt');
    }
}
