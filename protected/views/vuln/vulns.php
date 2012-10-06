<div class="active-header">
    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<div class="container">
    <div class="row">
        <div class="span8">
            <?php if (count($checks) > 0): ?>
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
                            <tr<?php if (User::checkRole(User::ROLE_USER) && $check->vuln && $check->vuln->overdued && $check->vuln->status == TargetCheckVuln::STATUS_OPEN) echo ' class="delete-row"'; ?>>
                                <td class="check">
                                    <?php if ($project->checkAdmin()): ?>
                                        <a href="<?php echo $this->createUrl('vuln/edit', array( 'id' => $project->id, 'target' => $check->target_id, 'check' => $check->check_id )); ?>"><?php echo CHtml::encode($check->check->localizedName); ?></a>
                                    <?php else: ?>
                                        <?php echo CHtml::encode($check->check->localizedName); ?>
                                    <?php endif; ?>

                                    <div class="description">
                                        <?php if (User::checkRole(User::ROLE_USER)): ?>
                                            <a href="<?php echo $this->createUrl('project/target', array( 'id' => $project->id, 'target' => $check->target_id )); ?>"><?php echo CHtml::encode($check->target->host); ?></a>

                                            <span>|</span>

                                            <a href="<?php echo $this->createUrl('project/checks', array('id' => $project->id, 'target' => $check->target_id, 'category' => $check->check->control->check_category_id)); ?>">
                                                <?php echo CHtml::encode($check->check->control->category->localizedName); ?> /
                                                <?php echo CHtml::encode($check->check->control->localizedName); ?>
                                            </a>
                                        <?php else: ?>
                                            <?php echo CHtml::encode($check->target->host); ?>
                                        <?php endif; ?>
                                    </div>
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
                                            switch ($check->rating)
                                            {
                                                case TargetChecK::RATING_LOW_RISK:
                                                    echo '<span class="label label-low-risk">' . $ratings[TargetCheck::RATING_LOW_RISK] . '</span>';
                                                    break;

                                                case TargetChecK::RATING_MED_RISK:
                                                    echo '<span class="label label-med-risk">' . $ratings[TargetCheck::RATING_MED_RISK] . '</span>';
                                                    break;

                                                case TargetChecK::RATING_HIGH_RISK:
                                                    echo '<span class="label label-high-risk">' . $ratings[TargetCheck::RATING_HIGH_RISK] . '</span>';
                                                    break;
                                            }
                                        ?>
                                    </td>
                                <?php endif; ?>
                                <td class="status">
                                    <?php
                                        $status = $check->vuln ? $check->vuln->status : TargetCheckVuln::STATUS_OPEN;

                                        switch ($status)
                                        {
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
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php if ($p->pageCount > 1): ?>
                    <div class="pagination">
                        <ul>
                            <li <?php if (!$p->prevPage) echo 'class="disabled"'; ?>><a href="<?php echo $this->createUrl('vuln/vulns', array( 'id' => $project->id, 'page' => $p->prevPage ? $p->prevPage : $p->page )); ?>" title="<?php echo Yii::t('app', 'Previous Page'); ?>">&laquo;</a></li>
                            <?php for ($i = 1; $i <= $p->pageCount; $i++): ?>
                                <li <?php if ($i == $p->page) echo 'class="active"'; ?>>
                                    <a href="<?php echo $this->createUrl('vuln/vulns', array( 'id' => $project->id, 'page' => $i )); ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            <li <?php if (!$p->nextPage) echo 'class="disabled"'; ?>><a href="<?php echo $this->createUrl('vuln/vulns', array( 'id' => $project->id, 'page' => $p->nextPage ? $p->nextPage : $p->page )); ?>" title="<?php echo Yii::t('app', 'Next Page'); ?>">&raquo;</a></li>
                        </ul>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <?php echo Yii::t('app', 'No vulnerabilities yet.'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
