<div class="active-header">
    <?php if (!$control->isNewRecord): ?>
        <div class="pull-right">
            <ul class="nav nav-pills">
                <li><a href="<?php echo $this->createUrl('check/viewcontrol', array( 'id' => $category->id, 'control' => $control->id )); ?>"><?php echo Yii::t('app', 'View'); ?></a></li>
                <li class="active"><a href="<?php echo $this->createUrl('check/editcontrol', array( 'id' => $category->id, 'control' => $control->id )); ?>"><?php echo Yii::t('app', 'Edit'); ?></a></li>
            </ul>
        </div>
    <?php endif; ?>

    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<form class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

    <fieldset>
        <ul class="nav nav-tabs" id="languages-tab">
            <li class="active"><a href="#default"><?php echo Yii::t('app', 'Default'); ?></a></li>
            <?php foreach ($languages as $language): ?>
                <li>
                    <a href="#<?php echo CHtml::encode($language->code); ?>">
                        <img src="<?php echo Yii::app()->baseUrl; ?>/images/languages/<?php echo CHtml::encode($language->code); ?>.png" alt="<?php echo CHtml::encode($language->name); ?>">
                        <?php echo CHtml::encode($language->name); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active" id="default">
                <div class="control-group <?php if ($model->getError('name')) echo 'error'; ?>">
                    <label class="control-label" for="CheckControlEditForm_name"><?php echo Yii::t('app', 'Name'); ?></label>
                    <div class="controls">
                        <input type="text" class="input-xlarge" id="CheckControlEditForm_name" name="CheckControlEditForm[name]" value="<?php echo CHtml::encode($model->name); ?>" onkeyup="admin.check.updateTiedField('CheckControlEditForm_name', 'CheckControlEditForm_localizedItems_<?php echo $defaultLanguage; ?>_name');">
                        <?php if ($model->getError('name')): ?>
                            <p class="help-block"><?php echo $model->getError('name'); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php foreach ($languages as $language): ?>
                <div class="tab-pane" id="<?php echo CHtml::encode($language->code); ?>">
                    <div class="control-group">
                        <label class="control-label" for="CheckControlEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_name"><?php echo Yii::t('app', 'Name'); ?></label>
                        <div class="controls">
                            <input type="text" class="input-xlarge" id="CheckControlEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_name" name="CheckControlEditForm[localizedItems][<?php echo CHtml::encode($language->id); ?>][name]" value="<?php echo isset($model->localizedItems[$language->id]) ? CHtml::encode($model->localizedItems[$language->id]['name']) : ''; ?>">
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn"><?php echo Yii::t('app', 'Save'); ?></button>
        </div>
    </fieldset>
</form>

<script>
    $('#languages-tab a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    })
</script>