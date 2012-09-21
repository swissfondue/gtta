<div class="active-header">
    <div class="pull-right">
        <ul class="nav nav-pills">
            <li><a href="<?php echo $this->createUrl('user/edit', array( 'id' => $user->id )); ?>"><?php echo Yii::t('app', 'Edit'); ?></a></li>
            <li class="active"><a href="<?php echo $this->createUrl('user/projects', array( 'id' => $user->id )); ?>"><?php echo Yii::t('app', 'Projects'); ?></a></li>
        </ul>
    </div>

    <div class="pull-right buttons">
        <a class="btn" href="<?php echo $this->createUrl('user/addproject', array( 'id' => $user->id )); ?>"><i class="icon icon-plus"></i> <?php echo Yii::t('app', 'Add Project'); ?></a>
    </div>

    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

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
                            <th class="status"><?php echo Yii::t('app', 'Status'); ?></th>
                            <th class="actions">&nbsp;</th>
                        </tr>
                        <?php foreach ($projects as $project): ?>
                            <tr data-id="<?php echo $project->project_id; ?>" data-control-url="<?php echo $this->createUrl('user/controlproject', array( 'id' => $user->id )); ?>">
                                <td class="deadline">
                                    <?php echo $project->project->deadline; ?>
                                </td>
                                <td class="name">
                                    <a href="<?php echo $this->createUrl('project/view', array( 'id' => $project->project_id )); ?>"><?php echo CHtml::encode($project->project->name); ?></a>
                                    <div class="client">
                                        <a href="<?php echo $this->createUrl('client/view', array( 'id' => $project->project->client_id )); ?>"><?php echo CHtml::encode($project->project->client->name); ?></a>
                                    </div>
                                </td>
                                <td class="status">
                                    <?php
                                        switch ($project->project->status)
                                        {
                                            case Project::STATUS_OPEN:
                                                echo '<span class="label">' . $statuses[$project->project->status] . '</span>';
                                                break;

                                            case Project::STATUS_IN_PROGRESS:
                                                echo '<span class="label label-in-progress">' . $statuses[$project->project->status] . '</span>';
                                                break;

                                            case Project::STATUS_FINISHED:
                                                echo '<span class="label label-finished">' . $statuses[$project->project->status] . '</span>';
                                                break;
                                        }
                                    ?>
                                </td>
                                <td class="actions">
                                    <a href="#del" title="<?php echo Yii::t('app', 'Delete'); ?>" onclick="system.control.del(<?php echo $project->project_id; ?>);"><i class="icon icon-remove"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php if ($p->pageCount > 1): ?>
                    <div class="pagination">
                        <ul>
                            <li <?php if (!$p->prevPage) echo 'class="disabled"'; ?>><a href="<?php echo $this->createUrl('user/projects', array( 'id' => $user->id, 'page' => $p->prevPage ? $p->prevPage : $p->page )); ?>" title="<?php echo Yii::t('app', 'Previous Page'); ?>">&laquo;</a></li>
                            <?php for ($i = 1; $i <= $p->pageCount; $i++): ?>
                                <li <?php if ($i == $p->page) echo 'class="active"'; ?>>
                                    <a href="<?php echo $this->createUrl('user/projects', array( 'id' => $user->id, 'page' => $i )); ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            <li <?php if (!$p->nextPage) echo 'class="disabled"'; ?>><a href="<?php echo $this->createUrl('user/projects', array( 'id' => $user->id, 'page' => $p->nextPage ? $p->nextPage : $p->page )); ?>" title="<?php echo Yii::t('app', 'Next Page'); ?>">&raquo;</a></li>
                        </ul>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <?php echo Yii::t('app', 'No projects yet.'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
