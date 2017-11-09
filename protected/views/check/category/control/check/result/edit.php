<script src="/ckeditor/ckeditor.js"></script>
<script src="/ckeditor/adapters/jquery.js"></script>

<form id="result-form-<?= $result->id; ?>" class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">
    <fieldset>
        <?php if ($view == Check::VIEW_TABBED): ?>
            <ul class="nav nav-tabs" id="languages-tab">
                <?php foreach ($languages as $language): ?>
                    <li<?php if ($language->default) echo ' class="active"'; ?>>
                        <a href="#<?php echo CHtml::encode($language->code); ?>">
                            <img src="<?php echo Yii::app()->baseUrl; ?>/images/languages/<?php echo CHtml::encode($language->code); ?>.png"
                                 alt="<?php echo CHtml::encode($language->name); ?>">
                            <?php echo CHtml::encode($language->name); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <div class=<?= ($view == Check::VIEW_TABBED) ? "tab-content" : "row" ?>>
            <?php foreach ($languages as $language): ?>
                <div class="<?= (($view == Check::VIEW_SHARED) ? "span6" : ("tab-pane" . $language->default ? " active" : "")) ?>  <?= ($view == Check::VIEW_SHARED) ? "span6-shared" : "" ?>"
                     id="<?php echo CHtml::encode($language->code); ?>">
                    <?php if ($view == Check::VIEW_SHARED): ?>
                        <div class="controls">
                            <img src="<?php echo Yii::app()->baseUrl; ?>/images/languages/<?php echo CHtml::encode($language->code); ?>.png"
                                 alt="<?php echo CHtml::encode($language->name); ?>">
                            <?php echo CHtml::encode($language->name); ?>
                        </div>
                        <br>
                    <?php endif; ?>
                    <div class="control-group <?php if ($model->getError('title')) echo 'error'; ?>">
                        <label class="control-label"
                               for="CheckResultEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_title"><?php echo Yii::t('app', 'Title'); ?></label>
                        <div class="controls">
                            <input type="text" class="input-xlarge"
                                   id="CheckResultEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_title_<?= $result ? $result->id : "new"; ?>"
                                   name="CheckResultEditForm[localizedItems][<?php echo CHtml::encode($language->id); ?>][title]"
                                   value="<?php echo isset($model->localizedItems[$language->id]) ? CHtml::encode($model->localizedItems[$language->id]['title']) : ''; ?>">
                            <?php if ($model->getError('title')): ?>
                                <p class="help-block"><?php echo $model->getError('title'); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="control-group <?php if ($model->getError('result')) echo 'error'; ?>">
                        <label class="control-label"
                               for="CheckResultEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_result"><?php echo Yii::t('app', 'Result'); ?></label>
                        <div class="controls">
                            <textarea
                                    class="wysiwyg <?php if (isset($model->localizedItems[$language->id])) echo(Utils::isHtml($model->localizedItems[$language->id]['result']) ? 'html_content' : ''); ?> <?= ($view == Check::VIEW_SHARED) ? "wysiwyg-shared" : "" ?>"
                                    id="CheckResultEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_result_<?= $result ? $result->id : "new"; ?>"
                                    name="CheckResultEditForm[localizedItems][<?php echo CHtml::encode($language->id); ?>][result]"><?php echo isset($model->localizedItems[$language->id]) ? CHtml::encode($model->localizedItems[$language->id]['result']) : ''; ?></textarea>

                            <?php if ($model->getError('result')): ?>
                                <p class="help-block"><?php echo $model->getError('result'); ?></p>
                            <?php endif; ?>

                            <p class="help-block">
                                <a class="btn btn-default" href="#editor"
                                   onclick="user.check.toggleEditor('CheckResultEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_result_<?= $result ? $result->id : "new"; ?>');">
                                    <span class="glyphicon glyphicon-edit"></span>
                                    <?php echo Yii::t("app", "WYSIWYG"); ?>
                                </a>
                            </p>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            <?php endforeach; ?>
        </div>
        <hr>
        <div class="control-group <?php if ($model->getError('sortOrder')) echo 'error'; ?>">
            <label class="control-label"
                   for="CheckResultEditForm_sortOrder"><?php echo Yii::t('app', 'Sort Order'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="CheckResultEditForm_sortOrder"
                       name="CheckResultEditForm[sortOrder]"
                       value="<?php echo $model->sortOrder ? $model->sortOrder : 0; ?>">
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
    var resId = <?php echo json_encode($result->id) ?>;

    <?php if (!$newRecord): ?>
        user.check.submitResultBlock(resId);
    <?php endif; ?>

    $("#languages-tab a").click(function (e) {
        e.preventDefault();
        $(this).tab("show");
    });

    $(function () {
        user.check.enableResultWysiwyg();
    });
</script>