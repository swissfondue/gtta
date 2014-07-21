<div class="active-header">
    <div class="pull-right">
        <ul class="nav nav-pills">
            <li><a href="<?php echo $this->createUrl('check/editcheck', array( 'id' => $category->id, 'control' => $control->id, 'check' => $check->id )); ?>"><?php echo Yii::t('app', 'Edit'); ?></a></li>
            <?php if ($check->automated): ?>
                <li><a href="<?php echo $this->createUrl('check/scripts', array( 'id' => $category->id, 'control' => $control->id, 'check' => $check->id )); ?>"><?php echo Yii::t('app', 'Scripts'); ?></a></li>
            <?php endif; ?>
            <li><a href="<?php echo $this->createUrl('check/results', array( 'id' => $category->id, 'control' => $control->id, 'check' => $check->id )); ?>"><?php echo Yii::t('app', 'Results'); ?></a></li>
            <li><a href="<?php echo $this->createUrl('check/solutions', array( 'id' => $category->id, 'control' => $control->id, 'check' => $check->id )); ?>"><?php echo Yii::t('app', 'Solutions'); ?></a></li>
            <li class="active"><a href="<?php echo $this->createUrl("check/share", array("id" => $category->id, "control" => $control->id, "check" => $check->id)); ?>"><?php echo Yii::t('app', "Share"); ?></a></li>
        </ul>
    </div>

    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<p>
    <?php if ($check->external_id || $check->status == Package::STATUS_SHARE): ?>
        <?php echo Yii::t("app", "The check is already shared."); ?>
    <?php else: ?>
        <?php echo Yii::t("app", "If you press the button below, the check will be shared with the community and will be available for everyone with a valid GTTA license."); ?>
        <?php echo Yii::t("app", "Please make sure that you really want to share this check and it contains no sensitive information, because this action is irreversible."); ?>
    <?php endif; ?>
</p>

<br>

<form class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">
    <input type="hidden" name="ShareForm[share]" value="1">

    <fieldset>
        <div class="form-actions">
            <button type="submit" class="btn" <?php if ($check->external_id || $check->status == Package::STATUS_SHARE) echo "disabled"; ?>><?php echo Yii::t("app", "Share"); ?></button>
        </div>
    </fieldset>
</form>
