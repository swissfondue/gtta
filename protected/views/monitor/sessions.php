<h1><?php echo CHtml::encode($this->pageTitle); ?></h1>

<hr>

<div class="container">
    <div class="row">
        <div class="span8">
            <?php if (count($users) > 0): ?>
                <div>
                    <table class="table session-monitor">
                        <tbody>
                            <tr>
                                <th class="name"><?php echo Yii::t('app', 'User'); ?></th>
                                <th class="role"><?php echo Yii::t('app', 'Role'); ?></th>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <?php foreach ($users as $user): ?>
                    <div class="session-monitor" data-id="<?php echo $user->id; ?>">
                        <table class="session-monitor">
                            <tbody>
                                <tr>
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
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <?php echo Yii::t('app', 'No running processes.'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
