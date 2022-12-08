<?php

namespace frontend\controllers;


use common\models\Post;
use Error;
use frontend\models\post\CreatePostForm;
use frontend\models\post\DeletePostForm;
use frontend\models\post\UpdatePostForm;
use frontend\models\post\ViewPostForm;
use Throwable;
use Yii;
use yii\db\StaleObjectException;
use yii\filters\VerbFilter;


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

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'index' => ['GET'],
                'view' => ['GET'],
                'create' => ['GET', 'POST'],
                'update' => ['PUT', 'POST'],
                'delete' => ['POST', 'DELETE'],
            ],
        ];
        return $behaviors;
    }

    /**
     * @param int $offset
     * @return array
     */
    public function actionIndex(int $offset = 0): array
    {
        return $this->getPostsByOffset($offset);
    }

    /**
     * @return array
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionView(): array
    {
        $model = new ViewPostForm();
        $model->load(Yii::$app->request->get(), '');

        if (!$model->getMyPost()) {
            return $model->getErrors();
        }
        return $model->getPost();
    }


    /**
     * @return array
     */
    public function actionCreate(): array
    {
        $model = new CreatePostForm();
        $model->load(Yii::$app->request->post(), '');

        if (!$model->createPost()) {
            return $model->getErrors();
        }
        return $model->getPost();
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
