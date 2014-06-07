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

            <li><a href="<?php echo $this->createUrl('vulntracker/vulns', array( 'id' => $project->id )); ?>"><?php echo Yii::t('app', 'Vulnerabilities'); ?></a></li>
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
            <?php
                echo $this->renderPartial("partial/right-block", array(
                    "quickTargets" => null,
                    "project" => $project,
                    "client" => $client,
                    "statuses" => $statuses,
                    "category" => null,
                    "target" => null
                ));
            ?>
        </div>
    </div>
</div>
