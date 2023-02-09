<?php

namespace frontend\models\post;

use common\models\Post;
use frontend\models\BaseModelForm;
use Yii;


class PostListForm extends BaseModelForm
{
    public $limit;
    public $offset;
    public $fromDate;
    public $toDate;

    private $_posts;

    public function rules(): array
    {
        return [
            [['limit', 'offset', 'fromDate', 'toDate'], 'integer'],
            ['offset', 'default', 'value' => 0],
            ['limit', 'default', 'value' => Yii::$app->params['limitRecordsOnPage']],

        ];
    }

    public function selectPosts(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $query = Post::find()
            ->andFilterWhere(['>=', 'createdAt', $this->fromDate])
            ->andFilterWhere(['<=', 'createdAt', $this->toDate])
            ->offset($this->offset)
            ->limit($this->limit);

        $this->_posts = $query;

        return true;
    }

    public function serializeResponseToArray(): array
    {
        $responseObj = [];

        foreach ($this->_posts->each() as $post) {
            $responseObj[] = $post->serialize();
        }

        return ['posts' => $responseObj];
    }
}
