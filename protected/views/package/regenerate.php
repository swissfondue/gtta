<h1><?php echo CHtml::encode($this->pageTitle); ?></h1>

<hr>

<?php if ($system->status == System::STATUS_REGENERATE_SANDBOX): ?>
    <div class="form-description">
        <?php
            echo Yii::t(
                "app",
                "Regenerating scripts sandbox. This may take up to several minutes or hours, please be patient."
            );
        ?>
    </div>

    <script>
        $(function () {
            admin.pkg.regenerate("<?php echo $this->createUrl("package/regeneratestatus"); ?>");
        });
    </script>
<?php else: ?>
    <p>
        <?php echo Yii::t("app", "Please note that sandbox regeneration may take up to several minutes or hours to complete."); ?>
        <?php echo Yii::t("app", "The regeneration process cannot be cancelled, so use this feature only if you have to."); ?>
    </p>

    <form id="RegenerateForm" class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post">
        <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">
        <input type="hidden" value="1" name="RegenerateForm[proceed]">

        <fieldset>
            <div class="form-actions">
                <button type="submit" class="btn"><?php echo Yii::t("app", "Regenerate"); ?></button>
            </div>
        </fieldset>
    </form>
<?php endif; ?>
