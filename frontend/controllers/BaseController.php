<?php

namespace frontend\controllers;

use common\models\AccessToken;
use common\models\ApiResponse;
use common\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

class BaseController extends Controller
{

    public function checkToken(): ApiResponse
    {
        $response = new ApiResponse();
        $response->success = true;
        $accessToken = \Yii::$app->request->get("accessToken");
        if (empty($accessToken)) {
            $response->success = false;
            $response->errorMessage = "accessToken not found";
            return $response;
        }

        $user = User::findIdentityByAccessToken($accessToken);

        if ($user == null) {
            $response->addError("Invalid accessToken");
            return $response;
        }
        return $response;
    }

    public function actionError(): array
    {
        $exception = \Yii::$app->errorHandler->exception;
        $response = new ApiResponse();
        if ($exception !== null) {
            $response->errorMessage = $exception->getMessage();
        } else {
            $response->errorMessage = "Unknown error occurred";
        }
        return $response->serialize();
    }

    /**
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    public function findUserFromRequest($accessToken)
    {
        if (empty($accessToken)) {
            throw new ServerErrorHttpException('AccessToken should not be empty');
        }

        $foundUser = User::findIdentityByAccessToken($accessToken);

        if (empty($foundUser)) {
            throw new NotFoundHttpException('User not found');
        }

        return $foundUser;
    }

}