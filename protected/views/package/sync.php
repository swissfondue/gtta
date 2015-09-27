<h1><?php echo CHtml::encode($this->pageTitle); ?></h1>

<hr>

<?php if ($sync): ?>
    <div class="form-description" data-redirect-url="<?php print $this->createUrl("package/index"); ?>">
        <?php
        echo Yii::t(
            "app",
            "Git busy. This may take up to several minutes or hours, please be patient."
        );
        ?>
    </div>

    <script>
        $(function () {
            admin.pkg.checkSync("<?php echo $this->createUrl("package/syncStatus"); ?>");
        });
    </script>
<?php else: ?>
    <form class="form-horizontal" id="SyncForm" action="<?php print Yii::app()->request->url; ?>" method="POST">
        <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

        <fieldset>
            <div class="control-group">
                <label class="control-label" for="SyncForm_strategy"><?php echo Yii::t('app', 'Merge Strategy'); ?></label>
                <div class="controls">
                    <select class="input-xlarge" id="SyncForm_strategy" name="SyncForm[strategy]">
                        <option value="<?= System::GIT_MERGE_STRATEGY_OURS; ?>"><?php echo Yii::t("app", "Take My Version"); ?></option>
                        <option value="<?= System::GIT_MERGE_STRATEGY_THEIRS; ?>"><?php echo Yii::t("app", "Take Remote Version");; ?></option>
                    </select>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn"><?php echo Yii::t('app', 'Sync'); ?></button>
            </div>
        </fieldset>
    </form>
<?php endif; ?>