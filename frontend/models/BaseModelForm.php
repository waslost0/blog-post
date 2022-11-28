<?php

namespace frontend\models;

use common\models\ApiResponse;
use common\models\User;
use yii\base\Model;

class BaseModelForm extends Model
{
    public $accessToken;

    public function rules(): array
    {
        return [
            ['accessToken', 'string']
        ];
    }

    public function setError($error = '')
    {
        parent::addError("error", $error);
    }

    public function validate($attributeNames = null, $clearErrors = true): bool
    {
        if (!parent::validate($attributeNames, $clearErrors)) {
            return false;
        }
        if (!$this->checkToken()) {
            return false;
        }
        return true;
    }

    public function checkToken(): bool
    {
        $response = new ApiResponse();
        $response->success = true;
        $accessToken = $this->getTokenFromRequest();

        if (empty($accessToken)) {
            $this->setError("accessToken not found");
            return false;
        }

        $user = User::findIdentityByAccessToken($accessToken);
        \Yii::$app->user->login($user);

        if (empty($user)) {
            $this->setError("User not found");
            return false;
        }
        return true;
    }

    public function getTokenFromRequest(): ?string
    {
        $request = \Yii::$app->request;
        $token = $request->get("accessToken");
        if (!empty($token)) {
            return $token;
        }
        return $request->post("accessToken");
    }
}