<?php

namespace frontend\models\post;

use common\components\helpers\ModelHelper;
use common\models\Post;
use frontend\models\BaseModelForm;
use Throwable;
use yii\db\StaleObjectException;


class DeletePostForm extends BaseModelForm
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
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function deletePost(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        if (!$this->post->delete()) {
            $error = ModelHelper::getFirstError($this);
            $this->addError('', !empty($error) ? $error : 'Delete post fail');
            return false;
        }

        return true;
    }

    public function validate($attributeNames = null, $clearErrors = true): bool
    {
        if (!parent::validate($attributeNames, $clearErrors)) {
            return false;
        }

        $user = \Yii::$app->user->identity;
        $this->post = Post::findOne([
            'postId' => $this->postId,
            'userId' => $user->userId,
        ]);

        if (empty($this->post)) {
            $this->addError("", "Post not found");
            return false;
        }

        return true;
    }
}