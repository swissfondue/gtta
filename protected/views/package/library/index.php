<div class="active-header">
    <div class="pull-right">
        <ul class="nav nav-pills">
            <li><a href="<?php echo $this->createUrl("package/index"); ?>"><?php echo Yii::t("app", "Scripts"); ?></a></li>
            <li class="active"><a href="<?php echo $this->createUrl("package/libraries"); ?>"><?php echo Yii::t("app", "Libraries"); ?></a></li>
        </ul>
    </div>

    <div class="pull-right buttons">
        <a class="btn" href="<?php echo $this->createUrl("package/editlibrary") ?>"><i class="icon icon-plus"></i> <?php echo Yii::t("app", "New Library"); ?></a>&nbsp;
    </div>

    <h1>
        <?php echo CHtml::encode($this->pageTitle); ?>
    </h1>
</div>

<hr>

<div class="container">
    <div class="row">
        <div class="span8">
            <?php if (count($libraries) > 0): ?>
                <table class="table library-list">
                    <tbody>
                        <tr>
                            <th class="name"><?php echo Yii::t("app", "Library"); ?></th>
                            <th class="status">&nbsp;</th>
                            <th class="actions">&nbsp;</th>
                        </tr>
                        <?php foreach ($libraries as $library): ?>
                            <tr data-id="<?php echo $library->id; ?>" data-control-url="<?php echo $this->createUrl("package/control"); ?>">
                                <td class="name">
                                    <?php if ($library->status == Package::STATUS_INSTALLED): ?>
                                        <a href="<?php echo $this->createUrl("package/view", array("id" => $library->id)); ?>"><?php echo CHtml::encode($library->name); ?><?php echo $library->version ? " " . $library->version : ""; ?></a>
                                    <?php else: ?>
                                        <?php echo CHtml::encode($library->name); ?>
                                    <?php endif; ?>
                                </td>
                                <td class="status">
                                    <?php
                                        $labelClass = "";

                                        switch ($library->status) {
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
                                    <span class="label <?php echo $labelClass; ?>"><?php echo $library->statusName; ?></span>
                                </td>
                                <td class="actions">
                                    <?php if ($library->status != Package::STATUS_INSTALL && in_array($system->status, array(System::STATUS_IDLE, System::STATUS_PACKAGE_MANAGER))): ?>
                                        <a href="#del" title="<?php echo Yii::t("app", "Delete"); ?>" onclick="system.control.del(<?php echo $library->id; ?>);"><i class="icon icon-remove"></i></a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php echo $this->renderPartial("/layouts/partial/pagination", array("p" => $p, "url" => "package/libraries", 'params' => array())); ?>
            <?php else: ?>
                <?php echo Yii::t("app", "No libraries yet."); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
