<div class="post">
    <div class="row">
        <div class="col-sm-12">
            <h2 class="title">
                    <img src="/uploads/blogs/<?= $data->blog->icon ?>" title="<?php $data->title ?>" class="img-title left">
                    <?= CHtml::link(CHtml::encode($data->title), $data->getUrl()); ?>
            </h2>
        </div>
    </div>
    <hr>
</div>
