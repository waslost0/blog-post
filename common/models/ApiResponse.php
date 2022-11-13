<?php

namespace common\models;

use yii\db\ActiveRecord;

class ApiResponse extends ActiveRecord
{
    private string $errorText = "error";
    public bool $success = true;
    public $data;

    public function serialize(): array
    {
        $data = [];
        $data["success"] = $this->success;
        if (!empty($this->getFirstError($this->errorText))) {
            $data["success"] = false;
            $data[$this->errorText] = $this->getFirstError($this->errorText);
        } else {
            $data["data"] = $this->data;
        }
        return $data;
    }


    public function setError(string $errorMessage)
    {
        parent::addError($this->errorText, $errorMessage);
    }

    public function setData($data)
    {
        $this->data = $data;
        $this->success = true;
    }
}