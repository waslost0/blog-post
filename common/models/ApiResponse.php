<?php

namespace common\models;

class ApiResponse
{
    public bool $success;
    public string $message;
    public $data;

    function setSuccess($par)
    {
        $this->success = $par;
    }

    function setMessage($par)
    {
        $this->message = $par;
    }

    function setData($par)
    {
        $this->data = $par;
    }
}