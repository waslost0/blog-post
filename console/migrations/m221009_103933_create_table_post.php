<?php

use yii\db\Migration;

/**
 * Class m221009_103933_create_table_post
 */
class m221009_103933_create_table_post extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('post', [
            'postId' => $this->primaryKey(),
            'userId' => $this->integer()->notNull(),
            'title' => $this->string()->notNull(),
            'content' => $this->text(),
        ]);

        $this->addForeignKey(
            'fk-post-user-id-1',
            'post',
            'userId',
            'user',
            'userId',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('post');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221009_103933_create_table_post cannot be reverted.\n";

        return false;
    }
    */
}
