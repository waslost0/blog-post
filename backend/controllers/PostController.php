<?php

namespace backend\controllers;

use common\models\AccessToken;
use common\models\ApiResponse;
use common\models\Post;
use Throwable;
use yii\db\StaleObjectException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * PostController implements the CRUD actions for Post model.
 */
class PostController extends Controller
{

    public string $modelClass = 'common\models\Post';

    /**
     * @inheritDoc
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator'] = [
            'class' => 'yii\filters\ContentNegotiator',
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ]
        ];

//        $behaviors['access'] = [
//            'class' => AccessControl::class,
//            'only' => ['create', 'update'],
//            'rules' => [
//                // allow authenticated users
//                [
//                    'allow' => true,
//                    'roles' => ['@'],
//                ],
//                // everything else is denied by default
//            ],
//        ];
        return $behaviors;
    }

    public function actionIndex(): ApiResponse
    {
        $response = new ApiResponse();

        $accessToken = \Yii::$app->request->get("accessToken");
        if ($accessToken == null) {
            $response->setSuccess(false);
            $response->setMessage("accessToken not found");
            return $response;
        }

        $user = AccessToken::find()->where(["accessToken" => $accessToken])->one();
        if ($user == null) {
            $response->setSuccess(false);
            $response->setMessage("Invalid accessToken");
            return $response;
        }

        $posts = Post::find()->all();
        $response->setData($posts);
        $response->setSuccess(true);
        return $response;
    }

    public function actionView($id): ?Post
    {
        return Post::findOne($id);
    }

    /**
     * Creates a new Post model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new Post();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'postId' => $model->postId]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Post model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $postId Post ID
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate(int $postId)
    {
        $model = $this->findModel($postId);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'postId' => $model->postId]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Post model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $postId Post ID
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function actionDelete(int $postId): Response
    {
        $this->findModel($postId)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Post model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $postId Post ID
     * @return Post the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $postId): Post
    {
        if (($model = Post::findOne(['postId' => $postId])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
