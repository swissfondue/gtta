<script src="/ckeditor/ckeditor.js"></script>
<script src="/ckeditor/adapters/jquery.js"></script>

<h1><?php echo CHtml::encode($this->pageTitle); ?></h1>

<hr>

<form class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

    <fieldset>
        <div class="container">
            <div class="row">
                <div class="span8">
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
                                    <label class="control-label" for="ReportTemplateVulnSectionEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_title"><?php echo Yii::t('app', 'Title'); ?></label>
                                    <div class="controls">
                                        <input type="text" class="input-xlarge" id="ReportTemplateVulnSectionEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_title" name="ReportTemplateVulnSectionEditForm[localizedItems][<?php echo CHtml::encode($language->id); ?>][title]" value="<?php echo isset($model->localizedItems[$language->id]) ? CHtml::encode($model->localizedItems[$language->id]['title']) : ''; ?>">
                                        <?php if ($model->getError('title')): ?>
                                            <p class="help-block"><?php echo $model->getError('title'); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="control-group <?php if ($model->getError('intro')) echo 'error'; ?>">
                                    <label class="control-label" for="ReportTemplateVulnSectionEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_intro"><?php echo Yii::t('app', 'Section'); ?></label>
                                    <div class="controls">
                                        <textarea class="wysiwyg" style="height:200px;" id="ReportTemplateVulnSectionEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_intro" name="ReportTemplateVulnSectionEditForm[localizedItems][<?php echo CHtml::encode($language->id); ?>][intro]"><?php echo isset($model->localizedItems[$language->id]) ? str_replace('&', '&amp;', $model->localizedItems[$language->id]['intro']) : ''; ?></textarea>
                                        <?php if ($model->getError('intro')): ?>
                                            <p class="help-block"><?php echo $model->getError('intro'); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div>
                        <hr>
                    </div>
                    
                    <div class="control-group <?php if ($model->getError('categoryId')) echo 'error'; ?>">
                        <label class="control-label" for="ReportTemplateVulnSectionEditForm_categoryId"><?php echo Yii::t('app', 'Check Category'); ?></label>
                        <div class="controls">
                            <select class="input-xlarge" id="ReportTemplateVulnSectionEditForm_categoryId" name="ReportTemplateVulnSectionEditForm[categoryId]">
                                <option value="0"><?php echo Yii::t('app', 'Please select...'); ?></option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat->id; ?>" <?php if ($cat->id == $model->categoryId) echo 'selected'; ?>><?php echo CHtml::encode($cat->localizedName); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if ($model->getError('categoryId')): ?>
                                <p class="help-block"><?php echo $model->getError('categoryId'); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="control-group <?php if ($model->getError('sortOrder')) echo 'error'; ?>">
                        <label class="control-label" for="ReportTemplateVulnSectionEditForm_sortOrder"><?php echo Yii::t('app', 'Sort Order'); ?></label>
                        <div class="controls">
                            <input type="text" class="input-xlarge" id="ReportTemplateVulnSectionEditForm_sortOrder" name="ReportTemplateVulnSectionEditForm[sortOrder]" value="<?php echo $model->sortOrder ? $model->sortOrder : 0; ?>">
                            <?php if ($model->getError('sortOrder')): ?>
                                <p class="help-block"><?php echo $model->getError('sortOrder'); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="span4">
                    <?= $this->renderPartial("partial/variables"); ?>
                </div>
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
    });

    $(function () {
        $(".wysiwyg").ckeditor();
    });
</script>