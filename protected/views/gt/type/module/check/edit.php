<div class="active-header">
    <?php if (!$check->isNewRecord && $check->gt_dependency_processor_id): ?>
        <div class="pull-right">
            <ul class="nav nav-pills">
                <li class="active"><a href="<?php echo $this->createUrl('gt/editcheck', array('id' => $category->id, 'type' => $type->id, 'module' => $module->id, 'check' => $check->id)); ?>"><?php echo Yii::t('app', 'Edit'); ?></a></li>
                <li><a href="<?php echo $this->createUrl('gt/dependencies', array('id' => $category->id, 'type' => $type->id, 'module' => $module->id, 'check' => $check->id)); ?>"><?php echo Yii::t('app', 'Dependencies'); ?></a></li>
            </ul>
        </div>
    <?php endif; ?>

    <h1>
        <?php echo CHtml::encode($this->pageTitle); ?>
    </h1>
</div>

<hr>

<form class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post" id="object-selection-form" data-object-list-url="<?php echo $this->createUrl('app/objectlist'); ?>">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

    <fieldset>
        <ul class="nav nav-tabs" id="languages-tab">
            <?php foreach ($languages as $language): ?>
                <li<?php if ($language->default) echo ' class="active"'; ?>>
                    <a href="#<?php echo CHtml::encode($language->code); ?>">
                        <img src="<?php echo Yii::app()->baseUrl; ?>/images/languages/<?php echo CHtml::encode($language->code); ?>.png" alt="<?php echo CHtml::encode($language->name); ?>">
                        <?php echo CHtml::encode($language->name); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="tab-content">
            <?php foreach ($languages as $language): ?>
                <div class="tab-pane<?php if ($language->default) echo ' active'; ?>" id="<?php echo CHtml::encode($language->code); ?>">
                    <div class="control-group">
                        <label class="control-label" for="GtCheckEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_description"><?php echo Yii::t('app', 'Description'); ?></label>
                        <div class="controls">
                            <textarea class="wysiwyg" id="GtCheckEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_description" name="GtCheckEditForm[localizedItems][<?php echo CHtml::encode($language->id); ?>][description]"><?php echo isset($model->localizedItems[$language->id]) ? CHtml::encode($model->localizedItems[$language->id]['description']) : ''; ?></textarea>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label" for="GtCheckEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_targetDescription"><?php echo Yii::t('app', 'Target Description'); ?></label>
                        <div class="controls">
                            <textarea class="wysiwyg" id="GtCheckEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_targetDescription" name="GtCheckEditForm[localizedItems][<?php echo CHtml::encode($language->id); ?>][targetDescription]"><?php echo isset($model->localizedItems[$language->id]) ? CHtml::encode($model->localizedItems[$language->id]['targetDescription']) : ''; ?></textarea>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div>
            <hr>
        </div>
        
        <div class="control-group">
            <label class="control-label" for="GtCheckEditForm_controlId"><?php echo Yii::t('app', 'Control'); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="GtCheckEditForm_controlId" onchange="admin.check.loadChecks($(this), $('#GtCheckEditForm_checkId'));">
                    <option value="0"><?php echo Yii::t('app', 'Please select...'); ?></option>
                    <?php foreach ($categories as $cat): ?>
                        <?php foreach ($cat->controls as $ctrl): ?>
                            <option value="<?php echo $ctrl->id; ?>" <?php if ($controlId == $ctrl->id) echo "selected"; ?>><?php echo CHtml::encode($cat->localizedName); ?> / <?php echo CHtml::encode($ctrl->localizedName); ?></option>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="control-group <?php if ($model->getError('checkId')) echo 'error'; ?>" id="check-selector">
            <label class="control-label" for="GtCheckEditForm_checkId"><?php echo Yii::t('app', 'Check'); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="GtCheckEditForm_checkId" name="GtCheckEditForm[checkId]">
                    <option value="0"><?php echo Yii::t('app', 'Please select...'); ?></option>
                    <?php foreach ($checks as $check): ?>
                        <option value="<?php echo $check->id; ?>" <?php if ($model->checkId == $check->id) echo "selected"; ?>><?php echo CHtml::encode($check->localizedName); ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if ($model->getError('checkId')): ?>
                    <p class="help-block"><?php echo $model->getError('checkId'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div>
            <hr>
        </div>

        <div class="control-group <?php if ($model->getError('sortOrder')) echo 'error'; ?>">
            <label class="control-label" for="GtCheckEditForm_sortOrder"><?php echo Yii::t('app', 'Sort Order'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="GtCheckEditForm_sortOrder" name="GtCheckEditForm[sortOrder]" value="<?php echo $model->sortOrder ? $model->sortOrder : 0; ?>">
                <?php if ($model->getError('sortOrder')): ?>
                    <p class="help-block"><?php echo $model->getError('sortOrder'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="control-group <?php if ($model->getError('dependencyProcessorId')) echo 'error'; ?>" id="check-selector">
            <label class="control-label" for="GtCheckEditForm_dependencyProcessorId"><?php echo Yii::t('app', 'Dependency Processor'); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="GtCheckEditForm_dependencyProcessorId" name="GtCheckEditForm[dependencyProcessorId]">
                    <option value="0"><?php echo Yii::t('app', 'Please select...'); ?></option>
                    <?php foreach ($processors as $processor): ?>
                        <option value="<?php echo $processor->id; ?>" <?php if ($model->dependencyProcessorId == $processor->id) echo "selected"; ?>><?php echo CHtml::encode($processor->name); ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if ($model->getError('dependencyProcessorId')): ?>
                    <p class="help-block"><?php echo $model->getError('dependencyProcessorId'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn"><?php echo Yii::t('app', 'Save'); ?></button>
        </div>
    </fieldset>
</form>

<script>
    $('#languages-tab a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    });
</script>