<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "post".
 *
 * @property int $postId
 * @property int $userId
 * @property string $title
 * @property string|null $content
 *
 * @property User $user
 */
class BasePost extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'post';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['userId', 'title'], 'required'],
            [['userId'], 'integer'],
            [['content'], 'string'],
            [['title'], 'string', 'max' => 255],
            [['userId'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['userId' => 'userid']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'postId' => 'Post ID',
            'userId' => 'User ID',
            'title' => 'Title',
            'content' => 'Content',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['userid' => 'userId']);
    }
}
