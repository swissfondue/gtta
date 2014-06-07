<h1><?php echo CHtml::encode($this->pageTitle); ?></h1>

<hr>

<form class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

    <fieldset>
        <?php if ($user->isNewRecord): ?>
            <div class="control-group <?php if ($form->getError("userId")) echo "error"; ?>">
                <label class="control-label" for="ProjectUserEditForm_userId"><?php echo Yii::t("app", "User"); ?></label>
                <div class="controls">
                    <select class="input-xlarge" id="ProjectUserEditForm_userId" name="ProjectUserEditForm[userId]" onchange="admin.project.userAddFormChange();">
                        <option value="0"><?php echo Yii::t('app', 'Please select...'); ?></option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?php echo $user->id; ?>" <?php if ($form->userId == $user->id) echo 'selected'; ?> <?php echo 'data-role="' . $user->role . '"'; ?>><?php echo CHtml::encode($user->name ? $user->name : $user->email); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($form->getError("userId")): ?>
                        <p class="help-block"><?php echo $form->getError("userId"); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="control-group <?php if ($form->getError("admin")) echo "error"; ?>">
            <label class="control-label" for="ProjectUserEditForm_admin"><?php echo Yii::t("app", "Admin"); ?></label>
            <div class="controls">
                <input type="checkbox" id="ProjectUserEditForm_admin" name="ProjectUserEditForm[admin]" value="1" <?php if ($form->admin) echo "checked"; ?>>
                <?php if ($form->getError("admin")): ?>
                    <p class="help-block"><?php echo $form->getError("admin"); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="control-group <?php if ($form->getError("hoursAllocated")) echo "error"; ?>">
            <label class="control-label" for="ProjectUserEditForm_hoursAllocated"><?php echo Yii::t("app", "Hours Allocated"); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="ProjectUserEditForm_hoursAllocated" name="ProjectUserEditForm[hoursAllocated]" value="<?php echo CHtml::encode($form->hoursAllocated); ?>">
                <?php if ($form->getError("hoursAllocated")): ?>
                    <p class="help-block"><?php echo $form->getError("hoursAllocated"); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="control-group <?php if ($form->getError("hoursSpent")) echo "error"; ?>">
            <label class="control-label" for="ProjectUserEditForm_hoursSpent"><?php echo Yii::t("app", "Hours Spent"); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="ProjectUserEditForm_hoursSpent" name="ProjectUserEditForm[hoursSpent]" value="<?php echo CHtml::encode($form->hoursSpent); ?>">
                <?php if ($form->getError("hoursSpent")): ?>
                    <p class="help-block"><?php echo $form->getError("hoursSpent"); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn"><?php echo Yii::t("app", "Save"); ?></button>
        </div>
    </fieldset>
</form>
