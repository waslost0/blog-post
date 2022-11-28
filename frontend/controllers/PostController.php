<?php

namespace frontend\controllers;


use common\models\Post;
use frontend\models\post\CreatePostForm;
use frontend\models\post\DeletePostForm;
use Throwable;
use Yii;
use yii\db\StaleObjectException;


/**
 * @SWG\Swagger(
 *     basePath="/",
 *     produces={"application/json"},
 *     consumes={"application/x-www-form-urlencoded"},
 *     @SWG\Info(version="1.0", title="Simple API"),
 * )
 */
class PostController extends BaseController
{

    public $enableCsrfValidation = false;

    /**
     * @param int $offset
     * @return array
     */
    public function actionIndex(int $offset = 0): array
    {
        $posts = $this->getPostsByOffset($offset);
        $this->apiResponse->setData($posts);
        return $this->apiResponse->serialize();
    }

    /**
     * @param int $postId
     * @return array
     */
    public function actionView(int $postId): array
    {
        $post = Post::findOne($postId);
        $this->apiResponse->setData($post->serialize());
        return $this->apiResponse->serialize();
    }

    /**
     * @return array
     */
    public function actionCreate(): array
    {
        $model = new CreatePostForm();
        $model->load(Yii::$app->request->post(), '');

        if ($model->createPost()) {
            $this->apiResponse->setData($model->getPost(), "post");
        } else {
            $this->apiResponse->addErrors($model->getErrors());
        }
        return $this->apiResponse->serialize();
    }

    /**
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionDelete(): array
    {
        $model = new DeletePostForm();
        $model->load(Yii::$app->request->post(), '');

        if (!$model->deletePost()) {
            $this->apiResponse->addErrors($model->getErrors());
        }
        return $this->apiResponse->serialize();
    }

    private function getPostsByOffset($offset = 0): array
    {
        $posts = [];
        $query = Post::find()
            ->limit(10)
            ->offset($offset);
        foreach ($query->each() as $post) {
            $posts[] = $post->serialize();
        }
        return $posts;

    }
}
