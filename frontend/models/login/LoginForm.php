<?php

namespace frontend\models\login;

use common\models\AccessToken;
use common\models\User;
use Error;
use Exception;
use frontend\models\BaseModelForm;

class LoginForm extends BaseModelForm
{
    public $username;
    public $password;
    private $user;


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return
            array_merge(
                [
                    [['username', 'password'], 'string'],
                    [['username', 'password'], 'filter', 'filter' => 'trim'],
                    ['username', 'required', 'message' => 'username can not be null'],
                    ['password', 'required', 'message' => 'password can not be null'],
                    ['password', 'validatePassword'],
                ], parent::rules(),
            );
    }

    /**
     * @throws Exception
     * @throws \yii\base\Exception
     */
    public function loginUser(): bool
    {
        if (!$this->validate()) {
            return false;
        }
        $this->accessToken = AccessToken::createAccessToken($this->user->userId);
        return true;
    }

    public function validate($attributeNames = null, $clearErrors = true): bool
    {
        if (!parent::validate($attributeNames, $clearErrors)) {
            return false;
        }

        $this->user = User::findOne(['username' => $this->username]);
        if (empty($this->user)) {
            throw new Error("User not found");
        }
        return true;
    }

    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = \Yii::$app->user->identity;
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }

    }


    public function serializeToken(): array
    {
        return ['accessToken' => $this->accessToken->accessToken];
    }
}