<?php if (!$field->superHidden): ?>
    <?php
        $name = sprintf("CheckEditForm[fields][%s][%s]", $language->id, $field->global->name);
        $id = sprintf("CheckEditForm_fields_%s_%s", $language->id, $field->global->name);
        $value = isset($form->fields[$language->id][$field->global->name]) ? CHtml::encode($form->fields[$language->id][$field->global->name]) : "";
    ?>

    <div class="control-group">
        <label class="control-label" for="<?= $id ?>"><?= $field->global->localizedTitle ?></label>
        <div class="controls">
            <?php if (in_array($field->global->type, [GlobalCheckField::TYPE_TEXTAREA, GlobalCheckField::TYPE_WYSIWYG, GlobalCheckField::TYPE_WYSIWYG_READONLY])): ?>
                <?php $wysiwyg = in_array($field->global->type, [GlobalCheckField::TYPE_WYSIWYG, GlobalCheckField::TYPE_WYSIWYG_READONLY]); ?>
                <textarea class="max-width <?= $wysiwyg ? "wysiwyg" : '' ?>"
                          rows="10"
                          name="<?= $name ?>"
                          id="<?= $id ?>"
                    <?php if ($field->global->type == GlobalCheckField::TYPE_WYSIWYG_READONLY) echo "readonly"; ?>><?= $value ?></textarea>
            <?php endif; ?>

            <?php if ($field->global->type == GlobalCheckField::TYPE_TEXT): ?>
                <input type="text" class="input-xlarge" name="<?= $name ?>" value="<?= $value ?>">
            <?php endif; ?>

            <?php if ($field->global->type == GlobalCheckField::TYPE_RADIO): ?>
                <textarea class="input-xlarge" rows="10" name="<?= $name ?>" id="<?= isset($id) ? $id : '' ?>"><?= $value ?></textarea>
                <p class="help-block">
                    <?= $field->global->type == GlobalCheckField::TYPE_RADIO ? Yii::t("app", "Possible Values By Line-break") : "" ?>
                </p>
            <?php endif; ?>

            <?php if ($field->global->type == GlobalCheckField::TYPE_CHECKBOX): ?>
                <input type="checkbox" class="input-xlarge" name="<?= $name ?>" <?php if (isset($field->value) && $field->value) echo "checked"; ?>>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>