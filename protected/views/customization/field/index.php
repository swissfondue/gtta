<div class="active-header">
    <?php if (User::checkRole(User::ROLE_ADMIN)): ?>
        <div class="pull-right">
            <a class="btn" href="<?php echo $this->createUrl("customization/editcheckfield"); ?>"><i class="icon icon-plus"></i>&nbsp;<?php echo Yii::t("app", "New Field"); ?></a>
        </div>
    <?php endif; ?>

    <h1>
        <?php echo CHtml::encode($this->pageTitle); ?>
    </h1>
</div>

<hr>

<div class="container">
    <div class="row">
        <div class="span8">
            <?php if (count($fields)): ?>
                <table class="table field-list">
                    <tbody>
                    <tr>
                        <th class="name"><?php echo Yii::t("app", "Field"); ?></th>
                        <th class="type"><?php echo Yii::t("app", "Type"); ?></th>
                        <th class="visible"><?php echo Yii::t("app", "Visible"); ?></th>
                    </tr>
                        <?php foreach ($fields as $field): ?>
                            <tr data-id="<?= $field->id ?>" data-control-url="<?= $this->createUrl("customization/controlcheckfield", ["id" => $field->id]) ?>" data-sort-order="<?= $field->sort_order ?>">
                                <td class="name">
                                    <a href="<?= $this->createUrl("customization/editcheckfield", ["id" => $field->id]); ?>"><?= CHtml::encode($field->localizedTitle); ?></a>
                                </td>
                                <td class="type">
                                    <?= GlobalCheckField::$fieldTypes[$field->type]; ?>
                                </td>
                                <td class="visible">
                                    <?php if (!$field->hidden): ?>
                                        <i class="icon-ok"></i>
                                    <?php else: ?>
                                        <i class="icon-minus"></i>
                                    <?php endif; ?>
                                </td>
                                <td class="actions">
                                    <a href="#up" title="<?php echo Yii::t('app', 'Move Up'); ?>" onclick="system.control.up(<?= $field->id; ?>);">
                                        <i class="icon icon-arrow-up"></i>
                                    </a>
                                    <a href="#down" title="<?php echo Yii::t('app', 'Move Down'); ?>" onclick="system.control.down(<?= $field->id; ?>);">
                                        <i class="icon icon-arrow-down"></i>
                                    </a>
                                    <div class="action-del" style="width: 15px; height: 15px; display: inline-block;">
                                        <?php if (!in_array($field->name, GlobalCheckField::$system)): ?>
                                            <a href="#del" title="<?php echo Yii::t('app', 'Delete'); ?>" onclick="system.control.del(<?= $field->id; ?>);"><i class="icon icon-remove"></i></a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php echo $this->renderPartial("/layouts/partial/pagination", array("p" => $p, "url" => "customization/checksfields", "params" => array())); ?>
            <?php else: ?>
                <?php echo Yii::t("app", "No categories yet."); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
