<div class="active-header">
    <div class="pull-right buttons">
        <div class="search-form">
            <form class="form-search" action="<?php echo $this->createUrl('project/search'); ?>" method="post" onsubmit="return system.search.validate();">
                <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">
                <input name="SearchForm[query]" class="search-query" type="text" value="<?php echo $model->query ? CHtml::encode($model->query) : Yii::t('app', 'Search...'); ?>" onfocus="system.search.focus();" onblur="system.search.blur();" />
            </form>
        </div>
    </div>

    <h1>
        <?php echo CHtml::encode($this->pageTitle); ?>
    </h1>
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
                            <th class="stats"><?php echo Yii::t('app', 'Risk Stats'); ?></th>
                            <th class="percent"><?php echo Yii::t('app', 'Completed'); ?></th>
                            <th class="status"><?php echo Yii::t('app', 'Status'); ?></th>
                        </tr>
                        <?php foreach ($projects as $project): ?>
                            <tr>
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
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <?php echo Yii::t('app', 'No projects match your search criteria.'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
