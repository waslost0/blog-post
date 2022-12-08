<?php

namespace frontend\controllers;


use common\models\Post;
use Error;
use frontend\models\post\CreatePostForm;
use frontend\models\post\DeletePostForm;
use frontend\models\post\UpdatePostForm;
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

    /**
     * @param int $offset
     * @return array
     */
    public function actionIndex(int $offset = 0): array
    {
        return $this->getPostsByOffset($offset);
    }

    /**
     * @param int $postId
     * @return array
     */
    public function actionView(int $postId): array
    {
        $post = Post::findOne($postId);
        if (empty($post)) {
            throw new Error("Post not found");
        }
        return $post->serialize();
    }


    /**
     * @return array
     */
    public function actionCreate(): array
    {
        $model = new CreatePostForm();
        $model->load(Yii::$app->request->post(), '');

        if ($model->createPost()) {
            return $model->getPost();
        } else {
            return $model->getErrors();
        }
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
            return $model->getErrors();
        }
        return [true];
    }

    /**
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionUpdate(): array
    {
        $model = new UpdatePostForm();

        $model->load(Yii::$app->request->post(), '');

        if (!$model->updatePost()) {
            return $model->getErrors();
        }

        return $model->getPost();
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
