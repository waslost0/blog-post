<?php

namespace frontend\models\post;

use common\models\Post;
use Error;
use frontend\models\BaseModelForm;


class CreatePostForm extends BaseModelForm
{
    public $title;
    public $content;
    private $post;


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return
            array_merge(
                [
                    [['title', 'content'], 'string'],
                ], parent::rules(),
            );
    }

    public function createPost(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $user = \Yii::$app->user->identity;

        $this->post = new Post();
        $this->post->title = $this->title;
        $this->post->content = $this->content;
        $this->post->userId = $user->userId;
        if (!$this->post->save()) {
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
        if (empty($this->title)) {
            throw new Error("title can not be null");
        }
        return true;
    }


    public function getPost()
    {
        return $this->post->serialize();
    }
}