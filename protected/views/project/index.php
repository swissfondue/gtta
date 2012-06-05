<?php if (User::checkRole(User::ROLE_ADMIN)): ?>
    <div class="pull-right">
        <a class="btn" href="<?php echo $this->createUrl('project/edit'); ?>"><?php echo Yii::t('app', 'New Project'); ?></a>
    </div>
<?php endif; ?>

<h1><?php echo CHtml::encode($this->pageTitle); ?></h1>

<hr>

<div class="container">
    <div class="row">
        <div class="span8">
            <?php if (count($projects) > 0): ?>
                <table class="table project-list">
                    <tbody>
                        <tr>
                            <th class="deadline"><?php echo Yii::t('app', 'Deadline'); ?></th>
                            <th class="name"><?php echo Yii::t('app', 'Name'); ?></th>
                            <th class="stats">&nbsp;</th>
                            <th class="percent">&nbsp;</th>
                            <th class="status">&nbsp;</th>
                            <?php if (User::checkRole(User::ROLE_ADMIN)): ?>
                                <th class="actions">&nbsp;</th>
                            <?php endif; ?>
                        </tr>
                        <?php foreach ($projects as $project): ?>
                            <tr data-id="<?php echo $project->id; ?>" data-control-url="<?php echo $this->createUrl('project/control'); ?>">
                                <td class="deadline">
                                    <?php echo $project->deadline; ?>
                                </td>
                                <td class="name">
                                    <a href="<?php echo $this->createUrl('project/view', array( 'id' => $project->id )); ?>"><?php echo CHtml::encode($project->name); ?></a>
                                    <?php if (User::checkRole(User::ROLE_USER)): ?>
                                        <div class="client">
                                            <a href="<?php echo $this->createUrl('client/view', array( 'id' => $project->client_id )); ?>"><?php echo CHtml::encode($project->client->name); ?></a>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="stats">
                                    <span class="high-risk"><?php echo $stats[$project->id]['highRiskCount']; ?></span> /
                                    <span class="med-risk"><?php echo $stats[$project->id]['medRiskCount']; ?></span> /
                                    <span class="low-risk"><?php echo $stats[$project->id]['lowRiskCount']; ?></span>
                                </td>
                                <td class="percent">
                                    <?php echo $stats[$project->id]['checkCount'] ? sprintf('%.2f', ($stats[$project->id]['finishedCount'] / $stats[$project->id]['checkCount']) * 100) : '0.00'; ?>%
                                </td>
                                <td class="status">
                                    <?php
                                        switch ($project->status)
                                        {
                                            case Project::STATUS_OPEN:
                                                echo '<span class="label">' . $statuses[$project->status] . '</span>';
                                                break;

                                            case Project::STATUS_IN_PROGRESS:
                                                echo '<span class="label label-in-progress">' . $statuses[$project->status] . '</span>';
                                                break;

                                            case Project::STATUS_FINISHED:
                                                echo '<span class="label label-finished">' . $statuses[$project->status] . '</span>';
                                                break;
                                        }
                                    ?>
                                </td>
                                <?php if (User::checkRole(User::ROLE_ADMIN)): ?>
                                    <td class="actions">
                                        <a href="#del" title="<?php echo Yii::t('app', 'Delete'); ?>" onclick="system.control.del(<?php echo $project->id; ?>);"><i class="icon icon-remove"></i></a>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php if ($p->pageCount > 1): ?>
                    <div class="pagination">
                        <ul>
                            <li <?php if (!$p->prevPage) echo 'class="disabled"'; ?>><a href="<?php echo $this->createUrl('project/index', array( 'page' => $p->prevPage ? $p->prevPage : $p->page )); ?>" title="<?php echo Yii::t('app', 'Previous Page'); ?>">&laquo;</a></li>
                            <?php for ($i = 1; $i <= $p->pageCount; $i++): ?>
                                <li <?php if ($i == $p->page) echo 'class="active"'; ?>>
                                    <a href="<?php echo $this->createUrl('project/index', array( 'page' => $i )); ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            <li <?php if (!$p->nextPage) echo 'class="disabled"'; ?>><a href="<?php echo $this->createUrl('project/index', array( 'page' => $p->nextPage ? $p->nextPage : $p->page )); ?>" title="<?php echo Yii::t('app', 'Next Page'); ?>">&raquo;</a></li>
                        </ul>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <?php echo Yii::t('app', 'No projects yet.'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
