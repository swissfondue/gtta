<div class="active-header">
    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<form class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post" id="object-selection-form" data-object-list-url="<?php print $this->createUrl("app/objectlist"); ?>">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

    <?php if ($categories): ?>
        <fieldset>
            <div class="control-group <?php if ($model->getError('categoryId')) echo 'error'; ?>">
                <label class="control-label" for="ChecklistTemplateCheckCategory_categoryId"><?php echo Yii::t('app', 'Check Category'); ?></label>
                <div class="controls">
                    <select class="input-xlarge" id="ChecklistTemplateCheckCategory_categoryId" name="ChecklistTemplateCheckCategoryEditForm[categoryId]" <?php if (!$newRecord) print 'disabled="disabled"'; ?> onchange="admin.checklisttemplate.loadChecks($(this).val())">
                        <option value="0"><?php echo Yii::t('app', 'Please select...'); ?></option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category->id; ?>" <?php if ($category->id == $model->categoryId) echo 'selected'; ?>><?php echo CHtml::encode($category->localizedName); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($model->getError('categoryId')): ?>
                        <p class="help-block"><?php echo $model->getError('categoryId'); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="control-group check-list-wrapper <?php if (!$checks) print "hide"; ?>">
                <label class="control-label"><?php echo Yii::t('app', 'Checks'); ?></label>
                <div class="controls">
                    <div class="check-list">
                        <?php foreach ($checks as $check): ?>
                            <label class="checkbox">
                                <input type="checkbox" id="ChecklistTemplateCheckCategoryEditForm_checkIds_<?php echo $check->id; ?>" name="ChecklistTemplateCheckCategoryEditForm[checkIds][]" value="<?php echo $check->id; ?>" <?php if (in_array($check->id, $model->checkIds)) echo 'checked'; ?> onchange="admin.checklisttemplate.toggleChecklistButton()">
                                <?php echo CHtml::encode($check->localizedName); ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-submit" <?php if ($newRecord) print 'disabled="disabled"'; ?>><?php echo Yii::t('app', 'Save'); ?></button>
            </div>
        </fieldset>
    <?php else: ?>
        <?php print Yii::t("app", "No category to add."); ?>
    <?php endif; ?>
</form>
