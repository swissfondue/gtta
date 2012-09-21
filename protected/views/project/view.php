<div class="active-header">
    <div class="pull-right">
        <ul class="nav nav-pills">
            <li class="active"><a href="<?php echo $this->createUrl('project/view', array( 'id' => $project->id )); ?>"><?php echo Yii::t('app', 'View'); ?></a></li>
            <?php if (User::checkRole(User::ROLE_ADMIN)): ?>
                <li><a href="<?php echo $this->createUrl('project/edit', array( 'id' => $project->id )); ?>"><?php echo Yii::t('app', 'Edit'); ?></a></li>
                <li><a href="<?php echo $this->createUrl('project/users', array( 'id' => $project->id )); ?>"><?php echo Yii::t('app', 'Users'); ?></a></li>
            <?php endif; ?>
            <?php if (User::checkRole(User::ROLE_ADMIN) || User::checkRole(User::ROLE_CLIENT)): ?>
                <li><a href="<?php echo $this->createUrl('project/details', array( 'id' => $project->id )); ?>"><?php echo Yii::t('app', 'Details'); ?></a></li>
            <?php endif; ?>
        </ul>
    </div>

    <div class="pull-right buttons">
        <?php if (User::checkRole(User::ROLE_USER)): ?>
            <a class="btn" href="<?php echo $this->createUrl('project/edittarget', array( 'id' => $project->id )); ?>"><i class="icon icon-plus"></i> <?php echo Yii::t('app', 'New Target'); ?></a>&nbsp;
        <?php endif; ?>

        <a class="btn" href="#export" onclick="system.project.exportVulnForm();"><i class="icon icon-download"></i> <?php echo Yii::t('app', 'Export Vulns'); ?></a>
    </div>

    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<div class="container">
    <div class="row">
        <div class="span8">
            <?php if (count($targets) > 0): ?>
                <table class="table target-list">
                    <tbody>
                        <tr>
                            <th class="target"><?php echo Yii::t('app', 'Target'); ?></th>
                            <th class="stats"><?php echo Yii::t('app', 'Risk Stats'); ?></th>
                            <th class="percent"><?php echo Yii::t('app', 'Completed'); ?></th>
                            <th class="check-count"><?php echo Yii::t('app', 'Checks'); ?></th>
                            <?php if (User::checkRole(User::ROLE_ADMIN)): ?>
                                <th class="actions">&nbsp;</th>
                            <?php endif; ?>
                        </tr>
                        <?php foreach ($targets as $target): ?>
                            <tr data-id="<?php echo $target->id; ?>" data-control-url="<?php echo $this->createUrl('project/controltarget'); ?>">
                                <td class="target">
                                    <?php if (User::checkRole(User::ROLE_USER)): ?>
                                        <a href="<?php echo $this->createUrl('project/target', array( 'id' => $project->id, 'target' => $target->id )); ?>"><?php echo CHtml::encode($target->host); ?></a>
                                    <?php else: ?>
                                        <?php echo CHtml::encode($target->host); ?>
                                    <?php endif; ?>
                                </td>
                                <td class="stats">
                                    <span class="high-risk"><?php echo $target->highRiskCount ? $target->highRiskCount : 0; ?></span> /
                                    <span class="med-risk"><?php echo $target->medRiskCount ? $target->medRiskCount: 0; ?></span> /
                                    <span class="low-risk"><?php echo $target->lowRiskCount ? $target->lowRiskCount : 0; ?></span>
                                </td>
                                <td class="percent">
                                    <?php
                                        $finished = $target->finishedCount;

                                        if (!$finished)
                                            $finished = 0;

                                        echo $target->checkCount ? sprintf('%.2f', ($finished / $target->checkCount) * 100) : '0.00';
                                    ?>%
                                </td>
                                <td>
                                    <?php
                                        $checkCount = 0;

                                        foreach ($target->categories as $category)
                                            foreach ($category->controls as $control)
                                                $checkCount += $control->checkCount;

                                        echo $checkCount;
                                    ?>
                                </td>
                                <?php if (User::checkRole(User::ROLE_USER)): ?>
                                    <td class="actions">
                                        <a href="#del" title="<?php echo Yii::t('app', 'Delete'); ?>" onclick="system.control.del(<?php echo $target->id; ?>);"><i class="icon icon-remove"></i></a>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php if ($p->pageCount > 1): ?>
                    <div class="pagination">
                        <ul>
                            <li <?php if (!$p->prevPage) echo 'class="disabled"'; ?>><a href="<?php echo $this->createUrl('project/view', array( 'id' => $project->id, 'page' => $p->prevPage ? $p->prevPage : $p->page )); ?>" title="<?php echo Yii::t('app', 'Previous Page'); ?>">&laquo;</a></li>
                            <?php for ($i = 1; $i <= $p->pageCount; $i++): ?>
                                <li <?php if ($i == $p->page) echo 'class="active"'; ?>>
                                    <a href="<?php echo $this->createUrl('project/view', array( 'id' => $project->id, 'page' => $i )); ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            <li <?php if (!$p->nextPage) echo 'class="disabled"'; ?>><a href="<?php echo $this->createUrl('project/view', array( 'id' => $project->id, 'page' => $p->nextPage ? $p->nextPage : $p->page )); ?>" title="<?php echo Yii::t('app', 'Next Page'); ?>">&raquo;</a></li>
                        </ul>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <?php echo Yii::t('app', 'No targets yet.'); ?>
            <?php endif; ?>
        </div>
        <div class="span4">
            <div id="project-info-icon" class="pull-right expand-collapse-icon" onclick="system.toggleBlock('#project-info');"><i class="icon-chevron-up"></i></div>
            <h3><a href="#toggle" onclick="system.toggleBlock('#project-info');"><?php echo Yii::t('app', 'Project Information'); ?></a></h3>

            <div class="info-block" id="project-info">
                <table class="table client-details">
                    <tbody>
                        <?php if (!User::checkRole(User::ROLE_CLIENT)): ?>
                            <tr>
                                <th>
                                    <?php echo Yii::t('app', 'Client'); ?>
                                </th>
                                <td>
                                    <a href="<?php echo $this->createUrl('client/view', array( 'id' => $client->id )); ?>"><?php echo CHtml::encode($client->name); ?></a>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <th>
                                <?php echo Yii::t('app', 'Year'); ?>
                            </th>
                            <td>
                                <?php echo CHtml::encode($project->year); ?>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <?php echo Yii::t('app', 'Deadline'); ?>
                            </th>
                            <td>
                                <?php echo CHtml::encode($project->deadline); ?>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <?php echo Yii::t('app', 'Status'); ?>
                            </th>
                            <td>
                                <?php echo $statuses[$project->status]; ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <?php if ($project->details): ?>
                <div id="project-details-icon" class="pull-right expand-collapse-icon" onclick="system.toggleBlock('#project-details');"><i class="icon-chevron-up"></i></div>
                <h3><a href="#toggle" onclick="system.toggleBlock('#project-details');"><?php echo Yii::t('app', 'Project Details'); ?></a></h3>

                <div class="info-block" id="project-details">
                    <?php
                        $counter = 0;
                        foreach ($project->details as $detail):
                    ?>
                        <div class="project-detail <?php if (!$counter) echo 'borderless'; ?>">
                            <div class="subject"><?php echo CHtml::encode($detail->subject); ?></div>
                            <div class="content"><?php echo CHtml::encode($detail->content); ?></div>
                        </div>
                    <?php
                            $counter++;
                        endforeach;
                    ?>
                </div>
            <?php endif; ?>

            <?php if (!User::checkRole(User::ROLE_CLIENT)): ?>
                <?php if ($client->hasDetails): ?>
                    <div id="client-address-icon" class="pull-right expand-collapse-icon" onclick="system.toggleBlock('#client-address');"><i class="icon-chevron-up"></i></div>
                    <h3><a href="#toggle" onclick="system.toggleBlock('#client-address');"><?php echo Yii::t('app', 'Client Address'); ?></a></h3>

                    <div class="info-block" id="client-address">
                        <table class="table client-details">
                            <tbody>
                                <?php if ($client->country): ?>
                                    <tr>
                                        <th>
                                            <?php echo Yii::t('app', 'Country'); ?>
                                        </th>
                                        <td>
                                            <?php echo CHtml::encode($client->country); ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <?php if ($client->state): ?>
                                    <tr>
                                        <th>
                                            <?php echo Yii::t('app', 'State'); ?>
                                        </th>
                                        <td>
                                            <?php echo CHtml::encode($client->state); ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <?php if ($client->city): ?>
                                    <tr>
                                        <th>
                                            <?php echo Yii::t('app', 'City'); ?>
                                        </th>
                                        <td>
                                            <?php echo CHtml::encode($client->city); ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <?php if ($client->address): ?>
                                    <tr>
                                        <th>
                                            <?php echo Yii::t('app', 'Address'); ?>
                                        </th>
                                        <td>
                                            <?php echo CHtml::encode($client->address); ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <?php if ($client->postcode): ?>
                                    <tr>
                                        <th>
                                            <?php echo Yii::t('app', 'P.C.'); ?>
                                        </th>
                                        <td>
                                            <?php echo CHtml::encode($client->postcode); ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <?php if ($client->website): ?>
                                    <tr>
                                        <th>
                                            <?php echo Yii::t('app', 'Website'); ?>
                                        </th>
                                        <td>
                                            <a href="<?php echo CHtml::encode($client->website); ?>"><?php echo CHtml::encode($client->website); ?></a>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
                <?php if ($client->hasContact): ?>
                    <div id="client-contact-icon" class="pull-right expand-collapse-icon" onclick="system.toggleBlock('#client-contact');"><i class="icon-chevron-up"></i></div>
                    <h3><a href="#toggle" onclick="system.toggleBlock('#client-contact');"><?php echo Yii::t('app', 'Client Contact'); ?></a></h3>

                    <div class="info-block" id="client-contact">
                        <table class="table client-details">
                            <tbody>
                                <?php if ($client->contact_name): ?>
                                    <tr>
                                        <th>
                                            <?php echo Yii::t('app', 'Name'); ?>
                                        </th>
                                        <td>
                                            <?php echo CHtml::encode($client->contact_name); ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <?php if ($client->contact_email): ?>
                                    <tr>
                                        <th>
                                            <?php echo Yii::t('app', 'E-mail'); ?>
                                        </th>
                                        <td>
                                            <a href="mailto:<?php echo CHtml::encode($client->contact_email); ?>"><?php echo CHtml::encode($client->contact_email); ?></a>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <?php if ($client->contact_phone): ?>
                                    <tr>
                                        <th>
                                            <?php echo Yii::t('app', 'Phone'); ?>
                                        </th>
                                        <td>
                                            <?php echo CHtml::encode($client->contact_phone); ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="modal fade hide" id="export-modal">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3><?php echo Yii::t('app', 'Export Vulnerabilities'); ?></h3>
    </div>
    <div class="modal-body">
        <div class="modal-text">
            <?php echo Yii::t('app', 'Please select check ratings and columns that should be exported.'); ?>
        </div>
        <form id="ProjectVulnExportForm" class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post">
            <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

            <fieldset>
                <div class="control-group">
                    <label class="control-label" for="ProjectVulnExportForm_header"><?php echo Yii::t('app', 'Show Header'); ?></label>
                    <div class="controls">
                        <input type="checkbox" id="ProjectVulnExportForm_header" name="ProjectVulnExportForm[header]" value="1" checked onchange="system.project.exportVulnFormChange(this);">
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label"><?php echo Yii::t('app', 'Ratings'); ?></label>
                    <div class="controls">
                        <?php foreach ($ratings as $rating => $name): ?>
                            <label class="checkbox">
                                <input type="checkbox" id="ProjectVulnExportForm_ratings_<?php echo $rating; ?>" name="ProjectVulnExportForm[ratings][]" value="<?php echo $rating; ?>" checked onchange="system.project.exportVulnFormChange(this);">
                                <?php echo $name; ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label"><?php echo Yii::t('app', 'Columns'); ?></label>
                    <div class="controls">
                        <?php foreach ($columns as $column => $name): ?>
                            <label class="checkbox">
                                <input type="checkbox" id="ProjectVulnExportForm_columns_<?php echo $column; ?>" name="ProjectVulnExportForm[columns][]" value="<?php echo $column; ?>" checked onchange="system.project.exportVulnFormChange(this);">
                                <?php echo $name; ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal"><?php echo Yii::t('app', 'Cancel'); ?></button>
        <button id="export-button" class="btn btn-primary" onclick="system.project.exportVulnFormSubmit();"><?php echo Yii::t('app', 'Export'); ?></button>
    </div>
</div>
