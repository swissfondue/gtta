<div class="active-header">
    <?php if (User::checkRole(User::ROLE_ADMIN)): ?>
        <div class="pull-right">
            <ul class="nav nav-pills">
                <li class="active"><a href="<?php echo $this->createUrl('client/view', array( 'id' => $client->id )); ?>"><?php echo Yii::t('app', 'View'); ?></a></li>
                <li><a href="<?php echo $this->createUrl('client/edit', array( 'id' => $client->id )); ?>"><?php echo Yii::t('app', 'Edit'); ?></a></li>
            </ul>
        </div>
    <?php endif; ?>

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
                            <li <?php if (!$p->prevPage) echo 'class="disabled"'; ?>><a href="<?php echo $this->createUrl('client/view', array( 'id' => $client->id, 'page' => $p->prevPage ? $p->prevPage : $p->page )); ?>" title="<?php echo Yii::t('app', 'Previous Page'); ?>">&laquo;</a></li>
                            <?php for ($i = 1; $i <= $p->pageCount; $i++): ?>
                                <li <?php if ($i == $p->page) echo 'class="active"'; ?>>
                                    <a href="<?php echo $this->createUrl('client/view', array( 'id' => $client->id, 'page' => $i )); ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            <li <?php if (!$p->nextPage) echo 'class="disabled"'; ?>><a href="<?php echo $this->createUrl('client/view', array( 'id' => $client->id, 'page' => $p->nextPage ? $p->nextPage : $p->page )); ?>" title="<?php echo Yii::t('app', 'Next Page'); ?>">&raquo;</a></li>
                        </ul>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <?php echo Yii::t('app', 'No projects yet.'); ?>
            <?php endif; ?>
        </div>
        <div class="span4">
            <?php if ($client->hasDetails): ?>
                <div id="client-address-icon" class="pull-right expand-collapse-icon" onclick="system.toggleBlock('#client-address');"><i class="icon-chevron-up"></i></div>
                <h3><a href="#toggle" onclick="system.toggleBlock('#client-address')"><?php echo Yii::t('app', 'Client Address'); ?></a></h3>

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
        </div>
    </div>
</div>
