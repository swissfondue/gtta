<div class="active-header">
    <?php if (!$user->isNewRecord): ?>
        <div class="pull-right">
            <ul class="nav nav-pills">
                <li class="active"><a href="<?php echo $this->createUrl('user/edit', array( 'id' => $user->id )); ?>"><?php echo Yii::t('app', 'Edit'); ?></a></li>
                <li><a href="<?php echo $this->createUrl('user/projects', array( 'id' => $user->id )); ?>"><?php echo Yii::t('app', 'Projects'); ?></a></li>
            </ul>
        </div>
    <?php endif; ?>

    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<form class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

    <fieldset>
        <div class="control-group <?php if ($model->getError('email')) echo 'error'; ?>">
            <label class="control-label" for="UserEditForm_email"><?php echo Yii::t('app', 'E-mail'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="UserEditForm_email" name="UserEditForm[email]" value="<?php echo CHtml::encode($model->email); ?>">
                <?php if ($model->getError('email')): ?>
                    <p class="help-block"><?php echo $model->getError('email'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="control-group <?php if ($model->getError('name')) echo 'error'; ?>">
            <label class="control-label" for="UserEditForm_name"><?php echo Yii::t('app', 'Name'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="UserEditForm_name" name="UserEditForm[name]" value="<?php echo CHtml::encode($model->name); ?>">
                <?php if ($model->getError('name')): ?>
                    <p class="help-block"><?php echo $model->getError('name'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="control-group <?php if ($model->getError('role')) echo 'error'; ?>">
            <label class="control-label" for="UserEditForm_role"><?php echo Yii::t('app', 'Role'); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="UserEditForm_role" name="UserEditForm[role]" onchange="admin.user.toggleClientFields();">
                    <?php foreach ($roles as $k => $v): ?>
                        <option value="<?php echo $k; ?>" <?php if ($k == $model->role) echo 'selected'; ?>><?php echo CHtml::encode($v); ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if ($model->getError('role')): ?>
                    <p class="help-block"><?php echo $model->getError('role'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="control-group <?php if ($model->getError('clientId')) echo 'error'; ?> <?php if ($model->role != User::ROLE_CLIENT) echo 'hide'; ?>" id="client-input">
            <label class="control-label" for="UserEditForm_clientId"><?php echo Yii::t('app', 'Client'); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="UserEditForm_clientId" name="UserEditForm[clientId]">
                    <option value="0"><?php echo Yii::t('app', 'Please select...'); ?></option>
                    <?php foreach ($clients as $client): ?>
                        <option value="<?php echo $client->id; ?>" <?php if ($client->id == $model->clientId) echo 'selected'; ?>><?php echo CHtml::encode($client->name); ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if ($model->getError('clientId')): ?>
                    <p class="help-block"><?php echo $model->getError('clientId'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="control-group <?php if ($model->getError('password')) echo 'error'; ?>">
            <label class="control-label" for="UserEditForm_password"><?php echo Yii::t('app', 'Password'); ?></label>
            <div class="controls">
                <input type="password" class="input-xlarge" id="UserEditForm_password" name="UserEditForm[password]">
                <?php if ($model->getError('password')): ?>
                    <p class="help-block"><?php echo $model->getError('password'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="control-group <?php if ($model->getError('passwordConfirmation')) echo 'error'; ?>">
            <label class="control-label" for="UserEditForm_passwordConfirmation"><?php echo Yii::t('app', 'Password Confirmation'); ?></label>
            <div class="controls">
                <input type="password" class="input-xlarge" id="UserEditForm_passwordConfirmation" name="UserEditForm[passwordConfirmation]">
                <?php if ($model->getError('passwordConfirmation')): ?>
                    <p class="help-block"><?php echo $model->getError('passwordConfirmation'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="control-group <?php if ($model->role == User::ROLE_CLIENT) echo 'hide'; ?>" id="send-notifications">
            <label class="control-label" for="UserEditForm_sendNotifications"><?php echo Yii::t('app', 'Send Notifications'); ?></label>
            <div class="controls">
                <input type="checkbox" id="UserEditForm_sendNotifications" name="UserEditForm[sendNotifications]" value="1" <?php if ($model->sendNotifications) echo 'checked="checked"'; ?>>
            </div>
        </div>

        <div class="control-group <?php if ($model->role != User::ROLE_CLIENT) echo 'hide'; ?>" id="show-reports">
            <label class="control-label" for="UserEditForm_showReports"><?php echo Yii::t('app', 'Show Reports'); ?></label>
            <div class="controls">
                <input type="checkbox" id="UserEditForm_showReports" name="UserEditForm[showReports]" value="1" <?php if ($model->showReports) echo 'checked="checked"'; ?>>
            </div>
        </div>

        <div class="control-group <?php if ($model->role != User::ROLE_CLIENT) echo 'hide'; ?>" id="show-details">
            <label class="control-label" for="UserEditForm_showDetails"><?php echo Yii::t('app', 'Show Details'); ?></label>
            <div class="controls">
                <input type="checkbox" id="UserEditForm_showDetails" name="UserEditForm[showDetails]" value="1" <?php if ($model->showDetails) echo 'checked="checked"'; ?>>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn"><?php echo Yii::t('app', 'Save'); ?></button>
        </div>
    </fieldset>
</form>
