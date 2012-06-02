<h1><?php echo CHtml::encode($this->pageTitle); ?></h1>

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
                <select class="input-xlarge" id="UserEditForm_role" name="UserEditForm[role]" onchange="admin.user.toggleClientField();">
                    <?php foreach ($roles as $k => $v): ?>
                        <option value="<?php echo $k; ?>" <?php if ($k == $model->role) echo 'selected'; ?>><?php echo CHtml::encode($v); ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if ($model->getError('role')): ?>
                    <p class="help-block"><?php echo $model->getError('role'); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="control-group <?php if ($model->getError('clientId')) echo 'error'; ?> <?php if ($model->role != User::ROLE_CLIENT) echo 'hidden-object'; ?>" id="client-input">
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

        <div class="form-actions">
            <button type="submit" class="btn"><?php echo Yii::t('app', 'Save'); ?></button>
        </div>
    </fieldset>
</form>
