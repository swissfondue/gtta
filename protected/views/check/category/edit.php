<div class="active-header">
    <?php if (!$category->isNewRecord): ?>
        <div class="pull-right">
            <ul class="nav nav-pills">
                <li><a href="<?php echo $this->createUrl("check/view", array("id" => $category->id)); ?>"><?php echo Yii::t("app", "View"); ?></a></li>
                <li class="active"><a href="<?php echo $this->createUrl("check/edit", array("id" => $category->id)); ?>"><?php echo Yii::t("app", "Edit"); ?></a></li>
                <li><a href="<?php echo $this->createUrl("check/sharecategory", array("id" => $category->id)); ?>"><?php echo Yii::t("app", "Share"); ?></a></li>
            </ul>
        </div>
    <?php endif; ?>

    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<form class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

    <fieldset>
        <?php if ($view == Check::VIEW_TABBED): ?>
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
        <?php endif; ?>

        <div class=<?= ($view == Check::VIEW_TABBED) ? "tab-content" : "row" ?>>
            <?php foreach ($languages as $language): ?>
                <div class="<?= (($view == Check::VIEW_SHARED) ? "span6" : ("tab-pane" . $language->default ? " active" : "")) ?>" id="<?php echo CHtml::encode($language->code); ?>">
                    <?php if ($view == Check::VIEW_SHARED): ?>
                        <div class="controls">
                            <img src="<?php echo Yii::app()->baseUrl; ?>/images/languages/<?php echo CHtml::encode($language->code); ?>.png" alt="<?php echo CHtml::encode($language->name); ?>">
                            <?php echo CHtml::encode($language->name); ?>
                        </div>
                        <br>
                    <?php endif; ?>

                    <div class="control-group <?php if ($model->getError('name')) echo 'error'; ?>">
                        <label class="control-label" for="CheckCategoryEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_name"><?php echo Yii::t('app', 'Name'); ?></label>
                        <div class="controls">
                            <input type="text" class="input-xlarge" id="CheckCategoryEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_name" name="CheckCategoryEditForm[localizedItems][<?php echo CHtml::encode($language->id); ?>][name]" value="<?php echo isset($model->localizedItems[$language->id]) ? CHtml::encode($model->localizedItems[$language->id]['name']) : ''; ?>">
                            <?php if ($model->getError('name')): ?>
                                <p class="help-block"><?php echo $model->getError('name'); ?></p>
                            <?php endif; ?>
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