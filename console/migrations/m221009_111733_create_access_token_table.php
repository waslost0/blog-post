<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%access_token}}`.
 */
class m221009_111733_create_access_token_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('accessToken', [
            'accessTokenId' => $this->primaryKey(),
            'accessToken' =>  $this->string()->notNull(),
            'userId' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey(
            'fk-accessToken-user-id-1',
            'accessToken',
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
        $this->dropTable('access_token');
    }
}
