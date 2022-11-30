<?php

namespace frontend\models;

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
}