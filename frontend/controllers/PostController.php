<?php

namespace frontend\controllers;


use common\models\ApiResponse;
use common\models\Post;
use frontend\models\post\CreatePostForm;
use frontend\models\post\DeletePostForm;
use Throwable;
use yii\db\StaleObjectException;
use yii\web\Controller;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;


/**
 * @SWG\Swagger(
 *     basePath="/",
 *     produces={"application/json"},
 *     consumes={"application/x-www-form-urlencoded"},
 *     @SWG\Info(version="1.0", title="Simple API"),
 * )
 */
class PostController extends Controller
{
    public function init()
    {
        $module = \Yii::$app->getModule('api');
        $module->init();
        parent::init();
    }

    public $enableCsrfValidation = false;
    public string $modelClass = Post::class;

    public function behaviors(): array
    {
        $behaviors['contentNegotiator'] = [
            'class' => 'yii\filters\ContentNegotiator',
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ]
        ];
        return $behaviors;
    }


    /**
     * @param int $offset
     * @return array
     * @throws MethodNotAllowedHttpException
     */
    public function actionIndex(int $offset = 0): array
    {
        $request = \Yii::$app->request;
        $tokenResponse = $this->checkToken();
        $response = new ApiResponse();
        if (!$tokenResponse->success) {
            return $tokenResponse->serialize();
        }

        $posts = $this->getPostsByOffset($offset);
        $response->setData($posts);

        if ($request->isGet) {
            return $response->serialize();
        } else {
            throw new MethodNotAllowedHttpException;
        }
    }

    /**
     * @param int $postId
     * @return array
     * @throws MethodNotAllowedHttpException
     */
    public function actionView(int $postId): array
    {
        $tokenResponse = $this->checkToken();
        if (!$tokenResponse->success) {
            return $tokenResponse->serialize();
        }

        $request = \Yii::$app->request;
        $response = new ApiResponse();

        $post = Post::findOne($postId);

        if ($request->isGet) {
            $response->setData($post->serialize());
            return $response->serialize();
        } else {
            // TODO: customize response with Exception as ApiResponse
            throw new MethodNotAllowedHttpException;
        }
    }

    /**
     * @return array
     * @throws MethodNotAllowedHttpException
     * @throws ServerErrorHttpException|NotFoundHttpException
     */
    public function actionCreate(): array
    {
        $request = \Yii::$app->request;
        if ($request->isPost) {
            return $this->createPost($request);
        } else {
            throw new MethodNotAllowedHttpException;
        }
    }


    /**
     * @return array
     * @throws MethodNotAllowedHttpException
     * @throws ServerErrorHttpException|NotFoundHttpException
     */
    public function actionDelete(): array
    {
        $request = \Yii::$app->request;

        if ($request->isPost) {
            return $this->deletePost($request);
        } else {
            throw new MethodNotAllowedHttpException;
        }
    }


    /**
     * @param $request
     * @return array
     */
    private function createPost($request): array
    {
        $model = new CreatePostForm();
        $model->load(\Yii::$app->request->post(), '');
        $apiResponse = new ApiResponse();

        if ($model->createPost()) {
            $apiResponse->setData($model->getPost(), "post");
        } else {
            $apiResponse->addErrors($model->getErrors());
        }
        return $apiResponse->serialize();
    }


    /**
     * @param $request
     * @return array
     * @throws NotFoundHttpException
     * @throws StaleObjectException
     * @throws Throwable
     */
    private function deletePost($request): array
    {
        $model = new DeletePostForm();
        $model->load(\Yii::$app->request->post(), '');
        $apiResponse = new ApiResponse();

        if ($model->deletePost()) {
            $apiResponse->setData(null);
        } else {
            $apiResponse->addErrors($model->getErrors());
        }
        return $apiResponse->serialize();
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
