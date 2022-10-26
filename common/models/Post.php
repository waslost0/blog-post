<?php

namespace common\models;

use yii\db\BaseActiveRecord;

class Post extends BasePost
{
    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'postId' => 'ID поста',
            'userId' => 'ID юзера',
            'title' => 'Тайтл',
            'content' => 'Текст',
            'created_at' => 'created_at',
        ];
    }

    public function serialize(): array
    {
        return [
            "userId" => $this->postId,
            "authorId" => $this->userId,
            "title" => $this->title,
            "content" => $this->content,
            "createdAt" => $this->createdAt,
            "updatedAt" => $this->updatedAt,
        ];
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => ['createdAt', 'updatedAt'],
                    BaseActiveRecord::EVENT_BEFORE_UPDATE => ['updatedAt'],
                ],
            ],
        ];
    }
}