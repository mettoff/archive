<?php Yii::import('application.modules.user.UserModule'); ?>
<div class="pull-left panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title" style="display: inline;"><?= $user->first_name; ?> <?= $user->last_name; ?></h3>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-xs-12">
                <div class="right text-left profil-mini-desc">
                    <p><i class="glyphicon glyphicon-map-marker"></i> <?= $user->location; ?></p>
                    <p class="email"><i class="glyphicon glyphicon-envelope"></i> <?= $user->email; ?></p>
                    <p>
                        <?php $this->widget(
                            'bootstrap.widgets.TbButton',
                            [
                                'label'      => Yii::t('UserModule.user', 'Edit profile'),
                                'icon'       => 'glyphicon glyphicon-pencil',
                                'buttonType' => 'link',
                                'context'    => 'link',
                                'url'        => ['/user/profile/profile/'],
                            ]
                        ); ?>
                        <?php if(Yii::app()->hasModule('notify')):?>
                            <?php $this->widget(
                                'bootstrap.widgets.TbButton',
                                [
                                    'label'      => Yii::t('UserModule.user', 'Notify settings'),
                                    'icon'       => 'glyphicon glyphicon-pencil',
                                    'buttonType' => 'link',
                                    'context'    => 'link',
                                    'url'        => ['/notify/notify/settings/'],
                                ]
                            ); ?>
                        <?php endif;?>

                    </p>
                </div>
                <div class="right avatar-mini">
                <?= CHtml::link(
                    $this->widget(
                        'application.modules.user.widgets.AvatarWidget',
                        ['user' => $user, 'noCache' => true, 'imageHtmlOptions' => ['width' => 65, 'height' => 65]],
                        true
                    ),
                    ['/user/people/userInfo/', 'username' => $user->nick_name],
                    ['title' => Yii::t('UserModule.user', 'User profile')]
                ); ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <p>
                <ul class="user-info">
                   <?php /* ?>  <li>
                        <i class="glyphicon glyphicon-envelope"></i> <?= $user->email; ?>
                    </li>
                    <?php if ($user->site): { ?>
                        <li>
                            <i class="glyphicon glyphicon-globe"></i> <?= $user->site; ?>
                        </li>
                    <?php } endif; ?>
                    <?php if ($user->location): { ?>
                        <li>
                            <i class="glyphicon glyphicon-map-marker"></i> <?= $user->location; ?>
                        </li>
                    <?php } endif; ?> <?php */?>
                </ul>
            </div>
        </div>
    </div>
</div>
