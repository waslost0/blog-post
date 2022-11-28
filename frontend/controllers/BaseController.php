<?php

namespace frontend\controllers;

use common\models\ApiResponse;
use common\models\User;
use Yii;
use yii\base\InvalidConfigException;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;


class BaseController extends Controller
{
    protected ApiResponse $apiResponse;

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        $this->apiResponse = new ApiResponse();
        \Yii::$app->user->enableSession = false;
        $this->registerResponseComponent();
        parent::init();
    }

    public function behaviors(): array
    {
        return [
            'contentNegotiator' => [
                'class' => 'yii\filters\ContentNegotiator',
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'index' => ['GET'],
                    'view' => ['GET'],
                    'create' => ['GET', 'POST'],
                    'update' => ['GET', 'PUT', 'POST'],
                    'delete' => ['POST', 'DELETE'],
                ],
            ],
        ];
    }


    /**
     * @throws yii\base\InvalidConfigException
     */
    private function registerResponseComponent()
    {
        Yii::$app->set('response', [
            'class' => 'yii\web\Response',
            'format' => Response::FORMAT_JSON,
            'charset' => 'UTF-8',
            'on beforeSend' => function ($event) {
                $response = $event->sender;

                if (is_array($response->data)) {
                    // ответ с ошибками
                    if (!$response->isSuccessful) {
                        $response->data = [
                            "meta" => [
                                'success' => $response->isSuccessful,
                                'error' => $response->data["error"] ?? '',
                            ],
                            "data" => $response->data
                        ];
                    } else {
                        // положительный ответ
                        $response->data = [
                            "meta" =>
                                [
                                    'success' => $response->isSuccessful,
                                    'error' => ''
                                ],
                            "data" => $response->data
                        ];
                    }
                    $response->format = yii\web\Response::FORMAT_JSON;
                    if (YII_DEBUG) {
                        Yii::trace('Response . ' . var_export($response->data, true));
                    }
                } else if (is_string($response->data)) {
                    $response->format = yii\web\Response::FORMAT_RAW;
                }
                $response->statusCode = 200;
            },
        ]);
    }
}


/// {
//    "meta": {
//        "success": false,
//        "error": ""
//    },
//    "data": {
//        "name": "Exception",
//        "message": "yii\\web\\User::login(): Argument #1 ($identity) must be of type yii\\web\\IdentityInterface, null given, called in C:\\Server\\proj\\blog-post\\frontend\\controllers\\BaseController.php on line 124",
//        "code": 0,
//        "type": "TypeError",
//        "file": "C:\\Server\\proj\\blog-post\\vendor\\yiisoft\\yii2\\web\\User.php",
//        "line": 256,
//        "stack-trace": [
//            "#0 C:\\Server\\proj\\blog-post\\frontend\\controllers\\BaseController.php(124): yii\\web\\User->login()",
//            "#1 C:\\Server\\proj\\blog-post\\frontend\\controllers\\BaseController.php(58): frontend\\controllers\\BaseController->checkToken()",
//            "#2 C:\\Server\\proj\\blog-post\\vendor\\yiisoft\\yii2\\base\\Controller.php(176): frontend\\controllers\\BaseController->beforeAction()",
//            "#3 C:\\Server\\proj\\blog-post\\vendor\\yiisoft\\yii2\\base\\Module.php(552): yii\\base\\Controller->runAction()",
//            "#4 C:\\Server\\proj\\blog-post\\vendor\\yiisoft\\yii2\\web\\Application.php(103): yii\\base\\Module->runAction()",
//            "#5 C:\\Server\\proj\\blog-post\\vendor\\yiisoft\\yii2\\base\\Application.php(384): yii\\web\\Application->handleRequest()",
//            "#6 C:\\Server\\proj\\blog-post\\frontend\\web\\index.php(18): yii\\base\\Application->run()",
//            "#7 {main}"
//        ]
//    }
//}