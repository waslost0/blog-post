<?php

namespace frontend\models\post;

use common\components\helpers\ModelHelper;
use common\models\Post;
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
                    ['postId', 'required', 'message' => 'postId can not be null'],
                    [['title', 'content'], 'string'],
                    [['title', 'content'], 'filter', 'filter' => 'trim'],
                    ['title', 'required', 'message' => 'title can not be null'],
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
            $error = ModelHelper::getFirstError($this);
            $this->addError('', !empty($error) ? $error : 'Update post fail');
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


    public function serializePost(): array
    {
        return $this->post->serialize();
    }
}