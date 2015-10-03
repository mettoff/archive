<?php Yii::import('application.modules.blog.BlogModule'); ?>

<div class="posts">

    <p class="posts-header">
        <span class="posts-header-text"><?= Yii::t('BlogModule.blog', 'Last blog posts'); ?></span>
    </p>

    <div class="posts-list">
        <?php foreach ($posts as $post): ?>
            <div class="posts-list-block">
                <div class="posts-list-block-header">
                    <h2 class="title"><?= CHtml::link(CHtml::encode($post->title),$post->getUrl()); ?></h2>
                </div>

                <div class="posts-list-block-meta">

                    <span>
                        <i class="glyphicon glyphicon-calendar"></i>

                        <?= Yii::app()->getDateFormatter()->formatDateTime(
                            $post->publish_time,
                            "long",
                            "short"
                        ); ?>
                    </span>

                    <span class="posts-list-block-tags-block">
                            <i class="glyphicon glyphicon-tags"></i>

                            <?= Yii::t('BlogModule.blog', 'Tags'); ?>:

                            <?php foreach ((array)$post->getTags() as $tag): ?>
                                <span>
                                    <?= CHtml::link(
                                        CHtml::encode($tag),
                                        ['/posts/', 'tag' => CHtml::encode($tag)]
                                    ); ?>
                                </span>
                            <?php endforeach; ?>
                        </span>

                        <span class="posts-list-block-tags-comments">
                            <i class="glyphicon glyphicon-comment"></i>

                            <?= CHtml::link(
                                $post->getCommentCount(),
                                $post->getUrl(['#' => 'comments'])
                            ); ?>
                        </span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
