<?php

namespace frontend\models\post;

use common\models\Post;
use Error;
use frontend\models\BaseModelForm;


class ViewPostForm extends BaseModelForm
{
    public int $postId;
    private ?Post $post;

    public function rules(): array
    {
        return
            array_merge(
                [['postId', 'integer']],
                parent::rules(),
            );
    }

    /**
     * @return bool
     */
    public function getMyPost(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $this->post = Post::findOne([
            'postId' => $this->postId,
        ]);

        if (empty($this->post)) {
            throw new Error("Post not found");
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

        return true;
    }

    public function getPost(): array
    {
        return $this->post->serialize();
    }
}