<div class="active-header">
    <div class="pull-right">
        <ul class="nav nav-pills">
            <li><a href="<?php echo $this->createUrl("package/view", array("id" => $package->id)); ?>"><?php echo Yii::t("app", "View"); ?></a></li>
            <li class="active"><a href="<?php echo $this->createUrl("package/edit", array("id" => $package->id)); ?>"><?php echo Yii::t("app", "Edit"); ?></a></li>
            <li><a href="<?php echo $this->createUrl("package/share", array("id" => $package->id)); ?>"><?php echo Yii::t("app", "Share"); ?></a></li>
        </ul>
    </div>

    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<form class="form-horizontal" id="PackageEdit" action="<?php print Yii::app()->request->url; ?>" method="POST">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

    <fieldset>
        <div class="control-group">
            <label class="control-label" for="PackageEditForm_file"><?php echo Yii::t('app', 'File'); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="PackageEditForm_file" data-url="<?php print $this->createUrl('package/file', array( 'id' => $package->id )); ?>" onchange="admin.pkg.fileSelectChanged($(this).val());">
                    <option value="0" <?php if (!$selected) echo 'selected="selected"'; ?>><?php echo Yii::t("app", "New File"); ?></option>
                    <?php foreach ($files as $file): ?>
                        <option value="<?php echo $file; ?>" <?php if ($selected == $file) echo 'selected="selected"'; ?>><?php echo $file; ?></option>
                    <?php endforeach; ?>
                </select>
                &nbsp;
                <a class="del-button <?php if ($form->operation != 'save') echo 'hide'; ?>" href="#del" title="<?php echo Yii::t("app", "Delete"); ?>" onclick="admin.pkg.fileEdit('delete');">
                    <i class="icon icon-remove"></i>
                </a>
            </div>
        </div>

        <div class="control-group <?php if ($form->getError('path')) echo 'error'; ?> <?php if ($form->operation == 'save') echo 'hide'; ?>">
            <label class="control-label" for="PackageEditForm_path"><?php echo Yii::t('app', 'Path'); ?></label>
            <div class="controls">
                <input type="text" class="input-xxlarge" id="PackageEditForm_path" name="PackageEditForm[path]" value="<?php echo $selected; ?>">
                <?php if ($form->getError('path')): ?>
                    <p class="help-block"><?php echo $form->getError('path'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="control-group <?php if ($form->getError('content')) echo 'error'; ?>">
            <label class="control-label" for="PackageEditForm_content"><?php echo Yii::t('app', 'Content'); ?></label>
            <div class="controls">
                <textarea class="input-xxlarge" rows="20" id="PackageEditForm_content" name="PackageEditForm[content]" wrap="off"><?php echo CHtml::encode($form->content); ?></textarea>
                <?php if ($form->getError('content')): ?>
                    <p class="help-block"><?php echo $form->getError('content'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <input type="hidden" id="PackageEditForm_operation" name="PackageEditForm[operation]" />

        <div class="form-actions">
            <button type="submit" class="btn" onclick="admin.pkg.fileEdit('save');"><?php echo Yii::t('app', 'Save'); ?></button>
        </div>
    </fieldset>
</form>