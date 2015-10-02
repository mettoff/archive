<?php

/**
 * UserToProjectBackendController контроллер для управления участниками блога
 *
 * @author yupe team <team@yupe.ru>
 * @link http://yupe.ru
 * @copyright 2009-2013 amyLabs && Yupe! team
 * @package yupe.modules.project.controllers
 * @since 0.1
 *
 */
class UserToProjectBackendController extends yupe\components\controllers\BackController
{
    public function accessRules()
    {
        return [
            ['allow', 'roles' => ['admin']],
            ['allow', 'actions' => ['index'], 'roles' => ['Project.UserToProjectBackend.Index']],
            ['allow', 'actions' => ['view'], 'roles' => ['Project.UserToProjectBackend.View']],
            ['allow', 'actions' => ['create'], 'roles' => ['Project.UserToProjectBackend.Create']],
            ['allow', 'actions' => ['update', 'inline'], 'roles' => ['Project.UserToProjectBackend.Update']],
            ['allow', 'actions' => ['delete', 'multiaction'], 'roles' => ['Project.UserToProjectBackend.Delete']],
            ['deny']
        ];
    }

    public function actions()
    {
        return [
            'inline' => [
                'class'           => 'yupe\components\actions\YInLineEditAction',
                'model'           => 'UserToProject',
                'validAttributes' => ['status', 'role', 'note']
            ]
        ];
    }

    /**
     * Отображает участника по указанному идентификатору
     * @param integer $id Идинтификатор участника для отображения
     */
    public function actionView($id)
    {
        $this->render('view', ['model' => $this->loadModel($id)]);
    }

    /**
     * Создает новую модель участника.
     * Если создание прошло успешно - перенаправляет на просмотр.
     */
    public function actionCreate()
    {
        $model = new UserToProject();

        try {
            if (isset($_POST['UserToProject'])) {
                $model->attributes = $_POST['UserToProject'];

                if ($model->save()) {
                    Yii::app()->user->setFlash(
                        yupe\widgets\YFlashMessages::SUCCESS_MESSAGE,
                        Yii::t('ProjectModule.project', 'Member was added!')
                    );

                    $this->redirect(
                        (array)Yii::app()->getRequest()->getPost(
                            'submit-type',
                            ['create']
                        )
                    );
                }
            }
        } catch (Exception $e) {
            Yii::app()->user->setFlash(
                yupe\widgets\YFlashMessages::WARNING_MESSAGE,
                Yii::t('ProjectModule.project', 'Cannot add user to the project. Please make sure he is not a member already.')
            );

            $this->redirect(['create']);
        }

        $this->render('create', ['model' => $model]);
    }

    /**
     * Редактирование участника.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id)
    {
        $model = $this->loadModel($id);

        if (isset($_POST['UserToProject'])) {
            $model->attributes = $_POST['UserToProject'];

            if ($model->save()) {
                Yii::app()->user->setFlash(
                    yupe\widgets\YFlashMessages::SUCCESS_MESSAGE,
                    Yii::t('ProjectModule.project', 'Member was updated!')
                );

                if (!isset($_POST['submit-type'])) {
                    $this->redirect(['update', 'id' => $model->id]);
                } else {
                    $this->redirect([$_POST['submit-type']]);
                }
            }
        }
        $this->render('update', ['model' => $model]);
    }

    /**
     * Удаляет модель участника из базы.
     * Если удаление прошло успешно - возвращется в index
     * @param integer $id идентификатор участника, который нужно удалить
     */
    public function actionDelete($id)
    {
        if (Yii::app()->getRequest()->getIsPostRequest()) {
            // поддерживаем удаление только из POST-запроса
            $this->loadModel($id)->delete();

            Yii::app()->user->setFlash(
                yupe\widgets\YFlashMessages::SUCCESS_MESSAGE,
                Yii::t('ProjectModule.project', 'Member was deleted!')
            );

            // если это AJAX запрос ( кликнули удаление в админском grid view), мы не должны никуда редиректить
            if (!isset($_GET['ajax'])) {
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : ['index']);
            }
        } else {
            throw new CHttpException(400, Yii::t(
                'ProjectModule.project',
                'Wrong request. Please don\'t repeate requests like this!'
            ));
        }
    }

    /**
     * Управление участниками.
     */
    public function actionIndex()
    {
        $model = new UserToProject('search');
        $model->unsetAttributes(); // clear any default values
        if (isset($_GET['UserToProject'])) {
            $model->attributes = $_GET['UserToProject'];
        }
        $this->render('index', ['model' => $model]);
    }

    /**
     * Возвращает модель по указанному идентификатору
     * Если модель не будет найдена - возникнет HTTP-исключение.
     * @param integer идентификатор нужной модели
     * @return UserToProject $model
     */
    public function loadModel($id)
    {
        $model = UserToProject::model()->findByPk($id);

        if ($model === null) {
            throw new CHttpException(404, Yii::t('ProjectModule.project', 'Requested page was not found!'));
        }

        return $model;
    }
}
