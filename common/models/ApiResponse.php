<?php

namespace common\models;

class ApiResponse
{
    public bool $success = true;
    public ?string $errorMessage = null;
    public $data;

    public function serialize(): array
    {
        $data = [];
        $data["success"] = $this->success;
        if ($this->errorMessage != null) {
            $data["success"] = false;
            $data["error"] = $this->errorMessage;
        } else {
            $data["data"] = $this->data;
        }
        return $data;
    }

    public function addError(String $message) {
        $this->errorMessage = $message;
        $this->success = false;
    }

    public function setData($data) {
        $this->data = $data;
        $this->success = true;
    }
}