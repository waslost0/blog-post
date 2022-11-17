<?php

namespace common\models;

use yii\db\ActiveRecord;

class ApiResponse extends ActiveRecord
{
    private string $errorText = "error";
    public bool $success = true;
    public $data;
    public $dataName = "data";

    public function serialize(): array
    {
        $data = [];
        $data["success"] = $this->success;
        \Yii::$app->response->setStatusCode(404);
        if (!empty($this->getErrors($this->errorText))) {
            $data["success"] = false;
            $data[$this->errorText] = $this->getFirstError($this->errorText);
            $data["errors"] = $this->getErrors($this->errorText);
        } else {
            $data[$this->dataName] = $this->data;
            \Yii::$app->response->setStatusCode(200);
        }
        return $data;
    }

    public function setError(string $errorMessage)
    {
        parent::addError($this->errorText, $errorMessage);
    }

    public function setData($data, $dataName = "data")
    {
        $this->data = $data;
        $this->dataName = $dataName;
        $this->success = true;
    }
}