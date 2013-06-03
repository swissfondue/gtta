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
            <?php if (count($project->modules) == 0): ?>
                <a class="btn" href="#gt" onclick="user.project.toggleGuidedTest('<?php echo $this->createUrl('project/control'); ?>', <?php echo $project->id; ?>);"><i class="icon icon-wrench"></i> <?php echo Yii::t('app', 'Standard Mode'); ?></a>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<div class="container">
    <div class="row">
        <div class="span8">
            <?php if (count($categories) > 0): ?>
                <div class="gt-category-header-bold">
                    <?php echo Yii::t('app', 'Category'); ?>
                </div>

                <form class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post">
                    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">
                    <input type="hidden" value="0" name="ProjectGtForm[tmp]">

                    <fieldset>
                            <?php foreach ($categories as $category): ?>
                                <?php if (count($category->types)): ?>
                                    <div class="gt-category-header" data-id="<?php echo $category->id; ?>">
                                        <a href="#toggle" onclick="user.gtSelector.categoryToggle(<?php echo $category->id; ?>);">
                                            <?php echo CHtml::encode($category->localizedName); ?>
                                        </a>
                                    </div>
                                    <div class="gt-category-content hide" data-id="<?php echo $category->id; ?>">
                                        <?php if (count($category->types) > 0): ?>
                                            <?php foreach ($category->types as $type): ?>
                                                <div class="gt-category-type-header" data-id="<?php echo $type->id; ?>">
                                                    <a href="#toggle" onclick="user.gtSelector.typeToggle(<?php echo $type->id; ?>);">
                                                        <?php echo CHtml::encode($type->localizedName); ?>
                                                    </a>
                                                </div>
                                                <div class="gt-category-type-content hide" data-id="<?php echo $type->id; ?>">
                                                    <?php if (count($type->modules) > 0): ?>
                                                        <?php foreach ($type->modules as $module): ?>
                                                            <div class="gt-category-type-module">
                                                                <label>
                                                                    <input type="checkbox" name="ProjectGtForm[modules][<?php echo $module->id; ?>]" value="1" <?php if (in_array($module->id, $modules)) echo "checked"; ?>>
                                                                    <?php echo CHtml::encode($module->localizedName); ?>
                                                                </label>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <div class="gt-category-type-module-header">
                                                            <?php echo Yii::t('app', 'No modules yet.'); ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="gt-category-type-header">
                                                <?php echo Yii::t('app', 'No types yet.'); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>

                        <div class="form-actions">
                            <button type="submit" class="btn"><?php echo Yii::t('app', 'Save'); ?></button>&nbsp;
                            <?php if ($nextStep): ?>
                                <a class="btn" href="<?php echo $this->createUrl('project/gt', array('id' => $project->id)); ?>"><?php echo Yii::t('app', 'Start'); ?></a>
                            <?php endif; ?>
                        </div>
                    </fieldset>
                </form>
            <?php else: ?>
                <?php echo Yii::t('app', 'No categories yet.'); ?>
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
                                <?php if ($client->contact_fax): ?>
                                    <tr>
                                        <th>
                                            <?php echo Yii::t('app', 'Fax'); ?>
                                        </th>
                                        <td>
                                            <?php echo CHtml::encode($client->contact_fax); ?>
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
