<?php

namespace frontend\controllers;

use frontend\models\login\LoginForm;
use Yii;


class LoginController extends BaseController
{
    public bool $hasToCheckToken = false;
    public $enableCsrfValidation = false;


    /**
     * @return array
     */
    public function actionIndex(): array
    {
        $model = new LoginForm();
        $model->load(Yii::$app->request->post(), '');

        if (!$model->loginUser()) {
            return $model->getErrors();
        }
        return $model->getToken();
    }
}
