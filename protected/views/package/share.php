<div class="active-header">
    <div class="pull-right">
        <ul class="nav nav-pills">
            <li><a href="<?php echo $this->createUrl("package/view", array("id" => $package->id)); ?>"><?php echo Yii::t("app", "View"); ?></a></li>
            <li class="dropdown" aria-expanded="false">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                    <?php echo Yii::t("app", "Edit"); ?>
                    <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="<?php echo $this->createUrl("package/editproperties", array("id" => $package->id)); ?>">Properties</a></li>
                    <li><a href="<?php echo $this->createUrl("package/editfiles", array("id" => $package->id)); ?>">Files</a></li>
                </ul>
            </li>
            <li class="active"><a href="<?php echo $this->createUrl("package/share", array("id" => $package->id)); ?>"><?php echo Yii::t("app", "Share"); ?></a></li>
        </ul>
    </div>

    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<p>
    <?php if ($package->status == Package::STATUS_SHARE): ?>
        <?php echo Yii::t("app", "The package is already shared."); ?>
    <?php else: ?>
        <?php echo Yii::t("app", "If you press the button below, the package will be shared with the community and will be available for everyone with a valid GTTA license."); ?>
        <?php echo Yii::t("app", "Please make sure that you really want to share this package and it contains no sensitive information before sharing, because this action is irreversible."); ?>
    <?php endif; ?>
</p>

<form class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">
    <input type="hidden" value="1" name="SharePackageForm[proceed]">

    <fieldset>
        <div class="form-actions">
            <button type="submit" id="submit_button" class="btn" <?php if ($package->status == Package::STATUS_SHARE) echo "disabled"; ?>><?php echo Yii::t("app", "Share"); ?></button>
        </div>
    </fieldset>
</form>
