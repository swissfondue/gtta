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
        <?php echo Yii::t("app", "Please make sure that you really want to share this check and it contains no sensitive information before sharing, because this action is irreversible."); ?>
    <?php endif; ?>
</p>

<br>

<form class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

    <fieldset>
        <div class="control-group <?php if ($form->getError("externalControlId")) echo "error"; ?>">
            <label class="control-label" for="ShareCheckForm_externalControlId"><?php echo Yii::t('app', 'Control'); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="ShareCheckForm_externalControlId" name="ShareCheckForm[externalControlId]">
                    <option value="0"><?php echo Yii::t("app", "Please select..."); ?></option>
                    <?php foreach ($catalogs->categories as $cat): ?>
                        <?php foreach ($cat->controls as $ctrl): ?>
                            <option value="<?php echo $ctrl->id; ?>">
                                <?php
                                    $localized = null;

                                    foreach ($cat->l10n as $l10n) {
                                        if ($l10n->code == Yii::app()->language) {
                                            $localized = $l10n->name;
                                        }
                                    }

                                    echo $localized ? $localized : $cat->name;
                                ?>
                                /
                                <?php
                                    $localized = null;

                                    foreach ($ctrl->l10n as $l10n) {
                                        if ($l10n->code == Yii::app()->language) {
                                            $localized = $l10n->name;
                                        }
                                    }

                                    echo $localized ? $localized : $ctrl->name;
                                ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </select>
                <?php if ($form->getError("externalControlId")): ?>
                    <p class="help-block"><?php echo $form->getError("externalControlId"); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="control-group <?php if ($form->getError("externalReferenceId")) echo "error"; ?>">
            <label class="control-label" for="ShareCheckForm_externalReferenceId"><?php echo Yii::t("app", "Reference"); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="ShareCheckForm_externalReferenceId" name="ShareCheckForm[externalReferenceId]">
                    <option value="0"><?php echo Yii::t("app", "Please select..."); ?></option>
                    <?php foreach ($catalogs->references as $reference): ?>
                        <option value="<?php echo $reference->id; ?>"><?php echo CHtml::encode($reference->name); ?></option>
                    <?php endforeach; ?>
                </select>

                <?php if ($form->getError("externalReferenceId")): ?>
                    <p class="help-block"><?php echo $form->getError("externalReferenceId"); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn" <?php if ($check->external_id || $check->status == Package::STATUS_SHARE) echo "disabled"; ?>><?php echo Yii::t("app", "Save"); ?></button>
        </div>
    </fieldset>
</form>
