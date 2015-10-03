<div class="post">
    <div class="row">
        <div class="col-sm-12">
            <h2 class="title">
                    <?= CHtml::link(CHtml::image('/uploads/blogs/'.$data->blog->icon, $data->title, ['class' => 'img-title left'] ), '/blogs/'.$data->blog->slug); ?>
                    <?= CHtml::link(CHtml::encode($data->title), $data->getUrl()); ?>
            </h2>
        </div>
    </div>
    <hr>
</div>
