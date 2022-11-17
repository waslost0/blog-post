<?php


namespace frontend\controllers;

use yii;
use yii\base\Module;
use yii\web\Response;

class Api extends Module
{

    /**
     * @inheritdoc
     * @throws yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();
        \Yii::$app->user->enableSession = false;
        $this->registerResponseComponent();
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