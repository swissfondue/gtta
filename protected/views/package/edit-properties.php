<div class="active-header">
    <div class="pull-right">
        <ul class="nav nav-pills">
            <li><a href="<?php echo $this->createUrl("package/view", array("id" => $package->id)); ?>"><?php echo Yii::t("app", "View"); ?></a></li>
            <li class="active dropdown" aria-expanded="false">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                    <?php echo Yii::t("app", "Edit"); ?>
                    <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                    <li class="active"><a href="<?php echo $this->createUrl("package/editproperties", array("id" => $package->id)); ?>">Properties</a></li>
                    <li><a href="<?php echo $this->createUrl("package/editfiles", array("id" => $package->id)); ?>">Files</a></li>
                </ul>
            </li>
            <li><a href="<?php echo $this->createUrl("package/share", array("id" => $package->id)); ?>"><?php echo Yii::t("app", "Share"); ?></a></li>
        </ul>
    </div>

    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<form class="form-horizontal" id="PackageEditProperties" action="<?php print Yii::app()->request->url; ?>" method="POST">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

    <fieldset>
        <div class="control-group <?php if ($form->getError('timeout')) echo 'error'; ?>">
            <label class="control-label" for="PackageEditPropertiesForm_timeout"><?php echo Yii::t('app', 'Timeout'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="PackageEditPropertiesForm_timeout" name="PackageEditPropertiesForm[timeout]" value="<?php echo $package->timeout; ?>">
                <?php if ($form->getError('timeout')): ?>
                    <p class="help-block"><?php echo $form->getError('timeout'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn"><?php echo Yii::t('app', 'Save'); ?></button>
        </div>
    </fieldset>
</form>