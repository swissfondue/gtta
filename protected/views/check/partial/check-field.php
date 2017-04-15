<?php if (!$field->hidden): ?>
    <?php
        $name = sprintf("CheckEditForm[fields][%s][%s]", $language->id, $field->name);
        $id = sprintf("CheckEditForm_fields_%s_%s", $language->id, $field->name);
        $value = isset($form->fields[$language->id]) && isset($form->fields[$language->id][$field->name]) ?
            $form->fields[$language->id][$field->name] : $field->value;
        $error = $form->getError("fields_" . $field->name);

        $hiddenName = sprintf("CheckEditForm[hidden][%s]", $field->name, $language->id);
        $hiddenId = sprintf("CheckEditForm_hidden_%s_%s", $field->name, $language->id);
    ?>

    <div class="control-group <?php if ($error) print 'error'; ?>">
        <label class="control-label" for="<?= $id ?>"><?= $field->localizedTitle ?></label>

        <div class="controls">
            <div class="field-control-<?= $field->name; ?> <?php if (isset($form->hidden[$field->name]) && $form->hidden[$field->name]) echo "hide"; ?>">
                <?php if (in_array($field->type, [GlobalCheckField::TYPE_TEXTAREA, GlobalCheckField::TYPE_WYSIWYG, GlobalCheckField::TYPE_WYSIWYG_READONLY])): ?>
                    <?php $wysiwyg = in_array($field->type, [GlobalCheckField::TYPE_WYSIWYG, GlobalCheckField::TYPE_WYSIWYG_READONLY]); ?>
                    <textarea
                        class="max-width <?= $wysiwyg ? "wysiwyg" : '' ?>"
                        rows="10"
                        name="<?= $name ?>"
                        id="<?= $id ?>"
                        <?php if ($field->type == GlobalCheckField::TYPE_WYSIWYG_READONLY) echo "readonly"; ?>><?= $value ?></textarea>
                <?php elseif ($field->type == GlobalCheckField::TYPE_TEXT): ?>
                    <input type="text" class="input-xlarge" name="<?= $name ?>" value="<?= $value ?>">
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
                                    <input type="text" class="input-xlarge" value="<?= $value ?>" placeholder="<?= Yii::t("app", "Option Text"); ?>"/>
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
                    <input type="checkbox" class="input-xlarge" name="<?= $name ?>" <?php if ($value) echo "checked"; ?>>
                <?php endif; ?>

                <div class="clearfix"></div>
            </div>

            <label>
                <input type="checkbox"
                   id="<?= $hiddenId ?>"
                   name="<?= $hiddenName ?>"
                   value="1"
                   <?php if (isset($form->hidden[$field->name]) && $form->hidden[$field->name]) echo "checked"; ?>
                   onchange="admin.check.toggleHiddenField(this, '<?= $field->name; ?>')"
                >&nbsp;<?= Yii::t("app", "Hidden"); ?>
            </label>
        </div>
    </div>
<?php endif; ?>