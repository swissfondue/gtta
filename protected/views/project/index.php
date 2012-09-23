<div class="active-header">
    <?php if (User::checkRole(User::ROLE_ADMIN)): ?>
        <div class="pull-right">
            <a class="btn" href="<?php echo $this->createUrl('project/edit'); ?>"><i class="icon icon-plus"></i> <?php echo Yii::t('app', 'New Project'); ?></a>
        </div>
    <?php endif; ?>

    <div class="pull-right">
        <div class="search-form">
            <form class="form-search" action="<?php echo $this->createUrl('project/search'); ?>" method="post" onsubmit="return system.search.validate();">
                <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">
                <input name="SearchForm[query]" class="search-query" type="text" value="<?php echo Yii::t('app', 'Search...'); ?>" onfocus="system.search.focus();" onblur="system.search.blur();" />
            </form>
        </div>
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
                            <th class="stats"><?php echo Yii::t('app', 'Risk Stats'); ?></th>
                            <th class="percent"><?php echo Yii::t('app', 'Completed'); ?></th>
                            <th class="status"><?php echo Yii::t('app', 'Status'); ?></th>
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
                                        <a href="#del" title="<?php echo Yii::t('app', 'Delete'); ?>" onclick="system.control.del(<?php echo $project->id; ?>, '<?php echo Yii::t('app', 'WARNING! ALL INFORMATION WITHIN THIS PROJECT WILL BE DELETED!'); ?>');"><i class="icon icon-remove"></i></a>
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
        <div class="span4">
            <div id="filter-icon" class="pull-right expand-collapse-icon" onclick="system.toggleBlock('#filter');"><i class="icon-chevron-up"></i></div>
            <h3><a href="#toggle" onclick="system.toggleBlock('#filter');"><?php echo Yii::t('app', 'Filter'); ?></a></h3>

            <div class="info-block" id="filter">
                <table class="table client-details">
                    <tr>
                        <th>
                            <?php echo Yii::t('app', 'Status'); ?>
                        </th>
                        <td>
                            <label>
                                <input name="ProjectFilterForm[status]" type="checkbox" value="<?php echo Project::FILTER_STATUS_OPEN; ?>" <?php if (in_array(Project::STATUS_OPEN, $showStatuses)) echo 'checked'; ?> onchange="system.project.filterChange();">
                                <?php echo Yii::t('app', 'Open'); ?>
                            </label>
                            <label>
                                <input name="ProjectFilterForm[status]" type="checkbox" value="<?php echo Project::FILTER_STATUS_IN_PROGRESS; ?>" <?php if (in_array(Project::STATUS_IN_PROGRESS, $showStatuses)) echo 'checked'; ?> onchange="system.project.filterChange();">
                                <?php echo Yii::t('app', 'In Progress'); ?>
                            </label>
                            <label>
                                <input name="ProjectFilterForm[status]" type="checkbox" value="<?php echo Project::FILTER_STATUS_FINISHED; ?>" <?php if (in_array(Project::STATUS_FINISHED, $showStatuses)) echo 'checked'; ?> onchange="system.project.filterChange();">
                                <?php echo Yii::t('app', 'Finished'); ?>
                            </label>
                        </td>
                    </tr>
                </table>

                <hr>

                <table class="table client-details">
                    <tr>
                        <th>
                            <?php echo Yii::t('app', 'Sort By'); ?>
                        </th>
                        <td>
                            <select name="ProjectFilterForm[sortBy]" class="max-width" onchange="system.project.filterChange();">
                                <option value="<?php echo Project::FILTER_SORT_DEADLINE; ?>" <?php if ($sortBy == Project::FILTER_SORT_DEADLINE) echo 'selected'; ?>><?php echo Yii::t('app', 'Deadline'); ?></option>
                                <option value="<?php echo Project::FILTER_SORT_NAME; ?>" <?php if ($sortBy == Project::FILTER_SORT_NAME) echo 'selected'; ?>><?php echo Yii::t('app', 'Name'); ?></option>
                                <option value="<?php echo Project::FILTER_SORT_CLIENT; ?>" <?php if ($sortBy == Project::FILTER_SORT_CLIENT) echo 'selected'; ?>><?php echo Yii::t('app', 'Client'); ?></option>
                                <option value="<?php echo Project::FILTER_SORT_STATUS; ?>" <?php if ($sortBy == Project::FILTER_SORT_STATUS) echo 'selected'; ?>><?php echo Yii::t('app', 'Status'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>&nbsp;</th>
                        <td>
                            <select name="ProjectFilterForm[sortDirection]" class="max-width" onchange="system.project.filterChange();">
                                <option value="<?php echo Project::FILTER_SORT_ASCENDING; ?>" <?php if ($sortDirection == Project::FILTER_SORT_ASCENDING) echo 'selected'; ?>><?php echo Yii::t('app', 'Low to High'); ?></option>
                                <option value="<?php echo Project::FILTER_SORT_DESCENDING; ?>" <?php if ($sortDirection == Project::FILTER_SORT_DESCENDING) echo 'selected'; ?>><?php echo Yii::t('app', 'High to Low'); ?></option>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
