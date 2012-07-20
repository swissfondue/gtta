<h1><?php echo CHtml::encode($this->pageTitle); ?></h1>

<hr>

<form class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

    <fieldset>
        <div class="control-group <?php if ($model->getError('name')) echo 'error'; ?>">
            <label class="control-label" for="ReferenceEditForm_name"><?php echo Yii::t('app', 'Name'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="ReferenceEditForm_name" name="ReferenceEditForm[name]" value="<?php echo CHtml::encode($model->name); ?>">
                <?php if ($model->getError('name')): ?>
                    <p class="help-block"><?php echo $model->getError('name'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="control-group <?php if ($model->getError('url')) echo 'error'; ?>">
            <label class="control-label" for="ReferenceEditForm_url"><?php echo Yii::t('app', 'URL'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="ReferenceEditForm_url" name="ReferenceEditForm[url]" value="<?php echo CHtml::encode($model->url); ?>">
                <?php if ($model->getError('url')): ?>
                    <p class="help-block"><?php echo $model->getError('url'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn"><?php echo Yii::t('app', 'Save'); ?></button>
        </div>
    </fieldset>
</form>
