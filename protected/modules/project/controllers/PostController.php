<?php

/**
 * PostController контроллер для постов на публичной части сайта
 *
 * @author yupe team <team@yupe.ru>
 * @link http://yupe.ru
 * @copyright 2009-2013 amyLabs && Yupe! team
 * @package yupe.modules.project.controllers
 * @since 0.1
 *
 */
class PostController extends \yupe\components\controllers\FrontController
{

    public function actionIndex()
    {
        $this->render('index', ['model' => Post::model()]);
    }

    /**
     * Показываем пост по урлу
     *
     * @param  string $slug - урл поста
     * @throws CHttpException
     * @return void
     */
    public function actionView($slug)
    {
        $post = Post::model()->get($slug, ['project', 'createUser', 'comments.author']);

        if (null === $post) {
            throw new CHttpException(404, Yii::t('ProjectModule.project', 'Post was not found!'));
        }

        $this->render('view', ['post' => $post]);
    }

    /**
     * Показываем посты по тегу
     *
     * @param  string $tag - Tag поста
     * @throws CHttpException
     * @return void
     */
    public function actionTag($tag)
    {
        $tag = CHtml::encode($tag);

        $posts = Post::model()->getByTag($tag);

        if (empty($posts)) {
            throw new CHttpException(404, Yii::t('ProjectModule.project', 'Posts not found!'));
        }

        $this->render('tag', ['posts' => $posts, 'tag' => $tag]);
    }

    public function actionProject($slug)
    {
        $project = Project::model()->getByUrl($slug)->find();

        if (null === $project) {
            throw new CHttpException(404);
        }

        $this->render('project-post', ['target' => $project, 'posts' => $project->getPosts()]);
    }

    public function actionCategory($slug)
    {
        $category = Category::model()->getByAlias($slug);

        if (null === $category) {
            throw new CHttpException(404, Yii::t('ProjectModule.project', 'Page was not found!'));
        }

        $this->render(
            'category-post',
            ['target' => $category, 'posts' => Post::model()->getForCategory($category->id)]
        );
    }

    public function actionCategories()
    {
        $this->render('categories', ['categories' => Post::model()->getCategories()]);
    }
}
