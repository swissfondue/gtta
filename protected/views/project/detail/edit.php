<h1><?php echo CHtml::encode($this->pageTitle); ?></h1>

<hr>

<form class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

    <fieldset>
        <div class="control-group <?php if ($model->getError('subject')) echo 'error'; ?>">
            <label class="control-label" for="ProjectDetailEditForm_subject"><?php echo Yii::t('app', 'Subject'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="ProjectDetailEditForm_subject" name="ProjectDetailEditForm[subject]" value="<?php echo CHtml::encode($model->subject); ?>">
                <?php if ($model->getError('subject')): ?>
                    <p class="help-block"><?php echo $model->getError('subject'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="control-group <?php if ($model->getError('content')) echo 'error'; ?>">
            <label class="control-label" for="ProjectDetailEditForm_content"><?php echo Yii::t('app', 'Content'); ?></label>
            <div class="controls">
                <textarea class="input-xlarge" rows="4" id="ProjectDetailEditForm_content" name="ProjectDetailEditForm[content]"><?php echo CHtml::encode($model->content); ?></textarea>
                <?php if ($model->getError('content')): ?>
                    <p class="help-block"><?php echo $model->getError('content'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn"><?php echo Yii::t('app', 'Save'); ?></button>
        </div>
    </fieldset>
</form>
