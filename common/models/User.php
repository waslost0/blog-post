<?php

namespace common\models;

use Yii;
use yii\base\Exception;
use yii\db\BaseActiveRecord;

class User extends BaseUser
{
    public function attributeLabels(): array
    {
        return [
            'userId' => 'ID пользователя',
            'username' => 'Имя пользователя',
            'authKey' => 'Auth Key',
            'passwordHash' => 'Password Hash',
            'passwordResetToken' => 'Password Reset Token',
            'email' => 'Почта',
            'status' => 'Статус',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'verificationToken' => 'Verification Token',
        ];
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    BaseActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
        ];
    }


    public static function findIdentityByAccessToken($token, $type = null)
    {
        $accessToken = AccessToken::findOne(['accessToken' => $token]);

        if ($accessToken == null) {
            return null;
        }

        return $accessToken->getUser()->one();
    }


    /**
     * @throws Exception
     */
    public function setPassword($password)
    {
        $this->passwordHash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * @throws Exception
     */
    public function generateEmailVerificationToken()
    {
        $this->verificationToken = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * @throws Exception
     */
    public function generateAuthKey()
    {
        $this->authKey = Yii::$app->security->generateRandomString();
    }


    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey(): ?string
    {
        return $this->authKey;
    }

    public function validateAuthKey($authKey): ?bool
    {
        return $this->getAuthKey() === $authKey;
    }

    public function fields()
    {
        $fields = parent::fields();

        unset(
            $fields['authKey'], $fields['passwordHash'],
            $fields['passwordResetToken'], $fields['verificationToken'],
        );

        return $fields;
    }

    public function serialize(): array
    {
        return [
            "userId" => $this->userId,
            "username" => $this->username,
            "email" => $this->email,
            "status" => $this->status,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}