<?php if (in_array($type, [CheckField::TYPE_TEXTAREA, CheckField::TYPE_WYSIWYG, CheckField::TYPE_WYSIWYG_READONLY])): ?>
    <div class="control-group">
        <label class="control-label" for="<?= $id; ?>"><?= $label; ?></label>
        <div class="controls">
            <textarea class="wysiwyg" id="<?= $id ?>" name="<?= $name ?>"><?= $value ?></textarea>
        </div>
    </div>
<?php endif; ?>