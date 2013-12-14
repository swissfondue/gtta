<div class="active-header">
    <?php if (!$script->isNewRecord): ?>
        <div class="pull-right">
            <ul class="nav nav-pills">
                <li class="active"><a href="<?php echo $this->createUrl('check/editscript', array('id' => $category->id, 'control' => $control->id, 'check' => $check->id, 'script' => $script->id)); ?>"><?php echo Yii::t('app', 'Edit'); ?></a></li>
                <li><a href="<?php echo $this->createUrl('check/inputs', array('id' => $category->id, 'control' => $control->id, 'check' => $check->id, 'script' => $script->id)); ?>"><?php echo Yii::t('app', 'Inputs'); ?></a></li>
            </ul>
        </div>
    <?php endif; ?>

    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<form class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

    <fieldset>
        <div class="control-group <?php if ($model->getError('packageId')) echo 'error'; ?>">
            <label class="control-label" for="CheckScriptEditForm_packageId"><?php echo Yii::t('app', 'Package'); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="CheckScriptEditForm_packageId" name="CheckScriptEditForm[packageId]">
                    <option value="0"><?php echo Yii::t("app", "Please select..."); ?></option>
                        <?php foreach ($packages as $package): ?>
                            <option value="<?php echo $package->id; ?>" <?php if ($package->id == $model->packageId) echo "selected"; ?>><?php echo CHtml::encode($package->name) . " ". $package->version; ?></option>
                        <?php endforeach; ?>
                </select>

                <?php if ($model->getError("packageId")): ?>
                    <p class="help-block"><?php echo $model->getError("packageId"); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn"><?php echo Yii::t('app', 'Save'); ?></button>
        </div>
    </fieldset>
</form>