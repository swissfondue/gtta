<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap/bootstrap-wysihtml5.css">
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/wysihtml5.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/bootstrap/bootstrap-wysihtml5.js"></script>

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
                    <div class="control-group <?php if ($model->getError('title')) echo 'error'; ?>">
                        <label class="control-label" for="CheckSolutionEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_title"><?php echo Yii::t('app', 'Title'); ?></label>
                        <div class="controls">
                            <input type="text" class="input-xlarge" id="CheckSolutionEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_title" name="CheckSolutionEditForm[localizedItems][<?php echo CHtml::encode($language->id); ?>][title]" value="<?php echo isset($model->localizedItems[$language->id]) ? CHtml::encode($model->localizedItems[$language->id]['title']) : ''; ?>">
                            <?php if ($model->getError('title')): ?>
                                <p class="help-block"><?php echo $model->getError('title'); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="control-group <?php if ($model->getError('solution')) echo 'error'; ?>">
                        <label class="control-label" for="CheckSolutionEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_solution"><?php echo Yii::t('app', 'Solution'); ?></label>
                        <div class="controls">
                            <textarea class="wysiwyg" style="height:200px;" id="CheckSolutionEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_solution" name="CheckSolutionEditForm[localizedItems][<?php echo CHtml::encode($language->id); ?>][solution]"><?php echo isset($model->localizedItems[$language->id]) ? $model->localizedItems[$language->id]['solution'] : ''; ?></textarea>
                            <?php if ($model->getError('solution')): ?>
                                <p class="help-block"><?php echo $model->getError('solution'); ?></p>
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
            <label class="control-label" for="CheckSolutionEditForm_sortOrder"><?php echo Yii::t('app', 'Sort Order'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="CheckSolutionEditForm_sortOrder" name="CheckSolutionEditForm[sortOrder]" value="<?php echo $model->sortOrder ? $model->sortOrder : 0; ?>">
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

    $(function () {
        $('textarea').wysihtml5({
            'font-styles' : false,
            'image'       : false,
            'link'        : false,
            'html'        : false,
            'lists'       : false
        });
    });
</script>