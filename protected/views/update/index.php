<h1><?php echo CHtml::encode($this->pageTitle); ?></h1>

<hr>

<?php if ($updating): ?>
    <div class="form-description">
        <?php
            echo Yii::t(
                "app",
                "Updating GTTA to version {version}. This may take up to several minutes.",
                array("{version}" => $system->update_version)
            );
        ?>
    </div>
<?php elseif ($system->update_version && $forbidUpdate): ?>
    <div class="form-description">
        <?php echo $forbidMessage; ?>
    </div>
<?php endif; ?>

<form id="update-form" class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">
    <input type="hidden" value="1" name="UpdateForm[proceed]">

    <fieldset>
        <div class="control-group">
            <label class="control-label"><?php echo Yii::t("app", "Version"); ?></label>
            <div class="controls form-text">
                <?php if ($system->version): ?>
                    <?php echo $system->version; ?>

                    <?php if ($system->version_description): ?>
                        <a href="#toggle" class="info" onclick="system.toggleBlock('#version-description');" title="<?php echo Yii::t("app", "Description"); ?>"><i class="icon icon-question-sign"></i></a>
                    <?php endif; ?>
                <?php else: ?>
                    <?php echo Yii::t("app", "N/A"); ?>
                <?php endif; ?>

                <?php if ($system->version && $system->version_description): ?>
                    <div class="pre hide" id="version-description"><?php echo $system->version_description; ?></div>
                <?php endif; ?>

                <?php if ($system->update_time): ?>
                    <p class="small-block">
                        <?php echo Yii::t("app", "Updated at"); ?>:
                        <?php echo DateTimeFormat::toISO($system->update_time); ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label"><?php echo Yii::t("app", "Update"); ?></label>
            <div class="controls form-text">
                <?php if ($system->update_version): ?>
                    <?php echo $system->update_version; ?>

                    <?php if ($system->update_description): ?>
                        <a href="#toggle" class="info" onclick="system.toggleBlock('#update-description');" title="<?php echo Yii::t("app", "Description"); ?>"><i class="icon icon-question-sign"></i></a>
                    <?php endif; ?>

                    <?php if (!$forbidUpdate && !$updating): ?>
                        <a href="#update" title="<?php echo Yii::t("app", "Update"); ?>" onclick="$('#update-form').submit();"><i class="icon icon-refresh"></i></a>
                    <?php endif; ?>
                <?php else: ?>
                    <?php echo Yii::t("app", "N/A"); ?>
                <?php endif; ?>

                <?php if ($system->update_version && $system->update_description): ?>
                    <div class="pre hide" id="update-description"><?php echo $system->update_description; ?></div>
                <?php endif; ?>

                <?php if ($system->update_check_time): ?>
                    <p class="small-block">
                        <?php echo Yii::t("app", "Checked at"); ?>:
                        <?php echo DateTimeFormat::toISO($system->update_check_time); ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </fieldset>
</form>

<?php if ($updating): ?>
    <script>
        $(function () {
            admin.update.update("<?php echo $this->createUrl("update/status"); ?>");
        });
    </script>
<?php endif; ?>