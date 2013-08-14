<h1><?php echo CHtml::encode($this->pageTitle); ?></h1>

<hr>

<div class="container">
    <div class="row">
        <div class="span8">
            <?php if (count($checks) > 0): ?>
                <table class="table check-list">
                    <tbody>
                        <tr>
                            <th class="name"><?php echo Yii::t("app", "Check"); ?></th>
                            <th class="actions">&nbsp;</th>
                        </tr>
                        <?php foreach ($checks as $check): ?>
                            <tr data-id="<?php echo $check->id; ?>" data-control-url="<?php echo $this->createUrl("check/control/check/control"); ?>">
                                <td class="name">
                                    <a href="<?php echo $this->createUrl("check/editincoming", array("id" => $check->id)); ?>"><?php echo CHtml::encode($check->localizedName); ?></a>
                                    <?php if ($check->automated): ?>
                                        <i class="icon-cog" title="<?php echo Yii::t("app", "Automated"); ?>"></i>
                                    <?php endif; ?>
                                </td>
                                <td class="actions">
                                    <a href="#del" title="<?php echo Yii::t("app", "Delete"); ?>" onclick="system.control.del(<?php echo $check->id; ?>);"><i class="icon icon-remove"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php echo $this->renderPartial("/layouts/partial/pagination", array("p" => $p, "url" => "checks/incoming")); ?>
            <?php else: ?>
                <?php echo Yii::t("app", "No checks yet."); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
