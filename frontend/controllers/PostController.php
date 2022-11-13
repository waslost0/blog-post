<?php

namespace frontend\controllers;


use common\models\ApiResponse;
use common\models\Post;
use common\models\User;
use Throwable;
use yii\db\StaleObjectException;
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
class PostController extends BaseController
{
    public $enableCsrfValidation = false;

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
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
        $response->data = $posts;

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

        $query = Post::findOne($postId);

        if ($request->isGet) {
            $response->data = $query->serialize();
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
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    private function createPost($request): array
    {
        $response = new ApiResponse();

        $tokenResponse = $this->checkToken();
        if (!$tokenResponse->success) {
            return $tokenResponse->serialize();
        }


        $title = $request->post('title');
        $content = $request->post('content');

        if (empty($title)) {
            $response->errorMessage = "title can not be empty";
            return $response->serialize();
        }

        if (empty($content)) {
            $response->errorMessage = "content can not be empty";
            return $response->serialize();
        }
        $user = $this->findUserFromRequest($request->get('accessToken'));

        $post = new Post();
        $post->title = $title;
        $post->content = $content;
        $post->userId = $user->userId;

        if (!$post->save()) {
            $response->addError('Unable to save post: ' . var_export($post->getErrors(), true));
            return $response->serialize();
        }
        $response->success = true;
        $response->data = $post;
        return $response->serialize();
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
        $response = new ApiResponse();

        $tokenResponse = $this->checkToken();
        if (!$tokenResponse->success) {
            return $tokenResponse->serialize();
        }

        $postId = $request->post('postId');
        if (empty($postId)) {
            $response->addError("postId can not be null");
            return $response->serialize();
        }

        $accessToken = $request->get('accessToken');
        $user = User::findIdentityByAccessToken($accessToken);

        if (empty($user)) {
            $response->addError('User not found');
            return $response->serialize();
        }

        $post = Post::findOne(['postId' => $postId, 'userId' => $user->userId]);

        if (empty($post)) {
            $response->addError("Post not found");
            return $response->serialize();
        }

        if (!$post->delete()) {
            $response->addError('Unable to delete post:' . var_export($post->getErrors(), true));
            return $response->serialize();
        }

        $response->success = true;
        return $response->serialize();
    }

    private function getPostsByOffset($offset = 0): array
    {
        $posts = [];
        $query = Post::find()->limit(10)->offset($offset);
        foreach ($query->each() as $post) {
            $posts[] = $post->serialize();
        }
        return $posts;

    }
}
