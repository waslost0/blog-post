<?php

namespace common\components\helpers;


class ModelHelper
{
    public static function getFirstError($model)
    {
        $errors = $model->getErrors();

        if (!empty($errors)) {
            $firstAttributeErrors = current($errors);
            if (!empty($firstAttributeErrors)) {
                return $firstAttributeErrors[0];
            }
        }
    }
}
