<div class="active-header">
    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<form class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post">
    <input type="hidden" value="<?= $field->id ?>" name="GlobalCheckFieldEditForm[id]">
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

        <div class="control-group <?php if ($form->getError("name")) echo "error"; ?>">
            <label class="control-label" for="GlobalCheckFieldEditForm_name"><?php echo Yii::t("app", "Name"); ?></label>
            <div class="controls">
                <input type="text"
                       class="input-xlarge"
                       id="GlobalCheckFieldEditForm_name"
                       name="GlobalCheckFieldEditForm[name]"
                       value="<?php echo CHtml::encode($form->name); ?>"
                       <?php if (in_array($field->name, GlobalCheckField::$readonly)) print "readonly"; ?>>
                <?php if ($form->getError("name")): ?>
                    <p class="help-block"><?php echo $form->getError("name"); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="tab-content">
            <?php foreach ($languages as $language): ?>
                <div class="language-tab tab-pane<?php if ($language->default) echo ' active'; ?>" id="<?php echo CHtml::encode($language->code); ?>" data-language-id="<?= $language->id ?>">
                    <div class="control-group <?php if ($form->getError("title") || $form->getError("localizedItems")) echo "error"; ?>">
                        <label class="control-label" for="GlobalCheckFieldEditForm_localizedItems_<?= $language->id ?>_title"><?php echo Yii::t("app", "Title"); ?></label>
                        <div class="controls">
                            <input type="text" class="input-xlarge" id="GlobalCheckFieldEditForm_localizedItems_<?= $language->id ?>_title" name="GlobalCheckFieldEditForm[localizedItems][<?= $language->id?>][title]" value="<?= isset($form->localizedItems[$language->id]) ? str_replace('&', '&amp;', $form->localizedItems[$language->id]['title']) : ''; ?>">
                            <?php if ($form->getError("title") || $form->getError("localizedItems")): ?>
                                <p class="help-block"><?php echo $form->getError("title"); ?></p>
                                <p class="help-block"><?php echo $form->getError("localizedItems"); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="control-group <?php if ($form->getError("type")) echo "error"; ?>">
            <label class="control-label" for="GlobalCheckFieldEditForm_type"><?php echo Yii::t("app", "Type"); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="GlobalCheckFieldEditForm_type" name="GlobalCheckFieldEditForm[type]">
                    <?php foreach (GlobalCheckField::$fieldTypes as $key => $title): ?>
                        <option value="<?= $key ?>" <?php if ($form->type == $key) echo "selected"; ?>><?= $title; ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if ($form->getError("type")): ?>
                    <p class="help-block"><?php echo $form->getError("type"); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="control-group <?php if ($form->getError("hidden")) echo "error"; ?>">
            <label class="control-label" for="GlobalCheckFieldEditForm_hidden"><?php echo Yii::t("app", "Hidden"); ?></label>
            <div class="controls">
                <input type="checkbox" id="GlobalCheckFieldEditForm_hidden" name="GlobalCheckFieldEditForm[hidden]" value="1" <?php if ($form->hidden) echo "checked"; ?>>
                <?php if ($form->getError("hidden")): ?>
                    <p class="help-block"><?php echo $form->getError("hidden"); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn"><?php echo Yii::t("app", "Save"); ?></button>
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