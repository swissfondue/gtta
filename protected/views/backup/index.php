<script>
    var checkBackup = function () {
        setTimeout(function () {
            admin.backup.check('<?php echo $this->createUrl("backup/check", array( "action" => "backup" )); ?>', "backup");
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
                <table class="table backups-list">
                    <tbody>
                        <tr>
                            <th class="created-at"><?php echo Yii::t("app", "Created At"); ?></th>
                            <th class="actions">&nbsp;</th>
                        </tr>
                        <?php foreach ($backups as $backup): ?>
                            <?php
                                $filename = substr($backup['filename'], 0, strpos($backup["filename"], "."));
                            ?>
                            <tr data-id="<?php echo $filename; ?>" data-control-url="<?php echo $this->createUrl("backup/controlbackup"); ?>">
                                <td class="created-at">
                                    <?php echo $backup['created_at']; ?>
                                </td>
                                <td class="actions">
                                    <a href="<?php echo $this->createUrl("backup/download", array( "filename" => $filename )); ?>" title="<?php echo Yii::t("app", "Download"); ?>"><i class="icon icon-download"></i></a>
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
<?php if ($backuping): ?>
    <script>
        $('#backup').button('loading');
        checkBackup();
    </script>
<?php endif; ?>