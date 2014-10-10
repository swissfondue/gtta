<div class="active-header">
    <div class="pull-right">
        <?php echo $this->renderPartial('partial/submenu', array( 'page' => 'users', 'project' => $project )); ?>
    </div>

    <div class="pull-right buttons">
        <a class="btn" href="<?php echo $this->createUrl("project/edituser", array("id" => $project->id)); ?>"><i class="icon icon-plus"></i> <?php echo Yii::t("app", "New User"); ?></a>
    </div>

    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<div class="container">
    <div class="row">
        <div class="span8">
            <?php if (count($users) > 0): ?>
                <table class="table user-list">
                    <tbody>
                        <tr>
                            <th class="user"><?php echo Yii::t("app", "User"); ?></th>
                            <th class="hours"><?php echo Yii::t("app", "Allocated"); ?></th>
                            <th class="hours"><?php echo Yii::t("app", "Spent"); ?></th>
                            <th class="role"><?php echo Yii::t("app", "Role"); ?></th>
                            <th class="actions">&nbsp;</th>
                        </tr>
                        <?php foreach ($users as $user): ?>
                            <tr data-id="<?php echo $user->user_id; ?>" data-control-url="<?php echo $this->createUrl("project/controluser", array("id" => $project->id)); ?>">
                                <td class="user">
                                    <a href="<?php echo $this->createUrl("project/edituser", array("id" => $project->id, "user" => $user->user_id)); ?>"><?php echo CHtml::encode($user->user->name ? $user->user->name : $user->user->email); ?></a>
                                </td>
                                <td class="hours">
                                    <?php echo sprintf("%.1f", $user->hours_allocated); ?>
                                </td>
                                <td class="hours">
                                    <?php echo sprintf("%.1f", $user->hoursSpent); ?>
                                </td>
                                <td class="role">
                                    <?php if ($user->admin): ?>
                                        <span class="label label-admin"><?php echo Yii::t("app", "Admin"); ?></span>
                                    <?php elseif ($user->user->role == User::ROLE_CLIENT): ?>
                                        <span class="label label-client"><?php echo Yii::t("app", "Client"); ?></span>
                                    <?php else: ?>
                                        <span class="label label-user"><?php echo Yii::t("app", "User"); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="actions">
                                    <a href="#del" title="<?php echo Yii::t("app", "Delete"); ?>" onclick="system.control.del(<?php echo $user->user_id; ?>);"><i class="icon icon-remove"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php echo $this->renderPartial("/layouts/partial/pagination", array("p" => $p, "url" => "project/users", "params" => array("id" => $project->id))); ?>
            <?php else: ?>
                <?php echo Yii::t("app", "No users yet."); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
