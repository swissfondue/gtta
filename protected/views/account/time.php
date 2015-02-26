<div class="active-header">
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
                        <th class="interval"><?php echo Yii::t('app', 'Time'); ?></th>
                        <th class="project"><?php echo Yii::t('app', 'Project'); ?></th>
                        <th class="total"><?php echo Yii::t('app', 'Total'); ?></th>
                        <?php if (User::checkRole(User::ROLE_ADMIN)): ?>
                            <th class="actions">&nbsp;</th>
                        <?php endif; ?>
                    </tr>
                    <?php foreach ($records as $record): ?>
                        <tr data-id="<?php echo $record['id']; ?>" data-control-url="<?php echo $this->createUrl('account/controltimerecord'); ?>">
                            <td class="interval">
                                <?php print $record['create_time'] ?>
                                <?php print $record['start_time']; ?> - <?php print $record['stop_time']; ?>
                            </td>
                            <td class="project">
                                <a href="<?= $this->createUrl("project/view", array("id" => $record["project_id"])); ?>" target="_blank"><?php print $record['project']; ?></a>
                            </td>
                            <td class="total">
                                <?php print $record['total']; ?>
                            </td>
                            <?php if (User::checkRole(User::ROLE_ADMIN)): ?>
                                <td class="actions">
                                    <a href="#del" title="<?php echo Yii::t('app', 'Delete'); ?>" onclick="system.control.del(<?php echo $record['id']; ?>);"><i class="icon icon-remove"></i></a>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <?php echo $this->renderPartial('/layouts/partial/pagination', array('p' => $p, 'url' => 'account/time', "params" => array())); ?>
            <?php else: ?>
                <?php echo Yii::t('app', 'No tracked time records yet.'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
