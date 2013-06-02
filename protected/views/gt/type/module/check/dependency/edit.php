<h1><?php echo CHtml::encode($this->pageTitle); ?></h1>

<hr>

<form class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post" id="object-selection-form" data-object-list-url="<?php echo $this->createUrl('app/objectlist'); ?>">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

    <fieldset>
        <div class="control-group">
            <label class="control-label" for="GtCheckDependencyEditForm_categoryId"><?php echo Yii::t('app', 'Category'); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="GtCheckDependencyEditForm_categoryId" onchange="admin.gtCheck.loadTypes($(this), $('#GtCheckDependencyEditForm_typeId'));">
                    <option value="0"><?php echo Yii::t('app', 'Please select...'); ?></option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat->id; ?>" <?php if ($categoryId == $cat->id) echo "selected"; ?>><?php echo CHtml::encode($cat->localizedName); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="control-group" id="type-selector">
            <label class="control-label" for="GtCheckDependencyEditForm_typeId"><?php echo Yii::t('app', 'Type'); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="GtCheckDependencyEditForm_typeId" onchange="admin.gtCheck.loadModules($(this), $('#GtCheckDependencyEditForm_moduleId'));">
                    <option value="0"><?php echo Yii::t('app', 'Please select...'); ?></option>
                    <?php foreach ($types as $tp): ?>
                        <option value="<?php echo $tp->id; ?>" <?php if ($typeId == $tp->id) echo "selected"; ?>><?php echo CHtml::encode($tp->localizedName); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="control-group <?php if ($model->getError('moduleId')) echo 'error'; ?>" id="module-selector">
            <label class="control-label" for="GtCheckDependencyEditForm_moduleId"><?php echo Yii::t('app', 'Module'); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="GtCheckDependencyEditForm_moduleId" name="GtCheckDependencyEditForm[moduleId]">
                    <option value="0"><?php echo Yii::t('app', 'Please select...'); ?></option>
                    <?php foreach ($modules as $mod): ?>
                        <option value="<?php echo $mod->id; ?>" <?php if ($moduleId == $mod->id) echo "selected"; ?>><?php echo CHtml::encode($mod->localizedName); ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if ($model->getError('moduleId')): ?>
                    <p class="help-block"><?php echo $model->getError('moduleId'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div>
            <hr>
        </div>

        <div class="control-group <?php if ($model->getError('condition')) echo 'error'; ?>">
            <label class="control-label" for="GtCheckDependencyEditForm_condition"><?php echo Yii::t('app', 'Condition'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="GtCheckDependencyEditForm_condition" name="GtCheckDependencyEditForm[condition]" value="<?php echo $model->condition; ?>">
                <?php if ($model->getError('condition')): ?>
                    <p class="help-block"><?php echo $model->getError('condition'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn"><?php echo Yii::t('app', 'Save'); ?></button>
        </div>
    </fieldset>
</form>
