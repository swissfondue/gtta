<div class="active-header">
    <?php if (!$project->isNewRecord): ?>
        <div class="pull-right">
            <ul class="nav nav-pills">
                <li><a href="<?php echo $this->createUrl('project/view', array( 'id' => $project->id )); ?>"><?php echo Yii::t('app', 'View'); ?></a></li>
                <?php if (User::checkRole(User::ROLE_ADMIN)): ?>
                    <li class="active"><a href="<?php echo $this->createUrl('project/edit', array( 'id' => $project->id )); ?>"><?php echo Yii::t('app', 'Edit'); ?></a></li>
                    <li><a href="<?php echo $this->createUrl('project/users', array( 'id' => $project->id )); ?>"><?php echo Yii::t('app', 'Users'); ?></a></li>
                <?php endif; ?>
                <?php if (User::checkRole(User::ROLE_ADMIN) || User::checkRole(User::ROLE_CLIENT)): ?>
                    <li><a href="<?php echo $this->createUrl('project/details', array( 'id' => $project->id )); ?>"><?php echo Yii::t('app', 'Details'); ?></a></li>
                <?php endif; ?>
            </ul>
        </div>
    <?php endif; ?>

    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<form class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

    <fieldset>
        <?php if (User::checkRole(User::ROLE_ADMIN)): ?>
            <div class="control-group <?php if ($model->getError('name')) echo 'error'; ?>">
                <label class="control-label" for="ProjectEditForm_name"><?php echo Yii::t('app', 'Name'); ?></label>
                <div class="controls">
                    <input type="text" class="input-xlarge" id="ProjectEditForm_name" name="ProjectEditForm[name]" value="<?php echo CHtml::encode($model->name); ?>">
                    <?php if ($model->getError('name')): ?>
                        <p class="help-block"><?php echo $model->getError('name'); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="control-group <?php if ($model->getError('year')) echo 'error'; ?>">
                <label class="control-label" for="ProjectEditForm_year"><?php echo Yii::t('app', 'Year'); ?></label>
                <div class="controls">
                    <select class="input-xlarge" id="ProjectEditForm_year" name="ProjectEditForm[year]">
                        <?php for ($i = 2012; $i < 2024; $i++): ?>
                            <option value="<?php echo $i; ?>" <?php if ($model->year == $i) echo 'selected'; ?>><?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                    <?php if ($model->getError('year')): ?>
                        <p class="help-block"><?php echo $model->getError('year'); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="control-group <?php if ($model->getError('deadline')) echo 'error'; ?>">
                <label class="control-label" for="ProjectEditForm_deadline"><?php echo Yii::t('app', 'Deadline'); ?></label>
                <div class="controls">
                    <input type="text" class="input-xlarge" id="ProjectEditForm_deadline" name="ProjectEditForm[deadline]" value="<?php echo CHtml::encode($model->deadline); ?>"  readonly data-date="<?php echo $model->deadline; ?>" data-date-format="yyyy-mm-dd">
                    <?php if ($model->getError('deadline')): ?>
                        <p class="help-block"><?php echo $model->getError('deadline'); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="control-group <?php if ($model->getError('clientId')) echo 'error'; ?>">
                <label class="control-label" for="ProjectEditForm_clientId"><?php echo Yii::t('app', 'Client'); ?></label>
                <div class="controls">
                    <select class="input-xlarge" id="ProjectEditForm_clientId" name="ProjectEditForm[clientId]">
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
        <?php endif; ?>
        
        <div class="control-group <?php if ($model->getError('status')) echo 'error'; ?>">
            <label class="control-label" for="ProjectEditForm_status"><?php echo Yii::t('app', 'Status'); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="ProjectEditForm_status" name="ProjectEditForm[status]">
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

<?php if (User::checkRole(User::ROLE_ADMIN)): ?>
    <script>
        $(function() {
            $('#ProjectEditForm_deadline').datepicker();
        });
    </script>
<?php endif; ?>