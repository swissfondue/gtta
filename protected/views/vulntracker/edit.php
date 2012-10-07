<h1><?php echo CHtml::encode($this->pageTitle); ?></h1>

<hr>

<form class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

    <fieldset>
        <div class="control-group <?php if ($model->getError('userId')) echo 'error'; ?>">
            <label class="control-label" for="VulnEditForm_userId"><?php echo Yii::t('app', 'User'); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="VulnEditForm_userId" name="VulnEditForm[userId]">
                    <option value="0"><?php echo Yii::t('app', 'Please select...'); ?></option>
                    <?php foreach ($admins as $user): ?>
                        <option value="<?php echo $user->id; ?>" <?php if ($user->id == $model->userId) echo 'selected'; ?>><?php echo $user->name ? CHtml::encode($user->name) : $user->email; ?></option>
                    <?php endforeach; ?>
                    <?php foreach ($users as $user): ?>
                        <option value="<?php echo $user->user_id; ?>" <?php if ($user->user_id == $model->userId) echo 'selected'; ?>><?php echo $user->user->name ? CHtml::encode($user->user->name) : $user->user->email; ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if ($model->getError('userId')): ?>
                    <p class="help-block"><?php echo $model->getError('userId'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="control-group <?php if ($model->getError('deadline')) echo 'error'; ?>">
            <label class="control-label" for="VulnEditForm_deadline"><?php echo Yii::t('app', 'Deadline'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="VulnEditForm_deadline" name="VulnEditForm[deadline]" value="<?php echo CHtml::encode($model->deadline); ?>"  readonly data-date="<?php echo $model->deadline; ?>" data-date-format="yyyy-mm-dd">
                <?php if ($model->getError('deadline')): ?>
                    <p class="help-block"><?php echo $model->getError('deadline'); ?></p>
                <?php endif; ?>
            </div>
        </div>        
        
        <div class="control-group <?php if ($model->getError('status')) echo 'error'; ?>">
            <label class="control-label" for="VulnEditForm_status"><?php echo Yii::t('app', 'Status'); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="VulnEditForm_status" name="VulnEditForm[status]">
                    <?php foreach ($statuses as $k => $v): ?>
                        <option value="<?php echo $k; ?>" <?php if ($k == $model->status) echo 'selected'; ?>><?php echo CHtml::encode($v); ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if ($model->getError('status')): ?>
                    <p class="help-block"><?php echo $model->getError('status'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn"><?php echo Yii::t('app', 'Save'); ?></button>
        </div>
    </fieldset>
</form>

<script>
    $(function() {
        $('#VulnEditForm_deadline').datepicker();
    });
</script>
