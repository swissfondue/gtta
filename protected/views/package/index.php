<div class="active-header">
    <div class="pull-right">
        <ul class="nav nav-pills">
            <li class="active"><a href="<?php echo $this->createUrl("package/index"); ?>"><?php echo Yii::t("app", "Scripts"); ?></a></li>
            <li><a href="<?php echo $this->createUrl("package/libraries"); ?>"><?php echo Yii::t("app", "Libraries"); ?></a></li>
        </ul>
    </div>

    <div class="pull-right buttons">
        <a class="btn" href="<?php echo $this->createUrl("package/editscript") ?>"><i class="icon icon-plus"></i> <?php echo Yii::t("app", "New Script"); ?></a>&nbsp;

        <?php
            $disabled = false;

            if (!in_array($system->status, array(System::STATUS_IDLE, System::STATUS_REGENERATE_SANDBOX))) {
                $disabled = true;
            }
        ?>
        <a class="btn" href="<?php echo $disabled ? "#" : $this->createUrl("package/regenerate"); ?>" <?php if ($disabled) echo "disabled"; ?>><i class="icon icon-refresh"></i> <?php echo Yii::t("app", "Regenerate"); ?></a>
    </div>

    <h1>
        <?php echo CHtml::encode($this->pageTitle); ?>
    </h1>
</div>

<hr>

<div class="container">
    <div class="row">
        <div class="span8">
            <?php if (count($scripts) > 0): ?>
                <table class="table script-list">
                    <tbody>
                        <tr>
                            <th class="name"><?php echo Yii::t("app", "Script"); ?></th>
                            <th class="status">&nbsp;</th>
                            <th class="actions">&nbsp;</th>
                        </tr>
                        <?php foreach ($scripts as $script): ?>
                            <tr data-id="<?php echo $script->id; ?>" data-control-url="<?php echo $this->createUrl("package/control"); ?>">
                                <td class="name">
                                    <?php if ($script->status == Package::STATUS_INSTALLED): ?>
                                        <a href="<?php echo $this->createUrl("package/view", array("id" => $script->id)); ?>"><?php echo CHtml::encode($script->name); ?><?php echo $script->version ? " " . $script->version : ""; ?></a>
                                    <?php else: ?>
                                        <?php echo CHtml::encode($script->name); ?>
                                    <?php endif; ?>
                                </td>
                                <td class="status">
                                    <?php
                                        $labelClass = "";

                                        switch ($script->status) {
                                            case Package::STATUS_INSTALL:
                                                $labelClass = "label-install";
                                                break;

                                            case Package::STATUS_INSTALLED:
                                                $labelClass = "label-installed";
                                                break;

                                            case Package::STATUS_ERROR:
                                                $labelClass = "label-error";
                                                break;
                                        }
                                    ?>
                                    <span class="label <?php echo $labelClass; ?>"><?php echo $script->statusName; ?></span>
                                </td>
                                <td class="actions">
                                    <?php if (!$script->system && $script->status != Package::STATUS_INSTALL && in_array($system->status, array(System::STATUS_IDLE, System::STATUS_PACKAGE_MANAGER))): ?>
                                        <a href="#del" title="<?php echo Yii::t("app", "Delete"); ?>" onclick="system.control.del(<?php echo $script->id; ?>);"><i class="icon icon-remove"></i></a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php echo $this->renderPartial("/layouts/partial/pagination", array("p" => $p, "url" => "package/index", 'params' => array())); ?>
            <?php else: ?>
                <?php echo Yii::t("app", "No scripts yet."); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
