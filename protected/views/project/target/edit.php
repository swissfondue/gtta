<div class="active-header">
    <div class="pull-right">
        <ul class="nav nav-pills">
            <li><a href="<?php echo $this->createUrl('project/target', array( 'id' => $project->id, 'target' => $target->id )); ?>"><?php echo Yii::t('app', 'View'); ?></a></li>
            <li class="active"><a href="<?php echo $this->createUrl('project/edittarget', array( 'id' => $project->id, 'target' => $target->id )); ?>"><?php echo Yii::t('app', 'Edit'); ?></a></li>
        </ul>
    </div>

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
                        <?php echo Yii::t('app', 'Host name or IP address.'); ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>

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

        <div class="form-actions">
            <button type="submit" class="btn"><?php echo Yii::t('app', 'Save'); ?></button>
        </div>
    </fieldset>
</form>
