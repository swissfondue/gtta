<h1><?php echo CHtml::encode($this->pageTitle); ?></h1>

<hr>

<form class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

    <fieldset>
        <div class="control-group <?php if ($model->getError('userId')) echo 'error'; ?>">
            <label class="control-label" for="ProjectUserAddForm_userId"><?php echo Yii::t('app', 'User'); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="ProjectUserAddForm_userId" name="ProjectUserAddForm[userId]" onchange="admin.project.userAddFormChange();">
                    <option value="0"><?php echo Yii::t('app', 'Please select...'); ?></option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?php echo $user->id; ?>" <?php if ($model->userId == $user->id) echo 'selected'; ?> <?php echo 'data-role="' . $user->role . '"'; ?>><?php echo CHtml::encode($user->name ? $user->name : $user->email); ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if ($model->getError('userId')): ?>
                    <p class="help-block"><?php echo $model->getError('userId'); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="control-group <?php if ($model->getError('admin')) echo 'error'; ?>">
            <label class="control-label" for="ProjectUserAddForm_admin"><?php echo Yii::t('app', 'Admin'); ?></label>
            <div class="controls">
                <input type="checkbox" id="ProjectUserAddForm_admin" name="ProjectUserAddForm[admin]" value="1" <?php if ($model->admin) echo 'checked'; ?>>
                <?php if ($model->getError('admin')): ?>
                    <p class="help-block"><?php echo $model->getError('admin'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn"><?php echo Yii::t('app', 'Add'); ?></button>
        </div>
    </fieldset>
</form>
