<div class="active-header">
    <div class="pull-right buttons">
        <a class="btn" href="#expand-all" onclick="admin.riskCategory.expandAll();"><i class="icon icon-arrow-down"></i> <?php echo Yii::t('app', 'Expand'); ?></a>&nbsp;
        <a class="btn" href="#collapse-all" onclick="admin.riskCategory.collapseAll();"><i class="icon icon-arrow-up"></i> <?php echo Yii::t('app', 'Collapse'); ?></a>&nbsp;
    </div>

    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

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
                    <div class="control-group <?php if ($model->getError('name')) echo 'error'; ?>">
                        <label class="control-label" for="RiskCategoryEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_name"><?php echo Yii::t('app', 'Name'); ?></label>
                        <div class="controls">
                            <input type="text" class="input-xlarge" id="RiskCategoryEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_name" name="RiskCategoryEditForm[localizedItems][<?php echo CHtml::encode($language->id); ?>][name]" value="<?php echo isset($model->localizedItems[$language->id]) ? CHtml::encode($model->localizedItems[$language->id]['name']) : ''; ?>">
                            <?php if ($model->getError('name')): ?>
                                <p class="help-block"><?php echo $model->getError('name'); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <hr>

        <?php if (count($categories) > 0): ?>
            <div class="container">
                <div class="row">
                    <div class="span8">
                        <div class="risk-category-category-header-bold">
                            <?php echo Yii::t('app', 'Check'); ?>
                        </div>
                        <?php foreach ($categories as $category): ?>
                            <?php if (count($category->controls)): ?>
                                <?php foreach ($category->controls as $control): ?>
                                    <div class="risk-category-control-header" data-id="<?php echo $control->id; ?>">
                                        <a href="#toggle" onclick="admin.riskCategory.controlToggle(<?php echo $control->id; ?>);">
                                            <?php echo CHtml::encode($category->localizedName); ?>
                                            /
                                            <?php echo CHtml::encode($control->localizedName); ?>
                                        </a>
                                    </div>
                                    <div class="risk-category-control-content" data-id="<?php echo $control->id; ?>">
                                        <?php if (count($control->checks) > 0): ?>
                                            <?php
                                                foreach ($control->checks as $check):
                                                    $damage     = 1;
                                                    $likelihood = 1;

                                                    if ($check->riskCategories && $check->riskCategories[0])
                                                    {
                                                        $damage     = $check->riskCategories[0]->damage;
                                                        $likelihood = $check->riskCategories[0]->likelihood;
                                                    }
                                            ?>
                                                <div class="risk-category-check-header" data-id="<?php echo $check->id; ?>">
                                                    <a href="#toggle" onclick="admin.riskCategory.checkToggle(<?php echo $check->id; ?>);"><?php echo CHtml::encode($check->localizedName); ?></a>
                                                </div>
                                                <div class="risk-category-check-content hide" data-id="<?php echo $check->id; ?>">
                                                    <div class="control-group">
                                                        <label class="control-label"><?php echo Yii::t('app', 'Damage'); ?></label>
                                                        <div class="controls">
                                                            <?php for ($i = 1; $i <= 4; $i++): ?>
                                                                <input type="radio" name="RiskCategoryEditForm[checks][<?php echo $check->id; ?>][damage]" value="<?php echo $i; ?>" <?php if ($i == $damage) echo 'checked'; ?>>&nbsp;&nbsp;
                                                            <?php endfor; ?>
                                                        </div>
                                                    </div>
                                                    <div class="control-group">
                                                        <label class="control-label"><?php echo Yii::t('app', 'Likelihood'); ?></label>
                                                        <div class="controls">
                                                            <?php for ($i = 1; $i <= 4; $i++): ?>
                                                                <input type="radio" name="RiskCategoryEditForm[checks][<?php echo $check->id; ?>][likelihood]" value="<?php echo $i; ?>" <?php if ($i == $likelihood) echo 'checked'; ?>>&nbsp;&nbsp;
                                                            <?php endfor; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="risk-category-check-header">
                                                <?php echo Yii::t('app', 'No checks yet.'); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <?php echo Yii::t('app', 'No categories yet.'); ?>
        <?php endif; ?>

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