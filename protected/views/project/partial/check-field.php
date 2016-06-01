<?php $custom = isset($custom) && $custom; ?>

<?php if (($custom || !$field->superHidden) && !$project->isFieldHidden($field->name)): ?>
    <?php $value = $custom ? "" : $field->value; ?>
    <tr>
        <th>
            <?= $field->localizedTitle; ?>
        </th>
        <td class="text">
            <div class="limiter">
                <?php if (in_array($field->type, [GlobalCheckField::TYPE_TEXTAREA, GlobalCheckField::TYPE_WYSIWYG]) || $custom): ?>
                    <textarea class="max-width target-check-field <?= in_array($field->type, [GlobalCheckField::TYPE_WYSIWYG, GlobalCheckField::TYPE_WYSIWYG_READONLY]) ? "wysiwyg" : "" ?>"
                              rows="10"
                              id="<?= $htmlId ?>"
                              name="<?= $htmlName ?>"
                              data-field-name="<?= $field->name ?>"
                              <?php if ($field->type == GlobalCheckField::TYPE_WYSIWYG_READONLY && !$custom || $custom && User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>><?php echo CHtml::encode($value); ?></textarea>
                <?php endif; ?>

                <?php if ($field->type == GlobalCheckField::TYPE_WYSIWYG_READONLY && !$custom): ?>
                    <?= $value ? $value : Yii::t("app", "N/A"); ?>
                <?php endif ?>

                <?php if ($field->type == GlobalCheckField::TYPE_TEXT): ?>
                    <input type="text"
                           class="input-xlarge target-check-field"
                           id="<?= $htmlId?>"
                           name="<?= $htmlName ?>"
                           data-field-name="<?= $field->name ?>"
                           value="<?= $value ?>">
                <?php endif; ?>
                <?php if ($field->type == GlobalCheckField::TYPE_RADIO): ?>
                    <?php $possibleValues = @json_decode($field->field->getValue()); ?>

                    <?php if ($possibleValues): ?>
                        <ul style="list-style-type: none; margin-left: 0;">
                            <?php foreach ($possibleValues as $pv => $title): ?>
                                <li>
                                    <input type="radio"
                                           class="input-xlarge target-check-field"
                                           name="<?= $htmlName ?>"
                                           data-field-name="<?= $field->name ?>"
                                           value="<?= $value ?>"
                                           <?php if ($value == $pv) echo "checked=\"checked\""; ?>">&nbsp;<?= $title ?>
                                </li><br />
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <?= Yii::t("app", "No values.") ?>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ($field->type == GlobalCheckField::TYPE_CHECKBOX): ?>
                    <input type="checkbox"
                           class="input-xlarge target-check-field"
                           name="<?= $htmlName ?>"
                           data-field-name="<?= $field->name ?>"
                           id="<?= $htmlId ?>"
                           <?php if ($value) echo "checked"; ?>>
                <?php endif; ?>
            </div>
        </td>
    </tr>
<?php endif; ?>