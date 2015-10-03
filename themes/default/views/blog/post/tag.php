<?php
/**
 * @var $this PostController
 */

$this->title = [Yii::t('BlogModule.blog', 'Posts list with tag "{tag}"', ['{tag}' => CHtml::encode($tag)]), Yii::app()->getModule('yupe')->siteName]; ?>

<?php $this->breadcrumbs = [
    Yii::t('BlogModule.blog', 'Blogs') => ['/blog/blog/index/'],
    Yii::t('BlogModule.blog', 'Posts list'),
]; ?>

<p><?= Yii::t('BlogModule.blog', 'Posts with tag'); ?> <strong><?= CHtml::encode($tag); ?></strong>...</p>
<div class="items">
	<?php foreach ($posts as $post): ?>
	   <div class="post">
	    <div class="row">
	        <div class="col-sm-12">
	            <h2 class="title">
	                <?= CHtml::link(CHtml::image('/uploads/blogs/'.$post->blog->icon, $post->title, ['class' => 'img-title left'] ), '/blogs/'.$post->blog->slug); ?>
	                <?= CHtml::link(CHtml::encode($post->title), $post->getUrl()); ?>
	            </h2>
	        </div>
	    </div>
	    <hr>
	</div>
	<?php endforeach; ?>
</div>