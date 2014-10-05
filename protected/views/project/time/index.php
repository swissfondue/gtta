<div class="active-header">
    <div class="pull-right">
        <ul class="nav nav-pills">
            <li><a href="<?php echo $this->createUrl('project/view', array( 'id' => $project->id )); ?>"><?php echo Yii::t('app', 'View'); ?></a></li>

            <?php if (User::checkRole(User::ROLE_ADMIN)): ?>
                <li><a href="<?php echo $this->createUrl('project/edit', array( 'id' => $project->id )); ?>"><?php echo Yii::t('app', 'Edit'); ?></a></li>
                <li><a href="<?php echo $this->createUrl('project/users', array( 'id' => $project->id )); ?>"><?php echo Yii::t('app', 'Users'); ?></a></li>
            <?php endif; ?>

            <?php if (User::checkRole(User::ROLE_ADMIN) || User::checkRole(User::ROLE_CLIENT)): ?>
                <li><a href="<?php echo $this->createUrl('project/details', array( 'id' => $project->id )); ?>"><?php echo Yii::t('app', 'Details'); ?></a></li>
            <?php endif; ?>

            <li class="active"><a href="<?php echo $this->createUrl('project/time', array( 'id' => $project->id )); ?>"><?php echo Yii::t('app', 'Time'); ?></a></li>

            <li><a href="<?php echo $this->createUrl('vulntracker/vulns', array( 'id' => $project->id )); ?>"><?php echo Yii::t('app', 'Vulnerabilities'); ?></a></li>
        </ul>
    </div>

    <div class="pull-right buttons">
        <a class="btn" href="<?php echo $this->createUrl("project/tracktime", array("id" => $project->id)); ?>"><i class="icon icon-time"></i> <?php echo Yii::t("app", "Track Time"); ?></a>&nbsp;
        <a class="btn" <?php if (!count($records)) echo "disabled=\"disabled\""; ?> href="<?php echo $this->createUrl("report/trackedtime", array("id" => $project->id)); ?>"><i class="icon icon-share"></i> <?php echo Yii::t("app", "Export"); ?></a>
    </div>

    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<div class="container">
    <div class="row">
        <div class="span8">
            <?php if (count($records) > 0): ?>
                <table class="table time-records-list">
                    <tbody>
                    <tr>
                        <th class="user"><?php echo Yii::t('app', 'User'); ?></th>
                        <th class="time-logged"><?php echo Yii::t('app', 'Time Logged'); ?></th>
                        <th class="time-added"><?php echo Yii::t('app', 'Time Added'); ?></th>
                    </tr>
                    <?php foreach ($records as $record): ?>
                        <tr data-id="<?php echo $record->id; ?>" data-control-url="<?php echo $this->createUrl('project/controltime'); ?>">
                            <td class="user">
                                <a href="<?php echo $this->createUrl('user/edit', array( 'id' => $record->user_id )); ?>">
                                    <?php echo CHtml::encode($record->user ? ($record->user->name ? $record->user->name : $record->user->email) : $record->user_name); ?>
                                </a>
                                <div class="content">
                                    <?php echo CHtml::encode($record->description); ?>
                                </div>
                            </td>
                            <td class="time-logged">
                                <?php echo sprintf("%.1f", $record->hours); ?>
                            </td>
                            <td class="time-added">
                                <?php echo DateTimeFormat::toISO($record->create_time); ?>
                            </td>
                            <?php if (User::checkRole(User::ROLE_ADMIN)): ?>
                                <td class="actions">
                                    <a href="#del" title="<?php echo Yii::t('app', 'Delete'); ?>" onclick="system.control.del(<?php echo $record->id; ?>);"><i class="icon icon-remove"></i></a>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <?php echo $this->renderPartial('/layouts/partial/pagination', array('p' => $p, 'url' => 'project/time', 'params' => array( 'id' => $project->id ))); ?>
            <?php else: ?>
                <?php echo Yii::t('app', 'No tracked time records yet.'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
