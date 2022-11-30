<?php

namespace frontend\models\post;

use common\models\Post;
use frontend\models\BaseModelForm;
use Throwable;


class UpdatePostForm extends BaseModelForm
{
    public int $postId;
    public $title;
    public $content;

    private ?Post $post;

    public function rules(): array
    {
        return
            array_merge(
                [['title', 'content'], 'string'],
                [['postId', 'integer']],
                parent::rules(),
            );
    }

    /**
     * @throws Throwable
     */
    public function updatePost(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $this->post->title = $this->title;
        $this->post->content = $this->content;

        if (!$this->post->save()) {
            $this->setError('Unable to update post');
            $this->addErrors($this->post->getErrors());
            return false;
        }

        return true;
    }

    public function validate($attributeNames = null, $clearErrors = true): bool
    {
        if (!parent::validate($attributeNames, $clearErrors)) {
            return false;
        }
        if (empty($this->postId)) {
            $this->setError("postId can not be null");
            return false;
        }
        if (empty($this->title)) {
            $this->setError("title can not be null");
            return false;
        }

        $user = \Yii::$app->user->identity;
        $this->post = Post::findOne([
            'postId' => $this->postId,
            'userId' => $user->userId,
        ]);

        if (empty($this->post)) {
            $this->setError("Post not found");
            return false;
        }

        return true;
    }


    public function getPost(): array
    {
        return $this->post->serialize();
    }
}