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

        //TODO: try use rules
        if (empty($this->username)) {
            throw new Error("username must be not empty");
        }
        //TODO: try use rules "trim" + "required"
        if (empty($this->password)) {
            throw new Error("password must be not empty");
        }

        $this->user = User::findOne(['username' => $this->username]);
        if ($this->user == null) {
            throw new Error("User not found");
        }
        //TODO: check namespace common\models\LoginForm for validatePassword
        if (!$this->user->validatePassword($this->password)) {
            throw new Error("Invalid password");
        }
        return true;
    }


    public function getToken(): array
    {
        return ['accessToken' => $this->accessToken->accessToken];
    }
}