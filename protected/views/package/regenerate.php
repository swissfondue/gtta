<h1><?php echo CHtml::encode($this->pageTitle); ?></h1>

<hr>

<div class="regenerate-description" data-back-url="<?php print $this->createUrl("package/index"); ?>">
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