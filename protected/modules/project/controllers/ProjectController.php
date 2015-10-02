<?php

/**
 * ProjectController контроллер для блогов на публичной части сайта
 *
 * @author yupe team <team@yupe.ru>
 * @link http://yupe.ru
 * @copyright 2009-2013 amyLabs && Yupe! team
 * @package yupe.modules.project.controllers
 * @since 0.1
 *
 */
class ProjectController extends \yupe\components\controllers\FrontController
{
    /**
     * Выводит список блогов
     *
     * @return void
     */
    public function actionIndex()
    {
        $projects = new Project('search');
        $projects->unsetAttributes();
        $projects->status = Project::STATUS_ACTIVE;

        if (isset($_GET['Project']['name'])) {
            $projects->name = CHtml::encode($_GET['Project']['name']);
        }

        $this->render('index', ['projects' => $projects]);
    }

    /**
     * Отобразить карточку блога
     *
     * @param  string $slug - url блога
     * @throws CHttpException
     *
     * @return void
     */
    public function actionView($slug = null)
    {
        $project = Project::model()->getBySlug($slug);

        if ($project === null) {
            throw new CHttpException(404, Yii::t(
                'ProjectModule.project',
                'Project "{project}" was not found!',
                ['{project}' => $slug]
            ));
        }

        $this->render('view', ['project' => $project]);
    }

    /**
     * "вступление" в блог
     *
     * @throw CHttpException
     */
    public function actionJoin()
    {
        if (!Yii::app()->getRequest()->getIsPostRequest() || !Yii::app()->user->isAuthenticated()) {
            throw new CHttpException(404);
        }

        $projectId = (int)Yii::app()->request->getPost('projectId');

        if (!$projectId) {
            throw new CHttpException(404);
        }

        $project = Project::model()->get($projectId);

        if (!$project) {
            throw new CHttpException(404);
        }

        if ($project->join(Yii::app()->user->getId())) {
            Yii::app()->ajax->success(Yii::t('ProjectModule.project', 'You have joined!'));
        }

        //check if user is in project but blocked
        if ($project->hasUserInStatus(Yii::app()->getUser()->getId(), UserToProject::STATUS_BLOCK)) {
            Yii::app()->ajax->failure(Yii::t('ProjectModule.project', 'You are blocking in this project!'));
        }

        Yii::app()->ajax->failure(Yii::t('ProjectModule.project', 'An error occurred when you were joining the project!'));
    }

    /**
     * "покинуть" блог
     *
     * @throw CHttpException
     */
    public function actionLeave()
    {
        if (!Yii::app()->getRequest()->getIsPostRequest() || !Yii::app()->user->isAuthenticated()) {
            throw new CHttpException(404);
        }

        $projectId = (int)Yii::app()->request->getPost('projectId');

        if (!$projectId) {
            throw new CHttpException(404);
        }

        $project = Project::model()->get($projectId);

        if (!$project) {
            throw new CHttpException(404);
        }

        if ($project->leave(Yii::app()->user->getId())) {
            Yii::app()->ajax->success(Yii::t('ProjectModule.project', 'You left the project!'));
        }

        Yii::app()->ajax->failure(Yii::t('ProjectModule.project', 'An error occurred when you were leaving the project!'));
    }

    /**
     * @param $slug
     * @throws CHttpException
     */
    public function actionMembers($slug)
    {
        $project = Project::model()->getBySlug($slug);

        if (null === $project) {
            throw new CHttpException(404);
        }

        $this->render('members', ['project' => $project, 'members' => $project->getMembersList()]);
    }
}
