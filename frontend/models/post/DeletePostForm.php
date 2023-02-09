<?php

namespace frontend\models\post;

use common\models\Post;
use Error;
use frontend\models\BaseModelForm;
use Throwable;
use yii\db\StaleObjectException;


class DeletePostForm extends BaseModelForm
{
    public int $postId;
    private ?Post $post;

    public function rules(): array
    {
        return array_merge(
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
            //TODO: user $this->addError + return false;
            throw new Error($this->post->getErrors());
        }

        return true;
    }

    public function validate($attributeNames = null, $clearErrors = true): bool
    {
        if (!parent::validate($attributeNames, $clearErrors)) {
            return false;
        }
        //TODO: add "required" rule for postId
        if (empty($this->postId)) {
            throw new Error("postId can not be null");
        }

        $user = \Yii::$app->user->identity;
        $this->post = Post::findOne([
            'postId' => $this->postId,
            'userId' => $user->userId,
        ]);

        if (empty($this->post)) {
            throw new Error("Post not found");
        }

        return true;
    }
}