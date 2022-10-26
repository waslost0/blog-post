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
}
