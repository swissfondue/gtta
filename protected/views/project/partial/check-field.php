<?php
    $issue = isset($issue);
    $formName = $issue ? "IssueEvidenceEditForm" : "TargetCheckEditForm";
    $hidden = isset($hidden) ? $hidden : $field->getHidden();
    $hideRow = isset($hideRow) ? $hideRow : false;
?>

<?php if ($field && !$hidden): ?>
    <?php
        $name = sprintf("%s_%d[fields][%s]", $formName, $targetCheck->id, $field->name);
        $id = sprintf("%s_fields_%d_%s", $formName, $targetCheck->id, $field->name);
    ?>

    <tr id="row_<?= $targetCheck->id; ?>_<?= $field->name; ?>" <?php if ($hideRow) echo 'class="hide"'; ?>>
        <th>
            <?= $field->localizedTitle; ?>
        </th>
        <td class="text">
            <div class="limiter">
                <?php if (in_array($field->type, [GlobalCheckField::TYPE_TEXTAREA, GlobalCheckField::TYPE_WYSIWYG])): ?>
                    <?php $wysiwyg = in_array($field->type, [GlobalCheckField::TYPE_WYSIWYG, GlobalCheckField::TYPE_WYSIWYG_READONLY]); ?>

                    <textarea class="max-width target-check-field <?= ($wysiwyg || Utils::containsSpecificHtmlSymbols($field->value)) ? "wysiwyg" : "" ?>"
                              rows="10"
                              id="<?= $id ?>"
                              name="<?= $name ?>"
                        <?php if ($field->type == GlobalCheckField::TYPE_WYSIWYG_READONLY) echo "readonly"; ?>><?php echo CHtml::encode($field->value); ?></textarea>
                <?php endif; ?>

                <?php if ($field->type == GlobalCheckField::TYPE_WYSIWYG_READONLY): ?>
                    <?= $field->field->getValue() ? $field->field->getValue() : Yii::t("app", "N/A");; ?>
                <?php endif ?>
                <?php if ($field->type == GlobalCheckField::TYPE_TEXT): ?>
                    <input type="text"
                           class="input-xlarge target-check-field"
                           id="<?= $id?>"
                           name="<?= $name ?>"
                           value="<?= $field->value ?>">
                <?php endif; ?>
                <?php if ($field->type == GlobalCheckField::TYPE_RADIO): ?>
                    <?php $values = @json_decode($field->field->getValue()); ?>

                    <?php if ($values): ?>
                        <ul style="list-style-type: none; margin-left: 0;">
                            <li>
                                <label>
                                    <input type="radio"
                                           class="input-xlarge target-check-field"
                                           name="<?= $name ?>"
                                           value=""
                                           <?php if (!$field->value) echo "checked=\"checked\""; ?>/>&nbsp;<?= Yii::t("app", "N/A"); ?>
                                </label>
                            </li>
                            <?php foreach ($values as $value): ?>
                                <li>
                                    <label>
                                        <input type="radio"
                                           class="input-xlarge target-check-field"
                                           name="<?= $name ?>"
                                           value="<?= $value ?>"
                                        <?php if ($value == $field->value) echo "checked=\"checked\""; ?>">&nbsp;<?= $value ?></label>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <?= Yii::t("app", "No values.") ?>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ($field->type == GlobalCheckField::TYPE_CHECKBOX): ?>
                    <input type="checkbox" class="input-xlarge target-check-field" name="<?= $name ?>" id="<?= $id ?>" <?php if (isset($field->value) && $field->value) echo "checked"; ?>>
                <?php endif; ?>
            </div>

            <?php if ($field->name == GlobalCheckField::FIELD_RESULT): ?>
                <br>
                <a href="#show-result-fields" onclick="user.check.toggleAdditionalResultFields(<?php echo $targetCheck->id; ?>);">
                    <?php echo Yii::t("app", "Additional Result Fields"); ?>
                </a>
            <?php endif; ?>
        </td>
    </tr>

    <?php if ($field->name == GlobalCheckField::FIELD_RESULT): ?>
        <?php
            $this->renderPartial(
                "partial/check-field", [
                    "field" => $targetCheck->getField(GlobalCheckField::FIELD_TECHNICAL_RESULT),
                    "targetCheck" => $targetCheck,
                    "hidden" => false,
                    "hideRow" => true,
                ]
            );
        ?>
        <?php
            $this->renderPartial(
                "partial/check-field", [
                    "field" => $targetCheck->getField(GlobalCheckField::FIELD_MANAGEMENT_RESULT),
                    "targetCheck" => $targetCheck,
                    "hidden" => false,
                    "hideRow" => true,
                ]
            );
        ?>
    <?php endif; ?>
<?php endif; ?>
