<?php

namespace frontend\models\post;

use common\models\Post;
use frontend\models\BaseModelForm;


class ViewPostForm extends BaseModelForm
{
    public $postId;
    private ?Post $post;

    public function rules(): array
    {
        return
            array_merge(
                [
                    ['postId', 'integer'],
                    ['postId', 'required', 'message' => 'postId can not be null'],
                ],
                parent::rules(),
            );
    }

    /**
     * @return bool
     */
    public function loadMyPost(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $this->post = Post::findOne([
            'postId' => $this->postId,
        ]);

        if (empty($this->post)) {
            $this->addError('', "Post not found");
            return false;
        }
        return true;
    }

    public function serializePost(): array
    {
        return $this->post->serialize();
    }
}