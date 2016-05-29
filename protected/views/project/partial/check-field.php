<?php
    $id = isset($id) ? $id : "";
    $name = isset($name) ? $name : "";
?>

<div class="control-group">
    <label class="control-label" for="<?= $id ?>"><?php echo Yii::t('app', 'Name'); ?></label>
    <div class="controls">
        <?php if (in_array($field->global->type, [GlobalCheckField::TYPE_TEXTAREA, GlobalCheckField::TYPE_WYSIWYG, GlobalCheckField::TYPE_WYSIWYG_READONLY])): ?>
            <?php $wysiwyg = in_array($field->global->type, [GlobalCheckField::TYPE_WYSIWYG, GlobalCheckField::TYPE_WYSIWYG_READONLY]); ?>

            <textarea class="max-width <?= $wysiwyg ? "wysiwyg" : "" ?>"
                      rows="10"
                      id="<?= $id ?>"
                      name="<?= $name ?>"
                      <?php if ($field->global->type == GlobalCheckField::TYPE_WYSIWYG_READONLY) echo "readonly"; ?>>
                <?php echo CHtml::encode($field->value); ?>
            </textarea>
        <?php endif; ?>

        <?php if ($field->global->type == GlobalCheckField::TYPE_TEXT): ?>
            <input type="text"
                   class="input-xlarge"
                   id="<?= $id?>"
                   name="<?= $name ?>"
                   value="<?= $field->value ?>">
        <?php endif; ?>

        <?php if ($field->global->type == GlobalCheckField::TYPE_RADIO): ?>
            <?php $possibleValues = json_decode($field->field->possible_values); ?>

            <?php foreach ($possibleValues as $pv): ?>
                <input type="radio"
                       class="input-xlarge"
                       name="<?= $name ?>"
                       <?php if ($pv == $field->value) echo "checked"; ?>">
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if ($field->global->type == GlobalCheckField::TYPE_CHECKBOX): ?>
            <input type="checkbox" class="input-xlarge" name="<?= $name ?>" id="<?= $id ?>" <?php if (isset($field->value) && $field->value) echo "checked"; ?>>
        <?php endif; ?>
    </div>
</div>