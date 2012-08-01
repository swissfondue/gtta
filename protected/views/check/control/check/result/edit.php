<h1><?php echo CHtml::encode($this->pageTitle); ?></h1>

<hr>

<form class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

    <fieldset>
        <ul class="nav nav-tabs" id="languages-tab">
            <?php foreach ($languages as $language): ?>
                <li<?php if ($language->default) echo ' class="active"'; ?>>
                    <a href="#<?php echo CHtml::encode($language->code); ?>">
                        <img src="<?php echo Yii::app()->baseUrl; ?>/images/languages/<?php echo CHtml::encode($language->code); ?>.png" alt="<?php echo CHtml::encode($language->name); ?>">
                        <?php echo CHtml::encode($language->name); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="tab-content">
            <?php foreach ($languages as $language): ?>
                <div class="tab-pane<?php if ($language->default) echo ' active'; ?>" id="<?php echo CHtml::encode($language->code); ?>">
                    <div class="control-group <?php if ($model->getError('result')) echo 'error'; ?>">
                        <label class="control-label" for="CheckResultEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_result"><?php echo Yii::t('app', 'Result'); ?></label>
                        <div class="controls">
                            <input type="text" class="input-xlarge" id="CheckResultEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_result" name="CheckResultEditForm[localizedItems][<?php echo CHtml::encode($language->id); ?>][result]" value="<?php echo isset($model->localizedItems[$language->id]) ? CHtml::encode($model->localizedItems[$language->id]['result']) : ''; ?>">
                            <?php if ($model->getError('result')): ?>
                                <p class="help-block"><?php echo $model->getError('result'); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div>
            <hr>
        </div>

        <div class="control-group <?php if ($model->getError('sortOrder')) echo 'error'; ?>">
            <label class="control-label" for="CheckResultEditForm_sortOrder"><?php echo Yii::t('app', 'Sort Order'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="CheckResultEditForm_sortOrder" name="CheckResultEditForm[sortOrder]" value="<?php echo $model->sortOrder ? $model->sortOrder : 0; ?>">
                <?php if ($model->getError('sortOrder')): ?>
                    <p class="help-block"><?php echo $model->getError('sortOrder'); ?></p>
                <?php endif; ?>
            </div>
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