<?php

namespace frontend\controllers;

use common\models\AccessToken;
use common\models\User;
use Error;
use Yii;
use yii\base\Exception;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;


class LoginController extends BaseController
{
    public bool $hasToCheckToken = false;
    public $enableCsrfValidation = false;


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
        $username = $request->post('username');
        $password = $request->post('password');

        if ($password == null || $username == null) {
            throw new Error("User name and password must be not empty");
        }

        $user = User::findOne(['username' => $username]);

        if ($user == null) {
            throw new Error("User not found");
        }
        if (!$user->validatePassword($password)) {
            throw new Error("Invalid password");
        }

        $accessToken = $this->createAccessToken($user->userId);
        $accessToken->save();
        return ['accessToken' => $accessToken->accessToken];
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
