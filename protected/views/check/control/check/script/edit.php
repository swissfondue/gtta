<div class="active-header">
    <div class="pull-right">
        <ul class="nav nav-pills">
            <li class="active"><a href="<?php echo $this->createUrl('check/editscript', array('id' => $category->id, 'control' => $control->id, 'check' => $check->id, 'script' => $script->id)); ?>"><?php echo Yii::t('app', 'Edit'); ?></a></li>
            <li><a href="<?php echo $this->createUrl('check/inputs', array('id' => $category->id, 'control' => $control->id, 'check' => $check->id, 'script' => $script->id)); ?>"><?php echo Yii::t('app', 'Inputs'); ?></a></li>
        </ul>
    </div>

    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<form class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

    <fieldset>
        <div class="control-group <?php if ($model->getError('name')) echo 'error'; ?>">
            <label class="control-label" for="CheckScriptEditForm_name"><?php echo Yii::t('app', 'Name'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="CheckScriptEditForm_name" name="CheckScriptEditForm[name]" value="<?php echo CHtml::encode($model->name); ?>">
                <?php if ($model->getError('name')): ?>
                    <p class="help-block"><?php echo $model->getError('name'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn"><?php echo Yii::t('app', 'Save'); ?></button>
        </div>
    </fieldset>
</form>