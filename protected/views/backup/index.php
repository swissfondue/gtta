<script>
    var checkBackup = function () {
        setTimeout(function () {
            admin.backup.check($('.backups-list').data('check-backup-url'), "backup");
        }, admin.backup.checkTimeout);
    };

    var checkRestore = function() {
        setTimeout(function () {
            admin.backup.check($('.backups-list').data('check-restore-url'), "restore");
        }, admin.backup.checkTimeout);
    };
</script>

<div class="pull-right buttons">
    <div class="btn-group" data-toggle="buttons-radio">
        <button id="backup" class="btn" onclick="admin.backup.create('<?php echo $this->createUrl("backup/create"); ?>', checkBackup);">
            <i class="icon icon-backup"></i>  <?php echo Yii::t("app", "Backup"); ?>
        </button>
    </div>
</div>

<h1><?php echo CHtml::encode($this->pageTitle); ?></h1>

<hr>
<div class="container">
    <div class="row">
        <div class="span8">
            <?php if (!empty($backups)): ?>
                <table class="table backups-list" data-check-restore-url="<?php echo $this->createUrl("backup/check", array( "action" => "restore" )); ?>" data-check-backup-url="<?php echo $this->createUrl("backup/check", array( "action" => "backup" )); ?>">
                    <tbody>
                        <tr>
                            <th class="created-at"><?php echo Yii::t("app", "Backups"); ?></th>
                            <th class="actions">&nbsp;</th>
                        </tr>
                        <?php foreach ($backups as $backup): ?>
                            <?php
                                $filename = substr($backup['filename'], 0, strpos($backup["filename"], "."));
                            ?>
                            <tr data-id="<?php echo $filename; ?>" data-control-url="<?php echo $this->createUrl("backup/controlbackup"); ?>">
                                <td class="created-at">
                                    <a href="<?php echo $this->createUrl("backup/download", array("filename" => $filename)); ?>" title="<?php echo Yii::t("app", "Download"); ?>">
                                        <?php echo $backup['created_at']; ?>
                                    </a>
                                </td>
                                <td class="actions">
                                    <a href="#restore" class="btn-restore" title="<?php echo Yii::t("app", "Restore"); ?>" onclick="system.control.restore('<?php echo $filename; ?>');"><i class="icon icon-restore"></i></a>&nbsp;
                                    <a href="#delete" title="<?php echo Yii::t("app", "Delete"); ?>" onclick="system.control.del('<?php echo $filename; ?>');"><i class="icon icon-remove"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <?php echo Yii::t("app", "No backups yet."); ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if ($backingup): ?>
    <script>
        $('#backup').prop("disabled", true);
        checkBackup();
    </script>
<?php elseif ($restoring): ?>
    <script>
        $("#backup").prop("disabled", true);
        checkRestore();
    </script>
<?php endif; ?>