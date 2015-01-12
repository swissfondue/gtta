<div class="active-header">
    <?php if (!$target->isNewRecord): ?>
        <div class="pull-right">
            <ul class="nav nav-pills">
                <li><a href="<?php echo $this->createUrl('project/target', array( 'id' => $project->id, 'target' => $target->id )); ?>"><?php echo Yii::t('app', 'View'); ?></a></li>
                <li class="active"><a href="<?php echo $this->createUrl('project/edittarget', array( 'id' => $project->id, 'target' => $target->id )); ?>"><?php echo Yii::t('app', 'Edit'); ?></a></li>
            </ul>
        </div>
        <div class="pull-right buttons">
            <?php if (User::checkRole(User::ROLE_USER)): ?>
                <a class="btn" href="<?php echo $this->createUrl('project/edittarget', array('id' => $project->id)); ?>"><i class="icon icon-plus"></i> <?php echo Yii::t("app", "Add Another Target"); ?></a>&nbsp;
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<form class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

    <fieldset>
        <div class="control-group <?php if ($model->getError('host')) echo 'error'; ?>">
            <label class="control-label" for="TargetEditForm_host"><?php echo Yii::t('app', 'Host'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="TargetEditForm_host" name="TargetEditForm[host]" value="<?php echo CHtml::encode($model->host); ?>">
                <?php if ($model->getError('host')): ?>
                    <p class="help-block"><?php echo $model->getError('host'); ?></p>
                <?php else: ?>
                    <p class="help-block">
                        <?php echo Yii::t("app", "Host name or IP address."); ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <div class="control-group <?php if ($model->getError('port')) echo 'error'; ?>">
            <label class="control-label" for="TargetEditForm_port"><?php echo Yii::t('app', 'Port'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="TargetEditForm_port" name="TargetEditForm[port]" value="<?php echo CHtml::encode($model->port); ?>">
                <?php if ($model->getError('port')): ?>
                    <p class="help-block"><?php echo $model->getError('port'); ?></p>
                <?php else: ?>
                    <p class="help-block">
                        <?php echo Yii::t("app", "For example, 443. You may leave this field blank."); ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="control-group <?php if ($model->getError('description')) echo 'error'; ?>">
            <label class="control-label" for="TargetEditForm_description"><?php echo Yii::t('app', 'Description'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="TargetEditForm_description" name="TargetEditForm[description]" value="<?php echo CHtml::encode($model->description); ?>">
                <?php if ($model->getError('description')): ?>
                    <p class="help-block"><?php echo $model->getError('description'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <?php if (count($categories)): ?>
            <div class="control-group">
                <label class="control-label"><?php echo Yii::t('app', 'Check Categories'); ?></label>
                <div class="controls">
                    <?php foreach ($categories as $category): ?>
                        <label class="checkbox">
                            <input type="checkbox" id="TargetEditForm_categoryIds_<?php echo $category->id; ?>" name="TargetEditForm[categoryIds][]" value="<?php echo $category->id; ?>" <?php if (in_array($category->id, $model->categoryIds)) echo 'checked'; ?>>
                            <?php echo CHtml::encode($category->localizedName); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (count($references)): ?>
            <div class="control-group">
                <label class="control-label"><?php echo Yii::t('app', 'References'); ?></label>
                <div class="controls">
                    <?php foreach ($references as $reference): ?>
                        <label class="checkbox">
                            <input type="checkbox" id="TargetEditForm_referenceIds_<?php echo $reference->id; ?>" name="TargetEditForm[referenceIds][]" value="<?php echo $reference->id; ?>" <?php if ($target->isNewRecord || in_array($reference->id, $model->referenceIds)) echo 'checked'; ?>>
                            <?php echo CHtml::encode($reference->name); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="form-actions">
            <button type="submit" class="btn"><?php echo Yii::t('app', 'Save'); ?></button>
        </div>
    </fieldset>
</form>
