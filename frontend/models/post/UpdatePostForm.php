<?php

namespace frontend\models\post;

use common\models\Post;
use Error;
use frontend\models\BaseModelForm;
use Throwable;


class UpdatePostForm extends BaseModelForm
{
    public $postId;
    public $title;
    public $content;

    private ?Post $post;

    public function rules(): array
    {
        return
            array_merge(
                [
                    ['postId', 'integer'],
                    [['title', 'content'], 'string'],
                ],
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
            throw new Error($this->getErrors());
        }

        return true;
    }

    public function validate($attributeNames = null, $clearErrors = true): bool
    {
        if (!parent::validate($attributeNames, $clearErrors)) {
            return false;
        }
        if (empty($this->postId)) {
            throw new Error("postId can not be null");
        }
        if (empty($this->title)) {
            throw new Error("title can not be null");
        }

        $user = \Yii::$app->user->identity;
        //TODO: разделить получение сущности и проверку прав
        $this->post = Post::findOne([
            'postId' => $this->postId,
            'userId' => $user->userId,
        ]);

        if (empty($this->post)) {
            throw new Error("Post not found");
        }

        return true;
    }


    public function getPost(): array
    {
        return $this->post->serialize();
    }
}