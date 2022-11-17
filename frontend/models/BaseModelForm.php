<?php

namespace frontend\models;

use common\models\ApiResponse;
use common\models\User;
use yii\base\Model;

class BaseModelForm extends Model
{
    public $accessToken;

    private string $errorText = "error";


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            ['accessToken', 'string']
        ];
    }

    public function setError($error = '')
    {
        parent::addError($this->errorText, $error);
    }

    public function validate($attributeNames = null, $clearErrors = true): bool
    {
        if (!$this->checkToken()) {
            return false;
        }

        return parent::validate($attributeNames, $clearErrors);
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