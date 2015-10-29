<h1><?php echo CHtml::encode($this->pageTitle); ?></h1>

<hr>

<div class="container">
    <div class="row">
        <div class="span8">
            <?php if (count($checks) > 0): ?>
                <div>
                    <table class="table process-monitor">
                        <tbody>
                            <tr>
                                <th class="name"><?php echo Yii::t('app', 'Check'); ?></th>
                                <th class="user"><?php echo Yii::t('app', 'User'); ?></th>
                                <th class="actions">&nbsp;</th>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <?php foreach ($checks as $check): ?>
                    <div class="process-monitor" data-control-url="<?php echo $this->createUrl('monitor/controlprocess'); ?>" data-id="<?php echo $check->target_id; ?>-<?php echo $check->check_id; ?>">
                        <table class="process-monitor">
                            <tbody>
                                <tr>
                                    <td class="name">
                                        <a href="<?php echo $this->createUrl('project/checks', array( 'id' => $check->target->project_id, 'target' => $check->target_id, 'category' => $check->check->control->check_category_id )); ?>"><?php echo CHtml::encode($check->check->localizedName); ?></a><br>
                                        <a href="<?php echo $this->createUrl('project/target', array( 'id' => $check->target->project_id, 'target' => $check->target_id )); ?>"><?php echo CHtml::encode($check->target->host); ?></a>
                                        <?php if ($check->target->description): ?>
                                            /
                                            <span class="description"><?php echo CHtml::encode($check->target->description); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="user">
                                        <a href="<?php echo $this->createUrl('user/edit', array( 'id' => $check->user_id )); ?>"><?php echo CHtml::encode($check->user->name ? $check->user->name : $check->user->email); ?></a>
                                    </td>
                                    <td class="actions">
                                        <a href="#stop" title="<?php echo Yii::t('app', 'Stop'); ?>" onclick="admin.process.stop(<?php echo $check->target_id; ?>, <?php echo $check->check_id; ?>);"><i class="icon icon-stop"></i></a>
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
