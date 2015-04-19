<div class="active-header">
    <?php if (!$target->isNewRecord): ?>
        <div class="pull-right">
            <ul class="nav nav-pills">
                <li><a href="<?php echo $this->createUrl('project/target', array( 'id' => $project->id, 'target' => $target->id )); ?>"><?php echo Yii::t('app', 'View'); ?></a></li>
                <li class="active"><a href="<?php echo $this->createUrl('project/edittarget', array( 'id' => $project->id, 'target' => $target->id )); ?>"><?php echo Yii::t('app', 'Edit'); ?></a></li>
                <li><a href="<?php echo $this->createUrl('project/editchain', array( 'id' => $project->id, 'target' => $target->id )); ?>"><?php echo Yii::t('app', 'Check Chain'); ?></a></li>
            </ul>
        </div>
        <div class="pull-right buttons">
            <?php if (User::checkRole(User::ROLE_USER)): ?>
                <a class="btn" href="<?php echo $this->createUrl('project/edittarget', array('id' => $project->id)); ?>"><i class="icon icon-plus"></i> <?php echo Yii::t("app", "Add Another Target"); ?></a>&nbsp;
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<form id="TargetEditForm" class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

    <fieldset>
        <div class="control-group <?php if ($model->getError('host')) echo 'error'; ?>">
            <label class="control-label" for="TargetEditForm_host"><?php echo Yii::t('app', 'Host'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="TargetEditForm_host" name="TargetEditForm[host]" value="<?php echo CHtml::encode($model->host); ?>">
                <?php if ($model->getError('host')): ?>
                    <p class="help-block"><?php echo $model->getError('host'); ?></p>
                <?php else: ?>
                    <p class="help-block">
                        <?php echo Yii::t("app", "Host name or IP address."); ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <div class="control-group <?php if ($model->getError('port')) echo 'error'; ?>">
            <label class="control-label" for="TargetEditForm_port"><?php echo Yii::t('app', 'Port'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="TargetEditForm_port" name="TargetEditForm[port]" value="<?php echo CHtml::encode($model->port); ?>">
                <?php if ($model->getError('port')): ?>
                    <p class="help-block"><?php echo $model->getError('port'); ?></p>
                <?php else: ?>
                    <p class="help-block">
                        <?php echo Yii::t("app", "For example, 443. You may leave this field blank."); ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="control-group <?php if ($model->getError('description')) echo 'error'; ?>">
            <label class="control-label" for="TargetEditForm_description"><?php echo Yii::t('app', 'Description'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="TargetEditForm_description" name="TargetEditForm[description]" value="<?php echo CHtml::encode($model->description); ?>">
                <?php if ($model->getError('description')): ?>
                    <p class="help-block"><?php echo $model->getError('description'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="control-group <?php if ($model->getError('sourceType')) echo 'error'; ?>">
            <label class="control-label" for="TargetEditForm_sourceType"><?php echo Yii::t('app', 'Check Source'); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="TargetEditForm_sourceType" name="TargetEditForm[sourceType]" onchange="user.project.toggleChecksSource($(this).find('option:selected').data('source-type'));">
                    <?php if ($categories): ?>
                        <option data-source-type="categories" <?php if ($model->sourceType == Target::SOURCE_TYPE_CHECK_CATEGORIES) print 'selected="selected"'; ?> value="<?php print Target::SOURCE_TYPE_CHECK_CATEGORIES; ?>"><?php echo Yii::t('app', 'Check Categories'); ?></option>
                    <?php endif; ?>

                    <?php if ($templateCategories): ?>
                        <option data-source-type="templates" <?php if ($model->sourceType == Target::SOURCE_TYPE_CHECKLIST_TEMPLATES) print 'selected="selected"'; ?> value="<?php print Target::SOURCE_TYPE_CHECKLIST_TEMPLATES; ?>"><?php echo Yii::t('app', 'Checklist Templates'); ?></option>
                    <?php endif;?>
                </select>
                <?php if ($model->getError('sourceType')): ?>
                    <p class="help-block"><?php echo $model->getError('sourceType'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <?php if (count($categories)): ?>
            <div class="control-group checks-source-list categories-mode <?php if ($model->sourceType != Target::SOURCE_TYPE_CHECK_CATEGORIES) print "hide"; ?>" data-source-type="categories">
                <label class="control-label"><?php echo Yii::t('app', 'Check Categories'); ?></label>
                <div class="controls">
                    <?php foreach ($categories as $category): ?>
                        <label class="checkbox">
                            <input type="checkbox" id="TargetEditForm_categoryIds_<?php echo $category->id; ?>" name="TargetEditForm[categoryIds][]" value="<?php echo $category->id; ?>" <?php if (in_array($category->id, $model->categoryIds)) echo 'checked'; ?>>
                            <?php echo CHtml::encode($category->localizedName); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (count($templateCategories)): ?>
            <div class="control-group checks-source-list templates-mode <?php if ($model->sourceType != Target::SOURCE_TYPE_CHECKLIST_TEMPLATES) echo "hide"; ?>" data-source-type="templates">
                <label class="control-label"><?php echo Yii::t('app', 'Checklist Templates'); ?></label>
                <div class="controls">
                    <ul class="template-category-list">
                        <?php foreach ($templateCategories as $templateCategory): ?>
                            <?php if (!$templateCategory->templates) continue; ?>
                            <li>
                                <strong><?php print CHtml::encode($templateCategory->localizedName); ?></strong>
                                <ul class="template-list">
                                    <?php foreach ($templateCategory->templates as $template): ?>
                                        <li>
                                            <label class="checkbox">
                                                <input type="checkbox" id="TargetEditForm_templateIds_<?php echo $template->id; ?>" name="TargetEditForm[templateIds][]" value="<?php echo $template->id; ?>" <?php if (in_array($template->id, $model->templateIds)) echo 'checked'; ?>>
                                                <?php echo CHtml::encode($template->localizedName); ?>
                                            </label>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <?php if (count($references)): ?>
            <div class="control-group references-list categories-mode <?php if ($target->check_source_type != Target::SOURCE_TYPE_CHECK_CATEGORIES) print 'hide'; ?>">
                <label class="control-label"><?php echo Yii::t('app', 'References'); ?></label>
                <div class="controls">
                    <?php foreach ($references as $reference): ?>
                        <label class="checkbox">
                            <input type="checkbox" id="TargetEditForm_referenceIds_<?php echo $reference->id; ?>" name="TargetEditForm[referenceIds][]" value="<?php echo $reference->id; ?>" <?php if ($target->isNewRecord || in_array($reference->id, $model->referenceIds)) echo 'checked'; ?>>
                            <?php echo CHtml::encode($reference->name); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (count($relationTemplates)): ?>
            <div class="control-group">
                <label class="control-label"><?php echo Yii::t('app', 'Relation Template'); ?></label>
                <div class="controls">
                    <select id="TargetEditForm_relationTemplateId" name="TargetEditForm[relationTemplateId]">
                        <option value="0"><?php print Yii::t("app", "N/A"); ?></option>
                        <?php foreach ($relationTemplates as $template): ?>
                            <option value="<?php echo $template->id; ?>" <?php if ($template->id == $model->relationTemplateId) echo 'selected="selected"'; ?>><?php echo CHtml::encode($template->localizedName); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        <?php endif; ?>

        <div class="form-actions">
            <button type="submit" class="btn"><?php echo Yii::t('app', 'Save'); ?></button>
        </div>
    </fieldset>
</form>
