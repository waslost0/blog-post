<?php

namespace frontend\controllers;

use common\models\User;
use Error;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;


class BaseController extends Controller
{
    public $enableCsrfValidation = false;
    public bool $hasToCheckToken = true;

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        Yii::$app->user->enableSession = false;
        $this->registerResponseComponent();
        parent::init();
    }

    /**
     * @throws BadRequestHttpException
     */
    public function beforeAction($action): bool
    {
        if (!parent::beforeAction($action)) {
            return false;
        }
        if ($this->hasToCheckToken && !$this->checkToken()) {
            return false;
        }

        return true;
    }

    public function checkToken(): bool
    {
        $accessToken = $this->getTokenFromRequest();

        if (empty($accessToken)) {
            throw new Error("accessToken not found");
        }

        $user = User::findIdentityByAccessToken($accessToken);

        if (empty($user)) {
            throw new Error("User not found");
        }
        Yii::$app->user->login($user);
        return true;
    }

    public function getTokenFromRequest(): ?string
    {
        $request = Yii::$app->request;
        $token = $request->get("accessToken");
        if (!empty($token)) {
            return $token;
        }
        return $request->post("accessToken");
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
        ];
    }


    /**
     * @throws yii\base\InvalidConfigException
     */
    private function registerResponseComponent()
    {
        \Yii::$app->set('response', [
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
                                "success" => $response->isSuccessful,
                                "error" => $response->data["message"] ?? "",
                            ],
                            "data" => $response->data
                        ];
                    } else {
                        // положительный ответ
                        $response->data = [
                            "meta" => [
                                "success" => $response->isSuccessful,
                                "error" => ""
                            ],
                            "data" => $response->data
                        ];
                    }
                    $response->format = yii\web\Response::FORMAT_JSON;
                    if (YII_DEBUG) {
                        \Yii::trace('Response . ' . var_export($response->data, true));
                    }
                } else if (is_string($response->data)) {
                    $response->format = yii\web\Response::FORMAT_RAW;
                }
                $response->statusCode = 200;
            },
        ]);
    }
}