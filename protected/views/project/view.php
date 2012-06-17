<div class="active-header">
    <div class="pull-right">
        <ul class="nav nav-pills">
            <li class="active"><a href="<?php echo $this->createUrl('project/view', array( 'id' => $project->id )); ?>"><?php echo Yii::t('app', 'View'); ?></a></li>
            <?php if (User::checkRole(User::ROLE_USER)): ?>
                <li><a href="<?php echo $this->createUrl('project/edit', array( 'id' => $project->id )); ?>"><?php echo Yii::t('app', 'Edit'); ?></a></li>
            <?php endif; ?>
            <?php if (User::checkRole(User::ROLE_ADMIN) || User::checkRole(User::ROLE_CLIENT)): ?>
                <li><a href="<?php echo $this->createUrl('project/details', array( 'id' => $project->id )); ?>"><?php echo Yii::t('app', 'Details'); ?></a></li>
            <?php endif; ?>
        </ul>
    </div>

    <?php if (User::checkRole(User::ROLE_USER)): ?>
        <div class="btn-group pull-right buttons">
            <a class="btn dropdown-toggle" href="#report" <?php if (count($targets) == 0) echo 'disabled'; else echo 'data-toggle="dropdown"'; ?>><?php echo Yii::t('app', 'Report'); ?> <span class="caret"></span></a>
            <ul class="dropdown-menu">
                <li><a href="#project-report" onclick="user.project.reportDialog.show();"><?php echo Yii::t('app', 'Project Report...'); ?></a></li>
                <li><a href="#project-comparison-report" onclick="user.project.comparisonReportDialog.show();"><?php echo Yii::t('app', 'Comparison Report...'); ?></a></li>
            </ul>
        </div>
        <div class="pull-right buttons">
            <a class="btn" href="<?php echo $this->createUrl('project/edittarget', array( 'id' => $project->id )); ?>"><?php echo Yii::t('app', 'New Target'); ?></a>
        </div>
    <?php endif; ?>

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
                            <th class="stats">&nbsp;</th>
                            <th class="percent">&nbsp;</th>
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
            <h3><a href="#toggle" onclick="$('#project-info').slideToggle('slow');"><?php echo Yii::t('app', 'Project Information'); ?></a></h3>

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
                <h3><a href="#toggle" onclick="$('#project-details').slideToggle('slow');"><?php echo Yii::t('app', 'Project Details'); ?></a></h3>

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
                    <h3><a href="#toggle" onclick="$('#client-address').slideToggle('slow');"><?php echo Yii::t('app', 'Client Address'); ?></a></h3>

                    <div class="info-block hidden-object" id="client-address">
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
                    <h3><a href="#toggle" onclick="$('#client-contact').slideToggle('slow');"><?php echo Yii::t('app', 'Client Contact'); ?></a></h3>

                    <div class="info-block hidden-object" id="client-contact">
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

<?php if (User::checkRole(User::ROLE_USER)): ?>
    <div class="modal fade hidden-object" id="project-report">
        <div class="modal-header">
            <a href="#close" class="close" data-dismiss="modal">×</a>
            <h3><?php echo Yii::t('app', 'Project Report'); ?></h3>
        </div>

        <div class="modal-body">
            <div class="alert alert-error hidden-object">
                <?php echo Yii::t('app', 'Please select at least 1 target.'); ?>
            </div>

            <p>
                <?php echo Yii::t('app', 'Please select targets you want to see in the report.'); ?>
            </p>

            <form action="<?php echo $this->createUrl('project/report', array( 'id' => $project->id )); ?>" method="post">
                <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

                <ul class="report-target-list">
                    <?php foreach ($targets as $target): ?>
                        <li>
                            <label class="checkbox">
                                <input type="checkbox" id="ProjectReportForm_targetIds_<?php echo $target->id; ?>" name="ProjectReportForm[targetIds][]" value="<?php echo $target->id; ?>" checked>
                                <?php echo $target->host; ?>
                            </label>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </form>
        </div>

        <div class="modal-footer">
            <a href="#generate" class="btn" onclick="user.project.reportDialog.generate();"><?php echo Yii::t('app', 'Generate'); ?></a>
            <a href="#cancel" class="btn" data-dismiss="modal"><?php echo Yii::t('app', 'Cancel'); ?></a>
        </div>
    </div>

    <div class="modal fade hidden-object" id="project-comparison-report">
        <div class="modal-header">
            <a href="#close" class="close" data-dismiss="modal">×</a>
            <h3><?php echo Yii::t('app', 'Project Comparison Report'); ?></h3>
        </div>

        <div class="modal-body">
            <div class="alert alert-error hidden-object">
                <?php echo Yii::t('app', 'Please select a project.'); ?>
            </div>

            <p>
                <?php echo Yii::t('app', 'Please select a project you want to compare with the current project.'); ?>
            </p>

            <form action="<?php echo $this->createUrl('project/comparisonreport', array( 'id' => $project->id )); ?>" method="post">
                <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

                <select name="ProjectComparisonForm[projectId]" id="ProjectComparisonForm_projectId">
                    <option value="0"><?php echo Yii::t('app', 'Please select...'); ?></option>

                    <?php foreach ($clientProjects as $clientProject): ?>
                        <option value="<?php echo $clientProject->id; ?>"><?php echo $clientProject->name; ?> (<?php echo $clientProject->year; ?>)</option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>

        <div class="modal-footer">
            <a href="#generate" class="btn" onclick="user.project.comparisonReportDialog.generate();"><?php echo Yii::t('app', 'Generate'); ?></a>
            <a href="#cancel" class="btn" data-dismiss="modal"><?php echo Yii::t('app', 'Cancel'); ?></a>
        </div>
    </div>
<?php endif; ?>
