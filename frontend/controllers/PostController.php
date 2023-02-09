<?php

namespace frontend\controllers;


use common\components\helpers\ModelHelper;
use common\models\Post;
use ErrorException;
use frontend\models\post\CreatePostForm;
use frontend\models\post\DeletePostForm;
use frontend\models\post\PostListForm;
use frontend\models\post\UpdatePostForm;
use frontend\models\post\ViewPostForm;
use Throwable;
use Yii;
use yii\db\StaleObjectException;
use yii\filters\VerbFilter;


class PostController extends BaseController
{

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'index' => ['GET'],
                'view' => ['GET'],
                'create' => ['GET', 'POST'],
                'update' => ['PUT', 'POST'],
                'delete' => ['POST', 'DELETE'],
            ],
        ];
        return $behaviors;
    }

    /**
     * @return array
     * @throws ErrorException
     */
    public function actionList(): array
    {
        $form = new PostListForm();
        $form->load(Yii::$app->request->get(), '');

        if ($form->validate() && $form->selectPosts()) {
            return $form->serializeResponseToArray();
        } else {
            throw new ErrorException(ModelHelper::getFirstError($form));
        }
    }

    /**
     * @return array
     * @throws Throwable
     */
    public function actionView(): array
    {
        $model = new ViewPostForm();
        $model->load(Yii::$app->request->get(), '');

        if (!$model->loadMyPost()) {
            throw new ErrorException(ModelHelper::getFirstError($model));
        }
        return $model->serializePost();
    }


    /**
     * @return array
     * @throws ErrorException
     */
    public function actionCreate(): array
    {
        $model = new CreatePostForm();
        $model->load(Yii::$app->request->post(), '');

        if (!$model->createPost()) {
            throw new ErrorException(ModelHelper::getFirstError($model));
        }
        return $model->serialisePost();
    }

    /**
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionDelete(): array
    {
        $model = new DeletePostForm();
        $model->load(Yii::$app->request->post(), '');

        if (!$model->deletePost()) {
            throw new ErrorException(ModelHelper::getFirstError($model));
        }
        return [true];
    }

    /**
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionUpdate(): array
    {
        $model = new UpdatePostForm();
        $model->load(Yii::$app->request->post(), '');

        if (!$model->updatePost()) {
            throw new ErrorException(ModelHelper::getFirstError($model));
        }

        return $model->getPost();
    }
}
