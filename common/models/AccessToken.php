<?php

namespace common\models;


use Yii;
use yii\base\Exception;

class AccessToken extends BaseAccessToken
{

    /**
     * @throws Exception
     */
    public static function generateToken(): string
    {
        return Yii::$app->security->generateRandomString();
    }

    /**
     * @throws Exception
     */
    public static function createAccessToken(int $userId): AccessToken
    {
        $accessToken = new AccessToken();
        $accessToken->userId = $userId;
        $accessToken->accessToken = AccessToken::generateToken();
        $accessToken->save();
        return $accessToken;
    }
}
