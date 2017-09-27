<div class="active-header">
    <?php if (!$control->isNewRecord): ?>
        <div class="pull-right">
            <ul class="nav nav-pills">
                <li><a href="<?php echo $this->createUrl("check/viewcontrol", array("id" => $category->id, "control" => $control->id)); ?>"><?php echo Yii::t("app", "View"); ?></a></li>
                <li class="active"><a href="<?php echo $this->createUrl("check/editcontrol", array("id" => $category->id, "control" => $control->id)); ?>"><?php echo Yii::t("app", "Edit"); ?></a></li>
                <li><a href="<?php echo $this->createUrl("check/sharecontrol", array("id" => $category->id, "control" => $control->id)); ?>"><?php echo Yii::t("app", "Share"); ?></a></li>
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
                        <a>
                            <img src="<?php echo Yii::app()->baseUrl; ?>/images/languages/<?php echo CHtml::encode($language->code); ?>.png" alt="<?php echo CHtml::encode($language->name); ?>">
                            <?php echo CHtml::encode($language->name); ?>
                        </a>
                    <?php endif; ?>

                    <div class="control-group <?php if ($model->getError('name')) echo 'error'; ?>">
                        <label class="control-label" for="CheckControlEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_name"><?php echo Yii::t('app', 'Name'); ?></label>
                        <div class="controls">
                            <input type="text" class="input-xlarge" id="CheckControlEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_name" name="CheckControlEditForm[localizedItems][<?php echo CHtml::encode($language->id); ?>][name]" value="<?php echo isset($model->localizedItems[$language->id]) ? CHtml::encode($model->localizedItems[$language->id]['name']) : ''; ?>">
                            <?php if ($model->getError('name')): ?>
                                <p class="help-block"><?php echo $model->getError('name'); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <hr>
        <div class="control-group <?php if ($model->getError('categoryId')) echo 'error'; ?>">
            <label class="control-label" for="CheckControlEditForm_categoryId"><?php echo Yii::t('app', 'Category'); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="CheckControlEditForm_categoryId" name="CheckControlEditForm[categoryId]">
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