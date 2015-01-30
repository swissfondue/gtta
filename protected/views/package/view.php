<div class="active-header">
    <?php if (!$package->external_id): ?>
        <div class="pull-right">
            <ul class="nav nav-pills">
                <li class="active"><a href="<?php echo $this->createUrl("package/view", array("id" => $package->id)); ?>"><?php echo Yii::t("app", "View"); ?></a></li>
                <li class="dropdown" aria-expanded="false">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <?php echo Yii::t("app", "Edit"); ?>
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo $this->createUrl("package/editproperties", array("id" => $package->id)); ?>">Properties</a></li>
                        <li><a href="<?php echo $this->createUrl("package/editfiles", array("id" => $package->id)); ?>">Files</a></li>
                    </ul>
                </li>
                <li><a href="<?php echo $this->createUrl("package/share", array("id" => $package->id)); ?>"><?php echo Yii::t("app", "Share"); ?></a></li>
            </ul>
        </div>
    <?php endif; ?>

    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<form class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post">
    <fieldset>
        <div class="control-group">
            <label class="control-label"><?php echo Yii::t("app", "Type"); ?></label>
            <div class="controls form-text"><?php echo $data["type"]; ?></div>
        </div>

        <div class="control-group">
            <label class="control-label"><?php echo Yii::t("app", "Version"); ?></label>
            <div class="controls form-text"><?php echo $package->version; ?></div>
        </div>

        <div class="control-group">
            <label class="control-label"><?php echo Yii::t("app", "Dependencies"); ?></label>
            <div class="controls form-text">
                <?php
                    $deps = $data["dependencies"];

                    if ($deps["library"] || $deps["script"] || $deps["system"] || $deps["python"] || $deps["perl"]):
                ?>
                    <?php if ($data["dependencies"]["library"]): ?>
                        <?php echo Yii::t("app", "Library"); ?><br>
                        <ul>
                            <?php foreach ($data["dependencies"]["library"] as $id => $dep): ?>
                                <li>
                                    <a href="<?php echo $this->createUrl("package/view", array("id" => $id)); ?>"><?php echo $dep; ?></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                    <?php if ($data["dependencies"]["script"]): ?>
                        <?php echo Yii::t("app", "Script"); ?><br>
                        <ul>
                            <?php foreach ($data["dependencies"]["script"] as $id => $dep): ?>
                                <li>
                                    <a href="<?php echo $this->createUrl("package/view", array("id" => $id)); ?>"><?php echo $dep; ?></a>
                                </li
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                    <?php if ($data["dependencies"]["system"]): ?>
                        <?php echo Yii::t("app", "System"); ?><br>
                        <ul>
                            <?php foreach ($data["dependencies"]["system"] as $dep): ?>
                                <li><?php echo $dep; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                    <?php if ($data["dependencies"]["python"]): ?>
                        <?php echo Yii::t("app", "Python"); ?><br>
                        <ul>
                            <?php foreach ($data["dependencies"]["python"] as $dep): ?>
                                <li><?php echo $dep; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                    <?php if ($data["dependencies"]["perl"]): ?>
                        <?php echo Yii::t("app", "Perl"); ?><br>
                        <ul>
                            <?php foreach ($data["dependencies"]["perl"] as $dep): ?>
                                <li><?php echo $dep; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                <?php else: ?>
                    <?php echo Yii::t("app", "N/A"); ?>
                <?php endif; ?>
            </div>
        </div>
    </fieldset>
</form>