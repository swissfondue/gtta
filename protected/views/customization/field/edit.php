<script src="/ckeditor/ckeditor.js"></script>
<script src="/ckeditor/adapters/jquery.js"></script>

<div class="active-header">
    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<form class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post" id="GlobalCheckFieldEditForm">
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
                <div class="language-tab tab-pane<?php if ($language->default) echo ' active'; ?>" id="<?php echo CHtml::encode($language->code); ?>" data-language-id="<?= $language->id ?>">
                    <div class="control-group <?php if ($form->getError("title") || $form->getError("localizedItems")) echo "error"; ?>">
                        <label class="control-label" for="GlobalCheckFieldEditForm_localizedItems_<?= $language->id ?>_title"><?php echo Yii::t("app", "Title"); ?></label>
                        <div class="controls">
                            <input type="text" class="input-xlarge" id="GlobalCheckFieldEditForm_localizedItems_<?= $language->id ?>_title" name="GlobalCheckFieldEditForm[localizedItems][<?= $language->id; ?>][title]" value="<?= isset($form->localizedItems[$language->id]) ? CHtml::encode($form->localizedItems[$language->id]["title"]) : ""; ?>">
                            <?php if ($form->getError("title") || $form->getError("localizedItems")): ?>
                                <p class="help-block"><?php echo $form->getError("title"); ?></p>
                                <p class="help-block"><?php echo $form->getError("localizedItems"); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (!$field->isNewRecord): ?>
                        <?php
                            $name = "GlobalCheckFieldEditForm[localizedItems][" . $language->id . "][value]";
                            $value = isset($form->localizedItems[$language->id]) && array_key_exists("value", $form->localizedItems[$language->id]) ? $form->localizedItems[$language->id]["value"] : "";
                        ?>

                        <div class="control-group">
                            <label class="control-label"">
                                <?php echo Yii::t("app", "Value"); ?>
                            </label>

                            <div class="controls">
                                <?php if (in_array($field->type, [GlobalCheckField::TYPE_TEXTAREA, GlobalCheckField::TYPE_WYSIWYG, GlobalCheckField::TYPE_WYSIWYG_READONLY])): ?>
                                    <?php $wysiwyg = in_array($field->type, [GlobalCheckField::TYPE_WYSIWYG, GlobalCheckField::TYPE_WYSIWYG_READONLY]); ?>

                                    <textarea
                                        class="max-width <?= $wysiwyg ? "wysiwyg" : '' ?>"
                                        rows="10"
                                        name="<?= $name; ?>"
                                        <?php if ($field->type == GlobalCheckField::TYPE_WYSIWYG_READONLY) echo "readonly"; ?>><?= $wysiwyg ? $value : CHtml::encode($value); ?></textarea>
                                <?php elseif ($field->type == GlobalCheckField::TYPE_TEXT): ?>
                                    <input type="text" class="input-xlarge" name="<?= $name; ?>" value="<?= CHtml::encode($value); ?>">
                                <?php elseif ($field->type == GlobalCheckField::TYPE_RADIO): ?>
                                    <?php $values = @json_decode($value); ?>

                                    <ul class="check-field-radio span4" data-field-name="<?= $name ?>" style="list-style-type: none; margin-left:0px;">
                                        <?php if (!count($values)): ?>
                                            <li class="radio-field-item">
                                                <input type="text" class="input-xlarge" placeholder="<?= Yii::t("app", "Option Text"); ?>"/>
                                                <a class="link" onclick="admin.check.removeRadioFieldItem(this); return false;"><i class="icon icon-remove"></i></a>
                                            </li>
                                        <?php else: ?>
                                            <?php foreach ($values as $value): ?>
                                                <li class="radio-field-item">
                                                    <input type="text" class="input-xlarge" value="<?= CHtml::encode($value); ?>" placeholder="<?= Yii::t("app", "Option Text"); ?>"/>
                                                    <a class="link" onclick="admin.check.removeRadioFieldItem(this); return false;"><i class="icon icon-remove"></i></a>
                                                </li>
                                            <?php endforeach; ?>
                                        <?php endif; ?>

                                        <button class="btn" onclick="admin.check.appendRadioFieldItem(this); return false;">
                                            <i class="icon icon-plus"></i>
                                            <?= Yii::t("app", "Add Option") ?>
                                        </button>
                                    </ul>
                                <?php elseif ($field->type == GlobalCheckField::TYPE_CHECKBOX): ?>
                                    <input type="checkbox" class="input-xlarge" name="<?= $name ?>" <?php if (isset($field->value) && $field->value) echo "checked"; ?>>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <hr>

        <div class="control-group <?php if ($form->getError("name")) echo "error"; ?>">
            <label class="control-label" for="GlobalCheckFieldEditForm_name"><?php echo Yii::t("app", "Name"); ?></label>
            <div class="controls">
                <input type="text"
                       class="input-xlarge"
                       id="GlobalCheckFieldEditForm_name"
                       name="GlobalCheckFieldEditForm[name]"
                       value="<?php echo CHtml::encode($form->name); ?>"
                       <?php if (in_array($field->name, GlobalCheckField::$system)) print "readonly"; ?>>
                <?php if ($form->getError("name")): ?>
                    <p class="help-block"><?php echo $form->getError("name"); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="control-group <?php if ($form->getError("type")) echo "error"; ?>">
            <label class="control-label" for="GlobalCheckFieldEditForm_type"><?php echo Yii::t("app", "Type"); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="GlobalCheckFieldEditForm_type" name="GlobalCheckFieldEditForm[type]" <?= !$newRecord ? "disabled" : "" ?>>
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
            <button type="submit" class="btn" onclick="admin.check.save('GlobalCheckFieldEditForm'); return false;"><?php echo Yii::t("app", "Save"); ?></button>
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