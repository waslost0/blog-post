<?php

namespace frontend\controllers;

use common\models\ApiResponse;
use common\models\User;
use yii\web\Controller;

class BaseController extends Controller
{

    public function checkToken(): ApiResponse
    {
        $response = new ApiResponse();
        $response->success = true;
        $accessToken = $this->getTokenFromRequest();

        if (empty($accessToken)) {
            $response->setError("accessToken not found");
            return $response;
        }

        $user = User::findIdentityByAccessToken($accessToken);

        if (empty($user)) {
            $response->setError("User not found");
            return $response;
        }
        return $response;
    }

    public function getTokenFromRequest(): String
    {
        $request = \Yii::$app->request;
        $token = $request->get("accessToken");
        if (!empty($token)) {
            return $token;
        }
        return $request->post("accessToken");
    }

    public function actionError(): array
    {
        $exception = \Yii::$app->errorHandler->exception;
        $response = new ApiResponse();
        if ($exception !== null) {
            $response->setError($exception->getMessage());
        } else {
            $response->setError("Unknown error occurred");
        }
        return $response->serialize();
    }
}