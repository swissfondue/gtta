<div class="check-field-item">
    <div class="control-group">
        <div class="controls">
            <input type="text" class="check-field-system-name input-xlarge" name="[name]" placeholder="<?= Yii::t("app", "System Name") ?>"
        </div>
    </div>

    <div class="control-group">
        <div class="controls">
            <input type="text" class="check-field-title input-xlarge" name="[title]" placeholder="<?= Yii::t("app", "Title") ?>" value="" />
        </div>
    </div>

    <div class="control-group">
        <div class="controls">
            <input type="radio" name="" value="textarea" /><?= Yii::t("app", "Textarea") ?><br>
            <input type="radio" name="" value="wysiwyg" /><?= Yii::t("app", "WYSIWYG") ?><br>
            <input type="radio" name="" value="wysiwyg-ro" /><?= Yii::t("app", "WYSIWYG (Read Only)") ?><br>
            <input type="radio" name="" value="text" /><?= Yii::t("app", "Text") ?><br>
            <input type="radio" name="" value="radio" /><?= Yii::t("app", "Radio") ?><br>
            <input type="radio" name="" value="checkbox" /><?= Yii::t("app", "Checkbox") ?><br>
        </div>
    </div>
</div>