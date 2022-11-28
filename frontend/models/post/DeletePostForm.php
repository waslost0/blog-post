<?php

namespace frontend\models\post;

use common\models\Post;
use frontend\models\BaseModelForm;
use Throwable;
use yii\db\StaleObjectException;


class DeletePostForm extends BaseModelForm
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
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function deletePost(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        if (!$this->post->delete()) {
            $this->setError('Unable to delete post');
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
}