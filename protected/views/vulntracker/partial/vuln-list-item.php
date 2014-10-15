<tr<?php if (User::checkRole(User::ROLE_USER) && $check->vuln && $check->vuln->overdued && $check->vuln->status == TargetCheckVuln::STATUS_OPEN) echo ' class="delete-row"'; ?>>
    <td class="check">
        <?php if ($project->guided_test): ?>
            <?php if ($project->checkAdmin()): ?>
                <a href="<?php echo $this->createUrl('vulntracker/edit', array('id' => $project->id, 'target' => '0', 'check' => $check->gt_check_id)); ?>"><?php echo CHtml::encode($check->check->check->localizedName); ?></a>
            <?php else: ?>
                <?php echo CHtml::encode($check->check->check->localizedName); ?>
            <?php endif; ?>

            <div class="description">
                <?php if ($check->target): ?>
                    <?php echo CHtml::encode($check->target); ?>
                <?php else: ?>
                    <?php echo Yii::t('app', 'N/A'); ?>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <?php if ($type == Yii::app()->params['checks']['types']['targetCheck']): ?>
                <?php if ($project->checkAdmin()): ?>
                    <a href="<?php echo $this->createUrl('vulntracker/edit', array( 'id' => $project->id, 'target' => $check->target_id, 'check' => $check->check_id, 'type' => 'check' )); ?>"><?php echo CHtml::encode($check->check->localizedName); ?></a>
                <?php else: ?>
                    <?php echo CHtml::encode($check->check->localizedName); ?>
                <?php endif; ?>
            <?php elseif ($type == Yii::app()->params['checks']['types']['targetCustomCheck']): ?>
                <?php if ($project->checkAdmin()): ?>
                    <a href="<?php echo $this->createUrl('vulntracker/edit', array( 'id' => $project->id, 'target' => $check->target_id, 'check' => $check->id, 'type' => 'custom' )); ?>"><?php echo $check->name ? CHtml::encode($check->name) : "CUSTOM-CHECK-" . $check->reference; ?></a>
                <?php else: ?>
                    <?php echo CHtml::encode($check->check->name); ?>
                <?php endif; ?>
            <?php endif; ?>

            <div class="description">
                <?php if (User::checkRole(User::ROLE_USER)): ?>
                    <a href="<?php echo $this->createUrl('project/target', array( 'id' => $project->id, 'target' => $check->target_id )); ?>"><?php echo CHtml::encode($check->target->host); ?></a>
                <?php else: ?>
                    <?php echo CHtml::encode($check->target->host); ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </td>
    <?php if (User::checkRole(User::ROLE_USER)): ?>
        <td class="assigned">
            <?php if ($check->vuln && $check->vuln->user_id): ?>
                <?php echo $check->vuln->user->name ? CHtml::encode($check->vuln->user->name) : $check->vuln->user->email; ?>
            <?php else: ?>
                <i class="icon icon-minus"></i>
            <?php endif; ?>
            <?php if ($check->vuln && $check->vuln->deadline): ?>
                <div class="description">
                    <?php echo $check->vuln->deadline; ?>
                </div>
            <?php endif; ?>
        </td>
    <?php endif; ?>
    <?php if (User::checkRole(User::ROLE_CLIENT)): ?>
        <td class="rating">
            <?php
            switch ($check->rating) {
                case TargetCheck::RATING_LOW_RISK:
                    echo '<span class="label label-low-risk">' . $ratings[TargetCheck::RATING_LOW_RISK] . '</span>';
                    break;

                case TargetCheck::RATING_MED_RISK:
                    echo '<span class="label label-med-risk">' . $ratings[TargetCheck::RATING_MED_RISK] . '</span>';
                    break;

                case TargetCheck::RATING_HIGH_RISK:
                    echo '<span class="label label-high-risk">' . $ratings[TargetCheck::RATING_HIGH_RISK] . '</span>';
                    break;
            }
            ?>
        </td>
    <?php endif; ?>
    <td class="status">
        <?php
        $status = $check->vuln ? $check->vuln->status : TargetCheckVuln::STATUS_OPEN;

        switch ($status) {
            case TargetCheckVuln::STATUS_OPEN:
                echo '<span class="label">' . $statuses[$status] . '</span>';
                break;

            case TargetCheckVuln::STATUS_RESOLVED:
                echo '<span class="label label-finished">' . $statuses[$status] . '</span>';
                break;
        }
        ?>
    </td>
</tr>