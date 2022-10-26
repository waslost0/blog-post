<?php

namespace frontend\controllers;

use common\models\AccessToken;
use common\models\ApiResponse;
use common\models\User;
use Yii;
use yii\base\Exception;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;


class LoginController extends BaseController
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
     * @throws NotFoundHttpException
     * @throws Exception
     */
    public function actionIndex(): array
    {
        $request = Yii::$app->request;

        if ($request->isPost) {
            return $this->login($request);
        } else {
            throw new MethodNotAllowedHttpException;
        }
    }

    /**
     * @throws Exception
     */
    private function login($request): array
    {
        $response = new ApiResponse();
        $username = $request->post('username');
        $password = $request->post('password');

        if ($password == null || $username == null) {
            $response->addError('User name and password must be not empty');
            return $response->serialize();
        }

        $user = User::findOne(['username' => $username]);

        if ($user == null) {
            $response->addError('User not found');
            return $response->serialize();
        }
        if (!$user->validatePassword($password)) {
            $response->addError('Invalid password');
            return $response->serialize();
        }

        $accessToken = $this->createAccessToken($user->userId);
        $accessToken->save();
        $response->data = ['accessToken' => $accessToken->accessToken];

        return $response->serialize();
    }


    /**
     * @throws Exception
     */
    private static function createAccessToken(int $userId): AccessToken
    {
        $accessToken = new AccessToken();
        $accessToken->userId = $userId;
        $accessToken->accessToken = AccessToken::generateToken();
        return $accessToken;
    }
}
