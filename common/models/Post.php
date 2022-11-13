<?php

namespace common\models;

use Swagger\Annotations as SWG;
use yii\db\BaseActiveRecord;

/**
 * @SWG\Definition()
 *
 * @SWG\Property(property="postId", type="integer")
 * @SWG\Property(property="authorId", type="integer")
 * @SWG\Property(property="title", type="string")
 * @SWG\Property(property="text", type="string")
 * @SWG\Property(property="createdAt", type="integer")
 * @SWG\Property(property="updatedAt", type="integer")
 */
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
            "postId" => $this->postId,
            "userId" => $this->userId,
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