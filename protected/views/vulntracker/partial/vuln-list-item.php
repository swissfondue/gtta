<tr<?php if (User::checkRole(User::ROLE_USER) && $check->vulnOverdued && $check->vuln_status == TargetCheck::STATUS_VULN_OPEN) echo ' class="delete-row"'; ?>>
    <td class="check">
        <?php if ($type == TargetCheck::TYPE): ?>
            <?php if ($project->checkAdmin()): ?>
                <a href="<?php echo $this->createUrl('vulntracker/edit', array( 'id' => $project->id, 'target' => $check->target_id, 'check' => $check->check_id, 'type' => 'check' )); ?>"><?php echo CHtml::encode($check->check->localizedName); ?></a>
            <?php else: ?>
                <?php echo CHtml::encode($check->check->localizedName); ?>
            <?php endif; ?>
        <?php elseif ($type == TargetCustomCheck::TYPE): ?>
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
    </td>
    <?php if (User::checkRole(User::ROLE_USER)): ?>
        <td class="assigned">
            <?php if ($check->vulnUser): ?>
                <?php echo $check->vulnUser->name ? CHtml::encode($check->vulnUser->name) : $check->vulnUser->email; ?>
            <?php else: ?>
                <i class="icon icon-minus"></i>
            <?php endif; ?>
            <?php if ($check->vuln_deadline): ?>
                <div class="description">
                    <?php echo $check->vuln_deadline; ?>
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
        $status = $check->vuln_status ? $check->vuln_status : TargetCheck::STATUS_VULN_OPEN;

        switch ($status) {
            case TargetCheck::STATUS_VULN_OPEN:
                echo '<span class="label">' . $statuses[$status] . '</span>';
                break;

            case TargetCheck::STATUS_VULN_RESOLVED:
                echo '<span class="label label-finished">' . $statuses[$status] . '</span>';
                break;
        }
        ?>
    </td>
</tr>