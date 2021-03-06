<div class="pull-right">
    <a class="btn" href="<?php echo $this->createUrl('user/edit') ?>"><i class="icon icon-plus"></i> <?php echo Yii::t('app', 'New User'); ?></a>
</div>

<h1><?php echo CHtml::encode($this->pageTitle); ?></h1>

<hr>

<div class="container">
    <div class="row">
        <div class="span8">
            <?php if (count($users) > 0): ?>
                <table class="table user-list">
                    <tbody>
                        <tr>
                            <th class="name"><?php echo Yii::t('app', 'User'); ?></th>
                            <th class="role"><?php echo Yii::t('app', 'Role'); ?></th>
                            <th class="actions">&nbsp;</th>
                        </tr>
                        <?php foreach ($users as $user): ?>
                            <tr data-id="<?php echo $user->id; ?>" data-control-url="<?php echo $this->createUrl('user/control'); ?>">
                                <td class="name">
                                    <a href="<?php echo $this->createUrl('user/edit', array( 'id' => $user->id )); ?>"><?php echo CHtml::encode($user->name ? $user->name : $user->email); ?></a>
                                </td>
                                <td class="role">
                                    <?php
                                        switch ($user->role)
                                        {
                                            case User::ROLE_ADMIN:
                                                echo '<span class="label label-admin">' . $roles[$user->role] . '</span>';
                                                break;

                                            case User::ROLE_USER:
                                                echo '<span class="label label-user">' . $roles[$user->role] . '</span>';
                                                break;

                                            case User::ROLE_CLIENT:
                                                echo '<span class="label label-client">' . $roles[$user->role] . '</span>';
                                                break;
                                        }
                                    ?>
                                </td>
                                <td class="actions">
                                    <a href="#del" title="<?php echo Yii::t('app', 'Delete'); ?>" onclick="system.control.del(<?php echo $user->id; ?>);"><i class="icon icon-remove"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php echo $this->renderPartial('/layouts/partial/pagination', array('p' => $p, 'url' => 'user/index', 'params' => array())); ?>
            <?php else: ?>
                <?php echo Yii::t('app', 'No users yet.'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
