<div class="active-header">
    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<div class="container">
    <div class="row">
        <div class="span8">
            <?php if ($checkCount > 0): ?>
                <table class="table vulnerability-list">
                    <tbody>
                        <tr>
                            <th class="check"><?php echo Yii::t('app', 'Check'); ?></th>
                            <?php if (User::checkRole(User::ROLE_USER)): ?>
                                <th class="assigned"><?php echo Yii::t('app', 'Assigned'); ?></th>
                            <?php endif; ?>
                            <?php if (User::checkRole(User::ROLE_CLIENT)): ?>
                                <th class="rating"><?php echo Yii::t('app', 'Rating'); ?></th>
                            <?php endif; ?>
                            <th class="status"><?php echo Yii::t('app', 'Status'); ?></th>
                        </tr>
                        <?php foreach ($checks as $check): ?>
                            <?php $this->renderPartial('partial/vuln-list-item', array( 'check' => $check, 'project' => $project, 'statuses' => $statuses, 'type' => 'check' )); ?>
                        <?php endforeach; ?>
                        <?php foreach ($customChecks as $customCheck): ?>
                            <?php $this->renderPartial('partial/vuln-list-item', array( 'check' => $customCheck, 'project' => $project, 'statuses' => $statuses, 'type' => 'custom' )); ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php echo $this->renderPartial('/layouts/partial/pagination', array('p' => $p, 'url' => 'vulntracker/index', 'params' => array('id' => $project->id))); ?>
            <?php else: ?>
                <?php echo Yii::t('app', 'No vulnerabilities yet.'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
