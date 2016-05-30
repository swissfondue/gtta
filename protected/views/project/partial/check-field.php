<?php if (!$field->hidden): ?>
    <?php
        $name = sprintf("TargetCheckEditForm[fields][%s]", $field->name);
        $id = sprintf("TargetCheckEditForm_fields_%s", $field->name);
    ?>

    <tr>
        <th>
            <?= $field->localizedTitle; ?>
        </th>
        <td class="text">
            <div class="limiter">
                <?php if (in_array($field->type, [GlobalCheckField::TYPE_TEXTAREA, GlobalCheckField::TYPE_WYSIWYG])): ?>
                    <?php $wysiwyg = in_array($field->type, [GlobalCheckField::TYPE_WYSIWYG, GlobalCheckField::TYPE_WYSIWYG_READONLY]); ?>

                    <textarea class="max-width <?= $wysiwyg ? "wysiwyg" : "" ?>"
                              rows="10"
                              id="<?= $id ?>"
                              name="<?= $name ?>"
                              <?php if ($field->type == GlobalCheckField::TYPE_WYSIWYG_READONLY) echo "readonly"; ?>>
                        <?php echo CHtml::encode($field->value); ?>
                    </textarea>
                <?php endif; ?>

                <?php if ($field->type == GlobalCheckField::TYPE_WYSIWYG_READONLY): ?>
                    <?= $field->value; ?>
                <?php endif ?>

                <?php if ($field->type == GlobalCheckField::TYPE_TEXT): ?>
                    <input type="text"
                           class="input-xlarge"
                           id="<?= $id?>"
                           name="<?= $name ?>"
                           value="<?= $field->value ?>">
                <?php endif; ?>

                <?php if ($field->type == GlobalCheckField::TYPE_RADIO): ?>
                    <?php $values = @json_decode($field->value); ?>

                    <?php if ($values): ?>
                        <ul>
                            <?php foreach ($values as $value => $title): ?>
                                <li>
                                    <input type="radio" class="input-xlarge" name="<?= $name ?>" <?php if ($value == $field->value) echo "checked"; ?>"><?= $title ?>
                                </li><br>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <?= Yii::t("app", "No values.") ?>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ($field->type == GlobalCheckField::TYPE_CHECKBOX): ?>
                    <input type="checkbox" class="input-xlarge" name="<?= $name ?>" id="<?= $id ?>" <?php if (isset($field->value) && $field->value) echo "checked"; ?>>
                <?php endif; ?>
            </div>
        </td>
    </tr>
<?php endif; ?>