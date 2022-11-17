<?php

namespace frontend\models\post;

use common\models\Post;
use common\models\User;
use frontend\models\BaseModelForm;


class DeletePostForm extends BaseModelForm
{
    public $postId;
    private $post;


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return
            array_merge([['postId', 'integer']], parent::rules());
    }


    public function deletePost(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        if (!$this->post->delete()) {
            $this->setError('Unable to delete post:' . var_export($this->post->getErrors(), true));
            return false;
        }

        return true;
    }

    public function validate($attributeNames = null, $clearErrors = true): bool
    {
        if (empty($this->postId)) {
            $this->setError("postId can not be null");
            return false;
        }

        $user = User::findIdentityByAccessToken($this->getTokenFromRequest());
        $this->post = Post::findOne(['postId' => $this->postId, 'userId' => $user->userId]);

        if (empty($this->post)) {
            $this->setError("Post not found");
            return false;
        }

        return parent::validate($attributeNames, $clearErrors);
    }


}