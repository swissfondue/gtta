<div class="active-header">
    <div class="pull-right buttons">
        <a class="btn" href="<?php echo $this->createUrl("package/new") ?>"><i class="icon icon-plus"></i> <?php echo Yii::t("app", "New Package"); ?></a>&nbsp;

        <?php
            $disabled = false;

            if (!in_array($system->status, array(System::STATUS_IDLE))) {
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
            <?php if (count($packages) > 0): ?>
                <table class="table package-list">
                    <tbody>
                        <tr>
                            <th class="name"><?php echo Yii::t("app", "Package"); ?></th>
                            <th class="status">&nbsp;</th>
                            <th class="actions">&nbsp;</th>
                        </tr>
                        <?php foreach ($packages as $package): ?>
                            <tr data-id="<?php echo $package->id; ?>" data-control-url="<?php echo $this->createUrl("package/control"); ?>">
                                <td class="name">
                                    <?php if ($package->isActive()): ?>
                                        <a href="<?php echo $this->createUrl("package/view", array("id" => $package->id)); ?>"><?php echo CHtml::encode($package->name); ?><?php echo $package->version ? " " . $package->version : ""; ?></a>
                                    <?php else: ?>
                                        <?php echo CHtml::encode($package->name); ?><?php echo $package->version ? " " . $package->version : ""; ?>
                                    <?php endif; ?>
                                </td>
                                <td class="status">
                                    <?php
                                        $labelClass = "";

                                        switch ($package->status) {
                                            case Package::STATUS_NOT_INSTALLED:
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
                                    <span class="label <?php echo $labelClass; ?>"><?php echo $package->statusName; ?></span>
                                </td>
                                <td class="actions">
                                    <?php if ($package->status != Package::STATUS_NOT_INSTALLED && $system->status == System::STATUS_IDLE): ?>
                                        <a href="#del" title="<?php echo Yii::t("app", "Delete"); ?>" onclick="system.control.del(<?php echo $package->id; ?>);"><i class="icon icon-remove"></i></a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php echo $this->renderPartial("/layouts/partial/pagination", array("p" => $p, "url" => "package/index", 'params' => array())); ?>
            <?php else: ?>
                <?php echo Yii::t("app", "No packages yet."); ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    setInterval(function () {
        admin.pkg.messages('<?php echo $this->createUrl('packages/messages'); ?>');
    }, 5000);
</script>