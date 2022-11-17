<?php

namespace frontend\models\post;

use common\models\Post;
use common\models\User;
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
            array_merge([
                ['title', 'required', 'message' => 'title can not be empty'],
                [['title', 'content'], 'string']
            ], parent::rules());
    }


    public function createPost(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $user = User::findIdentityByAccessToken($this->getTokenFromRequest());

        $this->post = new Post();
        $this->post->title = $this->title;
        $this->post->content = $this->content;
        $this->post->userId = $user->userId;
        if (!$this->post->save()) {
            $this->setError('Unable to save post: ' . var_export($this->post->getErrors()));
            return false;
        }

        return true;
    }


    public function getPost()
    {
        return $this->post->serialize();
    }
}