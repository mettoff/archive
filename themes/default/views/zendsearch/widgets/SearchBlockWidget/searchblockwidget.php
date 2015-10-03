<div class="pull-left panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title" style="display: inline;">Поиск</h3>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">
        <?php Yii::import('application.modules.zendsearch.ZendSearchModule'); ?>
		<?= CHtml::beginForm(['/zendsearch/search/search'], 'get', ['class' => 'form-inline']); ?>
		<?= CHtml::textField(
		    'q',
		    '',
		    ['placeholder' => Yii::t('ZendSearchModule.zendsearch', 'Search...'), 'class' => 'form-control']
		); ?>
		<?= CHtml::submitButton(
		    Yii::t('ZendSearchModule.zendsearch', 'Find!'),
		    ['class' => 'btn btn-default', 'name' => '']
		); ?>
		<?= CHtml::endForm(); ?>
    </div>
</div>



