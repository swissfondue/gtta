<div class="active-header">
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