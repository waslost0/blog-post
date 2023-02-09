<?php

namespace frontend\models\post;

use common\models\Post;
use frontend\models\BaseModelForm;


//TODO: CreatePostForm + UpdatePostForm -> SavePostForm
//TODO: try to use https://www.yiiframework.com/doc/guide/2.0/ru/structure-models#scenarios
//TODO: or check by postId in params
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
                    [['title', 'content'], 'filter', 'filter' => 'trim'],
                    ['title', 'required', 'message' => 'title can not be null'],
                ],
                parent::rules(),
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

    public function serialisePost()
    {
        return $this->post->serialize();
    }
}