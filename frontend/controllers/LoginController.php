<?php

namespace frontend\controllers;

use frontend\models\login\LoginForm;
use Yii;
use yii\base\Exception;
use yii\filters\VerbFilter;


class LoginController extends BaseController
{
    public bool $hasToCheckToken = false;

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'index' => ['POST'],
            ],
        ];
        return $behaviors;
    }


    /**
     * @return array
     * @throws Exception
     */
    public function actionIndex(): array
    {
        $model = new LoginForm();
        $model->load(Yii::$app->request->post(), '');

        if (!$model->loginUser()) {
            return $model->getErrors();
        }
        return $model->getToken();
    }

//    /**
//     * @param int $postId
//     * @return array
//     */
//    public function actionView(int $postId): array
//    {
//        $post = User::findOne($postId);
//        if (empty($post)) {
//            throw new Error("Post not found");
//        }
//        return $post->serialize();
//    }
}
