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
                        <label class="control-label" for="ReportTemplateSummaryEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_title"><?php echo Yii::t('app', 'Title'); ?></label>
                        <div class="controls">
                            <input type="text" class="input-xlarge" id="ReportTemplateSummaryEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_title" name="ReportTemplateSummaryEditForm[localizedItems][<?php echo CHtml::encode($language->id); ?>][title]" value="<?php echo isset($model->localizedItems[$language->id]) ? CHtml::encode($model->localizedItems[$language->id]['title']) : ''; ?>">
                            <?php if ($model->getError('title')): ?>
                                <p class="help-block"><?php echo $model->getError('title'); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="control-group <?php if ($model->getError('summary')) echo 'error'; ?>">
                        <label class="control-label" for="ReportTemplateSummaryEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_summary"><?php echo Yii::t('app', 'Summary'); ?></label>
                        <div class="controls">
                            <textarea class="wysiwyg" style="height:200px;" id="ReportTemplateSummaryEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_summary" name="ReportTemplateSummaryEditForm[localizedItems][<?php echo CHtml::encode($language->id); ?>][summary]"><?php echo isset($model->localizedItems[$language->id]) ? str_replace('&', '&amp;', $model->localizedItems[$language->id]['summary']) : ''; ?></textarea>
                            <?php if ($model->getError('summary')): ?>
                                <p class="help-block"><?php echo $model->getError('summary'); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div>
            <hr>
        </div>

        <div class="control-group <?php if ($model->getError('ratingFrom') || $model->getError('ratingTo')) echo 'error'; ?>">
            <label class="control-label" for="ReportTemplateSummaryEditForm_ratingFrom"><?php echo Yii::t('app', 'Rating Range'); ?></label>
            <div class="controls">
                <input type="text" class="input-mini" id="ReportTemplateSummaryEditForm_ratingFrom" name="ReportTemplateSummaryEditForm[ratingFrom]" value="<?php echo $model->ratingFrom ? $model->ratingFrom : '0.00'; ?>">
                ..
                <input type="text" class="input-mini" id="ReportTemplateSummaryEditForm_ratingTo" name="ReportTemplateSummaryEditForm[ratingTo]" value="<?php echo $model->ratingTo ? $model->ratingTo : '0.00'; ?>">

                <?php if ($model->getError('ratingFrom')): ?>
                    <p class="help-block"><?php echo $model->getError('ratingFrom'); ?></p>
                <?php elseif ($model->getError('ratingTo')): ?>
                    <p class="help-block"><?php echo $model->getError('ratingTo'); ?></p>
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