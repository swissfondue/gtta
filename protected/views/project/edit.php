<div class="active-header">
    <?php if (!$project->isNewRecord): ?>
        <div class="pull-right">
            <?php echo $this->renderPartial('partial/submenu', array( 'page' => 'edit', 'project' => $project )); ?>
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
            
            <div class="control-group <?php if ($model->getError("startDate")) echo "error"; ?>">
                <label class="control-label" for="ProjectEditForm_startDate"><?php echo Yii::t("app", "Start Date"); ?></label>
                <div class="controls">
                    <input type="text" class="input-xlarge" id="ProjectEditForm_startDate" name="ProjectEditForm[startDate]" value="<?php echo CHtml::encode($model->startDate); ?>"  readonly data-date="<?php echo $model->startDate; ?>" data-date-format="yyyy-mm-dd">
                    <?php if ($model->getError("startDate")): ?>
                        <p class="help-block"><?php echo $model->getError("startDate"); ?></p>
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
            
            <div class="control-group <?php if ($model->getError("hoursAllocated")) echo "error"; ?>">
                <label class="control-label" for="ProjectEditForm_hoursAllocated"><?php echo Yii::t("app", "Hours Allocated"); ?></label>
                <div class="controls">
                    <input type="text" class="input-xlarge" id="ProjectEditForm_hoursAllocated" name="ProjectEditForm[hoursAllocated]" value="<?php echo CHtml::encode($model->hoursAllocated); ?>">
                    <?php if ($model->getError("hoursAllocated")): ?>
                        <p class="help-block"><?php echo $model->getError("hoursAllocated"); ?></p>
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
                    <?php foreach (Project::getStatusTitles() as $k => $v): ?>
                        <option value="<?php echo $k; ?>" <?php if ($k == $model->status) echo 'selected'; ?>><?php echo CHtml::encode($v); ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if ($model->getError('status')): ?>
                    <p class="help-block"><?php echo $model->getError('status'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="control-group <?php if ($model->getError('languageId')) echo 'error'; ?>">
            <label class="control-label" for="ProjectEditForm_languageId"><?php echo Yii::t("app", "Language"); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="ProjectEditForm_languageId" name="ProjectEditForm[languageId]">
                    <option value="0"><?= Yii::t("app", "N/A"); ?></option>
                    <?php foreach ($languages as $language): ?>
                        <option value="<?php echo $language->id; ?>" <?= $model->languageId == $language->id ? "selected" : ''; ?>>
                            <?php echo CHtml::encode($language->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if ($model->getError('languageId')): ?>
                    <p class="help-block"><?php echo $model->getError('languageId'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="control-group <?php if ($model->getError("hiddenFields")) echo 'error'; ?>">
            <label class="control-label" for="ProjectEditForm_hiddenFields"><?php echo Yii::t("app", "Hide Fields"); ?></label>
            <div class="controls">
                <ul style="list-style-type: none; margin-left: 0;">
                    <?php foreach ($fields as $f): ?>
                        <li>
                            <label style="padding-left: 15px; text-indent: -15px;">
                                <input style="margin: 0;" type="checkbox"
                                   id="ProjectEditForm_hiddenFields_<?= $f->name ?>"
                                   name="ProjectEditForm[hiddenFields][]"
                                   value="<?= $f->name ?>"
                                <?php if (in_array($f->name, $model->hiddenFields)) echo 'checked="checked"'; ?>>&nbsp;<?= CHtml::encode($f->title) ?></label>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <?php if ($model->getError("hiddenFields")): ?>
                    <p class="help-block"><?php echo $model->getError("hiddenFields"); ?></p>
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
            $("#ProjectEditForm_deadline").datepicker();
            $("#ProjectEditForm_startDate").datepicker();
        });
    </script>
<?php endif; ?>